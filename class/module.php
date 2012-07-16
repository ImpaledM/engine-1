<?
/**
 * 1. вызов в в конструкторе класса наследника parent::__construct ();
 *
 * $field_rules json строка :
 * - city : имеет смысл только параметр false, по дефолту true
 * - photo_one : одно или несколько полей для загрузки по одному фото
 * - photo_multi : одно или несколько полей для загрузки неограниченного числа фото
 * - bridge : необходимо передать имя поля в мосте касающегося второй таблицы
 * - checkbox : имя поля при отсутсвии которого в POST в БД будет обнуляться значение
 *
 * $field_verify json строка :
 * - empty : спсиок полей которые проверяются на заполнение
 *

 */
class Module extends Cache{
	public $db, $table, $verify = true, $where = '', $order = 't.name', $admin_brief=false,
	$islogin = true,
	$message = false,
	$get_list_mode = 'xml',
	$item_on_page_client = ITEM_ON_PAGE,
	$item_on_page_admin = ITEM_ON_PAGE;

	private $rules, $field_verify;

	function __construct($table=false, $field_rules = null, $field_verify = null) {
		$this->rules = json_decode ( $field_rules );
		$this->field_verify = json_decode ( $field_verify );
		$this->db = new Db ();
		if ($table) {
			$lang = (isset($_GET['lang'])) ? '_'.$_GET['lang'] : '';
			$this->table=($this->db->table_seek($table.$lang)) ? $table.$lang : $table;
		} else {
			$this->table=get_class($this);
		}
	}

	function is_owner($id){
		if ($this->db->get_one('SHOW FIELDS FROM `' . $this->table . '` WHERE Field="id_user"')){
			$id_user=$this->db->get_one('SELECT `id_user` FROM ! WHERE id=?', array ($this->table, $id ) );
			return ($id_user!=$_SESSION['user']['id'] && $_SESSION['user']['role']!=1)?false:true;
		}else 	return true;
	}

	function ajax_show() {
		if (isset ( $_REQUEST ['cmd'] ) && method_exists ( $this, 'cmd_' . $_REQUEST ['cmd'] )) {
			$cmd = 'cmd_' . $_REQUEST ['cmd'];
			return $this->$cmd ();
		}
	}
	function brief() {
		XML::from_db ( '/', 'SELECT * FROM `' . $this->table . '` WHERE `active`="1" ', null, 'brief' );
	}

	function show() {
		if (isset ( $_GET ['ACTIVE'] )) $this->active (  intval($_GET ['ACTIVE']));
		if (isset ( $_GET ['DEL'] )) $this->delete ( $_GET ['DEL'] );

		if ((isset ( $_POST ['save'] ) || isset ( $_POST ['savePublic'] ) || isset ( $_POST ['unsavePublic'] )) && $this->verify ()) {
			if (isset ( $_POST ['savePublic'] ) || isset ( $_GET ['ajax'] )) $_POST ['active'] = 1;
			if (isset ( $_POST ['unsavePublic'] )) $_POST ['active'] = 0;
			$id = (isset ( $_REQUEST ['EDIT'] )) ? intval ( $_REQUEST ['EDIT'] ) : null;
			$this->save ( $id );
			if (! isset ( $_GET ['ajax'] )) {
				$location = 'Location: ' . rtrim(DOMAIN,'/');
				$location .= rtrim ( (isset ( $_GET ['ADD'] )) ? GET ( 'ADD' ) : GET ( 'EDIT' ), '?' );
				header ( $location );
				die ();
			}
		}

		if (isset ( $_GET ['ITEM'] )) {
			$this->item ( $_GET ['ITEM'] );
		} elseif (isset ( $_GET ['ADD'] )) {
			unset ( $_SESSION ['edit_owner'] );
			$this->add ();
		} elseif (isset ( $_GET ['EDIT'] )) {
			$this->add ( $_GET ['EDIT'] );
		} else {
			$this->get_list ();
		}
	}

	function add($id = false, $fields="*") {
		if ($this->islogin)	Utils::isLogin ($this->message);
		XML::add_node ( '/', 'edit' );
		if (! empty ( $_POST )) {
			XML::from_array ( '//edit', array ($_POST ) );
		} else {
			if ($this->islogin) {
				return XML::from_db( '//edit', 'SELECT '.$fields.' FROM `'.$this->table.'` WHERE `id`=? AND (`id_user`=? OR ?&1=1)', array($id, $_SESSION['user']['id'], $_SESSION['user']['role']));
			} else {
				return XML::from_db ( '//edit', 'SELECT '.$fields.' FROM `' . $this->table . '` WHERE `id`=?', $id );
			}
		}
		return null;
	}

	function active($id) {
		if ($this->is_owner($id))
			exit ($this->db->query ( 'UPDATE `' . $this->table . '` SET active=NOT(active) WHERE id="' . $id.'"'));
		else
			exit(Error::status(403));
	}

	function verify() {
		if (isset ( $this->field_verify->empty )) {
			foreach ( ( array ) $this->field_verify->empty as $field => $value ) {
				if (!isset($_POST [$field]) || @$_POST [$field] == '' || (is_string($_POST [$field]) && strtoupper ( @$_POST [$field] ) == 'NULL')) {
					$this->verify = false;
					Message::error ( 'Не заполнено поле "' . $value . '"' );
				}
			}
		}
		return $this->verify;
	}

	function delete($id) {
		if (!$this->is_owner($id)) return false;

		if (isset ( $this->rules->photo_one )) {
			$row = $this->db->get_row ( 'SELECT * FROM ! WHERE id=?', array ($this->table, $id ) );
			foreach ( ( array ) $this->rules->photo_one as $field ) {
				if (isset($row [$field]) && trim($row [$field])!='') @unlink ( ROOT . 'uploads/' . $this->table . '/' . $row [$field] );
			}
		}
		if (isset ( $this->rules->photo_multi )) {
			foreach ( ( array ) $this->rules->photo_multi as $field ) {
				$res = $this->db->query ( 'SELECT name FROM ' . $this->table . '_file WHERE id_parent=? AND field=?', array ($id, $field ) );
				while ( $row = $this->db->fetch ( $res ) ) {
					@unlink ( ROOT . 'uploads/' . $this->table . '/' . $row ['name'] );
				}
			}
		}
		$this->db->query ( 'DELETE FROM ! WHERE id=?', array ($this->table, $id ) );

		$location = 'Location: ' . rtrim(DOMAIN,'/');
		$location .= rtrim ( GET ( 'DEL' ), '?' );
		header ( $location );
		die ();

	}

	function save_city() {
		$param_city = '';
		if (@$_POST ['city'] != '') {
			$arCity = explode ( ',', $_POST ['city'] );
			$id_city = $this->db->get_one ( ('SELECT id FROM geo_city WHERE name LIKE "' . trim ( $arCity [0] ) . '" AND id_country=(SELECT id FROM `geo_country` WHERE name LIKE "' . trim ( @$arCity [1] ) . '")') );
			if ($id_city) $param_city = $id_city;
		}
		return $param_city;
	}

	function save($id = null, $message = false) {
		if (!isset($_POST['id_section']) && isset($_GET['section']))	$_POST['id_section']=$_GET['section'];
		//var_dump($_POST);die();

		if (isset($_SESSION['user']) && $_SESSION['user']['role']!=1 && isset($this->field_verify->none_save)){
			$mode=(is_null($id))?1:2;
			foreach ( ( array ) $this->field_verify->none_save as $field=>$value)
				if (isset($_POST [$field]) && ($value==$mode || $value==3)) unset($_POST [$field]);
		}

		if (!is_null($id) && !$this->is_owner($id)) {
			ERROR::status(403);
			return false;
		}

		if (! isset ( $_POST ['alias'] )) {
			if (isset ( $_POST ['title'] ))
				$_POST ['alias'] = translitUrl ( $_POST ['title'] );
			if (isset ( $_POST ['name'] ))
				$_POST ['alias'] = translitUrl ( $_POST ['name'] );
		}
		$fields = $params = array ();
		$field_active = false;
		$res = $this->db->query ( 'SHOW COLUMNS FROM !', $this->table );
		while ( $row = $this->db->fetch ( $res ) ) {
			if ($row ['Field'] == 'active')
				$field_active = true;
			if ($row ['Field'] == 'id_city' && isset ( $this->rules->city)) {
				$fields [$row ['Field']] = '`' . $row ['Field'] . '`=?';
				$params [] = $this->save_city ();
			} elseif (isset ( $_POST [$row ['Field']] )) {
				//TODO не проходят NULL
				$fields [$row ['Field']] = '`' . $row ['Field'] . '`';
				$fields [$row ['Field']] .= (strtoupper ( $_POST [$row ['Field']] ) == 'NULL') ? '=!' : '=?';
				if (isset ( $this->rules->photo_one ) && in_array ( $row ['Field'], ( array ) $this->rules->photo_one )) {
					$params [] = pathinfo ( $_POST [$row ['Field']], PATHINFO_BASENAME );
				} else {
					$params [] = rtrim(trim($_POST [$row ['Field']] ), ',');
				}
			} elseif ($row ['Field'] == 'id_user') {
				//TODO теоритически дырка, если постом передать id_user
				if (is_null ( $id )) {
					$fields [$row ['Field']] = '`' . $row ['Field'] . '`=?';
					$params [] = (isset ( $_SESSION ['edit_owner'] )) ? $_SESSION ['edit_owner'] : @$_SESSION ['user'] ['id'];
				}
			} elseif (isset ( $this->rules->checkbox )) {
				foreach ( ( array ) $this->rules->checkbox as $field ) {
					if ($row ['Field'] == $field) {
						$fields [$row ['Field']] = '`' . $row ['Field'] . '`=?';
						$params [] = 0;
					}
				}
			}
		}
		if (count ( $fields ) > 0) {
			$add_query = '`' . $this->table . '` SET ' . implode ( ', ', $fields );
			if (is_null ( $id )) {
				if (isset($this->rules->create_date)) {
					foreach ( ( array ) $this->rules->create_date as $field ) {
						$add_query.=', '.$field.'=NOW()';
					}
				}
				$this->db->query ( 'INSERT ' . $add_query, $params );
				$id = $this->db->last_id ();
			} else {
				$params [] = $id;
				$this->db->query ( 'UPDATE ' . $add_query . ' WHERE id=?', $params );
			}
		}
		if (isset ( $this->rules->bridge )) {
			foreach ( ( array ) $this->rules->bridge as $field ) {
				$ar_field = explode ( '_', $field );
				if (is_array ( $ar_field )) {
					$other_table = $ar_field [1];
					$this->db->query ( 'DELETE FROM ' . $this->table . '_' . $other_table . ' WHERE id_' . $this->table . '=?', $id );
					if (isset ( $_POST [$field] ) && $_POST [$field] != 'NULL') {
						$query = 'INSERT ' . $this->table . '_' . $other_table . ' SET ' . $field . '=?, id_' . $this->table . '=?'; // переделать  на VALUES
						if (is_array ( $_POST [$field] )) {
							foreach ( $_POST [$field] as $other_id => $value ) {
								$this->db->query ( $query, array ($other_id, $id ) );
							}
						} else {
							$this->db->query ( $query, array ($_POST [$field], $id ) );
						}
					}
				}
			}
		}

		if (isset ( $this->rules->photo_one )) {
			foreach ( ( array ) $this->rules->photo_one as $field ) {
				$uploads_path = (isset ( $_POST ['uploads_path'] [$field] )) ? $uploads_path = $_POST ['uploads_path'] [$field] : $this->table;
				if (isset ( $_POST [$field] ) && trim ( $_POST [$field] ) != '') {
					if (Utils::moveFile ( ROOT . $_POST [$field], ROOT . 'uploads/' . $uploads_path . '/' . pathinfo ( $_POST [$field], PATHINFO_BASENAME ) )) {
						$_POST [$field] = pathinfo ( $_POST [$field], PATHINFO_BASENAME );
					}
				}
			}
		}

		if (isset ( $this->rules->photo_multi )) {
			foreach ( ( array ) $this->rules->photo_multi as $field ) {
				if (isset ( $_POST [$field] ) && is_array ( $_POST [$field] )) {

					$uploads_path = (isset ( $_POST ['uploads_path'] [$field] )) ? $uploads_path = $_POST ['uploads_path'] [$field] : $this->table;

					$this->db->query ( 'DELETE FROM ' . $this->table . '_file WHERE id_parent=? AND field=?', array ($id, $field ) );

					foreach ( ( array ) $_POST [$field] as $key => $path ) {
						$this->db->query ( 'INSERT ' . $this->table . '_file SET id_parent=?, field=?, name=?, note=?', array ($id, $field, pathinfo ( $path, PATHINFO_BASENAME ), @$_POST ['note'] [$key] ) );
						if (Utils::moveFile ( ROOT . $path, ROOT . 'uploads/' . $uploads_path . '/' . pathinfo ( $path, PATHINFO_BASENAME ) )) {
							$_POST [$field] [$key] = pathinfo ( $path, PATHINFO_BASENAME );
						}
					}
				}
			}
		}
		if ($message === false) {
			if ($field_active) {
				$active = $this->db->get_one ( 'SELECT active FROM ' . $this->table . ' WHERE id=' . $id );
			}
			$message = (! isset ( $active ) || $active == 1) ? 'Данные сохранены!' : 'Данные сохранены, но не будут отображаться до тех пор, пока Вы не нажмете на линк [&nbsp;отобразить&nbsp;] напротив соответствующей записи!';
		}
		Message::success ( $message );
		if (POST_REPORT && DEBUG == 0) {
			Error::report ( $id, true );
		}
		return $id;
	}

	function item($id, $query='',$params=null) {
		$ar = ($query=='')
		? XML::from_db ( '/', 'SELECT * FROM `' . $this->table . '`WHERE `id`="' . $id . '"')
		: XML::from_db ( '/', $query, $params);
		return $ar;
	}

	function setMeta($ar) {
		$title = array('meta_title', 'name', 'title');
		$description = array('meta_description', 'anons', 'description');
		foreach ($title as $value) {
			if (isset($ar[0][$value]) && $ar[0][$value]!='') {
				Utils::setMeta( $ar[0][$value]);
				break;
			}
		}
		foreach ($description as $value) {
			if (isset($ar[0][$value]) && $ar[0][$value]!='') {
				Utils::setMeta( $ar[0][$value],'description');
				break;
			}
		}
	}

	function get_list($query = '', $param = null, $item_on_page = false, $visible_pages = VISIBLE_PAGES) {
		if ($query == '')	$query2 = 'SELECT SQL_CALC_FOUND_ROWS t.id, t.name, t.active';
		if (isset ( $_GET ['ADMIN'] )) {
			Utils::isLogin ( $this->message );
			if ($query == '')	$query2 .= ' FROM ' . $this->table . ' AS t WHERE (t.id_user=' . $_SESSION ['user'] ['id'] . ' OR (' . $_SESSION ['user'] ['role'] . ' & 1 = 1))';
			if ($this->where != '')	$this->where = ' AND ' . $this->where;
			$tag_name = 'list_admin';
			if (!$item_on_page)	$item_on_page = $this->item_on_page_admin;
		} else {
			if ($query == '')
				$query2 .= ' FROM ' . $this->table . ' AS t';
			if ($this->where != '')
				$this->where = ' WHERE ' . $this->where;
			$tag_name = 'list';
			if ($item_on_page===false) {
				$item_on_page = $this->item_on_page_client;
			}
		}

		if ($query == '') {
			$query = $query2 . $this->where . ' ORDER BY ' . $this->order;
		}

		if (! isset ( $_GET ['PAGE'] ))
			$_GET ['PAGE'] = 1;
		if (intval ( $item_on_page) > 0) {
			$query .= ' LIMIT ' . ($item_on_page * ($_GET['PAGE'] - 1)) . ', ' . $item_on_page;
		}
		if ($this->get_list_mode=='query') {
			$ar = $this->db->get_all($query, $param);
			XML::add_node('/',$tag_name);
		} else {
			$ar = XML::from_db('/', $query, $param, $tag_name);
			if (!$ar)
				XML::add_node('/',$tag_name);
		}

		if (intval($item_on_page) > 0) {
			$dip = new Div_into_pages($item_on_page, $visible_pages, $_GET['PAGE']);
			$count_item=$this->db->get_one('SELECT FOUND_ROWS()');
			$pages = $dip->get_pages($count_item);

			//var_dump($this->db->get_one('SELECT FOUND_ROWS()'));
			XML::from_array('//' . $tag_name, $pages, 'pages');
			XML::add_node('//pages', 'get', GET('PAGE'));
			XML::add_node('//pages', 'count_item', $count_item);
		}

		return $ar;
	}
}

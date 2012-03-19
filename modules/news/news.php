<?
class news extends Module {
	
	function __construct() {
		$this->table = 'news';
		$field_verify = '{ "empty" : { "title"         : "Название",  "text"         : "Описание", "description"   : "Анонс"}}';
		//$field_rules = '{"photo_one" : "photo_anons"}';
		parent::__construct ( 'news', null, $field_verify );
	}
	
	function add($id = false) {
		Utils::isLogin ();
		if (is_add () &&  @$_SESSION ['user'] ['role'] & 1 == 1) {
			if (! empty ( $_POST )) {
				if (! isset ( $_POST ['sendall'] )) {
					$_POST ['role'] = 0;
					if (isset ( $_POST ['sendorg'] ))
						$_POST ['role'] += 4;
					if (isset ( $_POST ['senduser'] ))
						$_POST ['role'] += 2;
					if (isset ( $_POST ['sendnotuser'] ))
						$_POST ['role'] += 8;
				
				}
				
				if (trim ( @$_POST ['pubDate'] ) == '')
					$_POST ['pubDate'] = date ( "Y-m-d H:i:s" );
				else
					$_POST ['pubDate'] = date ( "Y-m-d H:i:s", strtotime ( $_POST ['pubDate'] ) );
				XML::from_array ( '/', array ($_POST ), 'edit' );
			} else {
				if (! $id)
					$_POST ['role'] = 15;
			}
			if ($id) {
				if (empty ( $_POST )) {
					$ar = XML::from_db ( '/', 'SELECT * FROM `news` WHERE `id`=? AND (`id_user`=? OR ' . $_SESSION ['user'] ['role'] . '&1=1)', array ($_GET ['EDIT'], $_SESSION ['user'] ['id'] ), 'edit' );
					
					if (! isset ( $ar [0] ))
						Message::error ( 'Доступ запрещен или не существует такой записи!' );
				}
			}
			if (! Message::errorState ())
				XML::add_node ( '/', 'add' );
		}
	}
	
	function get_list() {
		if (!isset($_SESSION ['user'] ['role'])) $_SESSION ['user'] ['role']=8;
		if (isset ( $_GET ['ADMIN'] ) && (is_add () || is_view ())) {
			Utils::isLogin ();
			$ars = parent::get_list ( 'SELECT SQL_CALC_FOUND_ROWS * FROM `' . $this->table . '` WHERE `id_user`="' . $_SESSION ['user'] ['id'] . '" OR ' . $_SESSION ['user'] ['role'] . '&1=1 ORDER BY `pubDate`, `id` DESC' );
		} else{
			$ars = parent::get_list ( 'SELECT SQL_CALC_FOUND_ROWS * FROM `' . $this->table . '` WHERE `active`="1" AND ((`role`&' . $_SESSION ['user'] ['role'] . ')=' . $_SESSION ['user'] ['role'] . ' OR ' . $_SESSION ['user'] ['role'] . '=1) ORDER BY `pubDate`, `id` DESC' );
		$this->brief();
		}
		if ($ars)
		foreach ( $ars as $ar )
			$title [] = $ar ['title'];
		
		if (isset ( $title ))
			Utils::setMeta ( implode ( ', ', $title ) );
		Utils::setMeta ( 'Новости на '.DOMAIN_CLEAR, 'description' );
	
	}
	
	function brief() {
		if (!isset($_SESSION ['user'] ['role'])) $_SESSION ['user'] ['role']=8;
		XML::from_db ( '/', 'SELECT * FROM `news` WHERE `active`="1" AND ((`role`&' . @$_SESSION ['user'] ['role'] . ')=' . @$_SESSION ['user'] ['role'] . ' OR ' . @$_SESSION ['user'] ['role'] . '=1) ORDER BY `pubDate` DESC, `id` DESC LIMIT 3', null, 'brief_list' );
	}
	
	function item($id) {
		$ar = parent::item ( $id );
		Utils::setMeta ( $ar [0] ['title'] );
		Utils::setMeta ( @$ar [0] ['description'], 'description' );
		$this->brief();
	}
	
	function save($id = null) {
		if (is_add ()) {
			$_POST ['role'] = 15;
			if (! isset ( $_POST ['sendall'] )) {
				$_POST ['role'] = 0;
				if (isset ( $_POST ['sendorg'] ))
					$_POST ['role'] += 4;
				if (isset ( $_POST ['senduser'] ))
					$_POST ['role'] += 2;
				if (isset ( $_POST ['sendnotuser'] ))
					$_POST ['role'] += 8;
			
			}
			$pubDate = $_POST ['pubDate'];
			
			if (trim ( @$_POST ['pubDate'] ) == '')
				$_POST ['pubDate'] = date ( "Y-m-d H:i:s" );
			else
				$_POST ['pubDate'] = date ( "Y-m-d H:i:s", strtotime ( $_POST ['pubDate'] ) );
			
			if (is_null ( $id )) {
				if (trim ( @$_POST ['pubDate'] ) == '')
					$_POST ['pubDate'] = date ( "Y-m-d H:i:s" );
				$_POST ['id_user'] = $_SESSION ['user'] ['id'];
				$_POST ['id_section'] = $_GET ['section'];
			}
			parent::save ( $id );
			
			$_POST ['pubDate'] = $pubDate;
		}
	}
}
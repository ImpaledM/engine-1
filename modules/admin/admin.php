<?

class admin extends sections {

	public $newParent, $admin_brief=true;

	private $parent_temp = array ();

	function __construct() {
		parent::__construct ();
		if (isset ( $_POST ['ids'] )) {
			$this->sort ();
			exit ();
		}
	}

	function ajax_show() {
		if (isset ( $_GET ['EDIT'] )) {
			$this->ajax_edit ( $_GET ['EDIT'] );
		} elseif (isset ( $_GET ['SAVE'] )) {
			$this->ajax_save ( $_GET ['SAVE'] );
		} elseif (isset ( $_GET ['DELETE'] )) {
			$this->ajax_delete ( $_GET ['DELETE'] );
		} elseif (isset ( $_GET ['REFRESH'] )) {
			$this->ajax_xsl = (file_exists ( MODULES_LOCAL . 'admin/admin.xsl' )) ? file_get_contents ( MODULES_LOCAL . 'admin/admin.xsl' ) : file_get_contents ( MODULES . 'admin/admin.xsl' );
			$this->show ();
		}
	}

	function saveSort($parent, $children) {
		global $str, $ar;
		$parent = ( int ) $parent;
		foreach ( $children as $k => $v ) {
			$id = @( int ) $children [$k] ['id'];
			if ($id != 0)
			$this->ar [$parent] [$id] = $id;
			if ($_POST ['ids'] == $id)
			$this->newParent = $parent;
			if (isset ( $children [$k] ['children'] [0] )) {
				$this->saveSort ( $id, $children [$k] ['children'] );
			}
		}
	}

	function sort() {
		$this->newParent = '';
		$this->ar = array ();
		$this->saveSort ( 0, $_POST ['tree'] );
		$str = implode ( ',', $this->ar [$this->newParent] );
		$sql1 = 'SET @a:=1;';
		$this->db->query ( $sql1 );
		foreach ( $this->ar [$this->newParent] as $k => $v ) {
			$id_parent = ($this->newParent == 0) ? 'NULL' : '"' . $this->newParent . '"';
			$this->db->query ( 'UPDATE `section` SET `id_parent`=' . $id_parent . ', `priority`=(@a:=(@a+1)) WHERE `id`="' . $v . '"' );
		}
		$this->create_path ( $this->newParent );
	}

	function brief () {
		if ((trim(@$_GET ['path'],'/') == 'admin' || isset($_GET['ADMIN']) || isset($_GET['REFRESH']) || isset($_GET['EDIT'])) && @$_SESSION['user']['position'] == 'superadmin') {
			XML::from_array('/', $this->ar, 'sections');
		}
	}

	function show() {
		Utils::isLogin ();
		$this->brief();
	}

	function ajax_delete($id) {
		$this->db->query ( 'DELETE FROM `section` WHERE id=?', $id );
	}

	function ajax_edit($id = null) {
		global $system_modules;
		$modules=array();
		if ($id != '') {
			XML::from_db ( '/', 'SELECT * FROM `section` WHERE `id`=?', $id, 'main' );
			XML::from_db ( '/', 'SELECT `title`, `description`, `keywords` FROM `meta_tags` WHERE `id_section`=?', $id, 'meta_tags' );
			XML::from_db ( '/', 'SELECT * FROM `section_present` WHERE id1=?', $id, 'section_present' );
		} else {
			XML::add_node ( '/', 'main' );
			XML::from_array ( '//main', array (array ('module' => 'article', 'sub_module' => 'article' ) ) );
			XML::add_node ( '/', 'meta_tags' );
			XML::from_db ( '/', 'SELECT id AS id2 FROM `section` WHERE `present`="anywhere"', null, 'section_present' );
		}

		if ($handle = opendir ( MODULES_LOCAL )) {
			while ( false !== ($file = readdir ( $handle )) ) {
				if ($file != '.' && $file != '..' && ! preg_match ( "'\..+'i", $file ) && $file != 'admin' && (! in_array ( $file, $system_modules ['anywhere'] )) && (! in_array ( $file, $system_modules ['only_self'] ))) {
					$modules [] = $file;
				}
			}
			closedir ( $handle );
		}

		if ($handle = opendir ( MODULES )) {
			while ( false !== ($file = readdir ( $handle )) ) {
				if ($file != '.' && $file != '..' && (!in_array($file,$modules)) && ! preg_match ( "'\..+'i", $file ) && $file != 'admin' && (! in_array ( $file, $system_modules ['anywhere'] )) && (! in_array ( $file, $system_modules ['only_self'] ))) {
					$modules [] = $file;
				}
			}
			closedir ( $handle );
		}

		asort ( $modules );
		XML::from_array ( '/', $modules, 'modules' );
		$this->show ();
	}

	function ajax_save($id) {
		if ($this->ajax_verify ()) {
			if (isset ( $_GET ['ADD'] )) {
				if (intval ( $id ) == 0)
				$id = 'NULL';
				$this->db->query ( 'INSERT `section` SET
      `name`=?, 
      `alias`=?,
      `module`=?,
      `sub_module`=?, 
      `present`=?,           
      `id_parent`=!', array ($_POST ['name'], $_POST ['alias'], $_POST ['module'], $_POST ['sub_module'], @$_POST ['present_anywhere'], $id ) );
				$id = $this->db->last_id ();
				$_POST ['section_present'] [$id] = $id;
				unset ( $_POST ['section_present'] [0] );
				if (trim ( $_POST ['alias'] ) == '') {
					$_POST ['alias'] = translitUrl($_POST['name']);
					$this->db->query ( 'UPDATE `section` SET `alias`=? WHERE id=?', array ($_POST ['alias'], $id ) );
				}
				$this->create_path ( $id );
				if (trim ( $_POST ['title'] ) != '' || trim ( $_POST ['description'] ) != '' || trim ( $_POST ['keywords'] ) != '') {
					$query = 'INSERT  `meta_tags` SET `title`=?, `description`=?, `keywords`=?, `id_section`=?';
					$this->db->query ( $query, array ($_POST ['title'], $_POST ['description'], $_POST ['keywords'], $id ) );
				}
				$present_old = NULL;
			} else {
				if (trim ( $_POST ['alias'] ) == '') {
					$_POST ['alias'] = translitUrl($_POST['name']);
				}
				$alias_old = $this->db->get_one ( 'SELECT `alias` FROM `section` WHERE `id`=?', $id );
				$present_old = $this->db->get_one ( 'SELECT `present` FROM `section` WHERE `id`=?', $id );
				$this->db->query ( 'UPDATE `section` SET
			`name`=?, 
			`alias`=?,
			`module`=?,
			`sub_module`=?,
			`present`=?		
			WHERE `id`=?', array ($_POST ['name'], $_POST ['alias'], $_POST ['module'], $_POST ['sub_module'], @$_POST ['present_anywhere'], $id ) );
				if ($_POST ['alias'] != $alias_old) {
					$this->create_path ( $id );
				}
				if (trim ( $_POST ['title'] ) != '' || trim ( $_POST ['description'] ) != '' || trim ( $_POST ['keywords'] ) != '') {
					$query = ($this->db->get_one ( 'SELECT COUNT(*) FROM `meta_tags` WHERE `id_section`=?', $id ) == 0) ? 'INSERT  `meta_tags` SET `title`=?, `description`=?, `keywords`=?, `id_section`=?' : 'UPDATE  `meta_tags` SET `title`=?, `description`=?, `keywords`=? WHERE `id_section`=?';
					$this->db->query ( $query, array ($_POST ['title'], $_POST ['description'], $_POST ['keywords'], $id ) );
				} else {
					$this->db->query ( 'DELETE FROM `meta_tags` WHERE id_section=?', $id );
				}
			}
				
			// сброс
			if (isset ( $_POST ['present_reset'] )) {
				$this->db->query ( 'DELETE FROM `section_present` WHERE `id2`=?', $id );
			}
			// Если выставлен флаг Везде, проверяем где не установлен - туда доставляем
			if (isset ( $_POST ['present_anywhere'] )) {
				$res = $this->db->query ( 'SELECT s.id FROM `section` AS s LEFT JOIN `section_present` AS sp ON sp.id1=s.id AND sp.id2=? WHERE sp.id1 IS NULL', $id );
				$second = false;
				while ( $row = $this->db->fetch ( $res ) ) {
					if ($second) {
						$add_query .= ', ';
					} else {
						$second = true;
						$add_query = '';
					}
					$add_query .= '(' . $row ['id'] . ',' . $id . ',"")';
				}
				if (isset ( $add_query )) {
					$this->db->query ( 'INSERT `section_present` (`id1`,`id2`,`param`) VALUES ' . $add_query );
				}
			}
			// если было изменено состояние чекбоксов на странице настройки скидываем флаг в NULL в соответсвующем разделе
			$custom_id = array ();
			$checked = $_POST ['section_present'];
			if (isset ( $_GET ['ADD'] )) {
				// если добавляли новый
				$res = $this->db->query ( 'SELECT id AS id2 FROM `section` WHERE `present`="anywhere" AND id<>?', $id );
			} else {
				// если редактировали
				$res = $this->db->query ( 'SELECT id2 FROM `section_present` WHERE `id1`=? AND `id2`<>?', array ($id, $id ) );
			}
			unset ( $checked [$id] );
			while ( $row = $this->db->fetch ( $res ) ) {
				if (! isset ( $checked [$row ['id2']] )) {
					$custom_id [$row ['id2']] = $row ['id2'];
				} else {
					unset ( $checked [$row ['id2']] );
				}
			}
			/*logging(var_export($custom_id,true));
			 logging(var_export($checked,true));*/
			if (count ( $custom_id ) > 0) {
				$custom_id += $checked;
			} else {
				$custom_id = $checked;
			}
			if (count ( $custom_id ) > 0) {
				$this->db->query ( 'UPDATE `section` SET `present`=NULL WHERE `id` IN (!)', implode ( ',', $custom_id ) );
			}
			// пересохранем отмеченные разделы и параметры к ним (теоретически можно проверить и если не было изменения то не пересохранять)
			$this->db->query ( 'DELETE FROM `section_present` WHERE `id1`=?', $id );
			if (isset ( $_POST ['section_present'] )) {
				$second = false;
				foreach ( $_POST ['section_present'] as $id_present => $value ) {
					if ($second) {
						$add_query .= ', ';
					} else {
						$second = true;
						$add_query = '';
					}
					$add_query .= '(' . $id . ',' . intval ( $id_present ) . ', "' . @$_POST ['section_present_param'] [$id_present] . '")';
				}
				$res = $this->db->query ( 'INSERT `section_present` (`id1`, `id2`, `param`) VALUES ' . $add_query );
			}
		} else {
			return false;
		}
	}

	function create_path($id) {
		$this->update_plain ();
		if ($id != 0) {
			$id_parent = $this->ar_plain [$id] ['id_parent'];
			if ($id_parent == NULL) {
				$this->ar_plain [$id] ['path'] = $this->ar_plain [$id] ['alias'];
				$this->db->query ( 'UPDATE `section` SET `path`= ? WHERE `id`=?', array ($this->ar_plain [$id] ['path'], $id ) );
			} else {
				if (is_null ( $this->ar_plain [$id_parent] ['path'] ) || trim ( $this->ar_plain [$id_parent] ['path'] ) == '') {
					$this->ar_plain [$id] ['path'] = $this->ar_plain [$id] ['alias'];
				} else {
					$this->ar_plain [$id] ['path'] = $this->ar_plain [$id_parent] ['path'] . '/' . $this->ar_plain [$id] ['alias'];
				}
				$this->db->query ( 'UPDATE `section` SET `path`=? WHERE `id`=?', array ($this->ar_plain [$id] ['path'], $id ) );
			}
		} else {
			$id = NULL;
		}
		$this->create_children_path ( $id );
	}

	function create_children_path($id) {
		$ar_temp = array ();
		foreach ( $this->ar_plain as $key => $value ) {
			if ($this->ar_plain [$key] ['id_parent'] == $id) {
				$ar_temp [] = $key;
				if (trim ( $this->ar_plain [$id] ['path'] ) == '') {
					$this->ar_plain [$key] ['path'] = $this->ar_plain [$key] ['alias'];
				} else {
					$this->ar_plain [$key] ['path'] = $this->ar_plain [$id] ['path'] . '/' . $this->ar_plain [$key] ['alias'];
				}
				if (! in_array ( $id, $this->parent_temp )) {
					if (trim ( $this->ar_plain [$id] ['path'] ) == '') {
						$add_query = (is_null ( $id )) ? ' IS ?' : '=?';
						$this->db->query ( 'UPDATE `section` SET `path`=`alias` WHERE `id_parent`' . $add_query, array ($id ) );
					} else {
						$this->db->query ( 'UPDATE `section` SET `path`=CONCAT(?, "/", `alias`) WHERE `id_parent`=?', array ($this->ar_plain [$id] ['path'], $id ) );
					}
					$this->parent_temp [] = $id;
				}
				$this->create_children_path ( $key );
			}
		}
	}

	function ajax_verify() {
		if ($_POST ['name'] == '') {
			Message::error ( 'Не заполнено поле "Название"' );
			return false;
		} else {
			return true;
		}
	}
}
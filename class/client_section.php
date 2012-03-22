<?php
class client_section {
	private $db, $ar, $index, $mites, $cursor, $add_ar, $children_id = array (), $ar_full;

	function __construct() {
		$this->db = new Db ( );
		if (isset ( $_GET ['path'] )) {
			$_GET ['section'] = $this->get_section_id ( $_GET ['path'] );
		} elseif (! isset ( $_GET ['section'] )) {
			$_GET ['section'] = 1;
		}
		if ($_GET ['section']>0){
			$this->get_param ();

			$this->cursor = $this->db->get_row ( 'SELECT a.name AS current_name, a.id_parent AS parent_id, a.module AS current_module, a.path AS current_path FROM section AS a WHERE a.id=' . $_GET ['section'] );

			$this->cursor ['current_module'] = 'mod_' . $this->cursor ['current_module'];

			$this->cursor ['current_id'] = $_GET ['section'];

			if ($this->cursor ['current_path'] == '' || $this->cursor ['current_path'] == null)
			$this->cursor ['current_path'] = $this->cursor ['current_id'];

			$_SESSION ['section'] ['current_path'] = $this->cursor ['current_path'];
		}
	}

	function add_ar() {
		$count = count ( $this->index );
		for($i = 0; $i < $count; $i ++) {
			$this->index [$i] ['add'] = $this->add_ar [$this->index [$i] ['id']];
		}
	}

	private function get_children_id($id) {
		array_push ( $this->children_id, $id );
		$row = $this->db->get_all ( 'SELECT `id` FROM `section` WHERE `id_parent`=?', array (

		$id ) );
		foreach ( $row as $key => $value ) {
			$this->get_children_id ( $value ['id'] );
		}
	}

	function get_childrens($id) {
		$this->get_children_id ( $id );
		return $this->children_id;
	}

	function get_section() {
		$ar = array ();
		//$res = $this->db->query ( 'SELECT s.module, s.id, s.name, s.id_parent, s.path, s.alias FROM section s LEFT JOIN meta_tags m ON s.id = m.id_section and m.id_item=0 ORDER BY s.priority' );
		$res = $this->db->query ( 'SELECT s.module, s.id, s.name, s.id_parent, s.path, s.alias FROM section s ORDER BY s.priority' );

		while ( $row = $this->db->fetch ( $res ) ) {
			$ar [$row ['id_parent']] [] = $row ['id'];
			if (trim ( $row ['path'] ) == '')
			$row ['path'] = $row ['id'];
			$this->add_ar [$row ['id']] = $row;
		}
		return $ar;
	}

	function pass($i, $j) {
		if (isset ( $this->ar [$i] [$j] ) || ($i == - 1 && $j == - 1)) {
			if (isset ( $this->ar [$i] [$j] )) {
				$id_sect = $this->ar [$i] [$j];
				$this->mites [] = array (
				$i, $j );
			} else
			$id_sect = 0;
			$this->index [] = array (

			'id' => ( int ) $id_sect, 'level' => count ( $this->mites ) );
			$this->pass ( $id_sect, 0 );
		} else {
			if ($i == 0) {
				return 1;
			}
			// сделать чтобы стартовало с доступного id а не c 1
			list ( $i, $j ) = @array_pop ( $this->mites );
			$this->pass ( $i, $j + 1 );
		}
	}

	function get_feed($id) {
		foreach ( $this->ar as $key => $value ) {
			if ($key == $id) {
				$this->feed_ar [$key] = $value;
				foreach ( $value as $value2 ) {
					$this->get_feed ( $value2 );
				}
			}
		}
	}

	function get_mites() {
		$mites = array ();
		$ar = $this->ar_full;
		$ar = array_reverse ( $ar, true );
		$id_current = @$_GET ['section'];
		if ($_SESSION['current']=='article' && isset($_GET['ITEM']) && $this->db->get_one('SELECT COUNT(*) FROM article WHERE id_section=?',$_GET['section'])>1) {
  		$mites [] = array ('path' => '', 'name' => $this->db->get_one('SELECT title FROM article WHERE id=?',$_GET['ITEM']));
		}
		foreach ( $ar as $id => $ar_id ) {
			if (in_array ( $id_current, $ar_id )) {
				$mites [] = array ('path' => $this->add_ar [$id_current] ['path'], 'name' => $this->add_ar [$id_current] ['name'] );
				$id_current = $id;
			}
		}
		if (defined('MAIN_IN_MITES') && MAIN_IN_MITES) $mites [] = array ('path' => $this->add_ar [1] ['path'], 'name' => $this->add_ar [1] ['name'] );

		return array_reverse ( $mites, true );
	}

	function show($tag_name = null, $id_feed = null) {
		if (! $tag_name)
		$tag_name = 'section';
		$this->ar = $this->get_section ();

		$this->ar_full = $this->ar;

		if (! is_null ( $id_feed )) {
			$this->feed_ar [""] [] = $id_feed;
			$this->get_feed ( $id_feed );
			$this->ar = $this->feed_ar;
		}

		$this->pass ( null, 0 );

		//*********************
		$this->ar = array_reverse ( $this->ar, true );

		if (@$_GET ['section']) {

			$id = @$_GET ['section'];
			$path = array ();

			foreach ( $this->ar as $k => $v ) {
				$k = intval ( $k );
				foreach ( $v as $val ) {
					// mites
					if ($k != 0) {
						foreach ( $path as $key => $value ) {
							if (array_key_exists ( $val, $value )) {
								$path [$key] [$k] = array ('id' => $k, 'name' => $this->add_ar [$k] ['name'], 'path' => $this->add_ar [$k] ['path'] );
							}
						}

						if (! array_key_exists ( $val, $path )) {
							$path [$val] [$val] = array ('id' => $val, 'name' => $this->add_ar [$val] ['name'], 'path' => $this->add_ar [$val] ['path'] );
							$path [$val] [$k] = array ('id' => $k, 'name' => $this->add_ar [$k] ['name'], 'path' => $this->add_ar [$k] ['path'] );
						}
					}
					// end mites
				}

				if (in_array ( $id, $v )) {
					@$this->add_ar [$id] ['view'] = 1;
					foreach ( $v as $val ) {
						@$this->add_ar [$val] ['view'] = 1;
					}
					$id = $k;
				} elseif ($k == $id) {
					foreach ( $v as $val ) {
						@$this->add_ar [$val] ['view'] = 1;
					}
				}

			}

			foreach ( $path as $key => $value ) {
				$this->add_ar [$key] ['full_path'] = array_reverse ( $path [$key] );
			}
		}
		$this->ar = array_reverse ( $this->ar, true );
		//*********************

		$this->add_ar ();

		if (isset ( $this->cursor )) {
			$this->index = array_merge ( $this->index, $this->cursor );
		}
		XML::from_array ( '/', $this->index, $tag_name );
	}
	// 	function get_section_id($path) {
	// 		$path = preg_replace ( '/\/$/', '', $path );
	// 		$id = $this->db->get_one ( 'SELECT `id` FROM `section` WHERE `path`=?', $path );
	// 		return ($id) ? $id : 1;
	// 	}
	function get_section_id($path) {
		global $system_modules;
		$path = preg_replace ( '/\/$/', '', $path );
		if (in_array ( $path, $system_modules ['anywhere'] ) || in_array ( $path, $system_modules ['only_self'] )) {
			return 0;
		} else {
			$id = $this->db->get_one ( 'SELECT `id` FROM `section` WHERE `path`=?', $path );
			if ($id) return $id;
			else {
				ERROR::status(404);
				//header("Location: /errors/?STATUS=404");
				die();
			}
		}
	}
	function get_module_name($id) {
		return $this->db->get_one ( 'SELECT `module` FROM `section` WHERE `id`=!', $id );
	}

	function get_present($id) {
		if (!is_null($id)) {
			$present = $this->db->get_all ('SELECT sp.id2 AS id, s.module FROM section AS s, section_present AS sp WHERE sp.id1=? AND sp.id2=s.id AND sp.id2<>?', array ($id,$id));
			$present [] = array ('id' => $id, 'module' => $this->get_module_name ( $id ), 'current' => '' );
		} else {
			$present = $this->db->get_all ('SELECT id, module FROM section WHERE present="anywhere"');
		}
		return $present;
	}

	function get_param() {
		$res = $this->db->query ( 'SELECT param FROM `section_present` WHERE id1=?', $_GET ['section'] );
		while ( $row = $this->db->fetch ( $res ) ) {
			parse_str ( $row ['param'], $param );
			foreach ( $param as $key => $value ) $_GET [$key] = $value;
		}
	}
}
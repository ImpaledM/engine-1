<?
class gallery extends Module {

	function __construct() {
		$field_verify = '{ "empty" : { "title" : "Название"}}';
		$field_rules = '{"photo_one" : "photo_anons",	"photo_multi" : "photo"}';
		parent::__construct ( 'gallery', $field_rules, $field_verify );
	}

	function add($id = false) {
		
		Utils::isLogin ();
		if (! empty ( $_POST ))
			XML::from_array ( '/', array ($_POST ), 'edit' );
		if ($id) {
			if (empty ( $_POST )) {
				$ar = XML::from_db ( '/', 'SELECT * FROM `' . $this->table . '` WHERE `id`=?', array ($_GET ['EDIT'] ), 'edit' );
				if (isset ( $ar [0] ))
					XML::from_db ( '//edit/item', 'SELECT `name`, `note` FROM `' . $this->table . '_file` WHERE id_parent=? AND field=?', array ($id, 'photo' ), 'photo' );
				else
					Message::error ( 'Доступ запрещен или не существует такой записи!' );
			}
		}

	}

	function get_list() {

		if (isset ( $_GET ['ADMIN'] )) {
			Utils::isLogin ();
			$ars = parent::get_list ( 'SELECT * FROM `' . $this->table . '` ORDER BY `sort`' );
		} else {

			$list = $this->db->get_all ( 'SELECT * FROM `' . $this->table . '` WHERE `active`="1"  ORDER BY `sort`' );

			foreach ( $list as &$row ) {
				$row ['photo'] = $this->db->get_all ( 'SELECT `name`, `note` FROM `' . $this->table . '_file` WHERE id_parent="' . $row ['id'] . '" AND field="photo"' );

			}

			Xml::from_array ( '/', $list );

		}
		//Utils::setMeta ( 'Фотогалерея' );

	}

	function item($id) {
		$ar = parent::item ( $id );
		Utils::setMeta ( $ar [0] ['title'] );
		Utils::setMeta ( @$ar [0] ['anons'], 'description' );
		XML::from_db ( '//item', 'SELECT `name`, `note` FROM `' . $this->table . '_file` WHERE id_parent="' . $id . '" AND field="photo"', null, 'photo' );

	}

	function save($id = null) {
		parent::save ( $id );
	}
}
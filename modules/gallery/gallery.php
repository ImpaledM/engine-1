<?
class gallery extends Module {

	function __construct() {
		$field_verify = '{ "empty" : { "name" : "Название"}}';
		$field_rules = '{"photo_one" : "photo_anons",	"photo_multi" : "photo"}';
		parent::__construct ( 'gallery', $field_rules, $field_verify );
	}

	function add($id = false) {
		if (! is_null ( parent::add ( $id ) ))
		XML::from_db ( '//edit/item', 'SELECT `name`, `note` FROM `' . $this->table . '_file` WHERE id_parent=? AND field=?', array ($id, 'photo' ), 'photo' );
	}

	function item($id) {
		$ar = parent::item ( $id );
		XML::from_db ( '//item', 'SELECT `name`, `note` FROM `' . $this->table . '_file` WHERE id_parent="' . $id . '" AND field="photo"', null, 'photo' );
	}

	function get_list() {
		$query='SELECT * FROM `' . $this->table . '`';
		if (!isset($_GET['ADMIN'])) $query.=' WHERE active=1';
		parent::get_list($query);
	}

}
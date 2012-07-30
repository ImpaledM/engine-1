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
		if (isset($_GET['ADMIN'])) {
			parent::get_list('SELECT * FROM `' . $this->table . '` WHERE id_section=?', $_GET['section']);
		} else {
			$ar=$this->db->get_all('SELECT * FROM `' . $this->table . '` WHERE id_section=? AND active=1', $_GET['section']);
			if (count($ar)==1) {
				$this->item($ar[0]['id']);
			} else {
				XML::from_array('/', array('list'=>$ar));
			}
		}
	}

}
<?
class counters extends Module {
	function __construct(){
		$field_verify='{ "empty" : { "name" : "Название",  "text" : "Код счетчика"}}';
		parent::__construct( false, null, $field_verify );
	}

	function get_list() {
		parent::get_list('SELECT SQL_CALC_FOUND_ROWS * FROM `'.$this->table.'`');
	}
}
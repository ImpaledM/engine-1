<?
class payment {
	
	private $db;
	
	function __construct() {
		$this->db = new DB ();
	}
	
	function brief() {
	
	}
	function ajax_show() {
		exit ( 'Ajax_Ok' );
	}
	
	function show() {
		exit ( 'Ok' );
	}
}
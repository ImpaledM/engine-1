<?
class counters extends Module {

	function __construct(){
		$field_verify='{ "empty" : { "title" : "Название",  "text" : "Код счетчика"}}';
		parent::__construct( 'counters', null, $field_verify );
	}

	function ajax_show(){
		$i=0;
		foreach ($_POST['tree'] as $row){
			$this->db->query('UPDATE `'.$this->table.'` SET `sort`="'.$i.'" WHERE `id`="'.$row['id'].'"');
			$i++;
		}
	}

	function add( $id=false ){
		parent::add();
		Utils::isLogin();
		if ( ! empty( $_POST ) ) XML::from_array( '/', array( $_POST ), 'edit' );
		if ( $id ){
			if ( empty( $_POST ) ){
				$ar=XML::from_db( '/', 'SELECT * FROM `'.$this->table.'` WHERE `id`=? ', array( $_GET['EDIT']), 'edit' );
			}
		}
		if ( ! Message::errorState() ) XML::add_node( '/', 'add' );
	}

	function get_list(){
		if ( isset( $_GET['ADMIN'] )){
			Utils::isLogin();
			$ars=parent::get_list( 'SELECT SQL_CALC_FOUND_ROWS * FROM `'.$this->table.'` ORDER BY `sort`' );
		}
	}

	function brief(){
		XML::from_db( '/', 'SELECT * FROM `'.$this->table.'` WHERE `active`="1" ORDER BY `sort`', null, 'show' );
	}

	function item( $id ){}
}
<?
class article extends Module {
	function __construct(){
		$field_verify='{ "empty" : { "title" : "Название",  "text" : "Полный текст"}}';
		$field_rules='{"photo_one" : "photo_anons",	"photo_multi" : "photo"}';
		parent::__construct( 'article', $field_rules, $field_verify );
	}

	function ajax_show(){
		$i=0;
		foreach ($_POST['tree'] as $row){
			$this->db->query('UPDATE `'.$this->table.'` SET `sort`="'.$i.'" WHERE `id`="'.$row['id'].'"');
			$i++;
		}
	}
	function add( $id=false ){
		Utils::isLogin();
		if ( ! empty( $_POST ) ) XML::from_array( '/', array( $_POST ), 'edit' );
		if ( $id ){
			if ( empty( $_POST ) ){
				$ar=XML::from_db( '/', 'SELECT * FROM `'.$this->table.'` WHERE `id`=? AND (`id_user`=? OR ' . $_SESSION['user']['role'] . '&1=1)', array( $_GET['EDIT'], $_SESSION['user']['id'] ), 'edit' );
				if ( isset( $ar[0] ) ) XML::from_db( '//edit/item', 'SELECT `name`, `note` FROM `'.$this->table.'_file` WHERE id_parent=? AND field=?', array( $id, 'photo' ), 'photo' );
				else Message::error( 'Доступ запрещен или не существует такой записи!' );
			}
		}
		if ( ! Message::errorState() ) XML::add_node( '/', 'add' );
	}

	function get_list(){
		if ( isset( $_GET['ADMIN'] ) ){
			Utils::isLogin();
			$this->order='`sort`';
			$ars=parent::get_list( 'SELECT SQL_CALC_FOUND_ROWS * FROM `'.$this->table.'` WHERE (`id_user`="' . $_SESSION['user']['id'] . '" OR ' . $_SESSION['user']['role'] . '&1=1) AND `id_section`="' . $_GET['section'] . '"', null, 50 );

		}else{
			$this->order='`sort`';
			$ars=parent::get_list( 'SELECT SQL_CALC_FOUND_ROWS * FROM `'.$this->table.'` WHERE `active`="1" AND `id_section`="' . $_GET['section'] . '"' );
			if ( count( $ars ) == 1 ) $this->item( $ars[0]['id'] ); else Utils::setMeta( '', 'description' );
		}
		//$ars=parent::get_list();
		if ($ars)
		foreach( $ars as $ar )
		$title[]=$ar['title'];

		if ( isset( $title ) ) Utils::setMeta( implode( ', ', $title ) );

	}

	function item( $id ){
		$ar=parent::item( $id );
		Utils::setMeta( @$ar[0]['title'] );
		Utils::setMeta( @$ar[0]['anons'], 'description' );
		XML::from_db( '//item', 'SELECT `name`, `note` FROM `'.$this->table.'_file` WHERE id_parent="' . $id . '" AND field="photo"', null, 'photo' );

	}

	function save( $id=null ){
		if ( is_null( $id ) ){
			$_POST['date']=date( "Y-m-d H:i:s" );
			$_POST['id_user']=$_SESSION['user']['id'];
			$_POST['id_section']=$_GET['section'];
		}
		//$_POST['alias']=translitUrl($_POST['title']);
		$_POST['date_edit']=date( "Y-m-d H:i:s" );
		parent::save( $id );
	}
}
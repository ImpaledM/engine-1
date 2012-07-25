<?
class article extends Module {
	function __construct(){
		$field_verify='{ "empty" : { "name" : "Заголовок"}}';
		$field_rules='{"photo_one" : "photo_anons",	"photo_multi" : "photo"}';
		parent::__construct( 'article', $field_rules, $field_verify );
	}

	function add($id = false) {
		if (! is_null ( parent::add ( $id ) ))
		XML::from_db ( '//edit/item', 'SELECT `name`, `note` FROM `' . $this->table . '_file` WHERE id_parent=? AND field=?', array ($id, 'photo' ), 'photo' );
	}

	function brief($id) {
		XML::from_db('/', 'SELECT a.*,b.name AS section_name, b.path FROM article AS a, section AS b WHERE a.id_section=b.id AND b.id=? AND a.active=1',$id,'brief');
	}

	function get_list(){
		if ( isset( $_GET['ADMIN'] ) ){
			Utils::isLogin();
			parent::get_list( 'SELECT SQL_CALC_FOUND_ROWS id, name, photo_anons, active FROM `'.$this->table.'` WHERE (`id_user`="' . $_SESSION['user']['id'] . '" OR ?&1=1) AND `id_section`=? ORDER BY sort', array($_SESSION['user']['role'],$_GET['section']));
		}else{
			$ar=parent::get_list( 'SELECT SQL_CALC_FOUND_ROWS a.*, b.path FROM `'.$this->table.'` AS a, section b WHERE a.id_section=b.id AND a.`active`="1" AND a.`id_section`=?  ORDER BY sort', $_GET['section']);
			parent::setMeta($ar);
		}
	}

	function item( $id ){
		$ar=parent::item( $id );
		XML::from_db( '//item', 'SELECT `name`, `note` FROM `'.$this->table.'_file` WHERE id_parent="' . $id . '" AND field="photo"', null, 'photo' );

	}

	function save( $id=null ){
		if ( is_null( $id ) ){
			$_POST['date']=date( "Y-m-d H:i:s" );
			$_POST['id_user']=$_SESSION['user']['id'];
			$_POST['id_section']=$_GET['section'];
		}
		$_POST['date_edit']=date( "Y-m-d H:i:s" );
		parent::save( $id );
	}
}

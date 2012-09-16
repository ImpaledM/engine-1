<?
class sape_article {
	public $sape_article;
	private $on=true;

	function __construct(){
		if ( !defined( '_SAPE_USER' ) || _SAPE_USER==''){
			$this->on=false;
		}else{
			require_once ($_SERVER['DOCUMENT_ROOT'] . '/' . _SAPE_USER . '/sape.php');
			$this->sape_articles=new SAPE_articles();
		}

	}

	function show(){}


	function brief(){
		if ($this->on) XML::from_array( '/', array( $this->sape_articles->return_announcements() ), 'list' );
	}

	function item( $id ){
		if ($this->on) {
			$str=$this->sape_articles->process_request();

			preg_match("'###(.+)###'im",$str, $ar);
			$str=preg_replace("'###.+###'i",'',$str);
			Utils::setMeta($ar[1]);
			preg_match("'%%%(.+)%%%'im",$str, $ar);
			$str=preg_replace("'%%%.+%%%'i",'',$str);
			Utils::setMeta( $ar[1], 'description' );
			XML::from_array( '/', array( $str ) );
		}

	}

}
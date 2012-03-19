<?
error_reporting ( E_ERROR );
require_once 'Zend/Search/Lucene.php';

Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding ( LUCENE_ENCODING );
Zend_Search_Lucene_Analysis_Analyzer::setDefault ( new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive () );

define('SEARCH_MIN_LENGTH', 2);
class search {
	
	private $db, $text_search, $stop_words;
	
	function __construct() {
		$this->db = new DB ();
		if (isset($_GET ['text_search'])) $_GET ['text_search']=stripslashes($_GET ['text_search']);
		$this->text_search = (isset ( $_GET ['text_search'] )) ? mb_strtolower(trim ( $_GET ['text_search'] ), LUCENE_ENCODING) : false;
		$this->stop_words=array('под', 'над', 'для', 'вас', 'перед', 'при' );
	}
	
	function show() {
		if ($this->text_search && mb_strlen ( $this->text_search, LUCENE_ENCODING ) > 3 && file_exists ( SEARCH_PATH_INDEX )) {
			$original=$this->text_search;
			$wr = array ();
			if (!is_admin()) $this->text_search = str_replace ( array ('-', '+', '*', '"', "'", ',', '.', '^', '`', '~' ), '', $this->text_search );
			$words = explode ( ' ', trim ( $this->text_search ) );
			foreach ( $words as &$word )
				if (mb_strwidth ( $word, LUCENE_ENCODING ) > SEARCH_MIN_LENGTH && !in_array($word, $this->stop_words))
					$wr [] = $word;
			$this->text_search = implode ( ' ', $wr );
			if (!is_admin()) $txtsearch = '+' . implode ( '* +', $wr ) . '*';
			else $txtsearch =  implode ( ' ', $wr ) . '';
			//if (is_admin()) var_dump($txtsearch);
			$index = new Zend_Search_Lucene ( SEARCH_PATH_INDEX );
			$search = $this->text_search;
			$hits = $index->find ( $txtsearch );
			$Query = Zend_Search_Lucene_Search_QueryParser::parse ( $txtsearch, LUCENE_ENCODING );
			$ar = array ();
			
			$i=$out = 0;

			if (! isset ( $_GET ['PAGE'] ))
				$_GET ['PAGE'] = 1;
			$j = ITEM_ON_PAGE * $_GET ['PAGE'];
			for($i = (ITEM_ON_PAGE * ($_GET ['PAGE'] - 1)); $i < $j; $i ++) {
				if (isset ( $hits [$i] )) {
					$hit = $hits [$i];
					if ($hit->score > SEARCH_MIN_SCORE) {
						preg_match ( "'.+/(items|city|webcams)/([0-9]+)-.+'i", $hit->link, $ars );
						if (! isset ( $ars [1] ))
							$ars [1] = '';
						switch ($ars [1]) {
							case 'city' :
								$row = $this->db->get_row ( 'SELECT * FROM `city`  WHERE `id`="' . intval ( $ars [2] ) . '" AND `active`=1' );
								if ($row && $row ['photo_anons'] != '' && $row ['text'] != '') {
									$ar [] = array ('active' => $row ['active'], 'id' => intval ( $ars [2] ), 'table' => $ars [1], 'photo_anons' => $row ['photo_anons'], 'pagetext' => $Query->htmlFragmentHighlightMatches ( mb_substr ( strip_tags ( $row ['text'] ), 0, 490, LUCENE_ENCODING ), LUCENE_ENCODING ), 'link' => $hit->link, 'title' => $Query->htmlFragmentHighlightMatches ( mb_substr ( $row ['name'], 0, 100, LUCENE_ENCODING ), LUCENE_ENCODING ) );
								} else
									$out --;
								break;
							case 'items' :
								$row = $this->db->get_row ( 'SELECT i.*, c.name AS city, c.alias as city_alias, 
								(SELECT GROUP_CONCAT(a.`name` SEPARATOR ", ") as types FROM `types` AS a, `items` AS b, `items_types` AS cc WHERE a.id=cc.id_types AND b.id=cc.id_items AND cc.id_items="' . intval ( $ars [2] ) . '" ORDER BY a.name) as types 
								FROM `items` AS i, city AS c WHERE i.id_city=c.id AND i.`id`="' . intval ( $ars [2] ) . '" AND i.`active`=1' );
								
								if ($row) {
									$ar [] = array ('active' => $row ['active'], 'id' => intval ( $ars [2] ), 'types' => $row ['types'], 'table' => $ars [1], 'highlighting' => $row ['highlighting'], 'photo_anons' => $row ['photo_anons'], 'pagetext' => $Query->htmlFragmentHighlightMatches ( mb_substr ( $row ['anons'], 0, 490, LUCENE_ENCODING ), LUCENE_ENCODING ), 'link' => $hit->link, 'title' => $Query->htmlFragmentHighlightMatches ( mb_substr ( $row ['types'].' '.$row ['name'] . ', ' . $row ['city'], 0, 100, LUCENE_ENCODING ), LUCENE_ENCODING ) );
								}
								
								break;
							
							case 'webcams' :
								$row = $this->db->get_row ( 'SELECT i.*, c.name AS city, c.alias as city_alias FROM `webcams` AS i, city AS c WHERE i.id_city=c.id AND i.`id`="' . intval ( $ars [2] ) . '" AND i.`active`=1' );
								if ($row) {
									$ar [] = array ('active' => $row ['active'], 'id' => intval ( $ars [2] ), 'table' => $ars [1], 'photo_anons' => $row ['photo_anons'], 'pagetext' => $Query->htmlFragmentHighlightMatches ( mb_substr ( $row ['anons'], 0, 490, LUCENE_ENCODING ), LUCENE_ENCODING ), 'link' => $hit->link, 'title' => $Query->htmlFragmentHighlightMatches ( mb_substr ( $row ['name'] . ', ' . $row ['city'], 0, 100, LUCENE_ENCODING ), LUCENE_ENCODING ) );
								}
								
								break;
							default :
								$out --;
								break;
						}
					}
				
				}
			}
			
			$allCount = $out;
			foreach ( $hits as $hit ) {
				if ($hit->score > SEARCH_MIN_SCORE)
					$allCount ++;
			}
			XML::from_array ( '/', $ar, 'list' );
			$dip = new Div_into_pages ( ITEM_ON_PAGE, VISIBLE_PAGES, $_GET ['PAGE'] );
			$pages = $dip->get_pages ( $allCount );
			$tag_name = 'list';
			XML::from_array ( '//' . $tag_name, $pages, 'pages' );
			XML::add_node ( '//pages', 'get', GET ( 'PAGE' ) );
			XML::add_node ( '/', 'allSearch', $allCount );
			XML::add_node ( '/', 'allIndex', $index->count () );
			
			if (@$_SESSION['user']['role']!=1 && $_GET ['PAGE'] == 1) {
				if ($allCount > 0)
					file_put_contents ( ROOT . 'temp/search_index/' . DOMAIN_CLEAR . '.log', "$original\n", FILE_APPEND );
				else
					file_put_contents ( ROOT . 'temp/search_index/' . DOMAIN_CLEAR . '_.log', "$original\n", FILE_APPEND );
			}
		}
		
		if ((!$this->text_search || $allCount==0)  && !isset ( $_GET ['ADMIN'] )) XML::add_node ( '/', 'google_search' );
		
		if (isset ( $_GET ['STAT'] ) && is_admin ())
			$this->stat ();
//		if (isset ( $_GET ['OPTIMIZE'] ) && is_admin ())
//			$this->optimize ();
	}
	function optimize() {
		set_time_limit ( 0 );
		if (file_exists ( SEARCH_PATH_INDEX )) {
			$index = new Zend_Search_Lucene ( SEARCH_PATH_INDEX );
			$index->optimize ();
		}
	}
	
	function stat() {
		XML::add_node ( '/', 'stat' );
		$file = ROOT . 'temp/search_index/' . DOMAIN_CLEAR . '.log';
		if (file_exists ( $file )) {
			$ar = file ( $file );
			if ($ar) {
				$ar = array_count_values ( $ar );
				arsort ( $ar );
				reset ( $ar );
				foreach ( $ar as $k => $v )
					$ar1 [] = array ('key' => $k, 'hit' => $v );
				XML::from_array ( '//stat', $ar1, 'searched' );
			}
		}
		$file = ROOT . 'temp/search_index/' . DOMAIN_CLEAR . '_.log';
		if (file_exists ( $file )) {
			$ar = file ( $file );
			if ($ar) {
				$ar = array_count_values ( $ar );
				arsort ( $ar );
				reset ( $ar );
				foreach ( $ar as $k => $v )
					$ar2 [] = array ('key' => $k, 'hit' => $v );
				XML::from_array ( '//stat', $ar2, 'notsearched' );
			}
		}
	
	}
}
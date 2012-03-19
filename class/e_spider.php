<?
if (LUCENE == 1) {
	require_once 'Zend/Search/Lucene.php';
	
	Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding ( LUCENE_ENCODING );
	Zend_Search_Lucene_Analysis_Analyzer::setDefault ( new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive () );
}
function getmicrotime() {
	list ( $usec, $sec ) = explode ( " ", microtime () );
	return (( float ) $usec + ( float ) $sec);
}

class e_Spider {
	
	private $current_page;
	private $current_id;
	private $db;
	private $time_start, $time_end;
	private $encoding;
	
	function __construct($encoding = 'utf-8') {
		$this->encoding = $encoding;
		$this->time_start = getmicrotime ();
		$this->db = new Db ();
		$this->current_page = '';
		$this->current_id = 1;
	}
	
	function real_path($path) {
		$parts_path = explode ( '/', $path );
		$output = array ();
		for($i = 0, $max = sizeof ( $parts_path ); $i < $max; $i ++) {
			if ('' == $parts_path [$i] || '.' == $parts_path [$i])
				continue;
			if ('..' == $parts_path [$i] && $i > 0 && '..' != @$output [sizeof ( $output ) - 1]) {
				array_pop ( $output );
				continue;
			}
			array_push ( $output, $parts_path [$i] );
		}
		return implode ( '/', $output );
	}
	
	function correct_url($current_url, $in_url) {
		$txt=$current_url;
		$current_url = @parse_url ( $current_url );
		$in_url = @parse_url ( $in_url );
		
		// если абсолютная ссылка
		if (isset ( $in_url ['scheme'] ) || isset ( $in_url ['host'] )) {
			if ((isset ( $in_url ['scheme'] ) && $in_url ['scheme'] != 'http') || (isset ( $in_url ['host'] ) && ! preg_match ( "'{DOMAIN}$'is", $in_url ['host'] ))) {
				$in_url = false;
			}
		
		// если относительная ссылка
		} else {
			
			$in_url ['scheme'] = $current_url ['scheme'];
			$in_url ['host'] = $current_url ['host'];
			
			if (isset ( $in_url ['path'] )) {
				if (substr ( $in_url ['path'], 0, 1 ) != '/' && isset ( $current_url ['path'] )) {
					if (substr ( $current_url ['path'], 0, - 1 ) != '/') {
						$pathinfo = pathinfo ( $current_url ['path'] );
						if (isset ( $pathinfo ['extension'] )) {
							($pathinfo ['dirname'] == '/' || $pathinfo ['dirname'] == '\\') ? $current_url ['path'] = '' : $current_url ['path'] = $pathinfo ['dirname'];
						}
					}
				} else {
					$current_url ['path'] = '';
				}
				
				$in_url ['path'] = '/' . $this->real_path ( $current_url ['path'] . '/' . $in_url ['path'] );

				if ($in_url ['path'] != '/' && substr ( $in_url ['path'], 0, - 1 ) != '/') {
					$pathinfo = pathinfo ( $in_url ['path'] );
					if (! isset ( $pathinfo ['extension'] )) {
						$in_url ['path'] .= '/';
					}
				}
			} elseif (isset ( $in_url ['query'] )) {
				$in_url ['path'] = $current_url ['path'];
			} else {
				$in_url = false;
			}
		}
		
		if ($in_url) {
			$str = $in_url ['scheme'] . '://' . $in_url ['host'];
			if (isset ( $in_url ['path'] )) {
				$str .= $in_url ['path'];
			} else {
				$str .= '/';
			}
			if (isset ( $in_url ['query'] )) {
				$str .= '?' . $in_url ['query'];
			}
			return $str;
		} else
			return false;
	}
	
	/**
	 * Добавление новой линки в базу данных
	 */
	function link_add($links) {
		foreach ( $links as $link ) {
			$original = $link;
			
			if (! defined ( 'DELETE' ) || (defined ( 'DELETE' ) && ! strstr ( $link, DELETE ))) {
				if (! preg_match ( '/\.(jpe?g|pdf|doc|gif|png|rar|zip|djvu|bmp|avi|mov|mkv|exe|ppt|xls|doc|xlsx|docx|flv|sfw)\b/si', $link )) {
					$link_ = str_replace ( '://www.', '://', $link );
					if ($link = $this->correct_url ( $this->current_page, $link_ )) {
						$link = html_entity_decode ( $link );
						if ($this->db->get_one ( 'select count(id) from spider_links where link=?', $link ) == 0) {
							if (DEBUG || isset ( $_GET ['d'] ))
								echo '[=] ' . $this->current_page . ' [-] ' . $link . ' [=]<hr/>';
							$this->db->query ( 'insert into spider_links set link=?, indexed=0;', $link );
						}
					}
				}
			}
		}
		$links = null;
	}
	
	/**
	 * @param string $str Содержание страницы
	 */
	function url_hilight($str) {
		preg_match_all ( "'<a.+?href\s*=\s*[\"\']([^\s]+?)[\"\'].*?>'si", $str, $result, PREG_PATTERN_ORDER );
		$this->link_add ( $result [1] );
		//preg_match_all ( "'<(frame|iframe).+src[\s=\"|\']+(.+)[\"|\'][^>]*>'siU", $str, $result, PREG_PATTERN_ORDER );
		//$this->link_add ( $result [2] );
		return $str;
	}
	
	function valid_page($url) {
		if ($url) {
			$handle = fopen ( $url, "r" );
			$ar = stream_get_meta_data ( $handle );
			return (($ar ['wrapper_data'] [0] == 'HTTP/1.1 200 OK' || $ar ['wrapper_data'] [0] == 'HTTP/1.1 302 Found') /*&& in_array( 'Content-Type: text/html; charset=UTF-8', $ar['wrapper_data'] ) */);
		} else
			return false;
	}
	
	function start_index($url, $start = true, $debug = DEBUG) {
		//  		`indexed` tinyint(4) NOT NULL DEFAULT '0',
		if ($start) {
			$this->db->query ( "CREATE TABLE IF NOT EXISTS `spider_links` (
  		`id` int(10) NOT NULL auto_increment,
  		`link` text NOT NULL,
  		`indexed` int(1) NOT NULL default '0',
  		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;" );
			$this->db->query ( "CREATE TABLE IF NOT EXISTS `spider_pages` (
  		`id` int(10) NOT NULL auto_increment,
  		`id_link` int(10) NOT NULL default '0',
  		`title` varchar(250) NOT NULL default '',
		  `pagetext` text NOT NULL,
  		`link` text,
  		PRIMARY KEY  (`id`),
  		FULLTEXT KEY `text` (`pagetext`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;" );
			Utils::delete ( SEARCH_PATH_INDEX );
			$this->db->query ( 'TRUNCATE `spider_links`;' );
			$this->db->query ( 'TRUNCATE `spider_pages`;' );
			$this->db->query ( 'insert into spider_links set link=?, indexed=0', $url );
			$this->current_id = 1;
			$start = false;
		}
		
		if (file_exists ( SEARCH_PATH_INDEX ))
			$index = new Zend_Search_Lucene ( SEARCH_PATH_INDEX );
		else
			$index = new Zend_Search_Lucene ( SEARCH_PATH_INDEX, true );
		$time = 0;
		while ( ($row = $this->db->get_row ( 'SELECT * FROM `spider_links`  WHERE `indexed`="0" LIMIT 1' )) && ($time < 25) ) {
			$url = $row ['link'];
			$this->current_id = $row ['id'];
			
			if ($this->valid_page ( $url )) {
				$page_str = @file_get_contents ( $url );
				$this->current_page = $url;
				
				$page_str = $this->url_hilight ( $page_str ); // Захват ссыло
				$search = array ("'<(noindex|noscript|script|head|style|unstore)[^>]*.*</(noindex|noscript|script|head|style|unstore)>'isU", "'<!--START-->.*?<!--END-->'si", 

				"'&(quot|#34);'i", "'&(amp|#38);'i", "'&(lt|#60);'i", "'&(gt|#62);'i", "'&(nbsp|#160);'i", "'&(iexcl|#161);'i", "'&(cent|#162);'i", "'&(pound|#163);'i", "'&(copy|#169);'i", "'&hellip;'i", "'&ndash;'i", "'&laquo;'i", "'&raquo;'i" );
				
				$replace = array ("", "", "\"", "&", "<", ">", " ", chr ( 161 ), chr ( 162 ), chr ( 163 ), chr ( 169 ), "...", "-", "\"", "\"" );
				
				if (strtolower ( $this->encoding ) != 'utf-8') {
					if (is_string ( $page_str ))
						$page_str = iconv ( $this->encoding, "utf-8", $page_str );
				}
				
				preg_match ( '/<title>(.*)<\/title>/imU', $page_str, $ar );
				$title = (isset ( $ar [1] )) ? $ar [1] : '';
				
				$page_str = str_replace ( '<', ' <', $page_str );
				
				// Удаление ненужных тегов !!!!!!!!!!!
				$page_str = preg_replace ( $search, $replace, $page_str );
				$page_str = strip_tags ( $page_str );
				
				$page_str = preg_replace ( '/(\,|\.|\?|\r|\n|\t| )+/', ' ', $page_str );
				
				$this->db->query ( 'update  spider_links set indexed = 1 where id = ?;', $this->current_id );
				//$this->db->query ( 'insert into spider_pages set id_link=!, title=?, pagetext=?, link=?', array ($this->current_id, $title, $page_str, $this->current_page ) );
				$this->db->query ( 'insert into spider_pages set id_link=!, title=?, link=?', array ($this->current_id, $title, $this->current_page ) );
				
				//*******************************************************************************************
				

				$urls = array (DOMAIN, DOMAIN . 'login/', DOMAIN . 'items/', DOMAIN . 'city/', DOMAIN . 'types/', DOMAIN . 'signup/', DOMAIN . 'login/' );
				
				if (! preg_match ( "'.+ADMIN+'is", $this->current_page ) && ! preg_match ( "'.+\/types\/+'is", $this->current_page ) && ! preg_match ( "'.+PAGE=.+'is", $this->current_page ) && ! preg_match ( "'.+LETTER=.+'is", $this->current_page ) && ! in_array ( $this->current_page, $urls )) {
					$doc = new Zend_Search_Lucene_Document ();
					$doc->addField ( Zend_Search_Lucene_Field::UnIndexed ( 'link', $this->current_page, LUCENE_ENCODING ) );
					$doc->addField ( Zend_Search_Lucene_Field::Text ( 'title', $title, LUCENE_ENCODING ) );
					$doc->addField ( Zend_Search_Lucene_Field::UnStored ( 'contents', $page_str, LUCENE_ENCODING ) );
					$index->addDocument ( $doc );
					
					if (DEBUG || isset ( $_GET ['d'] ))
						echo '<div style="color:green">' . $this->current_page . '</div><hr/>';
				}
				//*******************************************************************************************
				

				$this->time_end = getmicrotime ();
				$time = $this->time_end - $this->time_start;
			
			} else {
				if (! empty ( $this->current_id )) {
					file_put_contents ( ROOT . 'error_links.txt', $this->db->get_one ( 'SELECT `link` from `spider_links` where `id`=?;', $this->current_id ), FILE_APPEND );
					$this->db->query ( 'delete from spider_links where id=?;', $this->current_id );
				}
			}
		
		}
		$index->commit ();
		$index->optimize ();
	
	}
}
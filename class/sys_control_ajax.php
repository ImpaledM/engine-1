<?
header ( 'Content-type: text/html; charset=UTF-8' );
session_start ();
include $_SERVER ['DOCUMENT_ROOT'] . '/config.php';
include ROOT . 'engine/init.php';

$cache = (defined('CACHE') && CACHE && isset ( $_REQUEST ['CACHE'] )) ? true : false;
$db = new Db ();
$subclass = (isset ( $_REQUEST ['subclass'] )) ? $_REQUEST ['subclass'] : '';
$nameclass = (isset ( $_REQUEST ['nameclass'] )) ? $_REQUEST ['nameclass'] : '';
$cmd = (isset ( $_REQUEST ['cmd'] )) ? $_REQUEST ['cmd'] : '';
$param = (isset ( $_REQUEST ['CACHE'] )) ? $_REQUEST ['CACHE'] : '';
if ($cache) {
	$ar = $db->get_row ( 'SELECT xml, html FROM cache WHERE uri=? AND module=? AND subclass=? AND cmd=? AND param=? AND id_user=? AND ajax=1', array ($_SERVER ['REQUEST_URI'], $nameclass, $subclass, $cmd, $param, intval ( @$_SESSION ['user'] ['id'] ) ) );
} else {
	$ar = false;
}

// нет кэша или он в xml
if (! ($ar) || $ar ['xml'] != '') {
	if (isset ( $_REQUEST ['subclass'] )) {
		$path = classname_exists ( $_REQUEST ['nameclass'], $_REQUEST ['subclass'] );
		if ($path) {
			include_once $path.'.php';
			$name = $_REQUEST ['subclass'];
		}
	} else {
		$name = $_REQUEST ['nameclass'];
	}
	$obj = new $name ();
	if (method_exists ( $obj, 'ajax_show' )) {
		// отработать метод если нет кэша
		if (! ($ar)) {
			$root_tag = (isset ( $_REQUEST ['ROOT_TAG'] )) ? $_REQUEST ['ROOT_TAG'] : 'mod_' . $name;
			XML::add_node ( '/', $root_tag );
			$str = $obj->ajax_show ();

		}
		// если метод отдает строку то выводим ее
		if (isset ( $str ) && ! is_null ( $str ) && is_string ( $str )) {
			echo $str;
			// запись в кэш html
			if ($cache && ! ($ar)) {
				$db->query ( 'INSERT cache SET uri=?, module=?, subclass=?, cmd=?, param=?, id_user=?, html=?, ajax=1', array ($_SERVER ['REQUEST_URI'], $nameclass, $subclass, $cmd, $param, intval ( @$_SESSION ['user'] ['id'] ), addcslashes ( $str, '\\' ) ) );
			}

			// если есть кэш или метод ничего не отдает или отдает не строку
		} else {
			// определяем есть ли xsl
			if (isset ( $obj->ajax_xsl )) {
				$xsl = $obj->ajax_xsl;
			} else {
			if (isset ( $name ) && file_exists ( MODULES_LOCAL . $_REQUEST['nameclass'] . '/' . $name . '_ajax.xsl' )) {
					$xsl = file_get_contents ( MODULES_LOCAL . $_REQUEST['nameclass'] . '/' . $name . '_ajax.xsl' );
				}elseif (isset ( $name ) && file_exists ( MODULES . $_REQUEST['nameclass'] . '/' . $name . '_ajax.xsl' )) {
					$xsl = file_get_contents ( MODULES . $_REQUEST['nameclass'] . '/' . $name . '_ajax.xsl' );
				}
			}
			if (isset ( $xsl ) && $xsl != '') {
				$xsl =str_replace('#ROOT#', ROOT, $xsl);
				// если нет кэша то достраиваем DOM
				if (! ($ar)) {
					XML::add_node ( '/', 'requests' );
					XML::from_array ( '//requests', $_POST, 'post' );
					XML::from_array ( '//requests', $_GET, 'get' );
					//XML::from_array ( '//requests', $_SESSION, 'session' );
					XML::from_array ( '/', Message::get () );
					$dom = XML::get_dom ();

					// запись в кэш xml
					if ($cache) {
						$db->query ( 'INSERT cache SET uri=?, module=?, subclass=?, cmd=?, param=?, id_user=?, xml=?, ajax=1', array ($_SERVER ['REQUEST_URI'], $nameclass, $subclass, $cmd, $param, intval ( @$_SESSION ['user'] ['id'] ), XML::dom2str ( $dom ) ) );
					}
				} // иначе берем DOM из кэша
				else {
					$dom = XML::str2dom ( $ar ['xml'] );
				}

				echo XML::transform ( false, $xsl, $dom );
			}
		}
	} else {
		echo 'У класcа ' . $name . ' не существует метода ajax_show';
	}
} elseif ($ar ['html'] != '')
echo $ar ['html'];
<?php
set_error_handler ( 'error_handler', E_ALL & ~ E_NOTICE );
function error_handler($errno, $errstr, $errfile, $errline) {
	Error::report ( array ($errno, $errstr ) );
}

function classname_exists($classname, $subclass = false) {
	$classname = strtolower ( $classname );
	$classname_update = ($subclass) ? strtolower ( $subclass ) : $classname;
	//var_dump(CL_LOCAL . $classname_update . '.php');
	if (file_exists ( CL_LOCAL . $classname_update . '.php' ))
	$classname = CL_LOCAL . $classname_update;
	elseif (file_exists ( CL .$classname_update . '.php' ))
	$classname = CL .$classname_update;
	elseif (file_exists ( MODULES_LOCAL . $classname . '/' . $classname_update . '.php' ))
	$classname = MODULES_LOCAL . $classname . '/' . $classname_update;
	elseif (file_exists ( MODULES . $classname . '/' . $classname_update . '.php' ))
	$classname = MODULES . $classname . '/' . $classname_update;
	else return false;
	return $classname;
}

function __autoload($classname) {
	if ($classname = classname_exists ( $classname ))
	include $classname . '.php';
}

/*                PERMISSIONS                   */
function is_view($section = false) {
	return (@$_SESSION ['user'] ['role'] & 1 == 1 || !in_array ( (($section)?$section:$_GET ['section']), $_SESSION ['permission'] ['view'] ))?TRUE:FALSE;
}

function is_add($section = false) {
	return (@$_SESSION ['user'] ['role'] & 1 == 1 || !in_array ( (($section)?$section:$_GET ['section']), $_SESSION ['permission'] ['add'] ))?TRUE:FALSE;
}

function is_owner($id_user = 0) {
	return (@$_SESSION ['user'] ['role'] & 1 == 1 || @ $_SESSION ['user'] ['id'] == intval ( $id_user ))?TRUE:FALSE;
}

function is_admin() {
	return (isset ( $_SESSION ['user'] ['role'] ) && $_SESSION ['user'] ['role'] & 1 == 1)?TRUE:FALSE;
}
/*                PERMISSIONS                   */

function safety($ar) {
	foreach ( $ar as $key => $value ) {
		if (is_array ( $value )) {
			$ar [$key] = safety ( $value );
		} elseif (is_string ( $value )) {
			$ar [$key] = preg_replace ( "'\h+'u", ' ', trim ( stripslashes ( $ar [$key] ) ) );
		}
	}
	return $ar;
}


function GET($param = '') {
	if ($param != '' && strstr ( $_SERVER ['REQUEST_URI'], $param )) {
		$pattern = array ("'^([^\?]*)\?" . $param . "(?:=[0-9]+)?$'", "'^([^\?]*\?)" . $param . "(?:=[0-9]+)?&(.+)$'", "'^(.*)&" . $param . "(?:=[0-9]+)?(.*)$'" );
		$replacement = array ("$1?", "$1$2&", "$1$2&" );
		return preg_replace ( $pattern, $replacement, $_SERVER ['REQUEST_URI'] );
	} else {
		if (strstr ( $_SERVER ['REQUEST_URI'], '?' )) {
			if (substr ( $_SERVER ['REQUEST_URI'], - 1 ) != '&' && substr ( $_SERVER ['REQUEST_URI'], - 1 ) != '?') {
				return $_SERVER ['REQUEST_URI'] . '&';
			} else {
				return $_SERVER ['REQUEST_URI'];
			}
		} else {
			return $_SERVER ['REQUEST_URI'] . '?';
		}
	}
}

function strip_tags_attributes($string, $allowtags = NULL, $allowattributes = NULL) {
	$string = strip_tags ( $string, $allowtags );
	if (! is_null ( $allowattributes )) {
		if (! is_array ( $allowattributes ))
		$allowattributes = explode ( ",", $allowattributes );
		if (is_array ( $allowattributes ))
		$allowattributes = implode ( ")(?<!", $allowattributes );
		if (strlen ( $allowattributes ) > 0)
		$allowattributes = "(?<!" . $allowattributes . ")";
		$string = preg_replace_callback ( "/<[^>]*>/i", create_function ( '$matches', 'return preg_replace("/ [^ =]*' . $allowattributes . '=(\"[^\"]*\"|\'[^\']*\')/i", "", $matches[0]);' ), $string );
	}
	return $string;
}

function translitUrl($str) {
	if (defined('URL_CUT')) {
		$str=Utils::trimText($str, URL_CUT);
	}
	$str = mb_convert_case ( $str, MB_CASE_LOWER);
	$tr = array ("айс" => "ice", "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "e", "ж" => "j", "з" => "z", "и" => "i", "й" => "j", "к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y", "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya", " " => "-", "." => "", "/" => "_", ";" => "", ":" => "", "Є" => "E", "Ї" => "Y", "І" => "I", "є" => "e", "ї" => "y", "і" => "i" );

	if (preg_match ( '/[^A-Za-z0-9_\-]/', $str )) {
		$str = strtr ( $str, $tr );
		$str = preg_replace ( '/[^A-Za-z0-9_\-]/', '', $str );
	}

	return $str;
}
function get_uid($id) {
	return USER_PREFIX . str_pad ( $id, USER_UID_NUMBER, "0", STR_PAD_LEFT );
}
function get_user_path($id) {
	$add = intval ( $id / USER_NUMBER_ON_FOLDER ) + 1;
	return 'users/' . $add . '/' . get_uid ( $id );
}
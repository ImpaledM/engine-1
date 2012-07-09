<?
setlocale ( LC_ALL, 'ru_RU.UTF-8' );
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");
if (defined ( 'SERVICE' ) && trim ( SERVICE ) != '' && ! (@$_COOKIE ['admin'] || @$_SESSION ['user'] ['role'] == 1 || @$_REQUEST ['func'] == 'uploadify' || @$_REQUEST ['func'] == 'upload_file')) {
	include ROOT . 'service.php';
	exit ();
}

if (isset ( $_SERVER ['SERVER_NAME'] ))
	define ( 'DOMAIN', 'http://' . $_SERVER ['SERVER_NAME'] . '/' );
else
	define ( 'DOMAIN', 'http://' . DOMAIN_CLEAR . '/' );


define ( 'ENGINE', ROOT . 'engine/' );
define ( 'MODULES', ENGINE . 'modules/' );
define ( 'MODULES_LOCAL', ROOT . 'modules/' );
define ( 'CL', ENGINE . 'class/' );
define ( 'CL_LOCAL', ROOT . 'class/' );
define ( 'IMG', ENGINE . 'img/' );
define ( 'XML', ROOT . 'xml/' );
define ( 'UPLOADS', 'uploads/' );

(file_exists ( ROOT . 'debug' )) ? define ( 'DEBUG', 1 ) : define ( 'DEBUG', 0 );

include ENGINE . 'function.php';
include ROOT . 'arrays.php';

DB::connect ( DBLOGIN, DBPASSWORD, DBHOST, DBNAME );
Fb::setEnabled ( DEBUG );

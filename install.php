<?
header ( 'Content-type: text/html; charset=UTF-8' );
session_start ();
define ( 'ROOT', $_SERVER['DOCUMENT_ROOT'].'/');
$folders=array('users','temp','file');
foreach ($folders as $folder) {
	if (! file_exists ( ROOT . 'uploads/'.$folder )) {
		symlink ( ROOT . $folder, ROOT . 'uploads/'.$folder );
		echo 'Добавлен симлинк с '.ROOT . 'uploads/'.$folder.' на '.ROOT .$folder.'<br/>';
	} else {
		echo ROOT . 'uploads/'.$folder.' уже существует!<br/>';
	}
}
echo 'Финиш';

<?
header ( 'Content-type: text/html; charset=UTF-8' );
session_start ();
exit;
/*include '../config.php';
include 'init.php';
$dirs = array ('temp', 'file', 'users', 'uploads/article' );
$cd = array ();

foreach ( $dirs as $path ) Utils::createPath($path);*/

define ( 'ROOT', $_SERVER['DOCUMENT_ROOT'].'/');
if (! file_exists ( ROOT . 'uploads/users' )) symlink ( ROOT . 'users', ROOT . 'uploads/users' );
if (! file_exists ( ROOT . 'uploads/temp' )) symlink ( ROOT . 'temp', ROOT . 'uploads/temp' );
if (! file_exists ( ROOT . 'uploads/file' )) symlink ( ROOT . 'file', ROOT . 'uploads/file' );
if (! file_exists ( ROOT . 'engine' )) symlink ( '/usr/local/lib/php/engine/engine',   ROOT . 'engine');

//Create config_user.php
//
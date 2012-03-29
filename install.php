<?
header ( 'Content-type: text/html; charset=UTF-8' );
session_start ();
define ( 'ROOT', $_SERVER['DOCUMENT_ROOT'].'/');
if (! file_exists ( ROOT . 'uploads/users' )) symlink ( ROOT . 'users', ROOT . 'uploads/users' );
if (! file_exists ( ROOT . 'uploads/temp' )) symlink ( ROOT . 'temp', ROOT . 'uploads/temp' );
if (! file_exists ( ROOT . 'uploads/file' )) symlink ( ROOT . 'file', ROOT . 'uploads/file' );
echo 'done';

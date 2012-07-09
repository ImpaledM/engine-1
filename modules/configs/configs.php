<?
class configs {
	public $user, $table;
	function __construct() {
		global $USER_CONSTANTS;
		$this->user = $USER_CONSTANTS;
	}
	
	function show() {
		Utils::isLogin ();
		if ($_SESSION ['user'] ['role'] == 1) {
			if (isset ( $_POST ['saveConfig'] )) {
				$this->save ();
			}
			$cfgs = file_get_contents ( ROOT . 'config_user.php' );
			preg_match_all ( "'^define.*[\'\"]([a-z0-9_]+)[\'\"][^\'\"]*[\'\"]?[^\'\"]+[\'\"]?.*(?://(.+))?$'imU", $cfgs, $ar );
			if (count ( $ar [1] ) > 0) {
				foreach ( $ar [1] as $k => $v ) {
					$this->user [$v] = array ($this->user [$v], $ar [2] [$k] );
				}
				XML::from_array ( '/', $this->user, 'defines' );
			}
		
		}
	}
	
	function save() {
		$str = '';
		foreach ( $_POST ['DEF'] as $k => $v ) {
			$str .= "define('$k', '" . addslashes ( $v ) . "');";
			if (isset ( $_POST ['DEF_HID'] [$k] ))
				$str .= " //" . $_POST ['DEF_HID'] [$k] . "\n";
			else
				$str .= "\n";
		}
		if (trim ( $str ) != '') {
			$stat = file_put_contents ( ROOT . 'config_user.php', "<?php\n" . $str );
			if ($stat) {
				Message::success ( 'Конфигурация сохранена.' );
				header ( 'Location: /' . $_GET ['path'] . '?ADMIN' );
				exit ();
			} else
				Message::error ( 'Конфигурация не сохранена! <br/> Возможно нету прав доступа к файлу!' );
		}
	
	}
}
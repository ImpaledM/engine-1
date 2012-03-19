<?
class ajax extends Module {
	
	function __construct() {
	parent::__construct();
	}
	
	function cmd_uploadify() {
		$dir = (isset ( $_REQUEST ['folder'] ) && trim ( $_REQUEST ['folder'] ) != '') ? $_REQUEST ['folder'] : 'temp';
		$file = $_FILES ['Filedata'];
		$filename = UTILS::uploadFile ( $dir, $_REQUEST ['destination'], $file, $_REQUEST ['prefix'] );
		$path = str_replace ( '//', '/', ROOT . $dir . '/' . $filename );
		if (isset ( $_REQUEST ['width'] ) && isset ( $_REQUEST ['height'] ) &&  $_REQUEST ['type'] =='image')
			Utils::writeFoto ( $path, $path, $_REQUEST ['width'], $_REQUEST ['height'], 85 );
		$filename = pathinfo ( $filename );
		echo $filename ['basename'];
	}
	
	function cmd_rotateImg() {
		//TODO проверка на owners
		$name = ROOT . $_REQUEST ['file'];
		$degrees = $_REQUEST ['rot'];
		echo Utils::rotateFoto ( $name, $degrees );
	}
	
	function cmd_deleteFile() {
		 //TODO проверка на owners
		
		if (isset ( $_POST ['file'] )) {
			$file = strstr ( $_POST ['file'], 'temp' );
			if ($file)
				$_POST ['file'] = $file;
			$file = pathinfo ( $_POST ['file'] );
			
			if ($_POST ['multi'] == 'true') {
				if (intval ( $_POST ['id_parent'] ) != 0) {
					$this->db->query ( 'DELETE FROM `!` WHERE id_parent=? AND name=?', array ($_POST ['table'] . '_file', $_POST ['id_parent'], $file ['basename'] ) );
				}
			} else {
				$this->db->query ( 'UPDATE `!` SET `!`="" WHERE `!`=?', array ($_POST ['table'], $_POST ['field'], $_POST ['field'], $file ['basename'] ) );
			}
			if (($_POST ['table'] == 'users')) {
				Users::refreshUserData ();
			}
			Utils::delete ( ROOT . $_POST ['file'] );
		}
	}
}
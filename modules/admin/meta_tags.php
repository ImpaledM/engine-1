<?
class meta_tags extends Module {
	function __construct() {
		parent::__construct ('section');
	}

	function show() {
		//var_dump($_SESSION, $_GET);
		if (isset($_GET['ADMIN'])) {
			if (!isset($_GET['EDIT'])) {
				header("Location: ".DOMAIN."meta_tags/?ADMIN&EDIT=".$_SESSION['section']['id']);
				die();
			}
			parent::show();
		}
	}

	function save($id) {
		parent::save($id,'Данные сохранены!');
	}
}
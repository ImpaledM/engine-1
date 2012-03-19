<?
class Profile extends Users {

	function __construct() {
		$this->islogin = false;
		$field_rules = '{"photo_one" : "foto"}';
		parent::__construct ( 'users', $field_rules );
	}


	function show() {
		Utils::isLogin ();
		$_GET['EDIT']=$_SESSION['user']['id'];
		parent::show();
	}


	function save() {
		if ($this->verify()) {
			if (@trim ( $_POST ['password'] ) != '')
			$_POST ['password'] = md5 ( $_POST ['password'] );
			else
			unset ( $_POST ['password'] );
			parent::save ( $_SESSION['user']['id'], 'Ваши данные сохранены!' );
			parent::refreshUserData ();
		}
	}

	function verify() {
		if (trim($_POST ['password'])!='') {
			if (trim($_POST ['repassword']=='')) {
				Message::error ( 'Для смены пароля необходимо заполнить оба поля "Пароль" и "Пароль (повтор)"');
			} else {
				if ($_POST ['password'] != $_POST ['repassword']) {
					Message::error ( 'Введенные пароли не совпадают!' );
				}
			}
		}
		return ! Message::errorState ();
	}
}

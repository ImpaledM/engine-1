<?
class Login extends Users {

	function __construct() {
		parent::__construct ( 'users' );
		$this->admin_brief=true;
	}

	function ajax_show() {
		parent::ajax_show ();
	}

	function brief() {
		if (isset ( $_REQUEST ['logout'] )) {
			if ( isset( $_SESSION['admin']['id'] ) ){
				$_SESSION['user']['id']=$_SESSION['admin']['id'];
				Users::refreshUserData();
				unset( $_SESSION['admin'] );
				header( 'Location: /users/?ADMIN' );
				exit();
			}else{
				$this->db->query ( 'UPDATE `' . $this->table . '` SET `date_last`=(NOW()-INTERVAL 2 MINUTE) WHERE `id`="' . $_SESSION ['user'] ['id'] . '"' );
				unset ( $_SESSION ['user'] );
				unset ( $_SESSION ['reg_email'] );
				setcookie ( 'login', "", time () - COOKIE_LIFE_TIME );
				setcookie ( 'password', "", time () - COOKIE_LIFE_TIME );
				$_SESSION ['user'] ['role'] = 8;
				header ( 'Location: /' );
				exit ();
			}
		}
		if (isset ( $_SESSION ['user'] ['id'] )) {
			XML::add_node ( '/', 'user', $_SESSION ['user'] );
		} elseif (isset ( $_COOKIE ['login'] ) && isset ( $_COOKIE ['password'] )) {
			$_POST ['login'] = $_COOKIE ['login'];
			$_POST ['password'] = $_COOKIE ['password'];
			$_POST ['remember'] = 1;
			if ($this->verify (true)) {
				XML::add_node ( '/', 'user', $_SESSION ['user'] );
			}
		}
	}

	function show() {
		utils::setMeta('Авторизация');
		
		if (isset ( $_GET ['change_password'] ) && ENABLE_RECOVERY_PASSWORD) {
			$this->change_password ();
		} elseif (isset ( $_GET ['change_hash'] ) && ENABLE_RECOVERY_PASSWORD) {
			$this->new_password ();
		} else {
			$this->login ();
		}
	}

	function login() {
		XML::add_node ( '/', 'form_login' );
		if (isset ( $_POST ['save'] ) && $this->verify ()) {
			if (isset ( $_SESSION ['REDIRECT_URL'] ) && $_SESSION ['REDIRECT_URL'] != '') {
				header ( 'Location: ' . $_SESSION ['REDIRECT_URL'] );
				unset ( $_SESSION ['REDIRECT_URL'] );
				die();
			} else {
				header ( 'Location: ' . DOMAIN );
				die();
			}
		}
	}

	function change_password() {
		if (isset ( $_POST ['save'] )) {
			$row = $this->db->get_row ( 'SELECT id, login, password FROM `' . $this->table . '` WHERE `active`="1" AND login=? ',  $_POST ['login']);
			if ($row) {
				$hash = md5 ( $row ['login'] . $row ['password'] . time () );
				$this->db->query ( 'UPDATE `' . $this->table . '` SET `hash`="' . $hash . '" WHERE `id`="' . $row ['id'] . '"' );
				$send = new sendmail ();
				$msg = 'Здравствуйте2!<br /><br />
Вы получили это письмо, так как Ваш e-mail был указан для восстановлении пароля на сайте <a href="http://' . DOMAIN_CLEAR . '">' . DOMAIN_CLEAR . '</a>.<br />                                      
Если Вы не делали этого просто проигнорируйте и удалите это письмо.<br /><br />                                                                                                     
                                                                                                                                                                                    
Для продолжения восстановления проследуйте по следующей ссылке или скопируйте в адресную строку браузера <a href="http://' . DOMAIN_CLEAR . '/login/?change_hash=' . $hash . '">http://' . DOMAIN_CLEAR . '/login/?change_hash=' . $hash . '</a>' . SIGNATURE;
				$send->addHtml ( $msg );
				$send->send ( $row ['email'], 'Восстановление пароля на ' . DOMAIN_CLEAR );

				Message::success ( 'Вам на почту отправлена инструкция по восстановлению пароля' );
			} else {
				unset ( $_POST );
				Message::error ( 'Такого аккаунта не существует!', 'login' );
				XML::add_node ( '/', 'form_change_password' );
			}
		} else {
			XML::add_node ( '/', 'form_change_password' );
		}
	}

	function new_password() {

		if (isset ( $_POST ['save'] )) {
			if (trim ( @$_POST ['password'] == '' ))
			Message::error ( 'Не заполнено поле "Пароль"', 'password' );
			if (trim ( @$_POST ['repassword'] == '' ))
			Message::error ( 'Не заполнено поле "Повтор пароля"', 'repassword' );
			if (trim ( @$_POST ['password'] ) != '' && trim ( @$_POST ['repassword'] ) != '' && @$_POST ['password'] != @$_POST ['repassword'])
			Message::error ( 'Введенные пароли не совпадают!', 'repassword' );

			if (! Message::errorState ()) {
				$this->db->query ( 'UPDATE `' . $this->table . '` SET `password`="' . md5 ( $_POST ['password'] ) . '", `hash`="" WHERE `active`="1" AND `hash`="' . $_GET ['change_hash'] . '"' );
				if ($this->db->error) {
					Message::success ( 'Пароль успешно изменен!<br/>Теперь вы можете авторизоваться' );
					header ( 'Location: /login/' );
					exit ();
				}
			} else
			XML::add_node ( '/', 'form_new_password' );
		} else {
			$id = $this->db->get_one ( 'SELECT `id` FROM `' . $this->table . '` WHERE `active`="1" AND `hash`=?', array ($_GET ['change_hash'] ) );
			if ($id)
			XML::add_node ( '/', 'form_new_password' );
			else
			header ( 'Location: /' );
		}
	}

	function verify($cookie = false) {
		if (trim ( $_POST ['login'] ) == '' || trim ( $_POST ['password'] ) == '') {
			Message::error ( 'Для входа необходимо заполнить оба поля' );
		} else {
			$password=($cookie) ? $_POST['password'] : md5($_POST['password']);
			$row = XML::from_db ( '/', 'SELECT ' . USER_FIELDS . ' FROM `' . $this->table . '` WHERE login=?  AND `password`=? AND `active`="1"', array ($_POST ['login'], $password), null, 'user' );
			if (! @$row [0]) {
				return Message::error ( "<b>Такие данные для входа не зарегистрированы<br/> Неверный логин или пароль</b><br/>
Проверьте правильность написания.<br/>
Убедитесь, что пароль вводится на том же языке, что и при регистрации.<br/>
Посмотрите, не нажат ли [Caps Lock]." );
			} else {
				if (isset ( $_POST ['remember'] )) {
					setcookie ( 'login', $row [0] ['login'], time () + COOKIE_LIFE_TIME, '/' );
					setcookie ( 'password', $row [0] ['password'], time () + COOKIE_LIFE_TIME, '/' );
				}
				if ($row [0] ['role'] == 1)
				setcookie ( 'admin', $row [0] ['login'], time () + COOKIE_LIFE_TIME * 28, '/' );
				$_SESSION ['user'] = $row [0];
				$this->db->query ( 'UPDATE `users` SET `date_last`=NOW() WHERE `id`="' . $row [0] ['id'] . '"' );
			}
		}
		return ! Message::errorState ();
	}
}
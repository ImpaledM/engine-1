<?php
class Signup extends Users{

	function __construct() {
		$this->islogin=false;
		$field_verify = '{ "empty" : { "email" : "E-mail", "login": "Логин", "password" : "Пароль",  "repassword" : "Повтор пароля" }}';
		parent::__construct ('users', null, $field_verify);
	}

	function show() {
		if (isset ( $_GET ['hash'] )) {
			$row = $this->db->get_row ( 'SELECT `id` FROM `' . $this->table . '` WHERE `active`="0" AND `hash`=?', array ($_GET ['hash'] ) );
			if ($row) {
				$this->db->query ( 'UPDATE `' . $this->table . '` SET `active`="1", `hash`="", `path`="'.get_user_path($row ['id']).'" WHERE `id`="' . $row ['id'] . '"' );
				if (ENABLE_USER_DIR)
				Utils::createPath ( '/'.get_user_path($row ['id'] ) );
				Message::success ( 'Ваш аккаунт успешно активирован!' );
				header ( 'Location: /login/' );
				exit ();
			} else {
				Message::error ( 'Не существует такого аккаунта или он уже активирован!' );
			}
		} elseif (!isset( $_GET ['INFO'] )) {
			$_GET['ADD']='';
			Utils::setMeta ( 'Регистрация нового пользователя' );
			parent::show();
		} else {
			XML::add_node ( '/', 'signup_info', $_SESSION['reg_email']);
			Utils::setMeta ( 'Информация для зарегистрировавшегося пользователя' );
		}
	}

	function ajax_show() {
		if (isset ( $_POST ['checkLogin'] )) {
			$check = $this->checkLogin ( $_POST ['login'] );
			$err = $check;
			if ($check == 1)
			$err = 'Неправильный логин!';
			if ($check == 2)
			$err = 'Такой логин уже существует!';
			echo $err;
			exit ();
		}
	}

	function checkLogin($login = false) {
		$stat = 0;
		$login = trim ( $login );
		if ($login && preg_match ( "'^[a-z0-9_-]{3,}$'i", $login )) {

			$cn = $this->db->get_one ( 'SELECT COUNT(*) FROM `' . $this->table . '` WHERE `login`="' . $login . '"' );
			if ($cn > 0)
			$stat = 2;
		} else
		$stat = 1;
		return $stat; //0 - ok | 1 - неправильный | 2 - уже существует
	}

	function verify() {
		parent::verify ();

		if (isset ( $_POST ['email'] ) && trim($_POST ['email'] )!='' && ! Utils::validEmail ( $_POST ['email'] ))
		Message::error ( 'Некорректный E-mail!' );

		if ($this->db->get_one('SELECT COUNT(*) FROM users WHERE email=?',strtolower(trim($_POST['email'])))>0) {
			Message::error ( 'Этот email уже зарегистрирован в системе');
		}

		if (! Message::errorState () && (trim ( @$_POST ['password'] ) != '' || trim ( @$_POST ['repassword'] ) != '') && @$_POST ['password'] != @$_POST ['repassword'])
		Message::error ( 'Введенные пароли не совпадают!' );

		if (! isset ( $_POST ['EDIT'] )) {
			if (ENABLE_CAPTCHA_SIGNUP && @$_SESSION ['captcha'] != @$_POST ['captcha'])
			Message::error ( 'Не правильно введен "Код подтверждения"' );
			if (! Message::errorState ()) {
				$code = $this->checkLogin ( $_POST ['login'] );
				if ($code == 1)
				Message::error ( 'Неправильный логин' );
				if ($code == 2)
				Message::error ( 'Такой логин уже существует!' );
			}
		}

		if (Message::errorState ())
		$this->verify = false;
		return $this->verify;
	}

	function save() {
		if (@$_POST ['signature'] != '')
		$_POST ['signature'] = substr ( strip_tags_attributes ( $_POST ['signature'] ), 0, 100 );
		$_POST ['hash'] = md5 ( $_POST ['password'] . $_POST ['email'] );
		$_POST ['date_reg'] = date ( "Y-m-d H:i:s" );
		$_POST ['ip_reg'] = UTILS::getUserIP ();
		$password=$_POST ['password'];
		$_POST ['password'] = md5 ($password);
		$_SESSION['reg_email']=$_POST['email']=strtolower(trim($_POST['email']));
		parent::save ( $id, 'Регистрация прошла успешно.' );
		$send = new sendmail ();
		$send->addHtml ( 'Поздравляем! Вы зарегистрировались на ' . DOMAIN_CLEAR . ' <br/>
			Для активации перейдите по ссылке <br/><a href="' . DOMAIN . 'signup/?hash=' . $_POST ['hash'] . '">' . DOMAIN . 'signup/?hash=' . $_POST ['hash'] . '</a>' );
		$send->send ( $_POST ['email'], 'Активация аккаунта на сайте ' . DOMAIN_CLEAR, '', false, true );
		header ( 'Location: /signup/?INFO' );
		exit ();
	}

}
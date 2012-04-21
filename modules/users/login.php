<?
class Login extends Users {
	private $login='nick';

	function __construct() {
		define ('APP_ID','2742414');
		define ('APP_SHARED_SECRET','Ysl3ojLbE85kc22WTETj');
		define ('COOKIE_LIFE_TIME', 3600 * 24 * 14 );
		define ('COOKIE_REFER_TIME', 3600 * 24 * 30 );
		parent::__construct ( 'users' );
		$this->admin_brief=true;
	}

	function authOpenAPIMember() {
		$session = array();
		$member = FALSE;
		$valid_keys = array('expire', 'mid', 'secret', 'sid', 'sig');
		$app_cookie = $_COOKIE['vk_app_'.APP_ID];
		if ($app_cookie) {
			$session_data = explode ('&', $app_cookie, 10);
			foreach ($session_data as $pair) {
				list($key, $value) = explode('=', $pair, 2);
				if (empty($key) || empty($value) || !in_array($key, $valid_keys)) {
					continue;
				}
				$session[$key] = $value;
			}
			foreach ($valid_keys as $key) {
				if (!isset($session[$key])) return $member;
			}
			ksort($session);

			$sign = '';
			foreach ($session as $key => $value) {
				if ($key != 'sig') {
					$sign .= ($key.'='.$value);
				}
			}
			$sign .= APP_SHARED_SECRET;
			$sign = md5($sign);
			if ($session['sig'] == $sign && $session['expire'] > time()) {
				$member = array(
				        'id' => intval($session['mid']),
				        'secret' => $session['secret'],
				        'sid' => $session['sid']
				);
			}
		}
		return $member;
	}

	function cmd_vk_login() {
		if (! isset ( $_SESSION ['user'] ['id'] )) {
			$member = $this->authOpenAPIMember();
			if($member !== FALSE) {
				$row = $this->db->get_row('SELECT '.USER_FIELDS.' FROM users WHERE vk=?', $_POST['mid']);
				if ($row) {
					$_SESSION ['user'] = $row;
					$this->db->query ( 'UPDATE `users` SET `date_last`=NOW() WHERE `id`="' . $row ['id'] . '"' );
					echo '1';
				}
			}
		}
	}

	function cmd_vk_signup() {
		if (! isset ( $_SESSION ['user'] ['id'] )) {
			$member = $this->authOpenAPIMember();
			if($member !== FALSE) {
				$vk=$_POST['response'];
				$this->db->query('INSERT users SET vk=?, nick=?, first_name=?, last_name=?, avatar=?, date_reg=NOW()', array($vk['uid'], $vk['nickname'], $vk['first_name'], $vk['last_name'], $vk['photo_rec']));
				$id=$this->db->last_id();
				$row[0]=array('id'=>$id, 'nick'=>$vk['nickname'],'first_name'=>$vk['first_name'],'last_name'=>$vk['last_name'],'avatar'=>$vk['photo_rec']);
				$_SESSION ['user'] = $row [0];
				echo '1';
			}
		}
	}

	function cmd_form_login() {
		XML::add_node('/','form_login');
	}

	function brief() {
		if (isset($_POST['token'])) {
			$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
			$user = json_decode($s, true);
			XML::from_array('/', $user,'auth');
			if (isset($user['first_name'])) {
				$_SESSION['user']['id']=0;
				$_SESSION['user']['first_name']=$user['first_name'];
				$_SESSION['user']['last_name']=$user['last_name'];
				$_SESSION['user']['avatar']=$user['photo'];
				$_SESSION['user']['identity']=$user['identity'];
			}
		}

		if (isset ( $_REQUEST ['logout'] )) {
			$this->db->query ( 'UPDATE `' . $this->table . '` SET `date_last`=(NOW()-INTERVAL 2 MINUTE) WHERE `id`="' . $_SESSION ['user'] ['id'] . '"' );
			unset ( $_SESSION ['user'] );
			unset ( $_SESSION ['reg_email'] );
			unset ( $_SESSION ['REDIRECT_URL']);
			setcookie ( 'login', "", time () - COOKIE_LIFE_TIME );
			setcookie ( 'password', "", time () - COOKIE_LIFE_TIME );
			$_SESSION ['user'] ['role'] = 8;
			header ( 'Location: '.$_SERVER['HTTP_REFERER']);
			exit ();
		}
		if (isset ( $_SESSION ['user'] ['id'] )) {
			XML::add_node ( '/', 'user', $_SESSION ['user'] );
		} elseif (isset ( $_COOKIE ['login'] ) && isset ( $_COOKIE ['password'] )) {
			$_POST [$this->login] = $_COOKIE ['login'];
			$_POST ['password'] = $_COOKIE ['password'];
			$_POST ['remember'] = 1;
			if ($this->verify (true)) {
				XML::add_node ( '/', 'user', $_SESSION ['user'] );
			}
		}
	}

	function show() {
		if (isset ( $_GET ['change_password'] ) && ENABLE_RECOVERY_PASSWORD) {
			$this->change_password ();
		} elseif (isset ( $_GET ['change_hash'] ) && ENABLE_RECOVERY_PASSWORD) {
			$this->new_password ();
		} else {
			$this->login ();
		}
	}

	function cmd_login() {
		$this->verify();
		//var_dump($_SESSION);
	}

	function login() {
		if ((!isset($_SESSION ['REDIRECT_URL']) || strpos($_SESSION ['REDIRECT_URL'], '/login')) && isset($_SERVER['HTTP_REFERER'])) $_SESSION ['REDIRECT_URL'] = $_SERVER ['HTTP_REFERER'];
		/* 		fb::log($_SESSION);
		 fb::log($_SERVER); */
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
			$row = $this->db->get_row ( 'SELECT id, email, password FROM `' . $this->table . '` WHERE `active`="1" AND email=? ',  strtolower(trim($_POST ['email'])));
			if ($row) {
				$hash = md5 ( $row ['email'] . $row ['password'] . time () );
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
		if (isset($_POST[$this->login]) && $_POST[$this->login]=='Email') {
			$_POST[$this->login]='';
		} else {
			$_POST [$this->login]=strtolower(trim ( $_POST [$this->login] ));
		}
		if (isset($_POST['password']) && $_POST['password']=='Пароль') $_POST['password']='';

		if ($_POST [$this->login] == '' || trim ( $_POST ['password'] ) == '') {
			Message::error ( 'Для входа необходимо заполнить оба поля' );
		} else {
			$password=($cookie) ? $_POST['password'] : md5($_POST['password']);
			$row = XML::from_db ( '/', 'SELECT ' . USER_FIELDS . ' FROM `' . $this->table . '` WHERE '.$this->login.'=?  AND `password`=? AND `active`="1"', array ($_POST [$this->login], $password), null, 'user' );
			if (! @$row [0]) {
				return Message::error ( "<b>Такие данные для входа не зарегистрированы<br/> Неверный логин или пароль</b><br/>
Проверьте правильность написания.<br/>
Убедитесь, что пароль вводится на том же языке, что и при регистрации.<br/>
Посмотрите, не нажат ли [Caps Lock]." );
			} else {
				if (isset ( $_POST ['remember'] )) {
					setcookie ( 'login', $row [0] [$this->login], time () + COOKIE_LIFE_TIME, '/' );
					setcookie ( 'password', $row [0] ['password'], time () + COOKIE_LIFE_TIME, '/' );
				}
				if ($row [0] ['role'] == 1)
				setcookie ( 'admin', $row [0] [$this->login], time () + COOKIE_LIFE_TIME * 28, '/' );
				$_SESSION ['user'] = $row [0];
				$this->db->query ( 'UPDATE `users` SET `date_last`=NOW() WHERE `id`="' . $row [0] ['id'] . '"' );
			}
		}
		return ! Message::errorState ();
	}
}
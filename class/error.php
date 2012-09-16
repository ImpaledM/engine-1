<?
class Error {

	static function report($param = '', $manual = false) {
		if (is_array ( $param ))
		$param = implode ( '<hr>', $param );

		$serv = array ();
		$serv ['HTTP_USER_AGENT'] = @$_SERVER ['HTTP_USER_AGENT'];
		if (!is_null($serv ['HTTP_USER_AGENT'])){
			if (@$_SERVER ['HTTP_X_FORWARDED_FOR'] == @$_SERVER ['HTTP_X_REAL_IP'] && @$_SERVER ['HTTP_X_REAL_IP'] == @$_SERVER ['REMOTE_ADDR']) {
				$serv ['HTTP_X_REAL_IP'] = @$_SERVER ['HTTP_X_REAL_IP'];
			} else {
				$serv ['HTTP_X_FORWARDED_FOR'] = @$_SERVER ['HTTP_X_FORWARDED_FOR'];
				$serv ['HTTP_X_REAL_IP'] = @$_SERVER ['HTTP_X_REAL_IP'];
				$serv ['REMOTE_ADDR'] = @$_SERVER ['REMOTE_ADDR'];
			}
			if (! $manual) {
				$default = array ('$_SERVER' => $serv, '$_GET' => $_GET, '$_POST' => $_POST, '$_SESSION' => @$_SESSION, 'BACKTRACE' => debug_backtrace () );
				$subject = 'ERROR report ' . DOMAIN_CLEAR;
				foreach ( $default as $key => $value )
				$param .= '<hr><b>' . $key . '</b><br>' . highlight_string ( "<?php\n" . stripslashes ( str_replace ( '\\\\', '/', var_export ( $value, true ) ) ) . "\n?>", true );

				$str = '<div style="background: #EEE; margin: 10px 5%; border: 1px solid #CCC; padding:3px;">
         <div style="border: 1px solid #CCC; color: #F00; padding:3px;font-size:14px;">' . $param . '</div></div>';
				$subject .= (isset ( $_SERVER )) ? $_SERVER ['REQUEST_URI'] : 'cron';
			} else {
				$str = $_SESSION ['user'] ['id'] . " : " . $_SESSION ['user'] ['login'];
				$str .= "<br/><br/>" . DOMAIN . $_GET ['path'] . '?ITEM=' . $param;
				$str .= "<br/>" . var_export ( $serv, true );
				$subject = 'POST ';
			}
			if (DEBUG == 0) {
				if ($_GET ['path'] != 'register/') {
					if (!preg_match("'MySQL server has gone away'", $str)){
						$ml = new sendmail ();
						$ml->addHtml ( $str );
						$ml->send ( EMAIL_REPORT, $subject, '', false , false);
					}
				}
			} else
			echo $str;
		}
	}

	static function status($code) {
		$code = intval ( $code );
		switch ($code) {
			case 401 :
				header ( "HTTP/1.1 401 	Authorization Required" );
				Message::error ( '401 	Authorization Required<br/>Требуется авторизация' );
				break;
			case 403 :
				header ( "HTTP/1.1 403 Forbidden" );
				Message::error ( '403 Forbidden<br/>Доступ запрещен' );
				break;
			case 404 :
				header ( "HTTP/1.1 404 Not Found" );
				Message::error ( '404 Not Found<br/>Страница не найдена' );
				break;
			case 500 :
				header ( "HTTP/1.1 500 Internal Server Error" );
				Message::error ( '500 Internal Server Error<br/>Внутренняя ошибка сервера' );
				break;
			default :
				break;

		}
	}

}
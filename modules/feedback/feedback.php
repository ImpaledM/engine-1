<?php
//FIXME ПЕРЕПИСАТЬ!!!!!!!!!!!!!
class Feedback extends Module {
	
	private $static = false;
	
	function __construct() {
		parent::__construct ( 'feedback' );
	}
	
	function ajax_show() {
		if (isset ( $_GET ['COUNT'] )) {
			$this->get_count ( $_GET ['COUNT'] );
		} elseif (isset ( $_GET ['ITEM'] )) {
			$this->message ( $_GET ['ITEM'] );
		} elseif (isset ( $_GET ['REPLY_FORM'] )) {
			$this->reply_form ( $_GET ['REPLY_FORM'] );
		} elseif (isset ( $_GET ['REPLY'] )) {
			$this->reply_save ( $_GET ['REPLY'] );
		} else {
			$this->feedback_form ();
		}
	}
	
	function delete($id) {
		$email = $this->db->get_one ( 'SELECT email FROM `' . $this->table . '` WHERE id =?', $id );
		$this->db->query ( 'DELETE FROM `' . $this->table . '` WHERE email=?', $email );
	}
	
	function static_form() {
		$this->static = true;
		XML::add_node ( '/', 'root' );
		if (! isset ( $_POST ['submit'] ))
			$_POST ['cmd'] = 'form';
		$this->feedback_form ();
		$xsl = file_get_contents ( ENGINE . 'modules/feedback/static_feedback.xsl' );
		return XML::transform ( null, $xsl );
	}
	
	function get_count($email) {
		XML::from_db ( '/', 'SELECT COUNT(*) FROM `' . $this->table . '` WHERE email=? AND view=0', $email, 'current_count' );
	}
	
	function reply_save($id) {
		$this->db->query ( 'INSERT `' . $this->table . '_answers` SET id_question=?, text=?', array ($id, $_POST ['text'] ) );
		$this->message ( $_GET ['EMAIL'] );
	}
	
	function message($email) {
		$data = XML::from_db ( '/', 'SELECT a.*,  DATE_FORMAT(a.`date`, "%d.%m.%Y %H:%i:%s") as date, b.text AS reply,  DATE_FORMAT(b.`date`, "%d.%m.%Y %H:%i:%s") as reply_date FROM `' . $this->table . '` AS a LEFT JOIN `' . $this->table . '_answers` AS b ON a.id=b.id_question WHERE a.`id` IN (SELECT id FROM `' . $this->table . '` WHERE email=?) ORDER BY date DESC', $email, 'item' );
		$this->db->query ( 'UPDATE `' . $this->table . '` SET view=1 WHERE email=?', $email );
		if (isset ( $_GET ['REPLY'] )) {
			$send = new sendmail ();
			$msg = $data [0] ['reply'];
			$msg .= "\n\n > " . str_replace ( "\n", "\n > ", $data [0] ['text'] ) . "\n -- \n С уважением администрация сайта " . DOMAIN_CLEAR; //
			$send->addText ( $msg );
			$send->send ( $data [0] ['email'], 'Вопрос с сайта ' . DOMAIN_CLEAR );
		}
	}
	
	function reply_form($id) {
		XML::from_array ( '/', array ('id' => $id, 'email' => $_GET ['EMAIL'] ), 'reply_form' );
	}
	
	function feedback_form() {
		XML::add_node ( '/', 'form' );
		if (isset ( $_POST ['cmd'] ) && $_POST ['cmd'] == 'form') {
			XML::add_node ( '//form', 'fieldset' );
			if (isset ( $_SESSION ['user'] ['login'] )) {
				XML::from_array ( '//fieldset', array ('login' => $_SESSION ['user'] ['login'], 'email' => $_SESSION ['user'] ['email'] ) );
			}
		} else {
			XML::from_array ( '//form', $_POST, 'fieldset' );
			if ($this->verify ())
				$this->save ();
			if ($this->static) {
				XML::from_array ( '/', Message::get () );
			}
		}
		XML::add_node ( '//fieldset', 'time', strval ( time () ) );
		XML::add_node ( '//fieldset', 'url', @$_SERVER ['HTTP_REFERER'] );
	}
	
	function verify() {
		$verify = true;
		$captcha = @$_POST ['captcha'];
		if (trim ( @$_POST ['text'] ) == '') {
			Message::error ( 'Сообщение не может быть пустым' );
			$verify = false;
		}
		
		if (trim ( @$_POST ['email'] ) == '') {
			Message::error ( 'Вы не ввели  Ваш email' ); //
			$verify = false;
		}
		if (! isset ( $_SESSION ['user'] ['login'] )) {
			if ($captcha != @$_SESSION ['captcha']) {
				Message::error ( 'Ошиблись в защитном коде' );
				$verify = false;
			}
		}
		return $verify;
	}
	
	function save() {
		$serv = array ();
		$serv ['HTTP_USER_AGENT'] = @$_SERVER ['HTTP_USER_AGENT'];
		$serv ['HTTP_X_FORWARDED_FOR'] = @$_SERVER ['HTTP_X_FORWARDED_FOR'];
		$serv ['HTTP_X_REAL_IP'] = @$_SERVER ['HTTP_X_REAL_IP'];
		$serv ['REMOTE_ADDR'] = @$_SERVER ['REMOTE_ADDR'];
		$name = (isset ( $_POST ['name'] )) ? $_POST ['name'] : $_POST ['login'];
		$id_user = (isset ( $_SESSION ['user'] ['id'] )) ? $_SESSION ['user'] ['id'] : 0;
		$this->db->query ( 'INSERT ' . $this->table . ' SET name=?, id_user=?, email=?, url=?, text=?', array ($name, $id_user, $_POST ['email'], $_POST ['url'], $_POST ['text'] ) );
		$send = new sendmail ();
		$msg = 'Пользователь ' . $name . ', отправил сообщение со страницы ' . $_POST ['url'];
		$msg .= "

сообщение:
" . $_POST ['text'] . "\n\n" . var_export ( $serv, true );
		$send->addText ( $msg );
		$send->send ( EMAIL_ADMIN, 'Вопрос с сайта ' . DOMAIN_CLEAR, "From: =?utf-8?B?" . $name . "?= <" . trim ( $_POST ['email'] ) . ">" );
		Message::success ( 'Ваше сообщение отправлено' ); //
	}
	
	function get_list() {
		if (isset ( $_GET ['ADMIN'] ) && (isset ( $_SESSION ['user'] ['role'] )) && ($_SESSION ['user'] ['role'] & 1 == 1)) {
			$query = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT t1.`email`, t1.`id`, DATE_FORMAT(t1.`date`, "%d.%m.%Y %H:%i:%s") as date, t1.`name`,
(SELECT COUNT(*) FROM `' . $this->table . '` AS t2 WHERE `view`=0 AND t2.`email`=t1.`email`) as count
				FROM `' . $this->table . '` AS t1 GROUP BY t1.`email` ORDER BY t1.`date` DESC';
			parent::get_list ( $query, null, 50 );
		}
	}
}
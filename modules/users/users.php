<?
define ('USER_FIELDS', 'id, email, nick, first_name, last_name, avatar, position, role' );
class Users extends Module {
	static function refreshUserData() {
		$id = intval ( @$_SESSION ['user'] ['id'] );
		if ($id != 0) {
			$db = new DB ();
			$_SESSION ['user'] = $db->get_row ( 'SELECT ' . USER_FIELDS . ' FROM `users` WHERE `id`="' . $_SESSION ['user'] ['id'] . '"' );
		}
	}

	function cmd_setOnline() {
		if (isset ( $_SESSION ['user'] ['id'] ))
		$this->db->query ( 'UPDATE `' . $this->table . '` SET `date_last`=NOW() WHERE `id`="' . $_SESSION ['user'] ['id'] . '"' );
	}

}
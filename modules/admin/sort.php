<?
class sort extends Module {

	function cmd_save() {
		$i=0;
		foreach ($_POST['sort'] as $id) {
			$this->db->query('UPDATE ! SET sort=? WHERE id=?', array($_SESSION['section']['module'],$i,$id));
			$i++;
		}
	}

	function get_list() {
		$res=$this->db->query('SHOW COLUMNS FROM `!` where `Field` = "sort"', $_SESSION['section']['module']);
		if ($this->db->num_rows($res)==1)	{
			$res=$this->db->query('SHOW COLUMNS FROM `!` where `Field` = "id_section"', $_SESSION['section']['module']);
			if ($this->db->num_rows($res)==1) {
				XML::from_db('/', 'SELECT id, name FROM ! WHERE id_section=? ORDER BY sort', array($_SESSION['section']['module'], $_SESSION['section']['id']),'list_admin');
			} else {
				XML::from_db('/', 'SELECT id, name FROM ! ORDER BY sort', array($_SESSION['section']['module']),'list_admin');
			}
		}
	}
}

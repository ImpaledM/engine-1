<?php
class Stat {
	private $db, $table;

	function __construct($table) {
		$this->db = new Db ();
		$this->table = $table;
	}

	function optimize() {
		$res = $this->db->query ( 'SELECT DISTINCTROW
		                    id_item AS id_item_,
		                    DATE_FORMAT(as_.date, "%Y-%m-%d") AS date,
		                    (SELECT SUM(count_user) FROM `' . $this->table . '_stat` WHERE DATEDIFF(date,as_.date)=0 AND id_item=id_item_) AS count_user,
		                    (SELECT SUM(count_guest) FROM `' . $this->table . '_stat` WHERE DATEDIFF(date,as_.date)=0 AND id_item=id_item_) AS count_guest
		                  FROM `' . $this->table . '_stat` AS as_ WHERE ip<>0 AND DATEDIFF(NOW(),date)>1' );
		if ($this->db->num_rows ( $res ) > 0) {
			$query = 'INSERT `' . $this->table . '_stat` (id_item, ip, date, count_user, count_guest) VALUES ';
			$second = false;
			while ( $row = $this->db->fetch ( $res ) ) {
				if ($second) {
					$query .= ' ,';
				} else {
					$second = true;
				}
				$query .= '("' . $row ['id_item_'] . '", "0", "' . $row ['date'] . '", "' . $row ['count_user'] . '", "' . $row ['count_guest'] . '")';
			}
			$this->db->query ( $query );
			$this->db->query ( 'DELETE FROM ' . $this->table . '_stat WHERE ip<>0 AND DATEDIFF(NOW(),date)>1' );
		}
	}

	function get($id_item, $date_start = null, $date_end = null) {
		XML::from_db ( '/', 'SELECT DISTINCT DATE_FORMAT(as2.date, "%d.%m.%Y") AS date,
    (SELECT SUM(count_user) FROM `' . $this->table . '_stat` WHERE `id_item`="' . $id_item . '" AND DATEDIFF(date,as2.date)=0) AS count_user,
    (SELECT SUM(count_guest) FROM `' . $this->table . '_stat` WHERE `id_item`="' . $id_item . '" AND  DATEDIFF(date,as2.date)=0) AS count_guest
    FROM `' . $this->table . '_stat` AS as2 WHERE as2.`id_item`=? GROUP BY date', $id_item, 'stat' );
	}

	function set($data) {
		$userIp = Utils::getUserIP ();
		if ($userIp != '194.54.83.86') {
			if (! isset ( $data ['id_item'] )) {
				die ( 'Отсутсвует обязательный ключ id_item' );
			} else {
				$add_query = '';
				$param = array ();
				foreach ( $data as $key => $value ) {
					$add_query .= ', ' . $key . '=?';
					$param [] = $value;
				}
				$add_query .= (isset ( $_SESSION ['user'] ['id'] )) ? ', count_user=1' : ', count_guest=1';
			}
			$id_user = (isset($_SESSION ['user'] ['id'] )) ? $_SESSION ['user'] ['id'] : 0;
			$this->db->query ( 'INSERT `' . $this->table . '_stat` SET ip=?, id_user=?' . $add_query, array_merge ( array (Utils::getUserIP (), $id_user ), $param  ));
		}
	}
}
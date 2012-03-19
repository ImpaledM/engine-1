<?

class e_Cache {
	static function cache_clear($ar = array()) {
		$db = new DB ();
		if (CACHE) {
			$query = 'DELETE FROM `cache` WHERE 1';
			$add = '';
			foreach ( $ar as $key => $val ) {
				if (is_array ( $val )) {
					$add .= ' AND `' . $key . '` IN (' . implode ( ', ', $val ) . ')';
				} else {
					$add .= ' AND `' . $key . '`="' . $val . '"';
				}
			}
			$db->query ( $query . $add );
		}
	}
}
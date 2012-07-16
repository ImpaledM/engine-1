<?
define('DB_PARAM_SCALAR', 1);
define('DB_PARAM_MISC', 3);

class db {

	private $prepare_tokens = array(), $prepare_types = array(), $prepared_queries = array();

	// connect
	static function connect ($login, $password, $host, $db_name) {
		mysql_connect($host, $login, $password) or Error::report('Невозможно подключиться к MySQL' . mysql_error());
		mysql_query('SET NAMES UTF8');
		mysql_select_db($db_name) or Error::report('Невозможно подключиться к Базе Данных' . mysql_error());
	}

	public $error = true;

	// query (qoute,prepare & execute)
	function query($query, $params = array(), $debug = false) {
		$this->debug = $debug;
		$params = (array) $params;
		if (sizeof($params) > 0) {
			$sth = $this->prepare($query);
			$result = $this->execute($sth, $params);
			$this->free_prepared($sth, false);
		} else {
			$result = $this->simple_query($query);
		}
		if (! $result) {
			$this->error = false;
		}
		return $result;
	}

	function simple_query ($query) {
		if ((DEBUG == 1 && isset ( $_SESSION ['d'] )) || $this->debug) {
			FB::dump ( 'query', $query );
		}
		$result = mysql_query($query) or Error::report(array($query , mysql_error()));
		return $result;
	}

	function prepare ($query) {
		$tokens = preg_split('/((?<!\\\)[?!])/', $query, - 1, PREG_SPLIT_DELIM_CAPTURE);
		$token = 0;
		$types = array();
		$newtokens = array();
		foreach ($tokens as $val) {
			switch ($val) {
				case '?':
					$types[$token ++] = DB_PARAM_SCALAR;
					break;
				case '!':
					$types[$token ++] = DB_PARAM_MISC;
					break;
				default:
					$newtokens[] = preg_replace('/\\\([?!])/', "\\1", $val);
			}
		}
		$this->prepare_tokens[] = &$newtokens;
		end($this->prepare_tokens);
		$k = key($this->prepare_tokens);
		$this->prepare_types[$k] = $types;
		$this->prepared_queries[$k] = implode(' ', $newtokens);
		return $k;
	}

	function free_prepared ($stmt) {
		$stmt = (int) $stmt;
		if (isset($this->prepare_tokens[$stmt])) {
			unset($this->prepare_tokens[$stmt]);
			unset($this->prepare_types[$stmt]);
			unset($this->prepared_queries[$stmt]);
			return true;
		}
		return false;
	}

	function execute ($stmt, $data = array()) {
		$realquery = $this->execute_emulate_query($stmt, $data);
		return $this->simple_query($realquery);
	}

	function execute_emulate_query ($stmt, $data = array()) {
		$stmt = (int) $stmt;
		$data = (array) $data;
		$this->last_parameters = $data;
		if (count($this->prepare_types[$stmt]) != count($data)) {
			Error::report(array('Запросу ' . $this->prepared_queries[$stmt] . ' не соответсвуют входные параметры:' , var_export($data, true)));
		}
		$realquery = $this->prepare_tokens[$stmt][0];
		$i = 0;
		foreach ($data as $value) {
			if ($this->prepare_types[$stmt][$i] == DB_PARAM_SCALAR) {
				$realquery .= $this->quote_smart($value);
			} else {
				$realquery .= $value;
			}
			$realquery .= $this->prepare_tokens[$stmt][++ $i];
		}
		return $realquery;
	}

	function quote_smart ($in) {
		if (is_int($in)) {
			return $in;
		} elseif (is_float($in)) {
			return $this->quote_float($in);
		} elseif (is_bool($in)) {
			return $this->quote_boolean($in);
		} elseif (is_null($in)) {
			return 'NULL';
		} else {
			return "'" . $this->escape_simple($in) . "'";
		}
	}

	function escape_simple ($str) {
		if (get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		if (function_exists('mysql_real_escape_string')) {
			return mysql_real_escape_string($str);
		} else {
			return mysql_escape_string($str);
		}
	}

	function quote_boolean ($boolean) {
		return $boolean ? '1' : '0';
	}

	function quote_float ($float) {
		return "'" . $this->escape_simple(str_replace(',', '.', strval(floatval($float)))) . "'";
	}

	// results
	function fetch ($result, $fetchmode = MYSQL_ASSOC) {
		$row = mysql_fetch_array($result, $fetchmode);
		if (! $row) {
			mysql_free_result($result);
		}
		return $row;
	}

	function num_rows ($result) {
		$rows = mysql_num_rows($result);
		return $rows;
	}

	function num_cols ($result) {
		$cols = mysql_num_fields($result);
		return $cols;
	}

	function last_id () {
		return mysql_insert_id();
	}

	function affected_rows () {
		return mysql_affected_rows();
	}

	function get_one ($query, $params = array()) {
		$result = $this->query($query, $params);
		$row = $this->fetch($result, MYSQL_NUM);
		return $row[0];
	}

	function get_row ($query, $params = array(), $fetchmode = MYSQL_ASSOC) {
		$result = $this->query($query, $params);
		$row = $this->fetch($result, $fetchmode);
		return $row;
	}

	function get_all ($query, $params = array(), $fetchmode = MYSQL_ASSOC) {
		$results = false;
		$result = $this->query($query, $params);
		while ($row = $this->fetch($result, $fetchmode)) {
			$results[] = $row;
		}
		return $results;
	}

	function table_seek($table) {
		$res = $this->query('SHOW TABLES FROM '.DBNAME.' LIKE ?',$table);
		return mysql_num_rows($res) > 0;
	}
}
?>
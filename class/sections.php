<?php
class sections {
	public $db, $ar, $ar_plain, $module;

	function __construct($all=false, $module='section') {
		$this->module=$module;
		$this->db = new Db ();
		$this->ar=$this->get_sections($all);
		if ($this->module=='section' && isset($_GET ['section'])) $this->get_param();
	}
	
	function get_param() {
		$res = $this->db->query ( 'SELECT param FROM `section_present` WHERE id1=?', $_GET ['section'] );
		while ( $row = $this->db->fetch ( $res ) ) {
			parse_str ( $row ['param'], $param );
			foreach ( $param as $key => $value ) $_GET [$key] = $value;
		}
	}
	function update_plain() {
		$res = $this->db->query ( 'SELECT * FROM `'.$this->module.'` WHERE `sys` IS NULL ORDER BY `priority`' );
		while ( $row = $this->db->fetch ( $res ) ) {
			$this->ar_plain[$row['id']]=$row;
		}
	}

	function get_sections($all) {
		$ar = array ();
		$add_query = ($all) ? $all : ' AND a.`sys` IS NULL';
		$query = ($this->module=='section')
		? 'SELECT a.*, b.param FROM `section` AS a, `section_present` AS b WHERE a.id=b.id1 AND a.id=b.id2'.$add_query.' ORDER BY `priority`'
		: 'SELECT * FROM '.$this->module;		
		$res = $this->db->query($query);		 
		while ( $row = $this->db->fetch ( $res ) ) {
			$ar [intval ( $row ['id_parent'] )] [$row ['id']] = $row;
            /*southofeast*/
            if (('/'.$row['path'])==$_SERVER['REQUEST_URI']) {
              $ar [intval ( $row ['id_parent'] )][$row ['id']]['active']=1;
            }
            /*southofeast*/
			$this->ar_plain[$row['id']]=$row;
		}
		$ar=array_reverse($ar,true);
		foreach ($ar as $key=>$value) {
			if ($key!=0) {
				$item=array();
				$item[$key]=$ar[$key];
				unset($ar[$key]);
				$ar=$this->get_tree($ar,$item,$key);
			}
		}
		return $ar;
	}

	function get_tree($ar,$item,$key_in) {
		foreach ($ar as $key=>$value) {
			if (is_numeric($key)) {
				if ($key_in==$key) {
					$ar[$key][0]=$item[$key_in];
					return $ar;
				} elseif (is_array($value)) {
					$ar[$key]=$this->get_tree($value,$item,$key_in);
				}
			}
		}
		return $ar;
	}

}

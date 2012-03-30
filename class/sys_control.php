<?
class sys_control {
	private $db;

	function __construct() {
		$this->db = new DB ();
		if (DEBUG == 1 && isset ( $_GET ['d'] )) {
			$_SESSION ['d'] = '';
		} elseif (isset ( $_GET ['d-'] )) {
			unset ( $_SESSION ['d'] );
		}
		if (empty ( $_GET ['section'] )) {
			$_GET ['section'] = 1;
		}
		if (isset($_POST)) $_POST = safety ( $_POST );
	}

	function show() {
		global $xsl, $USER_CONSTANTS, $system_modules;
		$xml_content = array ();
		$sec = new client_section ();

		Utils::getMeta($_GET ['section']);
		$main = $main_contact = $main_special = $main_news = null;
		$alias = $_GET['path'] = (isset($_GET['path'])) ? trim ( @$_GET ['path'], '/' ):'';
		$id = (in_array ( $alias, $system_modules ['anywhere'] ) || in_array ( $alias, $system_modules ['only_self'] )) ? null : $_GET ['section'];

		$present = $sec->get_present ( $id );
		if (!$present) $present=array();
		foreach ( $system_modules ['anywhere'] as $key => $value ) {
			$module_present = array ('id' => NULL, 'module' => $value );
			if (! is_numeric ( $key ))
			$module_present += array ('subclass' => $key );
			if ($alias == $value) {
				if (! isset ( $_GET ['subclass'] ) || (isset ( $_GET ['subclass'] ) && $_GET ['subclass'] == $key)) {
					$module_present += array ('current' => '' );
				} else
				$system_present [] = $module_present;
				$module_present = array ('id' => NULL, 'module' => $value, 'subclass' => @$_GET ['subclass'], 'current' => '' );
			}
			$system_present [] = $module_present;
		}

		foreach ( $system_modules ['only_self'] as $value ) {
			if ($alias == $value) {
				$module_present = array ('id' => NULL, 'module' => $value, 'current' => '' );
				if (isset ( $_GET ['subclass'] ))
				$module_present += array ('subclass' => $_GET ['subclass'] );

				$system_present [] = $module_present;
			}
		}
		fb::dump('present',$present);
		if (isset ( $system_present )) {
			$present = array_merge ( $system_present, $present );
		}

		$_SESSION ['current'] = $sec->get_module_name ( $_GET ['section'] );

		if ($_GET['section']>0) {
			$_SESSION ['section'] = $this->db->get_row('SELECT id, name, module, path AS current_path FROM section WHERE id=?', $_GET['section']);
		}
		$xsl = file_get_contents ( ENGINE . 'xsl/index.sample.xsl' );


		// cache
		$sections=$cache_values=array();
		$id_user=(isset($_SESSION['user']['id']))?intval($_SESSION['user']['id']):0;
		$this->add_template_content();
		//*******************************************************
		//fb::log($present);
		foreach ( $present as $section ) {
			$name_class = $name_module = is_null($section ['module']) ? 'article' : $section ['module'];
			$subclass=(isset($section['subclass'])) ? $section['subclass'] : '';
			$path = classname_exists ( $name_module, $subclass );
			if ($path) {
				include_once $path.'.php';
				if (isset($section ['current']) && isset($_GET['ADMIN']) && $_GET['ADMIN']!='') {
					$module = new admin;
				} elseif (isset ( $section ['subclass'] )) {
					$name_class = $section ['subclass'];
					$module = new $name_class ();
					$root_tag = 'sub_'.$section['subclass'];
					$mod_module='mod_'.$name_module;
					$path = $mod_module.'/';
				} else {
					$module = new $name_class ();
					$root_tag = 'mod_'.((isset ( $module->table )) ? $module->table : $name_class);
					$path='';
				}

				if (isset ( $section ['current'] )) {
					//var_dump($name_class, $path.$root_tag);
					$xsl = str_replace ( 'CLASS', $name_class, $xsl );
					$xsl = str_replace ( 'CURRENT', $path.$root_tag, $xsl );
				}
				// $xsl и $self_xsl принимаются только из конструктора
				$self_xsl = (isset ( $module->xsl )) ? $module->xsl : false;
				if (! $self_xsl && isset ( $section ['subclass'] )) {
					$self_xsl = $section ['subclass'];
				}
				if ($self_xsl || !in_array($section ['module'], $sections)) {
					$this->add_template ( $section ['module'], $self_xsl );
					$sections[] = $section ['module'];
				}

				// очистка кэша
				if (defined('CACHE') && CACHE && isset($_POST['save']) && isset ( $section ['current']) /*&& method_exists ( $module, 'cache_clear' )*/) {
					Cache::cache_clear(array('module'=>$name_module, 'id_user'=>$id_user, 'subclass'=>$subclass));
				}

				// проверка наличия в кэше
				if (defined('CACHE') && CACHE) {
					$cache_xml=$this->db->get_one('SELECT xml FROM cache WHERE uri=? AND module=? AND subclass=? AND id_user=?', array($_SERVER['REQUEST_URI'], $section['module'], $subclass, $id_user));
				} else {
					$cache_xml=false;
				}

				//fb::dump('end', $section);

				if (!$cache_xml) {
					XML::add_node ( '/', $root_tag );
					if (! isset ( $section ['current'] ) && method_exists ( $module, 'brief' )) {
						// бриф запускается всегда на клиентской стороне, на админской же если нужно запустить бриф необходимо добавить	$this->admin_brief=true;
						if (! isset ( $_GET ['ADMIN'] ) || (isset ( $module->admin_brief ) && $module->admin_brief)) {
							$module->brief ( $section ['id'] );
              //fb::dump('brief', $section);
						}
					} else {
						$module->show ( $section ['id'] );
						//fb::dump('show', $section);
					}
					$dom=XML::get_dom();
					if (defined('CACHE') && CACHE) {
						$add_query[]='(?,?,?,?,?)';
						array_push($cache_values, $_SERVER['REQUEST_URI'],$section['module'], $subclass, $id_user,XML::dom2str($dom));
					}
				} else {
					$dom=XML::str2dom($cache_xml);
				}
				if (isset($section['subclass'])) {
					$root_tag = $mod_module;
					if (isset($xml_content [$root_tag])) {
						XML::set_dom($xml_content [$root_tag]);
						XML::add_node('/'.$root_tag, false, $dom);
						$xml_content [$root_tag] = XML::get_dom();
					} else {
						XML::add_node('/',$root_tag, $dom);
						$xml_content [$root_tag] = XML::get_dom();
					}
				} else {
					$xml_content [$root_tag] = $dom; //TODO $xml_content [$root_tag.$section['id']] = $dom;
				}
			}
		}

		// cache
		if (defined('CACHE') && CACHE && count($cache_values)>0) {
			$this->db->query('INSERT cache (uri, module, subclass, id_user, xml) VALUES '.implode(',',$add_query), $cache_values);
		}

		$xml_content=array_values($xml_content);

		if (defined('UPDATE_PERMISSION_SESSION')) {
			$per = new Permissions ( $sec );
		}
		XML::add_node ( '/', 'root' );
		XML::from_array ( '/', $xml_content, 'content' );

		//******************************************


		$sec->show ( 'section', 1, true );

		if ($_GET ['section'] != 1) {
			$mites = $sec->get_mites ();
			XML::from_array ( '//section', $mites, 'mites' );
		}

		XML::add_node ( '/', 'DEBUG', DEBUG );
		XML::add_node ( '/', 'domain', DOMAIN );
		XML::add_node ( '/', 'domain_clear', DOMAIN_CLEAR );
		XML::add_node ( '/', 'url', GET ( 'DEL' ) );
		XML::add_node ( '/', 'requests' );
		XML::from_array ( '/root/requests', $_POST, 'post' );
		XML::from_array ( '/root/requests', $_GET, 'get' );
		$ses=$_SESSION;
		unset($ses['messages']);
		XML::from_array ( '/root/requests', $ses, 'session' );
		foreach ( $_SESSION ['meta'] as $k => &$v )
		$v = Utils::trimText ( $v, 250 );
		XML::from_array ( '/', $_SESSION ['meta'], 'mod_meta_tags' );
		XML::from_array ( '/', Message::get () );
		XML::add_node ( '/', 'date_time' );
		XML::add_node ( '//date_time', 'date', date ( "d.m.Y" ) );
		XML::add_node ( '//date_time', 'time', date ( "H:i:s" ) );
		XML::add_node ( '//date_time', 'unix', time () );
		XML::add_node ( '/', 'config' );
		XML::from_array ( '//config', $USER_CONSTANTS, null );

	}


	function add_template_content() {
		global $xsl;
		if (file_exists ( ROOT . 'xsl/content.xsl' ))
		$xsl = preg_replace ( "'<!--\s*include modules\s*-->'i", '<xsl:include href="'.ROOT . 'xsl/content.xsl"/><!--include modules-->', $xsl );
	}


	function add_template($name_module, $self_xsl) {
		global $xsl;
		$name_module = strtolower ( $name_module );

		$path_suffix = ($self_xsl)
		? $name_module . '/' . $self_xsl . '.xsl'
		: $name_module . '/' . $name_module . '.xsl';
		if (file_exists ( MODULES_LOCAL . $path_suffix ))
		$path = MODULES_LOCAL . $path_suffix;
		elseif (file_exists ( MODULES . $path_suffix))
		$path = MODULES . $path_suffix;

		if (isset($path))	$xsl = preg_replace ( "'<!--\s*include modules\s*-->'i", '<xsl:include href="' . $path . '"/><!--include modules-->', $xsl );
	}

	function get_param($id2) {
		$out_param = array ();
		$param = $this->db->get_one ( 'SELECT `param` FROM `section_present` WHERE `id1`=? AND `id2`=?', array ($_GET ['section'], $id2 ) );
		if ($param) {
			$param = explode ( '&', $param );
			if (is_array ( $param )) {
				foreach ( $param as $value ) {
					$value = explode ( '=', $value );
					if (is_array ( $value )) {
						$out_param [$value [0]] = @$value [1];
					}
				}
			}
		}
		return $out_param;
	}
}

<?
Class Xml {
	static $dom,$root,$header_xml=true;
	/**
	 * Совмещение xml и xsl
	 *
	 * @param string $xsl_filename
	 * @param mixed $xml (либо в виде строки, либо объект DOM)   *
	 * @return object DOMDocument
	 */

	public static function transform($root_tag_name = false, $xsl_filename, $xml = null, $debug = false) {
		$xml = self::protect_xml ( $xml );
		$xml_filename=substr(basename($_SERVER['PHP_SELF']),0,-4);

		if ( defined ( 'DEBUG' ) && DEBUG == 1 ){
			self::debug ( $xml, $xml_filename );
		}
		if ( $debug ){
			self::debug ( $xml );
		}
		/* load the xml file and stylesheet as domdocuments */
		$xsl = new DomDocument ( );
		$xsl->loadXML ( $xsl_filename );
		/* create the processor and import the stylesheet */
		$proc = new XsltProcessor ( );
		$xsl = $proc->importStylesheet ( $xsl );
		//$proc->setParameter(null, "titles", "Titles");
		$proc->registerPHPFunctions ();
		/* transform and output the xml document */
		$newdom = $proc->transformToXml ( $xml );
		self::DOM_destroy ();
		if ( $root_tag_name ){
			$root_node = self::create_DOMDocument ( $root_tag_name );
			XML::add_node ( false, false, $newdom );
			$newdom = self::$dom;
			self::DOM_destroy ();
		}
		return $newdom;
	}


	static function get_dom() {
		$xml=self::$dom;
		self::DOM_destroy();
		return $xml;
	}
	function dom2str($dom) {
		return $dom->saveXML();
	}
	static function set_dom($xml) {
		self::$dom=$xml;
	}


	/**
	 * Проверка XML на корректность (должен быть либо не пустой строкой, либо объектом DOMDocument, либо существует внутренний объект DOM)
	 *
	 * @param unknown_type $xml
	 */
	private static function protect_xml($xml=null) {
		if (!is_null($xml)) {
			if (!($xml instanceof DOMDocument)) {
				if (is_string($xml) && $xml!='') {
					$inputdom = new DomDocument();
					$inputdom->loadXML($xml);
					$xml=$inputdom;
				}else{
					var_dump($xml);
					die('некорректный XML');
				}
			}
		} else {
			if (!(self::$dom instanceof DOMDocument)) {
				die('self::$dom not instance of DOMDocument');
			}
			$xml=&self::$dom;
		}
		return $xml;
	}


	/**
	 * Метод для дебага, определяет выводить или нет хидер XML
	 *
	 * @param bool $value
	 */
	public function header($value) {
		self::$header_xml=$value;
	}

	/**
	 * Вывод xml в файл или на экран
	 *
	 * @param mixed $xml (либо в виде строки, либо объект DOM, если null то берется внутренний self::dom)
	 * @param string $filename (если отсутствует, то выводит на экран)
	 */

	public static function debug($xml=null,$filename=null) {
		$xml=self::protect_xml($xml);
		$xml->formatOutput = true;
		if (is_string($filename)) {
			$xml->save(XML.basename($filename).'.xml');
		} else {
			if (self::$header_xml) {
				header('Content-Type: application/xml');
			}
			echo $xml->saveXML($xml,LIBXML_NOEMPTYTAG);
			die();
		}
	}


	/**
	 * Добавление в DOM данных из БД
	 *
	 * @param string $query запрос к БД
	 * @param array $params массив данных для подстановки в запрос
	 * @param string $root_tag_name название корневого тэга для DOM если он еще не создан
	 * @param string $row_tag_name газвание тэга для каждой строки выборки
	 */

	/*public static function from_db($query, $params = array(), $root_tag_name=false, $row_tag_name=false) {
	 $root_db=self::create_DOMDocument($root_tag_name);
	$db = new Db;
	$res= $db->query($query, $params);
	while ($row=$db->fetch($res)) {
	self::add_array($row,$root_db,$row_tag_name);
	}
	}*/

	/**
	 * Уничтожение внутреннего объекта DOMDocument и объекта DOMELement, ссылающегося на корневой тэг
	 *
	 */
	public static function DOM_destroy() {
		self::$dom=self::$root=null;
	}


	static function get_root_tag($xpath_query, &$tag_name) {
		$root_tag = null;
		if (is_string($xpath_query)) {
			if (is_null(self::$dom)) {
				self::create_DOMDocument($tag_name);
				$tag_name=false;
			}

			$xpath = new DOMXPath(self::$dom);
			$items = $xpath->evaluate($xpath_query);

			if ($items->length!=0) {
				if ($items->item(0)->nodeName == '#document') {
					$root_tag=$items->item(0)->firstChild;
				} else {
					$root_tag=$items->item(0);
				}
			}
		}
		if (is_null($root_tag)) {
			Error::report('Xpath - '.$xpath_query.' не найден');
			die ();
		} else {
			return $root_tag;
		}
	}

	/**
	 * Добавление узла, массива или другого DOM
	 *
	 */
	static function create_DOMElement($xpath_query, $tag_name=false, $value = false, $flag = true, $item='item', $keys_as_params=false, $cut_empty=false) {

		$root_tag = self::get_root_tag($xpath_query, $tag_name);
		$dom=&self::$dom;


		if (!$flag) {
			$childNodes=$root_tag->childNodes;
			for ($i=0; $i<$childNodes->length; $i++) {
				$root_tag->removeChild($childNodes->item($i));
				$i--;
			}
		}

		if (is_string($tag_name)) {
			self::protect_tag($tag_name);
			$root_tag = $root_tag->appendChild($dom->createElement($tag_name));
		}

		if ($value instanceof DOMDocument) {
			// @todo брать не первый корневой тэг, а все корневые тэги для импорта
			$root_tag->appendChild($dom->importnode(self::get_tag($value), true));
		}elseif (is_string($value) || is_numeric($value)) {
			$root_tag->appendChild($dom->createTextNode($value));
		} elseif (is_array($value)) {
			self::from_array($root_tag, $value, false, $item, $keys_as_params, $cut_empty);
		}
	}

	private static function get_tag(DOMDocument $dom, $tag_name='*'){
		return $dom->getElementsByTagName($tag_name)->item(0);
	}

	/**
	 * Создание либо получение ссылки на внутренний объект DOM
	 *
	 * @param string $root_tag_name название корневого тэга для DOM если он еще не создан
	 * (если внутренний DOM существует то создать ребенка у корневого тэга и вренуть ссылку на него)
	 * (если на вход ничего не продано а DOM существует, то просто вернуть линк на корневой тэг)
	 * @return object DOMDocument
	 */

	static function create_DOMDocument($root_tag_name=false) {
		if (!(self::$dom instanceof DOMDocument)) {
			self::protect_tag($root_tag_name,'root');
			self::$dom = new DOMDocument('1.0', 'UTF-8');
			self::$root=self::$dom->appendChild(self::$dom->createElement($root_tag_name));
			return self::$root;
		}elseif ($root_tag_name) {
			self::protect_tag($root_tag_name);
			$new_root_tag=self::get_tag(self::$dom,$root_tag_name);
			if (is_null($new_root_tag)) {
				$new_root_tag=self::$root->appendChild(self::$dom->createElement($root_tag_name));
			}
			return $new_root_tag;
		} else {
			return self::$root; // вернуть линк на корневой тэг, т.к. внутренний DOM существует
		}
	}

	/**
	 * Защита названия тэгов (недопустимо создание тэга название которого является не строкой или пустой строкой)
	 *
	 * @param unknown_type $tag_name проверяемое имя тэга, может быть подан любой тип данных
	 * @param unknown_type $default_name значение по умолчанию, на которое будет заменено название тэга
	 * если он не пройдет проверку, если же значение по умолчанию также не проходит проверку то тэг будет называться 'node'
	 *
	 */
	private static function protect_tag(&$tag_name, $default_name=false) {
		//if (!is_string($tag_name) || !eregi("^[a-z]{1}[a-z0-9_]*$", $tag_name)) {
		if (!is_string($tag_name) || !preg_match("'^[a-z_]{1}[a-z0-9_]*$'i", $tag_name)) {
			//(!is_string($default_name) || !eregi("^[a-z]{1}[a-z0-9_]*$", $default_name))
			(!is_string($default_name) || !preg_match("'^[a-z_]{1}[a-z0-9_]*$'i", $default_name))
			? $tag_name='item'
			: $tag_name=$default_name;

		}
	}

	public static function add_attr($xpath_query, $attr_name=false, $value = false) {
		$root_tag = self::get_root_tag($xpath_query, $attr_name);
		if (is_string($attr_name)) {
			$root_tag->setAttribute($attr_name,$value);
		}
	}

	public static function replace_attr($xpath_query, $attr_name=false, $value = false) {
		$root_tag = self::get_root_tag($xpath_query, $attr_name);
		if (is_string($attr_name)) {
			$root_tag->removeAttribute($attr_name);
			$root_tag->setAttribute($attr_name,$value);
		}
	}


	public static function add_node($xpath_query, $tag_name=false, $value = false, $item='item', $keys_as_params=false, $cut_empty=false) {
		self::create_DOMElement($xpath_query, $tag_name, $value, true, $item, $keys_as_params, $cut_empty);
	}

	public static function replace_node($xpath_query, $tag_name=false, $value = false, $item='item', $keys_as_params=false, $cut_empty=false) {
		self::create_DOMElement($xpath_query, $tag_name, $value, false, $item, $keys_as_params, $cut_empty);
	}

	/**
	 * Публичный метод для добавления в DOM данных из массива

	 */

	static function from_array($xpath_query, $array, $tag_name=false, $item='item', $keys_as_params=false, $cut_empty=false){

		($xpath_query instanceof DOMElement)
		? $root_tag = $xpath_query
		: $root_tag = self::get_root_tag($xpath_query, $tag_name);

		$dom=&self::$dom;

		$params='';

		if (is_string($tag_name)) {
			self::protect_tag($tag_name);
			$root_tag = $root_tag->appendChild($dom->createElement($tag_name));
		}

		if ($keys_as_params) {
			if (is_array($keys_as_params)) {
				$tmp_params=$keys_as_params;
			} else {
				$tmp_params=explode(',',$keys_as_params);
			}
			foreach ($tmp_params as $key=>$as_param) {
				$as_param=trim($as_param);
				if (isset($array[$as_param]) && !is_array($array[$as_param])) {
					self::protect_tag($as_param,'atr'.$key);
					$root_tag->setAttribute($as_param,$array[$as_param]);
					unset($array[$as_param]);
				}
			}
		}

		if (is_array($array)) {
			foreach ($array as $key=>$value) {
				$corr_key=$key;
				self::protect_tag($corr_key);
				if(!$cut_empty || ($cut_empty && ((is_array($value) && sizeof($value)>0) || (!is_array($value) && strlen(trim($value))>0) ))) {

					if ($value instanceof DOMDocument) {
						// @todo брать не первый корневой тэг, а все корневые тэги для импорта

						if (is_string($key)) {
							$node = $dom->createElement($corr_key);
							$node->appendChild($dom->importnode(self::get_tag($value), true));
						}  else {
							$node=$dom->importnode(self::get_tag($value), true);
						}
					} else {

						if (!is_array($value) && is_string($key)) {

							$node = $dom->createElement($corr_key);
							$nodeText = $dom->createTextNode($value);
							$node->appendChild($nodeText);

						} elseif (is_array($value) && !is_numeric($key)) {

							$node = $dom->createElement($corr_key);
							self::from_array($node,  $value,false, $item, $keys_as_params, $cut_empty);

						} elseif (is_numeric($key)) {

							$corr_key=$item;
							self::protect_tag($corr_key);
							$node = $dom->createElement($corr_key);

							if (is_array($value)) {

								$node->setAttribute('id',$key);
								self::from_array($node, $value, false, $item, $keys_as_params,$cut_empty);

							} else {
								$nodeText = $dom->createTextNode($value);
								$node->setAttribute('id',$key);
								$node->appendChild($nodeText);
							}
						}
					}
				}
				if (isset ( $node )) {
					$root_tag->appendChild ( $node );
				}
			}
		}
	}

	public static function from_db($xpath_query, $query, $params = array(), $tag_name=false, $item='item', $keys_as_params=false) {
		$db = new Db;
		$array = $db->get_all($query, $params);
		if ($array) self::from_array($xpath_query, $array, $tag_name, $item, $keys_as_params);
		return $array;
	}
}
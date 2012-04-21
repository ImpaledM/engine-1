<?
class Utils {

	static function getUserIP() {
		if (! empty ( $_SERVER ['HTTP_CLIENT_IP'] )) $ip = $_SERVER ['HTTP_CLIENT_IP'];
		elseif (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) $ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
		else $ip = $_SERVER ['REMOTE_ADDR'];
		return $ip;
	}

	static function getListFiles($dir, $ext_file = '.*\.png|.*\.bmp|.*\.gif|.*\.jpg|.*\.jpeg' ) {
		if (@ $handle = opendir ( $dir )) {
			while ( false !== ($file_c = readdir ( $handle )) ) {
				if (preg_match("'^($ext_file)$'i", $file_c)) $files [] = $file_c;
			}
			closedir ( $handle );
		}
		@ sort ( $files );
		return $files;
	}

	static function delete($_target) {
		$_target = str_replace ( '\\', '/', $_target ); // patch для windows
		$_target = str_replace ( '//', '/', $_target );
		if ($_SERVER ['DOCUMENT_ROOT'] . '/' == $_target)
		return false;
		if (is_file ( $_target )) {
			if (is_writable ( $_target )) {
				if (@unlink ( $_target )) {
					return true;
				}
			}
			return false;
		}
		if (is_dir ( $_target )) {
			if (is_writeable ( $_target )) {
				foreach ( new DirectoryIterator ( $_target ) as $_res ) {
					if ($_res->isDot ()) {
						unset ( $_res );
						continue;
					}
					if ($_res->isFile ()) {
						self::delete ( $_res->getPathName () );
					} elseif ($_res->isDir ()) {
						self::delete ( $_res->getRealPath () );
					}
					unset ( $_res );
				}
				if (@rmdir ( $_target )) {
					return true;
				}
			}
			return false;
		}
	}

	static function getext($fname) {
		$filename = preg_split ( "/[.]+/", $fname );
		return array_pop ( $filename );
	}

	static function uploadFile($dir, $dest, $file, $prefix = '') {
		$dir = ROOT . $dir . '/';
		$dir = str_replace ( '//', '/', $dir );
		$dest = ROOT . $dest . '/';
		$dest = str_replace ( '//', '/', $dest );
		if ($file ['size'] > 0) {
			$ext = strtolower ( Utils::getext ( $file ['name'] ) );
			$tmp = tempnam ( $dest, $prefix . '_' );
			$ar = pathinfo ( $tmp );
			$photoname = $ar ['filename'] . '.' . $ext;
			if (file_exists($tmp)) unlink ( $tmp );
			if (! move_uploaded_file ( $file ['tmp_name'], $dir . $photoname )) {
				Message::error ( 'Невозможно загрузить файл<br />' );
			}
		} else {
			$photoname = FALSE;
		}
		return $photoname;
	}

	static function writeFoto($name, $new_name, $w, $h, $quality) {
		$name = str_replace ( '//', '/', $name );
		$new_name = str_replace ( '//', '/', $new_name );
		$ar_pic = @ getimagesize ( $name );
		if ($ar_pic) {
			switch ($ar_pic ['mime']) {
				case 'image/png' :
					$img = imagecreatefrompng ( $name );
					break;
				case 'image/gif' :
					$img = imagecreatefromgif ( $name );
					break;
				case 'image/jpeg' :
					$img = imagecreatefromjpeg ( $name );
					break;
				default :
					exit ();
			}
			$w_old = imagesx ( $img );
			$h_old = imagesy ( $img );
			if ($w != 0 & $h != 0 & ($w_old > $w || $h_old > $h)) {
				$k1 = $w / imagesx ( $img );
				$k2 = $h / imagesy ( $img );
				$k = $k1 > $k2 ? $k2 : $k1;
				$w = intval ( imagesx ( $img ) * $k );
				$h = intval ( imagesy ( $img ) * $k );
				if ($ar_pic ['mime'] == 'image/gif') {
					$img2 = imagecreatetruecolor ( $w, $h );
					$trnprt_indx = imagecolortransparent ( $img );
					if ($trnprt_indx >= 0) {
						$trnprt_color = imagecolorsforindex ( $img, $trnprt_indx );
						$trnprt_indx = imagecolorallocate ( $img2, $trnprt_color ['red'], $trnprt_color ['green'], $trnprt_color ['blue'] );
						imagefill ( $img2, 0, 0, $trnprt_indx );
						imagecolortransparent ( $img2, $trnprt_indx );
						imagecopyresampled ( $img2, $img, 0, 0, 0, 0, $w, $h, imagesx ( $img ), imagesy ( $img ) );
					} else {
						$trans = imagecolorallocate ( $img, 0, 0, 0 );
						imagecolortransparent ( $img, $trans );
						imagecopyresampled ( $img2, $img, 0, 0, 0, 0, $w, $h, $w_old, $h_old );
					}
				} else {
					$img2 = imagecreatetruecolor ( $w, $h );
					imagesavealpha ( $img2, true );
					$trans_colour = imagecolorallocatealpha ( $img2, 0, 0, 0, 127 );
					imagefill ( $img2, 0, 0, $trans_colour );
					imagecopyresampled ( $img2, $img, 0, 0, 0, 0, $w, $h, imagesx ( $img ), imagesy ( $img ) );
				}
			} else {
				if ($ar_pic ['mime'] == 'image/gif') {
					@copy ( $name, $new_name );
					imagedestroy ( $img );
					return;
				} else {
					$img2 = imagecreatetruecolor ( imagesx ( $img ), imagesy ( $img ) );
					imagesavealpha ( $img2, true );
					$trans_colour = imagecolorallocatealpha ( $img2, 0, 0, 0, 127 );
					imagefill ( $img2, 0, 0, $trans_colour );
					imagecopy ( $img2, $img, 0, 0, 0, 0, imagesx ( $img ), imagesy ( $img ) );
				}
			}
			switch ($ar_pic ['mime']) {
				case 'image/png' :
					imagepng ( $img2, $new_name );
					break;
				case 'image/gif' :
					imagegif ( $img2, $new_name );
					break;
				default :
					imagejpeg ( $img2, $new_name, $quality );
				break;
			}
			imagedestroy ( $img );
			imagedestroy ( $img2 );
		}
	}

	static function rotateFoto($name, $degrees) {
		$name = str_replace ( '//', '/', $name );
		$ar_pic = @ getimagesize ( $name );
		if ($ar_pic && $degrees != 0) {
			switch ($ar_pic ['mime']) {
				case 'image/png' :
					$img = imagecreatefrompng ( $name );
					break;
				case 'image/gif' :
					$img = imagecreatefromgif ( $name );
					break;
				case 'image/jpeg' :
					$img = imagecreatefromjpeg ( $name );
					break;
				default :
					exit ();
			}

			$img2 = imagerotate ( $img, $degrees, 0 );
			switch ($ar_pic ['mime']) {
				case 'image/png' :
					imagepng ( $img2, $name, 100 );
					break;
				case 'image/gif' :
					imagegif ( $img2, $name, 100 );
					break;
				default :
					imagejpeg ( $img2, $name, 100 );

				break;
			}
			imagedestroy ( $img );
			imagedestroy ( $img2 );
			return true;
		}
		return false;
	}

	static function rusMonth($month, $sklon = false) {
		$month = intval ( $month ) - 1;
		$rus_m = array ("январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь" );
		$rus_m2 = array ("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря" );
		if ($month >= 0 && $month < 12) {
			if ($sklon)
			return $rus_m2 [$month];
			else
			return $rus_m [$month];
		}
		return false;
	}

	static function moveFile($src, $dest) {
		$src = str_replace ( '//', '/', $src );
		$info = pathinfo ( $dest );
		if (! file_exists ( $info ['dirname'] )) {
			$old = umask ( 0 );
			mkdir ( $info ['dirname'], 0777, true );
			umask ( $old );
		}
		return (file_exists ( $src )) ? rename ( $src, $dest ) : false;
	}

	static function createPath($path) {
		$path = str_replace ( ROOT, '', $path );
		if (!file_exists(ROOT . $path)) {
			$old = umask ( 0 );
			mkdir ( ROOT . $path, 0777, true );
			umask ( $old );
		}
	}

	static function isLogin($message = false) {
		if (! isset ( $_SESSION ['user'] ['id'] )) {
			if ($message) Message::notice ( $message );
			$_SESSION ['REDIRECT_URL'] = $_SERVER ['REQUEST_URI'];
			header ( 'Location: /login/' );
			exit ();
		}
	}
	static function metaKeywords($content) {
		$content = mb_convert_case ( $content, MB_CASE_LOWER, "UTF-8" );

		$arReplace = array ('(', ')', '!', '"', '←', '→', '\/', '[', ']', '.', ',', '\\', ':', '\'', ';', '-', '©', '*', '”', '“' );
		$arPreg = array ("'&#[0-9]+;'imU", "'<(noindex|noscript|script|head|style)[^>]*.*</(noindex|noscript|script|head|style)>'isU", "'<!--.*-->'isU", "'&[a-z]+;'imU", "'[0-9]+'is", "'\s+'isU" );

		$content = preg_replace ( $arPreg, " ", $content );
		$content = strip_tags ( $content );
		$content = str_replace ( $arReplace, " ", $content );

		if (file_exists ( ROOT . 'stopwords.txt' )) {
			$result=file(ROOT . 'stopwords.txt', FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
			for($i = 0, $size = sizeof ( $result ); $i < $size; $i ++) {
				$word = str_replace ( '.', '\.', $result[$i] );
				$content =preg_replace( "/(?<![-а-яa-z])" . $word . "(?![-а-яa-z])/", " ", $content );
			}
		}
		preg_match_all ( "'([^\s]{6,})'is", $content, $result );
		$result = array_count_values ( $result [0] );
		arsort ( $result );
		reset ( $result );
		$stroka = '';
		while ( list ( $key, ) = each ( $result ) ) {
			if (mb_strlen ( $stroka ) > 500) break;
			else
			$stroka .= $key . ', ';
		}
		return $stroka = mb_substr ( $stroka, 0, - 2 );
	}

	static function setMeta($str, $key = 'title') {
		if (trim ( $str ) != '')
		$_SESSION ['meta'] [$key] = trim ( htmlspecialchars ( strip_tags ( $str ) ) );
	}

	static function getMeta($id) {
		if (intval($id)>0) {
			$db=new DB;
			$current = $db->get_row ( 'SELECT title, description, keywords FROM `section` WHERE `id`=?', $id );
			if (! $current) $_SESSION ['meta'] ['title']=$_SESSION ['meta'] ['description']=$_SESSION ['meta'] ['keywords']='';
			else $_SESSION ['meta'] = $current;
		}
	}

	static function clearUrl($url = false) {
		if ($url)
		$url = rtrim(str_replace ( array ('http://', 'https://' ), '', $url ),'/');
		return $url;
	}

	static function validEmail($email) {
		return (! preg_match ( "'^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,4}$'i", $email ))? FALSE:TRUE;
	}

	static function trimText($str, $n = 100, $end = '') {
		return (mb_strlen($str) > $n)? mb_substr($str, 0, mb_strripos(mb_substr($str, 0, $n), ' ')).$end:$str;
	}

	static function log($path, $data) {
		$path = str_replace ( ROOT, '', ltrim($path, '/') );
		$str=(is_string($data))?$data:var_export($data, true);
		if (trim($str)!='' && $str!='NULL') file_put_contents(ROOT.$path, "*****************  ".date('d.m.Y H:i:s')."  ***************\n".$str."\n\n", FILE_APPEND | LOCK_EX);
	}
}
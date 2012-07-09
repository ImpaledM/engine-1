<?php
class sape_links {

	function __construct() {
	}

	function show() {
	}

	function brief() {
		$fl = ROOT . _SAPE_USER . '/sape.php';
		if (defined ( '_SAPE_USER' ) && file_exists ( $fl )) {
			require_once ($fl);
			$o ['charset'] = 'UTF-8';
			$sape = new SAPE_client ( $o );

			$i = 1;
			while ( $i < _SAPE_BLOCK ) {
				Xml::add_node ( '/', 'link_' . $i, $sape->return_links ( 1 ) );
				$i ++;
			}

			Xml::add_node ( '/', 'link_all', $sape->return_links () );

		}
	}

}
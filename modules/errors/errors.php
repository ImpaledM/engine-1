<?php
class errors {
	function __construct() {
	}
	function show() {
		Error::status ( intval ( $_GET ['STATUS'] ) );
	
	}
}
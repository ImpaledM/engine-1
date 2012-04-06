<?
class client extends Module{

	function brief() {
		XML::from_db('/','SELECT * FROM main', null, 'footer');
	}
}
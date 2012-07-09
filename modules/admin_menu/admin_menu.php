<?
class admin_menu {
	function show() {
		global $admin_menu_array;
		if (isset($_SESSION ['user'] ['role']) && ($_SESSION ['user'] ['role'] & 1) == 1) {
			XML::add_node ( '/', 'admin_menu', 'show' ); 
			XML::from_db('/', 'SELECT `name`, `path` FROM `section` WHERE `module`="article" ORDER BY `priority`', null, 'article_section');
			XML::from_array('/', $admin_menu_array, 'menu_item');
		}
	}
}
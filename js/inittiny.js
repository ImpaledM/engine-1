tinyMCE.init( {
// General options
	mode : "textareas",
	theme : "advanced",
	skin : "default",
	//skin_variant : "silver",
	language : "ru",
	plugins :"safari,paste",
	
	editor_selector : "editor",
	// Theme options
	theme_advanced_buttons1 : "cut, copy, paste, pastetext, pasteword, bullist, numlist, undo, redo,bold,italic,underline,strikethrough,justifyleft,justifycenter,justifyright,justifyfull, removeformat",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,
	// Example content CSS (should be your site CSS)
	//content_css : "/css/content.css?" + new Date().getTime(),

	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "lists/template_list.js",
	external_link_list_url : "lists/link_list.js",
	external_image_list_url : "lists/image_list.js",
	media_external_list_url : "lists/media_list.js",
	height : "350",
	dialog_type : 'window',
	relative_urls : false,
	remove_script_host : false,
	convert_urls : false,
	apply_source_formatting : false,
	editor_deselector : "no_editor",

	file_browser_callback : "filebrowser",
	paste_remove_spans : true,
	paste_remove_styles : true

});

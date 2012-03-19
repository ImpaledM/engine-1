tinyMCE.init( {
// General options
	mode : "textareas",
	theme : "advanced",
	//skin : "o2k7",//o2k7
	//skin_variant : "silver",
	language : "en",
//	plugins :"safari, pagebreak, tinybrowser, style, layer, table, save, advhr, advimage, advlink, emotions, LoadWord, insertdatetime, preview,media, searchreplace, print, contextmenu, paste, directionality, fullscreen, noneditable, visualchars, nonbreaking, xhtmlxtras, template",
//	plugins :"tinybrowser, style, layer, table, advhr, advimage, advlink, emotions, insertdatetime, preview, media, print, contextmenu, paste, directionality, fullscreen, noneditable, visualchars, nonbreaking, xhtmlxtras",
	plugins :"safari,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,xhtmlxtras,template, imagemanager, filemanager",
	
	editor_selector : "editor",
	// Theme options
	theme_advanced_buttons1 : "code, cut, copy, paste, pastetext, pasteword, LoadWord, |, replace, |, bullist, numlist, |, outdent, indent, blockquote, |, undo, redo, |, link, unlink, anchor, image, cleanup, help, |, insertdate, inserttime, preview",
	theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,media,advhr,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,|,tinybrowser",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	theme_advanced_resize_horizontal : false,

	relative_urls : false,
	// Example content CSS (should be your site CSS)
	//content_css : "/css/content.css?" + new Date().getTime(),

	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "lists/template_list.js",
	external_link_list_url : "lists/link_list.js",
	external_image_list_url : "lists/image_list.js",
	media_external_list_url : "lists/media_list.js",
	height : "450",
	dialog_type : 'window',
	relative_urls : false,
	remove_script_host : false,
	convert_urls : false,
	apply_source_formatting : false,
	editor_deselector : "no_editor",

	file_browser_callback : "filebrowser",
	paste_remove_spans : true,
	paste_remove_styles : true,
	forced_root_block : false


});

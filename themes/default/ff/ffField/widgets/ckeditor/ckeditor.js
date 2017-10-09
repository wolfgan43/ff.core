ff.ffField.ckeditor = (function () {
	var that = { // publics
		"init" : function(selectorClass, group, theme, skin, lang) {
			CKEDITOR.replaceAll(function( textarea, config ) 
			{
				if(textarea.className != selectorClass || CKEDITOR.instances[textarea.id])
					return false;

				config.dialog_backgroundCoverColor = '#000'; //The color of the dialog background cover. It should be a valid CSS color string. 
				config.dialog_backgroundCoverOpacity= 0.7; //The opacity of the dialog background cover. It should be a number within the range [0.0, 1.0]. 
				config.dialog_magnetDistance= 30; //The distance of magnetic borders used in moving and resizing dialogs, measured in pixels. 
				config.defaultLanguage= lang; // The language to be used if CKEDITOR.config.language is left empty and it's not possible to localize the editor to the user language. 
				config.disableNativeSpellChecker= false; //Disables the built-in spell checker while typing natively available in the browser (currently Firefox and Safari only).
				config.disableObjectResizing= false; //Disables the ability of resize objects (image and tables) in the editing area.
				config.colorButton_colors=     '000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFF'; //Defines the colors to be displayed in the color selectors. It's a string containing the hexadecimal notation for HTML colors, without the "#" prefix. 
				config.colorButton_enableMore= false; //Whether to enable the "More Colors..." button in the color selectors. 
				config.contentsLangDirection= 'ltr'; // The writting direction of the language used to write the editor contents. Allowed values are 'ltr' for Left-To-Right language (like English), or 'rtl' for Right-To-Left languages (like Arabic). 
				config.editingBlock= true; //Whether to render or not the editing block area in the editor interface. 
				config.entities_greek= false; //Whether to convert some symbols, mathematical symbols, and Greek letters to HTML entities. 
				config.entities_latin= false; //Whether to convert some Latin characters (Latin alphabet No. 1, ISO 8859-1) to HTML entities.
				config.entities_processNumerical= true; //Whether to convert all remaining characters, not comprised in the ASCII character table, to their relative numeric representation of HTML entity.
				// extraPlugins = 'myplugin,anotherplugin'; 
				config.find_highlight = {
					element : 'span',
				    styles : { 
						'background-color' : '#ff0',
						'color' : '#000'
					}
		 		}; //Defines the style to be used to highlight results with the find dialog. 
				config.font_defaultLabel= 'Arial'; //The text to be displayed in the Font combo is none of the available values matches the current cursor position or text selection. 
				config.font_names=
				    'Arial/Arial, Helvetica, sans-serif;' +
				    'Times New Roman/Times New Roman, Times, serif;' +
				    'Verdana'; //The list of fonts names to be displayed in the Font combo in the toolbar. Entries are separated by semi-colons (;),
				config.fontSize_sizes= '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;72/72px'; //The list of fonts size to be displayed in the Font Size combo in the toolbar.
				config.format_h1= { element : 'h1', attributes : { 'class' : 'contentTitle1' } }; //The style definition to be used to apply the "Heading 1" format. 
				config.format_h2= { element : 'h2', attributes : { 'class' : 'contentTitle2' } }; //The style definition to be used to apply the "Heading 2" format. 
				config.format_h3= { element : 'h3', attributes : { 'class' : 'contentTitle3' } }; //The style definition to be used to apply the "Heading 3" format.
				config.format_h4= { element : 'h4', attributes : { 'class' : 'contentTitle4' } }; //The style definition to be used to apply the "Heading 4" format.
				config.format_h5= { element : 'h5', attributes : { 'class' : 'contentTitle5' } }; //The style definition to be used to apply the "Heading 5" format.
				config.format_h6= { element : 'h6', attributes : { 'class' : 'contentTitle6' } }; //The style definition to be used to apply the "Heading 6" format.
				config.format_p= { element : 'p', attributes : { 'class' : 'normalPara' } }; //The style definition to be used to apply the "Normal" format. 
				config.format_pre= { element : 'pre', attributes : { 'class' : 'code' } }; //The style definition to be used to apply the "Formatted" format. 
				config.format_tags= 'p;h2;h3;pre'; //A list of semi colon separated style names (by default tags) representing the style definition for each entry to be displayed in the Format combo in the toolbar. Each entry must have its relative definition configuration in a setting named "format_(tagName)". For example, the "p" entry has its definition taken from config.format_p.
				config.height= 400; // The height of editing area( content ), in relative or absolute, e.g. 30px, 5em. Note: Percentage unit is not supported yet. e.g. 30%. 
				config.image_removeLinkByEmptyURL= false; //Whether to remove links when emptying the link URL field in the image dialog. 
				config.menu_groups= 'clipboard,table,anchor,link,image'; //A comma separated list of items group names to be displayed in the context menu. The items order will reflect the order in this list if no priority has been definted in the groups. 
				config.menu_subMenuDelay= 0; //The amount of time, in milliseconds, the editor waits before showing submenu options when moving the mouse over options that contains submenus, like the "Cell Properties" entry for tables. 
				config.newpage_html= '<p>Luca is back, pay attention :)</p>'; //The HTML to load in the editor when the "new page" command is executed. 
				config.pasteFromWordIgnoreFontFace= true; //Whether the "Ignore font face definitions" checkbox is enabled by default in the Paste from Word dialog. 
				config.pasteFromWordKeepsStructure= true; //Whether to keep structure markup (<h1>, <h2>, etc.) or replace it with elements that create more similar pasting results when pasting content from Microsoft Word into the Paste from Word dialog. 
				config.pasteFromWordRemoveStyle= true; //Whether the "Remove styles definitions" checkbox is enabled by default in the Paste from Word dialog. 
				config.resize_enabled= true; //Whether to enable the resizing feature. If disabed the resize handler will not be visible. 
				config.resize_maxHeight= 800; //The maximum editor height, in pixels, when resizing it with the resize handle. 
				config.resize_maxWidth= 750; //The maximum editor width, in pixels, when resizing it with the resize handle. 
				config.resize_minHeight= 300; //The minimum editor height, in pixels, when resizing it with the resize handle. 
				config.skin= (skin || "kama"); // The skin to load. It may be the name of the skin folder inside the editor installation path, or the name and the path separated by a comma.
				config.smiley_descriptions= [
				    ':)', ':(', ';)', ':D', ':/', ':P',
				    '', '', '', '', '', '',
				    '', ';(', '', '', '', '',
				    '', ':kiss', '' ]; //The description to be used for each of the smileys defined in the CKEDITOR.config.smiley_images setting. Each entry in this array list must match its relative pair in the CKEDITOR.config.smiley_images setting. 
				config.smiley_images= [
				    'regular_smile.gif','sad_smile.gif','wink_smile.gif','teeth_smile.gif','confused_smile.gif','tounge_smile.gif',
				    'embaressed_smile.gif','omg_smile.gif','whatchutalkingabout_smile.gif','angry_smile.gif','angel_smile.gif','shades_smile.gif',
				    'devil_smile.gif','cry_smile.gif','lightbulb.gif','thumbs_down.gif','thumbs_up.gif','heart.gif',
				    'broken_heart.gif','kiss.gif','envelope.gif']; //The file names for the smileys to be displayed. These files must be contained inside the URL path defined with the CKEDITOR.config.smiley_path setting. 
				config.smiley_path= '/images/smileys/'; //The base path used to build the URL for the smiley images. It must end with a slash. 
				config.startupFocus= true; //Sets whether the editor should have the focus when the page loads. 
				config.startupMode= 'wysiwyg'; //The mode to load at the editor startup. It depends on the plugins loaded. By default, the "wysiwyg" and "source" modes are available. 
				config.theme= theme; // The theme to be used to build the UI. 
				config.toolbar= group; //The toolbox (alias toolbar) definition. It is a toolbar name or an array of toolbars (strips), each one being also an array, containing a list of UI items. 
				config.toolbar_default=
				[
					['Source','NewPage','Preview','-','Bold','Italic','Underline','-','Find','Replace','-','Cut','Copy','Paste','PasteFromWord'],
				    ['Maximize', 'ShowBlocks'],['Link','Unlink','Anchor'],['Format', 'TextColor'],['NumberedList','BulletedList']
				]; //This is the default toolbar definition used by the editor. It contains all editor features. 
				config.toolbar_administrators=
				[
				    ['Source','-','Save','NewPage','Preview','-','Templates'],
				    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
				    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
				    ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
				    '/',
				    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
				    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
				    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
				    ['Link','Unlink','Anchor'],
				    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
				    '/',
				    ['Styles','Format','Font','FontSize'],
				    ['TextColor','BGColor'],
				    ['Maximize', 'ShowBlocks','-','About']
				]; //This is the default toolbar definition used by the editor. It contains all editor features. 
				config.toolbar_dataentry=
				[
				    ['Source'],
				    ['Cut','Copy','Paste','PasteText','PasteFromWord'],
				    ['Undo','Redo'],
				    '/',
				    ['Bold','Italic','Underline','Strike'],
				    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
				    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
				    ['Link','Unlink','Anchor'],
				    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
				    '/',
				    ['Styles','Format','Font','FontSize'],
				    ['TextColor','BGColor'],
				    ['Maximize', 'ShowBlocks']
				]; //This is the default toolbar definition used by the editor. It contains all editor features. 
				config.toolbar_user=
				[
				    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
				    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
				    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
				    ['Link','Unlink','Anchor'],
				    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
				    '/',
				    ['Styles','Format','Font','FontSize'],
				    ['TextColor','BGColor'],
				]; //This is the default toolbar definition used by the editor. It contains all editor features. 

				config.toolbarCanCollapse= false; //Whether the toolbar can be collapsed by the user. If disabled, the collapser button will not be displayed. 
				config.toolbarLocation= 'top'; //The "theme space" to which rendering the toolbar. For the default theme, the recommended options are "top" and "bottom". 
				config.toolbarStartupExpanded= true; //Whether the toolbar must start expanded when the editor is loaded. 
				config.undoStackSize= 20; //The number of undo steps to be saved. The higher this setting value the more memory is used for it. 
				config.filebrowserBrowseUrl = '{source_path}/ckfinder/ckfinder.html'; 
				config.filebrowserImageBrowseUrl = '{source_path}/ckfinder/ckfinder.html?Type='; 
				config.filebrowserFlashBrowseUrl = '{source_path}/ckfinder/ckfinder.html?Type='; 
				config.filebrowserUploadUrl = '{source_path}/ckfinder/core/connector/php/connector.php?command=QuickUpload&type='; 
				config.filebrowserImageUploadUrl = '{source_path}/ckfinder/core/connector/php/connector.php?command=QuickUpload&type='; 
				config.filebrowserFlashUploadUrl = '{source_path}/ckfinder/core/connector/php/connector.php?command=QuickUpload&type='; 
				config.enterMode = CKEDITOR.ENTER_BR;
				//config.width= '100%'; // The editor width in CSS size format or pixel integer. 
			});

			CKEDITOR.add;
		}
	};
	return that;
})();
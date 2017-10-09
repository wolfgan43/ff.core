/*	based on jQuery Slug Generation Plugin by Perry Trinier (perrytrinier@gmail.com)
  Licensed under the GPL: http://www.gnu.org/copyleft/gpl.html*/

ff.ffField.slug = function(elem, title_field, pre, post) {
	var slugcontent = jQuery(elem).val().toLowerCase(); /* gli slug sono tutti minuscoli, converto a priori*/
	var pre = pre || "";
	var post = post || "";

	slugcontent = slugcontent.replace(/[\xC0-\xC5\xE0-\xE5]/g, 'a'); /* accentate tradotte nella versione normale*/
	slugcontent = slugcontent.replace(/[\xC8-\xCB\xE8-\xEB]/g, 'e');
	slugcontent = slugcontent.replace(/[\xCC-\xCF\xEC-\xEF]/g, 'i');
	slugcontent = slugcontent.replace(/[\xD2-\xD6\xF2-\xF6]/g, 'o');
	slugcontent = slugcontent.replace(/[\xD9-\xDC\xF9-\xFC]/g, 'u');
	slugcontent = slugcontent.replace(/[\x9F\xDD\xFD\xFF]/g, 'y');
	slugcontent = slugcontent.replace(/[\x8E\x9E]/g, 'z');
	slugcontent = slugcontent.replace(/[\x8A\x9A]/g, 's');
	slugcontent = slugcontent.replace(/[\xD1\xF1]/g, 'n');

	slugcontent = slugcontent.replace(/[^a-z0-9]/g, '-'); /* tutti i non-alfanumerici con uno trattino*/
	slugcontent = slugcontent.replace(/\-+/g, '-'); /* più di un trattino con un solo trattino*/
	slugcontent = slugcontent.replace(/^\-/g,''); /* rimuove eventuali trattini iniziali*/
	slugcontent = slugcontent.replace(/\-$/g,''); /* rimuove eventuali trattini finali*/

	if(title_field) {
		if(jQuery("#" + title_field).is("INPUT,SELECT"))
			jQuery("#" + title_field).val(pre + slugcontent + post);
		else
			jQuery("#" + title_field).text(pre + slugcontent + post);
	}
	return pre + slugcontent + post;
};

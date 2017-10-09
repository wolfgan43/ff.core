/**
 * Forms Framework Javascript Handling Object
 *	activecombo fields' plugin namespace
 */

ff.ffField.autocomplete = (function () {

/* private vars*/
var innerURL			= "";
var theme_dir			= "";

var data			= ff.hash();

var sources			= ff.hash();
var actualData      = {};
var cache           = { };
var that = { /* publics*/
__ff : true, /* used to recognize ff'objects*/

"init" : function (params) {
	innerURL			= params.innerURL;
	theme_dir			= params.theme_dir;

},

"addAutocomplete" : function (params) {
	data.set(params.id, params.data);

	that.doEvent({
		"event_name"	: "onAddAutocomplete",
		"event_params"	: [params.id]
	});
},

"split" : function ( val ) {
            return val.split( /,\s*/ );
}, 

"extractLast" : function ( term ) {
    return ff.ffField.autocomplete.split( term ).pop();
}, 

"recalc" : function ( term , control, readOnly) {
    var terms = ff.ffField.autocomplete.split( term );
    var strNewData = '';
    
    terms.each(function(key, value) {
        if(value.length > 0) {
	        if(actualData[control][value.replace(/^\s+|\s+$/g,"")] !== undefined) {
				if(strNewData.length > 0)
					strNewData = strNewData + ",";
	           
	            strNewData = strNewData + actualData[control][value.replace(/^\s+|\s+$/g,"")];
	        } else if(!readOnly) {
				if(strNewData.length > 0)
					strNewData = strNewData + ",";

        		strNewData = strNewData + value;
	        }
		}
    });
    /*
	if(!strNewData.length > 0) {
		if(actualData[term.replace(/^\s+|\s+$/g,"")] !== undefined) {
			strNewData = actualData[term.replace(/^\s+|\s+$/g,"")];
		}
	}*/
    return strNewData ;
}, 
 /* da gestire multi off
  image se possibile
  callback on select 
  da gestire compare bene*/
"loadData": function( prefix, control ) {
    var terms = ff.ffField.autocomplete.split( jQuery("#" + prefix + control).val() );
    var termsID = ff.ffField.autocomplete.split( jQuery("#" + control).val() );
	actualData[control] = [];
    terms.each(function(key, value) {
     	actualData[control][value.replace(/^\s+|\s+$/g,"")] = termsID[key];
    });

    return true;
},
"observe" : function (control, params) {
    var control = control;
    var enableCache = params.cache;
    var prefix = '';
    var enableMulti = params.multi;
    var altInnerUrl = params.service;

    var readOnly = params.readonly;

    //if(params.readonly) {
        var prefix = "autocomplete_";
    //}
                             
    if(enableMulti) {
    	ff.pluginLoad("jquery.fn.autogrow", "/themes/library/plugins/jquery.autogrow-textarea/jquery.autogrow-textarea.js", function() {
    		jQuery("#" + prefix + control).autogrow();
    	});
	}        
                             

	if(prefix) {
		ff.ffField.autocomplete.loadData(prefix, control);
	}
	
	jQuery("#" + prefix + control).parent().find(".actex-combo").click(function() {
		jQuery("#" + prefix + control).focus(); 
		jQuery("#" + prefix + control).autocomplete('search');
	});
	jQuery("#" + prefix + control).parent().find(".actex-search").click(function() {
		var e = jQuery.Event("keydown", { keyCode: 13}); 

		jQuery("#" + prefix + control).trigger(e);		
	});
	jQuery("#" + prefix + control).parent().find(".actex-menu").click(function() {
		jQuery("#" + jQuery(this).attr("rel")).toggleClass("hidden"); 
	});
	
    jQuery("#" + prefix + control)
        .keyup(function(event) {
            var stripChar = unescape(params.stripChar);
           
            if(prefix) {
                jQuery("#" + control).val( ff.ffField.autocomplete.recalc( jQuery(this).val(), control, readOnly ) );
            }
            if(stripChar.length > 0) {
                patt = new RegExp(stripChar);
                jQuery(this).val(jQuery(this).val().replace(patt, ''));
            }
			if(!jQuery(this).val()) {
	            if(!enableMulti) {
	            	jQuery("#suggest_" + control).val("");
	        		if(jQuery("#" + prefix + control).attr("data-placeholder")) {
	        			jQuery("#" + prefix + control).attr("placeholder", jQuery("#" + prefix + control).attr("data-placeholder"));
	        			jQuery("#" + prefix + control).attr("data-placeholder", "");
	        		}
				}
	            jQuery("#" + control).val("");
			} else if(!enableMulti && jQuery("#suggest_" + control).val().indexOf(jQuery("#" + prefix + control).val()) !== 0) {
				jQuery("#suggest_" + control).val("");
				//jQuery("#" + control).val("");
			}  
        })
        .keydown(function(event) {
            if ( event.keyCode === jQuery.ui.keyCode.TAB &&
                    jQuery( this ).data( "autocomplete" ).menu.active ) {
                event.preventDefault();
            }

			if(!enableMulti && !jQuery("#" + prefix + control).val())
        		jQuery("#suggest_" + control).val("");        	
        })
        .blur(function(event) {
        	if(!enableMulti)
            	jQuery("#suggest_" + control).val("");

	        if(jQuery("#" + prefix + control).attr("data-placeholder")) {
	        	jQuery("#" + prefix + control).attr("placeholder", jQuery("#" + prefix + control).attr("data-placeholder"));
	        	jQuery("#" + prefix + control).attr("data-placeholder", "");
	        }
        })  
 		.focus(function(event) {
 			if(!enableMulti && jQuery("#suggest_" + control).val().indexOf(jQuery("#suggest_" + control).nextAll("UL.ui-autocomplete").children("li:first").text()) === 0)
            	jQuery("#suggest_" + control).val(jQuery("#suggest_" + control).nextAll("UL.ui-autocomplete").children("li:first").text());
        })
        .autocomplete({
            disabled: params.disabled,
            minLength : params.minLength,
            delay: params.delay, 
            appendTo: jQuery("#" + prefix + control).parent(),
            messages: {
                noResults: '',
                results: function() {}
            },
            open: function(event, ui) {
                
                /*var p = jQuery(this).offset();*/
                
                jQuery(".ui-autocomplete").css({
                	"height"				: "200px"
                	, "overflow-y"			: "scroll"
                	, "overflow-x"			: "hidden"
                	, "position"			: "absolute"
                	, "top"					: ""
                	, "z-index" 			: 3 
					, "background"			: "#fff"
					, "border"				: "1px solid #ccc"
					, "border-top-color"	: "#d9d9d9"
					, "box-shadow"			: "0 2px 4px rgba(0,0,0,0.2)"
					, "cursor"				: "default"
                });
                
                jQuery(".ui-autocomplete .ui-menu-item").css({
                	"padding"				: "0 10px"
                	, "color"				: "#222"
                });
            },
            source: function( request, response ) {
                var strTerm = '';
                var realInnerUrl = "";
                var fatherValue = jQuery("#" + jQuery("#" + prefix + control).data("father")).val() || '';

                if(enableMulti) {
                    strTerm = ff.ffField.autocomplete.extractLast( request.term ).replace(/^\s+|\s+$/g,"");
                } else {
                    strTerm = request.term.replace(/^\s+|\s+$/g,"");
                }

                if(altInnerUrl)
                	realInnerUrl= altInnerUrl;
                else
                	realInnerUrl = innerURL;

                if(0 && enableCache) {
                    if ( strTerm in cache ) {
                        response( cache[ strTerm ] );
                        return;
                    }
					
                    lastXhr = jQuery.getJSON( realInnerUrl + (realInnerUrl.indexOf("?") >= 0 ? "&" : "?") + "data_src=" + params.data_src + "&compare=" + escape(params.compare) + "&compareh=" + escape(params.compareH) + "&operation=" + escape(params.operation) + "&fv=" + escape(fatherValue), {
                        term: strTerm
                    }, function( data, status, xhr ) {
                        cache[ strTerm ] = data;
                        if ( xhr === lastXhr ) {
                            response( data );
                        }
                    });               
                } else {
                    jQuery.getJSON( realInnerUrl + (realInnerUrl.indexOf("?") >= 0 ? "&" : "?") + "data_src=" + params.data_src + "&compare=" + escape(params.compare) + "&compareh=" + escape(params.compareH) + "&operation=" + params.operation + "&fv=" + escape(fatherValue), {
                        term: strTerm
                    }, response);               
                }
            },
            search: function(event, ui) { 
                if(prefix) {
                    jQuery("#" + control).val( ff.ffField.autocomplete.recalc( this.value, control, readOnly ) );
                }
                if(!enableMulti) {
                	jQuery("#" + prefix + control).attr("data-placeholder", jQuery("#" + prefix + control).attr("placeholder"));
                	jQuery("#" + prefix + control).attr("placeholder", "");
				}
               /* var term = ff.ffField.autocomplete.extractLast( this.value );
                if ( term.length < params.minLength ) {
                    return false;
                }*/
            },
			response: function(event, ui) { 
				if(!enableMulti) {
            		if(ui.content[0] && ui.content[0].label.indexOf(jQuery("#" + prefix + control).val()) === 0) {
		                jQuery("#suggest_" + control).val(ui.content[0].label);
					} else {
				 		jQuery("#suggest_" + control).val("");
					}
				}
               /* var term = ff.ffField.autocomplete.extractLast( this.value );
                if ( term.length < params.minLength ) {
                    return false;
                }*/
            },            
            focus: function(event, ui) {
            	if(!enableMulti) {
					if(ui.item && ui.item.label.indexOf(jQuery("#" + prefix + control).val()) === 0) {
		                jQuery("#suggest_" + control).val(ui.item.label);
					} else {
				 		jQuery("#suggest_" + control).val("");
					}
				}

                $(".ui-helper-hidden-accessible").hide();
                event.preventDefault();

                var terms = '';
                var termsID = '';
                var tmpData = '';

                var tmpLabel = ui.item.label.replace(/^\s+|\s+$/g,"").replace(/,/g, "");
                var tmpValue = ui.item.value.replace(/^\s+|\s+$/g,"");
                /*actualData[tmpLabel] = ui.item.value;*/

                if(enableMulti) {
                    terms = ff.ffField.autocomplete.split( this.value );
                    termsID = ff.ffField.autocomplete.split( jQuery("#" + control).val() );

                    terms.pop();
                    termsID.pop();

                    var tmpData = terms.join( ", " );

                    if(this.value.indexOf(",") == -1 
                        || 
                        (
                            (
                                tmpData.replace(/,\s+/g,",").indexOf(',' + tmpLabel) == -1
                                &&  
                                tmpData.replace(/\s+,/g,",").indexOf(tmpLabel + ',') == -1
                                && 
                                tmpData.replace(/^\s+|\s+$/g,"") != tmpLabel
                            )
                         && this.value.indexOf(",") >= 0 
                        )
                    ) {
                        
                        /* add the selected item*/
                        terms.push( tmpLabel );
                        termsID.push( tmpValue );

                        /* add placeholder to get the comma-and-space at the end
                        terms.push( "" );
                        termsID.push( "" );*/
                        if(prefix) {
                            this.value = terms.join( ", " );

                            if(this.value.indexOf(",") == -1) { 
                                jQuery(this).selection(tmpData.length, tmpData.length + tmpLabel.length);
                            } else {
                                jQuery(this).selection(tmpData.length + 2, tmpData.length + 2 + tmpLabel.length);
                            }
                        } else {
                            this.value = termsID.join( ", " );

                            if(this.value.indexOf(",") == -1) { 
                                jQuery(this).selection(tmpData.length, tmpData.length + tmpValue.length);
                            } else {
                                jQuery(this).selection(tmpData.length + 2, tmpData.length + 2 + tmpValue.length);
                            }
                        }
                    } else {
                        if(prefix) {  
                            jQuery(this).selection(this.value.indexOf(tmpLabel), this.value.indexOf(tmpLabel) + tmpLabel.length);
                        } else {
                            jQuery(this).selection(this.value.indexOf(tmpValue), this.value.indexOf(tmpValue) + tmpValue.length);
                        }
                    }
                } else {
                	return false;
                	if(prefix) {
                    	this.value = tmpLabel;
                        
                        jQuery(this).selection(this.value.indexOf(tmpLabel), this.value.indexOf(tmpLabel) + tmpLabel.length);
                    } else {
                    	this.value = tmpValue;
                        
                        jQuery(this).selection(this.value.indexOf(tmpValue), this.value.indexOf(tmpValue) + tmpValue.length);
                    }
/*                    terms = this.value;
                    termsID = jQuery("#" + control).val();*/
                    
                }
                /* remove the current input*/
                return false;
                
                /* prevent value inserted on focus
                return false;*/
            },
            select: function( event, ui ) {
                var terms = '';
                var termsID = '';
                var tmpData = '';

                var tmpLabel = ui.item.label.replace(/^\s+|\s+$/g,"").replace(/,/g, "");
                var tmpValue = ui.item.value.replace(/^\s+|\s+$/g,"");

                actualData[control][tmpLabel] = tmpValue;

                if(enableMulti) {
                    terms = ff.ffField.autocomplete.split( this.value );
                    termsID = ff.ffField.autocomplete.split( jQuery("#" + control).val() );

                    terms.pop();
                    termsID.pop();
                    
                    tmpData = terms.join( ", " );
                    
                    /* remove the current input*/
                    if(this.value.indexOf(",") == -1 
                        || 
                        (
                            (
                                tmpData.replace(/,\s+/g,",").indexOf(',' + tmpLabel) == -1
                                &&  
                                tmpData.replace(/\s+,/g,",").indexOf(tmpLabel + ',') == -1
                                && 
                                tmpData.replace(/^\s+|\s+$/g,"") != tmpLabel
                            )
                         && this.value.indexOf(",") >= 0 
                        )
                    ) {

                        /* add the selected item*/
                        terms.push( tmpLabel );
                        termsID.push( tmpValue );

                        /* add placeholder to get the comma-and-space at the end*/
                        if(prefix) {
                            terms.push( "" );
                        
                            this.value = terms.join( ", " );
                        } else {
                            termsID.push( "" );
                        
                            this.value = termsID.join( ", " );
                        }
                    }  

                } else {
                   /* terms = this.value;
                    termsID = jQuery("#" + control).val();
                    
                    tmpData = terms;  */
                    if(prefix)
                    	this.value = tmpLabel;
                    else 
                    	this.value = tmpValue;
                }


                if(prefix) {
                    jQuery("#" + control).val( ff.ffField.autocomplete.recalc( this.value, control, readOnly ) );
                }
				//jQuery("#suggest_" + control).val(this.value);

//				var e = $.Event("keydown", { keyCode: 13}); //"keydown" if that's what you're doing
//				$("#" + prefix + control).trigger(e);			    

				jQuery("#" + prefix + control).change();

                return false;
            }
        })
        .data("ui-autocomplete")._renderItem = function (ul, item) {
         return $("<li></li>")
             .data("item.autocomplete", item)
             .append("<a>" + (item.image ? item.image : "") + item.label + "</a>")
             .appendTo(ul);
     };

    
    
}


}; /* publics' end*/

/*if(ff.addEvent !== undefined)
	ff.addEvent({"event_name" : "onClearField", "func_name" : that.onClearField});

if(ff.ajax.addEvent !== undefined)
	ff.ajax.addEvent({"event_name" : "onUpdateField", "func_name" : that.onUpdateField});
*/
return that;

/* code's end.*/
})();


$.fn.selection = function(start, end) {
    if (start !== undefined) {
        return this.each(function() {
            if( this.createTextRange ){
                var selRange = this.createTextRange();
                if (end === undefined || start == end) {
                    selRange.move("character", start);
                    selRange.select();
                } else {
                    selRange.collapse(true);
                    selRange.moveStart("character", start);
                    selRange.moveEnd("character", end);
                    selRange.select();
                }
            } else if( this.setSelectionRange ){
                this.setSelectionRange(start, end);
            } else if( this.selectionStart ){
                this.selectionStart = start;
                this.selectionEnd = end;
            }
        });
    }
    var field = this[0];
    if ( field.createTextRange ) {
        var range = document.selection.createRange(),
            orig = field.value,
            teststring = "<->",
            textLength = range.text.length;
        range.text = teststring;
        var caretAt = field.value.indexOf(teststring);
        field.value = orig;
        this.selection(caretAt, caretAt + textLength);
        return {
            start: caretAt,
            end: caretAt + textLength
        }
    } else if( field.selectionStart !== undefined ){
        return {
            start: field.selectionStart,
            end: field.selectionEnd
        }
    }
};
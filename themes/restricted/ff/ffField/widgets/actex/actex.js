/**
 * Forms Framework Javascript Handling Object
 *	activecombo fields' plugin namespace
 **/

ff.ffField.actex = (function () {

// jQuery Override (patch)
ff.pluginAddInit("jquery-ui", function () {
	jQuery.widget("ui.menu", jQuery.ui.menu, {
		blur: function( event, fromFocus ) {
			if (this.element.hasClass("ui-autocomplete")) {
				if ((event === undefined || fromFocus === undefined) || (event && (event.type === "click" || event.type === "mouseout"))) {
					if ( !fromFocus ) {
						clearTimeout( this.timer );
					}

					if ( !this.active ) {
						return;
					}

					this.active = null;
					return;
				} else {
					return this._super(event, fromFocus);
				}
			}
		}
		, _create : function () {
			if (this.element.hasClass("ui-autocomplete")) {
				var mnu = this.element;
				this.element.scroll(function () {
					var itm_height = mnu.children(".ui-menu-item:first").height();
					var ori = mnu.scrollTop();
					mnu.scrollTop(parseInt(mnu.scrollTop() / itm_height) * itm_height);
				});
				
				var tmp_dlg = mnu.closest(".ui-dialog");
				if (tmp_dlg.length) {
					jQuery(".ui-dialog-content", tmp_dlg).scroll(function () {
						mnu.hide();
					});
				} else {
					jQuery(window).scroll(function () {
						mnu.hide();
					});
				}
			}
			this._super();
		}
		, _move : function (direction, filter, event) {
			if (this.element.hasClass("ui-autocomplete")) {
				if (!this.active && jQuery(".ui-menu-item .ui-state-focus", this.activeMenu).length) {
					this.active = jQuery(".ui-menu-item .ui-state-focus", this.activeMenu).parent();
				}
			}				
			var tmp =  this._super(direction, filter, event);
			var itm = jQuery(this.element.find(".ui-state-focus")[0]);
			if (itm.position().top < 0) {
				this.element.scrollTop(this.element.scrollTop() + itm.position().top);
			} else if (itm.position().top === this.element.height()) {
				this.element.scrollTop(this.element.scrollTop() + itm.height());
			} else if (itm.position().top > this.element.height()) {
				this.element.scrollTop(this.element.scrollTop() + itm.position().top - (itm.height() * 4));
			}
			return tmp;
		}
		, first: function( event ) {
			this._move( "first", "first", event );
		}

		, last: function( event ) {
			this._move( "last", "last", event );
		}
		, focus: function ( event, item ) {
			if (this.element.hasClass("ui-autocomplete")) {
				this.activeMenu.find( "a" ).removeClass( "ui-state-focus" );
			}
			return this._super( event, item );
		}
		, _scrollIntoView: function( event, item ) {
			if (this.element.hasClass("ui-autocomplete")) {
				return;
			} else {
				return this._super( event, item );
			}
		}
	});
	jQuery.widget("ui.autocomplete", jQuery.ui.autocomplete, {
		_initSource: function() {
			var array, url,
				that = this;
			if ( $.isArray(this.options.source) ) {
				array = this.options.source;
				this.source = function( request, response ) {
					response( $.ui.autocomplete.filter( array, request.term ), request.term );
				};
			} else if ( typeof this.options.source === "string" ) {
				url = this.options.source;
				this.source = function( request, response ) {
					if ( that.xhr ) {
						that.xhr.abort();
					}
					that.xhr = $.ajax({
						url: url,
						data: request,
						dataType: "json",
						success: function( data ) {
							response( data, request.term );
						},
						error: function() {
							response( [], request.term );
						}
					});
				};
			} else {
				this.source = this.options.source;
			}
		}

		, search: function( value, event ) {
			value = value != null ? value : this._value();

			if (value.toLowerCase() === this.old_term) {
				this.menu.element.show();
				this.menu.element.menu("widget").position({
					"my" : "left top"
					, "at" : "left bottom"
					, "of" : "#" + this.element[0].id
				});

				if (this.menu.element.find(".autocomp_selected").length) {
					this.menu.element.find( "a" ).removeClass( "ui-state-focus" );
					this.menu.element.find(".autocomp_selected").addClass("ui-state-focus");
					var itm = this.menu.element.find(".autocomp_selected:first");
					this.menu.element[0].scrollTop = this.menu.element[0].scrollTop + itm.position().top - (itm.height() * 4);
				}

				if ( this.options.autoFocus ) {
					this.menu.next();
				}
				return;
			}

			// always save the actual value, not the one passed as an argument
			this.term = this._value();

			if ( value.length < this.options.minLength ) {
				return this.close( event );
			}

			if ( this._trigger( "search", event ) === false ) {
				return;
			}

			return this._search( value );
		}

		, _response: function() {
			var index = ++this.requestIndex;

			return $.proxy(function( content, term) {
				if ( index === this.requestIndex ) {
					this.__response( content, term );
				}

				this.pending--;
				if ( !this.pending ) {
					this.element.removeClass( "ui-autocomplete-loading" );
				}
			}, this );
		}

		, __response: function( content, term ) {
			this.old_term = term.toLowerCase();
			if ( content ) {
				content = this._normalize( content );
			}
			this._trigger( "response", null, { content: content } );
			if ( !this.options.disabled && content && content.length && !this.cancelSearch ) {
				this._suggest( content );
				this._trigger( "open" );
			} else {
				// use ._close() instead of .close() so we don't cancel future searches
				this._close();
			}
		}

		, _move: function( direction, event ) {
			if ( !this.menu.element.is( ":visible" ) ) {
				this.search( null, event );
				return;
			}

			if ( this.menu.isFirstItem() && /^previous/.test( direction ) )
					direction = "last";
			else if (this.menu.isLastItem() && /^next/.test( direction ) )
					direction = "first";
			this.menu[ direction ]( event );
		}
	});
}, "abc077f0-f5ff-11e3-a3ac-0800200c9a66");

/* privates */
var innerURL			= "";
var theme_dir			= "";
var loading_markup		= "";

var instances			= ff.hash();

var sources				= ff.hash();
var controls_waiting	= ff.hash();

var initialized = false;

function normalizeVal(value) {
	if (value === null || value === undefined)
		return value;
	if (typeof value === "number")
		value = value.toString();
	if (typeof value !== "string")
		throw  "ff.ffField.actex - Invalid value";
	
	return value;
}

var activecombo = function(params) {
	// inits
	
	/* privates */
	var __id = params.id;
	var component = params.component;
	
	var filled = false;
	var first_fill = true;
	var displayed_value = undefined;
	var old_father_value = undefined;
	var buttons = ff.hash();
	buttons.set("add", true);
	buttons.set("delete", true);
	buttons.set("edit", true);
	
	/* privates' end*/
	
	var that = { /* publics */
		__ff : true /* used to recognize ff'objects*/
		, "father"					: params.father
		, "childs"					: params.childs
		, "data"					: params.data || []
		, "value"					: normalizeVal(params.selected_value)
		/*, "extra"					: params.extra_value || null*/
		, "options" 				: params.options
		, "insert_mode"				: false
		, "has_focus"				: false
		, "loading_markup"			: undefined
		
		, "getID" : function () {
			return __id;
		}

		, "getNode"  : function () {
			return jQuery.fn.escapeGet("actex_" + __id).get(0);
		}
		, "getService" : function () {
			var tmp;
			if (that.options.service !== null)
				tmp = that.options.service;
			else
				tmp = innerURL;
			
			if (component === "" || component === undefined)
				return tmp;
			
			var srv_uri_parts = tmp.parseUri();
			if (srv_uri_parts.host !== "")
				return tmp;
			
			var cmp_uri_parts = ff.struct.get("comps").get(component).url.parseUri();
			if (cmp_uri_parts.host === "")
				return tmp;

            var port = (cmp_uri_parts.port && cmp_uri_parts.port != 80
                    ? ":" + cmp_uri_parts.port
                    : ""
            );

            return cmp_uri_parts.protocol + "://" + cmp_uri_parts.host + port + "/" + tmp.ltrim("/");
		}

		, "getCacheDataSrc" : function () {
			return (that.options.data_src ? that.options.data_src : (
						that.options.service ? that.options.service : (
							innerURL ? innerURL : __id
						)
					));
		}
		
		, "setValue" : function (value) {
			that.value = normalizeVal(value);
		}
		
		, "getFather" : function() {
			if (that.father)
				return ff.ffField.actex.getInstance(that.father);
			else
				return undefined;
		}
		
		, "getFatherValue" : function() {
			if (that.father)
				return ff.ffField.actex.getInstance(that.father).value;
			else
				return null;
		}
		
		, "getOldFatherValue" : function() {
			if (that.father)
				return old_father_value;
			else
				return undefined;
		}
		
		, "isFilled"  : function () {
			return filled;
		}
		
		, "buttonToggle" : function(button, status) {
			if (status !== undefined) {
				buttons.set(button, status);
			} else {
				buttons.set(button, !buttons.get(button));
			}
		}
		
		, "change" : function (reset_childs, value) {
			value = normalizeVal(value);
			var old_value = that.value;
			
			if (value === undefined)
				that.value = jQuery.fn.escapeGet(__id).val();
			else
				that.value = value;
			
			if (that.options.autocomplete.enable)
				jQuery.fn.escapeGet(__id).val(that.value);

			/*that.extra = null;
			if (sources.isset(__id)) {
				var tmp_el = sources.get(__id)[jQuery.fn.comboGetIndexByVal(__id, that.value) - 1];
				if (tmp_el !== undefined) {
					that.extra = tmp_el.extra;
				}
			}*/

			var res = that.doEvent({
				"event_name"	: "change",
				"event_params"	: [that, old_value]
			});
		
			if (res !== undefined && res[res.length - 1])
				return;

			res = ff.ffField.actex.doEvent({
				"event_name"	: "change",
				"event_params"	: [that, old_value]
			});
	
			if (res !== undefined && res[res.length - 1])
				return res[res.length - 1];

			updatebt();

			if (that.childs.length) {
				var rc = true;
				that.childs.each(function (a, child) {
					rc &= ff.ffField.actex.getInstance(child).refill(reset_childs ? null : undefined);
				});
				return rc;
			} else {
				return true
			}
		}
		
		, "resetChildsPreRedir" : function (value) {
			if (that.childs.length) {
				that.childs.each(function (a, child) {
					var tmp = ff.ffField.actex.getInstance(child);
					tmp.value = (value === undefined ? null : value);
					jQuery.fn.escapeGet(child).val((value === undefined ? "" : value));
					tmp.resetChildsPreRedir(value);
				});
			}
		}

		, "update" : function (new_value, reset_childs) {
			new_value = normalizeVal(new_value);
			reset_childs = reset_childs === undefined ? that.options.reset_childs : reset_childs;
			
			var old_value = (displayed_value === undefined ? that.value : displayed_value);
			if (new_value === undefined)
				new_value = that.value;
			
			// visual update
			if (displayed_value === undefined || old_value !== new_value) {
				if (!that.options.autocomplete.enable) {
					var old_index = jQuery.fn.comboGetSelIndex(__id);
					var new_index = jQuery.fn.comboGetIndexByVal(__id, new_value);

					if (new_index < 0) {
						new_value = null;
						new_index = 0;
					}

					if (new_index !== old_index) {
						jQuery.fn.escapeGet(__id).val(new_value);
					}
				} else {
					var found_value = false;
					if (that.options.select_noone && that.options.select_noone_val === new_value) {
						jQuery.fn.escapeGet(__id + "_label").val(that.options.select_noone_label);
						found_value = true;
					} else {
						var father_value = that.getFatherValue();
						that.data.each(function (a, tmp_data){
							if ((father_value === null && that.father === null) || tmp_data[0] === father_value) {
								var opt_value = tmp_data[1];
								var opt_text = tmp_data[2];
								if (opt_value === new_value) {
									jQuery.fn.escapeGet(__id + "_label").val(opt_text);
									found_value = true;
									return true;
								}
							}
						});
					}

					if (!found_value) {
						new_value = null;
					} else {
						$this = jQuery.fn.escapeGet(__id + "_label");
						$widget = $this.autocomplete("widget");
						$menu = $widget.menu();
						$menu.find( "a" ).removeClass( "ui-state-focus" ).removeClass( "autocomp_selected" );
						
						var itm_found = false;
						$menu.find(".ui-menu-item").each(function(idx, item) {
							if(jQuery(item).data("ui-autocomplete-item").id == new_value) {
								itm_found = item;
								return false;
							}
						});

						if (itm_found !== false) {
							$widget.menu("focus", null, jQuery(itm_found));
							jQuery(itm_found).find("a.ui-state-focus").addClass("autocomp_selected");
						}
					}
				}
			}

			that.value = new_value;
			displayed_value = new_value;
			old_father_value = that.getFatherValue();

			if (new_value !== old_value) {
				if (that.options.autocomplete.enable)
					jQuery.fn.escapeGet(__id).val(that.value);

				var res = that.doEvent({
					"event_name"	: "change",
					"event_params"	: [that, old_value]
				});

				if (res !== undefined && res[res.length - 1])
					return;

				res = ff.ffField.actex.doEvent({
					"event_name"	: "change",
					"event_params"	: [that, old_value]
				});

				if (res !== undefined && res[res.length - 1])
					return;
			}
			
			updatebt();
			
			if (first_fill) {
				first_fill = false;
				ff.doEvent({
					"event_name" : "initIFElement"
					, "event_params" : [__id, "actex"]
				});
			}
			
			if (that.childs.length) {
				that.childs.each(function (a, child) {
					child = ff.ffField.actex.getInstance(child);
					if (!child.isFilled() || reset_childs || child.getOldFatherValue() !== new_value)
						child.refill(reset_childs ? null : undefined);
					else {
						var node = child.getNode();
						if (node && node.firstChild)
							node.firstChild.disabled = false;
						child.update();
					}
				});
			}
		}

		, "recalc" : function (countCheck, separator) {
			var hidden = jQuery.fn.escapeGet(__id).get(0);
			var tmp = "";
			var element = null;

			for (i = 0; i < countCheck; i++) {
				element = jQuery.fn.escapeGet(__id + "_" + i).get(0);
				if (element.checked) {
					if (tmp.length)
						tmp = tmp + separator;
					tmp = tmp + element.value;
				}
			}
			hidden.value = tmp;
		}

		, "refill" : function (new_value, father_value, force_refresh) {
			var node = that.getNode();
			new_value = normalizeVal(new_value);
			father_value = normalizeVal(father_value);

			if (new_value === undefined)
				new_value = that.value;

			if (father_value === undefined)
				father_value = that.getFatherValue();
			
			displayed_value = undefined;

			/* like activecombo, preloaded in page*/
			if (that.options.data_src == "" && that.options.service === null) {
				var found_value = undefined;
				var buffer = parseheader(new_value);

				if(that.options.control_type == "input") {
					found_value = false;
					that.data.each(function (a, tmp_data){
						if ((father_value == null && that.father == null) || tmp_data[0] == father_value) {
							if (tmp_data[0] == new_value)
								found_value = true;

							if (
									!that.options.limit_select
									|| (that.options.limit_select && tmp_data[0] == new_value)
								) {
								buffer += ' value="' + tmp_data[0] + '" ';
								return true;
							}
						}
					});
					buffer += ' />';
					node.innerHTML = buffer;
				} else if(that.options.control_type == "label") {
					found_value = false;
					that.data.each(function (a, tmp_data){
						if ((father_value == null && that.father == null) || tmp_data[0] == father_value) {
							if (tmp_data[0] == new_value)
								found_value = true;

							if (
									!that.options.limit_select
									|| (that.options.limit_select && tmp_data[1] == new_value)
								) {
								buffer += tmp_data[1];
								return true;
							}
						}
					});
					buffer += '</span>';
					node.innerHTML = buffer;
				} else if(that.options.control_type == "checkbox") {
					found_value = false;
					var separator = that.options.separator;
					if(!separator.length)
						separator = ",";

					var arrSelectedValue = new_value.split(separator);
					that.data.each(function (a, tmp_data){
						if ((father_value == null && that.father == null) || tmp_data[0] == father_value) {
							if (tmp_data[1] == new_value)
								found_value = true;

							if (
									!that.options.limit_select
									|| (that.options.limit_select && tmp_data[1] == new_value)
								) {
								buffer += '<div class="row"><input type ="checkbox" id="' + __id + '_' + a + '" value="' + tmp_data[1] + '" ';
								for (var x = 0; x < arrSelectedValue.length; x++) {
									if (tmp_data[1] == arrSelectedValue[x])
										buffer += 'checked="checked" ';
								}

								buffer += ' onChange="';
								buffer += 'ff.ffField.actex.recalc(\'' + __id + '\',\'' + that.data.length + '\',\'' + separator + '\');" ';
								buffer += ' /><label>' + tmp_data[2] + '</label></div>';
							}
						}
					});
					buffer += '</div>';
					node.innerHTML = buffer; 
				} else if (that.options.autocomplete.enable) {
					jQuery.fn.escapeGet(__id + "_label").autocomplete("destroy");
					buffer = parseheader(new_value);
					
					var tmp_autocomplete = [];

					/*if (that.options.select_one) {
						tmp_autocomplete.push({"label" : that.options.select_one_label, "id" : that.options.select_one_val});
						if (that.options.select_one_val == new_value) {
							found_value = true;
							buffer += ' value="' + that.options.select_one_label + '" ';
						}
					}*/
					if (that.options.select_noone) {
						tmp_autocomplete.push({"label" : that.options.select_noone_label, "id" : that.options.select_noone_val});
					}

					that.data.each(function (a, tmp_data){
						if ((father_value == null && that.father == null) || tmp_data[0] == father_value) {
							var opt_value = tmp_data[1];
							var opt_text = tmp_data[2];
							if (!that.options.limit_select || (that.options.limit_select && opt_value == new_value)) {
								tmp_autocomplete.push({"label" : opt_text, "id" : opt_value});
							}
						}
					});

					buffer += ' />';
					node.innerHTML = buffer;

					autocomplete(tmp_autocomplete, father_value);
				} else {
					that.data.each(function (a, tmp_data){
						if ((father_value == null && that.father == null) || tmp_data[0] == father_value) {
							if (
									!that.options.limit_select
									|| (that.options.limit_select && tmp_data[1] == new_value)
								) {
								buffer += '<option value="' + tmp_data[1] + '">' + tmp_data[2] + '</option>';
							}
						}
					});
					buffer += '</select>';
					if(that.options.addPlus) {
						buffer += '<a class="qta-piu" href="javascript:void(0);" onClick="jQuery(this).prev(\'select\').children(\'option:selected\').val(parseInt(jQuery(this).prev(\'select\').children(\'option:selected\').val()) + 1); jQuery(this).prev(\'select\').children(\'option:selected\').text(jQuery(this).prev(\'select\').children(\'option:selected\').val());">+</a><a class="qta-meno" href="javascript:void(0);" onClick="if(parseInt(jQuery(this).prev().prev(\'select\').children(\'option:selected\').val()) >= 2) { jQuery(this).prev().prev(\'select\').children(\'option:selected\').val(parseInt(jQuery(this).prev().prev(\'select\').children(\'option:selected\').val()) - 1); jQuery(this).prev().prev(\'select\').children(\'option:selected\').text(jQuery(this).prev().prev(\'select\').children(\'option:selected\').val()); }">-</a>';
					}
					node.innerHTML = buffer; 
				}
				
				filled = true;

				if(that.options.hideEmpty && !that.data.length) {
					if(that.options.hideEmpty === true)
						jQuery(node).hide();
					else 
						jQuery(node).closest("." + that.options.hideEmpty).hide();
				}

				if (found_value === false)
					new_value = null;
				else if (found_value === true)
					displayed_value = new_value;

				that.update(new_value);

				that.doEvent({
					"event_name"	: "refill",
					"event_params"	: [node]
				});
				
				ff.ffField.actex.doEvent({
					"event_name"	: "refill",
					"event_params"	: [__id, node]
				});
				
				return true;
			} else {
				/* async loading*/
				cascade_disable(true);  /* disable fathers*/
				cascade_disable(false); /* disable childs*/
				display_loading();

				var str_data = "father_value=" + escape(father_value);
				if (that.options.data_src)
					str_data += "&data_src=" + escape(that.options.data_src);
				if (new_value)
					str_data += "&sel_val=" + escape(new_value);

				/* get ancestor's data */
				var ancest_data = "";
				var tmp_father = that.getFather()
				while (tmp_father) {
					ancest_data = "&ffActex_parent_data[" + ff.doubleEncodeURIComponent(tmp_father.options.name) + "]=" + ff.encodeURIComponent(tmp_father.value) + ancest_data;
					/*if (tmp_father.options.extra.length && tmp_father.extra) {
						tmp_father.options.extra.each(function(i, v) {
							ancest_data = "&ffActex_parent_extra[" + ff.doubleEncodeURIComponent(tmp_father.options.name) + "][" + ff.doubleEncodeURIComponent(v) + "]=" + ff.doubleEncodeURIComponent(tmp_father.extra[v]) + ancest_data;
						});
					}*/
					tmp_father = tmp_father.getFather();
				}
				str_data = str_data + ancest_data;

				delayed_request(str_data, new_value, father_value, force_refresh);
				return false;
			}
		}

		, "child_error_display" : function () {
			var node = that.getNode();
			if (node) {
				node.innerHTML = 'Impossibile connettersi con il server, riprovare più tardi.';
				if(!that.options.hideEmpty || that.options.hideEmpty === true)
					jQuery(node).show();
				else 
					jQuery(node).closest("." + that.options.hideEmpty).show();


				if (that.childs.length) {
					that.childs.each(function (a, child) {
						ff.ffField.actex.getInstance(child).child_error_display();
					});
				}
			}
		}

		, "async_refill" : function (retData, selected_value, father_value) {
			var node = that.getNode();
			var buffer = "";

			if (retData === null) {
				that.child_error_display();
				return;
			}

			buffer = parseheader(selected_value);

			var opt_value = "";
			var opt_text = "";
			var found_value = undefined;

			if(that.options.control_type == "input") {
				found_value = false;
				for (var i = 0; i < retData.length; i++) {
					opt_value = retData[i].value;
					opt_text = retData[i].desc;
					found_value |= (opt_value == selected_value);
					if (!that.options.limit_select || (that.options.limit_select && opt_value == selected_value)) {
						buffer += ' value="' + opt_value + '" ';
						break;
					}
				}
				buffer += ' />';
				node.innerHTML = buffer;
			} else if(that.options.control_type == "label") {
				found_value = false;
				for (var i = 0; i < retData.length; i++) {
					opt_value = retData[i].value;
					opt_text = retData[i].desc;
					found_value |= (opt_value == selected_value);
					if (!that.options.limit_select || (that.options.limit_select && opt_value == selected_value)) {
						buffer += opt_text;
						break;
					}
				}
				buffer += '</span>';
				node.innerHTML = buffer;
			} else if(that.options.control_type == "checkbox") {
				found_value = false;
				var separator = that.options.separator;
				if(!(separator.length))
					separator = ",";

				var arrSelectedValue = ff.coalesce(selected_value, '').split(separator);

				for (var i = 0; i < retData.length; i++) {
					opt_value = retData[i].value;
					opt_text = retData[i].desc;
					found_value |= (opt_value == selected_value);
					if (
							!that.options.limit_select
							|| (that.options.limit_select && opt_value == selected_value)
						) {
						buffer += '<div class="row"><input type ="checkbox" id="' + __id + '_' + i + '" value="' + opt_value  + '" ';
						for (var x = 0; x < arrSelectedValue.length; x++) {
							if (opt_value == arrSelectedValue[x])
								buffer += 'checked="checked" ';
						}

						buffer += ' onChange="';
						buffer += 'ff.ffField.actex.recalc(\'' + __id + '\',\'' + retData.length + '\',\'' + separator + '\');" ';
						buffer += ' /><label>' + opt_text + '</label></div>';
					}
				}
				buffer += '</div>'; 
				node.innerHTML = buffer;
			} else if (that.options.autocomplete.enable) {
				var tmp_autocomplete = [];
				that.data = [];
				
				/*if (that.options.select_one) {
					tmp_autocomplete.push({"label" : that.options.select_one_label, "id" : that.options.select_one_val});
					if (that.options.select_one_val == selected_value) {
						found_value = true;
						buffer += ' value="' + that.options.select_one_label + '" ';
					}
				}*/
				if (that.options.select_noone) {
					tmp_autocomplete.push({"label" : that.options.select_noone_label, "id" : that.options.select_noone_val});
				}
				
				for (var i = 0; i < retData.length; i++) {
					opt_value = retData[i].value;
					opt_text = retData[i].desc;
					if (!that.options.limit_select || (that.options.limit_select && opt_value == selected_value)) {
						tmp_autocomplete.push({"label" : opt_text, "id" : opt_value});
						that.data.push([father_value, opt_value, opt_text]);
					}
				}
				buffer += ' />';
				node.innerHTML = buffer;
				
				autocomplete(tmp_autocomplete, father_value);
			} else {
				var bufferAttr = "";
				var bufferGroup = ff.hash();
				var bufferOption = "";

				for (var i = 0; i < retData.length; i++) {
					if (!that.options.limit_select || (that.options.limit_select && retData[i].value == selected_value)) {
						bufferAttr = "";
						if(retData[i].attr) {
							for(var x in retData[i].attr) {
								bufferAttr += " " + x + "=" + '"' + retData[i].attr[x] + '" ';
							}
						}

						bufferOption = '<option '
							+ bufferAttr
							+ 'value="' + retData[i].value + '" ' 
							+ '>' + retData[i].desc + '</option>';
//							+ 'data-actex-idx="' + i + '" '

						if(retData[i]["group"]) {
							var tmp = (bufferGroup.get(retData[i]["group"]) === undefined ? "" : bufferGroup.get(retData[i]["group"])) + bufferOption;
							bufferGroup.set(retData[i]["group"], tmp);
						} else {
							buffer += bufferOption;
						}
					}
				}

				bufferGroup.each(function (key, value) {
					buffer += '<optgroup label="' + key + '">' + value + '</optgroup>';	
				});

				buffer += '</select>';
				if(that.options.addPlus) {
					buffer += '<a class="qta-piu" href="javascript:void(0);" onClick="jQuery(this).prev(\'select\').children(\'option:selected\').val(parseInt(jQuery(this).prev(\'select\').children(\'option:selected\').val()) + 1); jQuery(this).prev(\'select\').children(\'option:selected\').text(jQuery(this).prev(\'select\').children(\'option:selected\').val());">+</a><a class="qta-meno" href="javascript:void(0);" onClick="if(parseInt(jQuery(this).prev().prev(\'select\').children(\'option:selected\').val()) >= 2) { jQuery(this).prev().prev(\'select\').children(\'option:selected\').val(parseInt(jQuery(this).prev().prev(\'select\').children(\'option:selected\').val()) - 1); jQuery(this).prev().prev(\'select\').children(\'option:selected\').text(jQuery(this).prev().prev(\'select\').children(\'option:selected\').val()); }">-</a>';
				}
				node.innerHTML = buffer;
			}

			filled = true;

			if(that.options.hideEmpty && !retData.length) {
				if(that.options.hideEmpty === true)
					jQuery(node).hide();
				else 
					jQuery(node).closest("." + that.options.hideEmpty).hide();
			}
			
			if (found_value === false)
				selected_value = null;
			else if (found_value === true)
				displayed_value = selected_value;

			that.update(selected_value);

			parent_enable();

			ff.ajax.unblockUI();

			that.doEvent({
				"event_name"	: "refill",
				"event_params"	: [node]
			});
				
			ff.ffField.actex.doEvent({
				"event_name"	: "refill",
				"event_params"	: [__id, node]
			});
			
			/*if (ff.ffField.actex.clickTarget && ff.ffField.actex.clickTarget.id === __id) {
				console.log(jQuery("#" + __id).get(0));
				jQuery("#" + __id).click();
			}*/
	}

	}; /* public's end */

	function delayed_request (str_data, selected_value, father_value, force_refresh) {
		ff.ajax.blockUI();

		if(that.options.use_cache && !force_refresh) {
			var id_data_src = that.getCacheDataSrc();

			if (sources.isset(id_data_src) && sources.get(id_data_src).isset(father_value)) {
				if (sources.get(id_data_src).get(father_value) === true) {
					if (!controls_waiting.isset(id_data_src))
						controls_waiting.set(id_data_src, ff.hash());
					controls_waiting.get(id_data_src).set(ff.getUniqueID(), {"id" : __id, "selected_value" : selected_value, "father_value" : father_value});
					return;
				}

				that.async_refill(sources.get(id_data_src).get(father_value), selected_value);
				return;
			}

			if (!sources.isset(id_data_src))
				sources.set(id_data_src, ff.hash());

			sources.get(id_data_src).set(father_value, true);
		/*} else { // extra
			sources.set(__id, null);*/
		}
		
		var mydata = {
			"selected_value" : selected_value
			, "father_value" : father_value
		};

		var url = ff.fixPath(that.getService());
		var dataType = (ff.httpGetOrigin() !== ff.httpGetOrigin(url) ? "jsonp json" : "json");
		
		jQuery.ajax({
			  url		: url
			, async		: true
			, data		: str_data
			, type		: "GET"
			, dataType	: dataType
			, jsonp		: (dataType == "jsonp json" ? "XHR_JSONP" : undefined)
			, success	: async_success
			, error		: async_error
			, mydata	: mydata
		});
	};
	
	function async_error() {
		that.child_error_display();
		ff.ajax.unblockUI();
	};

	function async_success (retData, textStatus) {
		var selected_value	= this.mydata.selected_value;
		var id_data_src = that.getCacheDataSrc();
		
		if (!(retData.widget && retData.widget.actex && retData.widget.actex["D" + id_data_src]))
			return async_error();
			
		var father_value = that.getFatherValue();
		retData = father_value ? retData.widget.actex["D" + id_data_src]["F" + father_value] : retData.widget.actex["D" + id_data_src];
		//sources.set(that.getID(), retData); // extra

		that.async_refill(retData, selected_value, father_value);

		if (that.options.use_cache) {
			sources.get(id_data_src).set(father_value, retData);
			if (controls_waiting.isset(id_data_src)) controls_waiting.get(id_data_src).each(function (k, v) {
				var tmp = ff.ffField.actex.getInstance(v.id);
				if (v.father_value === father_value) {
					tmp.async_refill(retData, v.selected_value, v.father_value);
					controls_waiting.get(id_data_src).unset(k);
				}
			});
	   }
	};

	function parent_enable (inst) {
		if (inst === undefined)
			inst = that;
		
		if (inst.has_focus) {
			if (!inst.options.autocomplete.enable)
				jQuery.fn.escapeGet(inst.getID()).focus();
			else
				jQuery.fn.escapeGet(inst.getID() + "_label").focus();

			inst.has_focus = false;
		}
		
		if (inst.father) {
			var father = inst.getFather();
			var node = father.getNode();
			if (node && node.firstChild && node.firstChild.disabled)
			{
				node.firstChild.disabled = false;
				var res = father.doEvent({
					"event_name"	: "enable",
					"event_params"	: [father]
				});
				
				var res = ff.ffField.actex.doEvent({
					"event_name"	: "enable",
					"event_params"	: [father]
				});
			}

			parent_enable(father);
		}
	};

	function cascade_disable (moveup, inst) {
		if (inst === undefined)
			inst = that;
		
		if (!inst.options.autocomplete.enable)
			inst.has_focus = jQuery.fn.escapeGet(inst.getID()).is(":focus");
		
		var node = inst.getNode();

		if (node && node.firstChild)
			node.firstChild.disabled = true;

		if (moveup && inst.father) {
			cascade_disable(true, inst.getFather());
		} else if (!moveup && inst.childs.length) {
			for (var a = 0; a < inst.childs.length; a++) {
				cascade_disable(false, ff.ffField.actex.getInstance(inst.childs[a]));
			}
		}
	};

	function display_loading () {
		filled = false;
		var node = that.getNode();
		
		if (that.options.autocomplete.enable) {
			jQuery.fn.escapeGet(__id + "_label").autocomplete("destroy");
		}
		
		node.innerHTML = that.loading_markup || loading_markup;
		if(!that.options.hideEmpty || that.options.hideEmpty === true)
			jQuery(node).show();
		else 
			jQuery(node).closest("." + that.options.hideEmpty).show();

	};

	function updatebt () {
		if (!jQuery.fn.escapeGet("actex_" + __id + "_dialogaddlink").length)
			return;

		var res = that.doEvent({
			"event_name"	: "updatebt",
			"event_params"	: [that]
		});
		
		if (res !== undefined && res[res.length - 1])
			return;

		res = ff.ffField.actex.doEvent({
			"event_name"	: "updatebt",
			"event_params"	: [that]
		});
			
		if (res !== undefined && res[res.length - 1])
			return;

		ff.pluginAddInit("ff.ffPage.dialog", function () {
			drawDialogButtons();
		});
	};
	
	function autocomplete(autocomp_source, father_value) {
		if (father_value === undefined)
			father_value = that.getFatherValue();
		
		var autocomp_modified = false;
		
	//jQuery("#calendar-modify_ID_customer_label").data("ui-autocomplete")._trigger("change");	
		jQuery.fn.escapeGet(__id + "_label").autocomplete({
			source : autocomp_source
			, minLength: 0
			, select: function( event, ui ) {
				autocomp_modified = false;
				that.change(false, ui.item.id);
				var $menu = jQuery(this).autocomplete("widget").menu();
				$menu.find( "a" ).removeClass( "autocomp_selected" );
				$menu.find(".ui-state-focus").addClass("autocomp_selected");
			}
			, open: function( event, ui ) {
				jQuery("ul.ui-autocomplete").removeClass("ui-corner-all");
				jQuery("ul.ui-autocomplete li.ui-menu-item a").removeClass("ui-corner-all");
				jQuery("ul.ui-autocomplete").width(jQuery.fn.escapeGet(__id + "_label").innerWidth());
				
				$this = jQuery(this);
				$widget = $this.autocomplete("widget");
				$menu = $widget.menu();
				var itm_found = false;

				if ($menu.find(".autocomp_selected").length) {
					$menu.find( "a" ).removeClass( "ui-state-focus" );
					$menu.find(".autocomp_selected").addClass("ui-state-focus");
				} else if (!$menu.find(".ui-menu-item.ui-state-focus").length) {
					$menu.find(".ui-menu-item").each(function(idx, item) {
						if(jQuery(item).data("ui-autocomplete-item").id == that.value) {
							itm_found = item;
							return false;
						}
					});

					if (itm_found !== false) {
						$widget.menu("focus", null, jQuery(itm_found));
						jQuery(itm_found).find("a.ui-state-focus").addClass("autocomp_selected");
					}
				}
				
				/*if ($this.parent() !== $menu.parent()) {
					$this.parent().append($menu);
					$menu.css("top", "")
						.css("position", "absolute")
						.css("left", $this.position().left)
						.css("width", $this.outerWidth())
						.css("margin-top", $this.outerHeight());
				}*/
				
				var itm_height = $menu.children(".ui-menu-item:first").height();
				var itm_number = $menu.children(".ui-menu-item").length;
				$menu
					.scrollTop(0)
					.css("overflow-y", "hidden")
					.height(parseInt(itm_number * itm_height))
					.find(".ui-menu-item")
						.width("auto")
						.width("100%");
				var max_offset_height = parseInt((jQuery(window).height() - ($menu.offset().top - jQuery(window).scrollTop())) / itm_height) * itm_height;
				if ($menu.height() > max_offset_height) {
					$menu.height(max_offset_height).css("overflow", "hidden").css("overflow-y", "scroll");
					var itm = $menu.find(".autocomp_selected:first");
					if (itm.length) {
						$menu.scrollTop($menu.scrollTop() + itm.position().top - (itm.height() * 4));
					}
				}
			}
			, search : function(event) {
				if (event !== undefined && (
						event.keyCode == jQuery.ui.keyCode.PAGE_UP
						|| event.keyCode == jQuery.ui.keyCode.PAGE_DOWN
						|| event.keyCode == jQuery.ui.keyCode.UP
						|| event.keyCode == jQuery.ui.keyCode.DOWN
						|| event.keyCode == jQuery.ui.keyCode.HOME
						|| event.keyCode == jQuery.ui.keyCode.END
					)) {
					jQuery(this).autocomplete('search', '');
					return false;
				}
			}
		}).keydown(function (event) {
			if (event !== undefined && (
					event.keyCode !== jQuery.ui.keyCode.PAGE_UP
					&& event.keyCode !== jQuery.ui.keyCode.PAGE_DOWN
					&& event.keyCode !== jQuery.ui.keyCode.UP
					&& event.keyCode !== jQuery.ui.keyCode.DOWN
					&& event.keyCode !== jQuery.ui.keyCode.HOME
					&& event.keyCode !== jQuery.ui.keyCode.END
					&& event.keyCode !== jQuery.ui.keyCode.ENTER
					&& event.keyCode !== jQuery.ui.keyCode.ESCAPE
					&& event.keyCode !== jQuery.ui.keyCode.LEFT
					&& event.keyCode !== jQuery.ui.keyCode.RIGHT
					&& event.keyCode !== jQuery.ui.keyCode.TAB
				))
				autocomp_modified = true;
		}).blur(function(event) {
			if (event.relatedTarget) {
				var related = event.relatedTarget.id.replace("_label", "");
				if (ff.ffField.actex.exists(related)) 
					ff.ffField.actex.getInstance(related).has_focus = true; // verificare se dopo il passaggio a blur ha ancora senso
			}

			$this = jQuery(this);
			var itm_found = null;
			var tmp_compare = $this.val().toLowerCase();
			
			var change_rc = null;

			if (/*ui.item === null &&*/ tmp_compare !== "") {
				// find right value
				that.data.each(function (a, tmp_data){ 
					if ((father_value == null && that.father == null) || tmp_data[0] == father_value) {
						var opt_value = tmp_data[1];
						var opt_text = tmp_data[2];  
						if (opt_text.toLowerCase() === tmp_compare) {
							itm_found = {"desc" : opt_text, "value" : opt_value};
							return true;
						}
					}
				});
				
				if (itm_found !== null) {
					$this.val(itm_found["desc"]);
					change_rc = that.change(false, itm_found["value"]);
				} else {
					$this.val("");
					tmp_compare = "";
				}
			}

			if (tmp_compare === "") {
				if (that.options.select_one) {
					change_rc = that.change(false, that.options.select_one_val);
				} else if (that.options.select_noone) {
					$this.val(that.options.select_noone_label);
					change_rc = that.change(false, that.options.select_noone_val);
				} else {
					that.data.each(function (a, tmp_data){
						if ((father_value == null && that.father == null) || tmp_data[0] == father_value) {
							var opt_value = tmp_data[1];
							var opt_text = tmp_data[1];
							$this.val(opt_text);
							change_rc = that.change(false, opt_value);
							return true;
						}
					});
				}
			}
			
			/*if (!change_rc && ff.ffField.actex.clickTarget) {
				/*var tmp = ff.ffField.actex.getInstance(ff.ffField.actex.clickTarget.id);
				if (tmp) {
					tmp.addEvent({
						"event_name" : "refill",
						"func_name" : function (id, node) {
							console.log(id, node, jQuery("#" + ff.ffField.actex.clickTarget.id));
						}
					});
				} else {
					jQuery.fn.escapeGet("#" + ff.ffField.actex.clickTarget.id).click();
				}*/
			//}
		});
		
		if (!that.options.autocomplete.ajax) {
			jQuery.fn.escapeGet(__id + "_label")
				.css("margin-right", 0)
				.after(
					jQuery( "<a>" )
						.attr( "tabIndex", -1 )
						.attr( "id", __id + "_autocomp_bt" )
						/*.attr( "title", "Show All Items" )
						.tooltip()*/
						.button({
							icons: {
								primary: "ui-icon-triangle-1-s"
							},
							text: false
						})
						.removeClass( "ui-corner-all" )
						.addClass( "custom-combobox-toggle ui-corner-right" )
						.click(function() {
							jQuery.fn.escapeGet(__id + "_label").focus();

							// Pass empty string as value to search for, displaying all results
							jQuery.fn.escapeGet(__id + "_label").autocomplete( "search", "" );
						})
				);
		}
	}

	function drawDialogButtons () {
		if (ff.ffPage.dialog.dialog_params.get("actex_dlg_" + __id)) {
			if (!buttons.get("add") || that.options.limit_select) {
				jQuery.fn.escapeGet("actex_" + __id + "_dialogaddlink").hide();
			} else {
				jQuery.fn.escapeGet("actex_" + __id + "_dialogaddlink").show();
			}
		}

		if (ff.ffPage.dialog.dialog_params.get("actex_dlg_edit_" + __id)) {
			if (!buttons.get("edit")) {
				jQuery.fn.escapeGet("actex_" + __id + "_dialogeditlink").hide();
			} else if (that.options.select_one && that.options.select_one_val == ff.coalesce(that.value, "")) {
				jQuery.fn.escapeGet("actex_" + __id + "_dialogeditlink").hide();
			} else if (that.options.select_noone && that.options.select_noone_val == ff.coalesce(that.value, "")) {
				jQuery.fn.escapeGet("actex_" + __id + "_dialogeditlink").hide();
			} else {
				jQuery.fn.escapeGet("actex_" + __id + "_dialogeditlink").show();
			}
		}

		if (ff.ffPage.dialog.dialog_params.get("actex_dlg_delete_" + __id)) {
			if (!buttons.get("delete")) {
				jQuery.fn.escapeGet("actex_" + __id + "_dialogdeletelink").hide();
			} else if (that.options.select_one && that.options.select_one_val == ff.coalesce(that.value, "")) {
				jQuery.fn.escapeGet("actex_" + __id + "_dialogdeletelink").hide();
			} else if (that.options.select_noone && that.options.select_noone_val == ff.coalesce(that.value, "")) {
				jQuery.fn.escapeGet("actex_" + __id + "_dialogdeletelink").hide();
			} else {
				jQuery.fn.escapeGet("actex_" + __id + "_dialogdeletelink").show();
			}
		}
	};

	function parseheader (value) {
		if (value === undefined)
			value = that.value;
		
		var buffer = '';
		if (that.options.control_type == "input") { 
			buffer = '<input id="' + __id + '" name="' + __id + '" class="'  + (that.options.class.length ? that.options.class + ' ' : '') + 'input" ';
			buffer += ' ' + that.options.properties + ' ';
		} else if (that.options.control_type == "label") {
			buffer = '<input type="hidden" id="' + __id + '" name="' + __id + '" value="' + value + '" />';
			buffer += '<span class="'  + (that.options.class.length ? that.options.class + ' ' : '') + 'label" ';
			buffer += ' ' + that.options.properties + ' >';
		} else if(that.options.control_type == "checkbox") {
			buffer = '<input type="hidden" id="' + __id + '" name="' + __id + '" value="' + value + '" />';
			buffer += '<div class="' + (that.options.class.length ? that.options.class + ' ' : '') + 'checkgroup">';
		} else if (that.options.autocomplete.enable) {
			buffer = '<input type="hidden" id="' + __id + '" name="' + __id + '" value="' + value + '" />';
			buffer += '<input id="' + __id + '_label" name="' + __id + '_label" class="'  + (that.options.class.length ? that.options.class + ' ' : '') + 'input" ';
			buffer += ' ' + that.options.properties + ' ';
			if (that.options.select_one) {
				buffer += 'placeholder="' + that.options.select_one_label + '" ';
			}
		} else {
			buffer = '<select';
			buffer += ' class="' + (that.options.class.length ? that.options.class + ' ' : '') + 'select"';
			buffer += ' name="' + __id + '" id="' + __id + '" ' + that.options.properties;
			buffer += ' onChange="ff.ffField.actex.change(\'' + __id +'\', ' + that.options.reset_childs + ');" >';

			if (that.options.select_one) {
				buffer += '<option value="' + that.options.select_one_val + '"';
				if (that.options.select_one_val == value)
					buffer += 'selected ';
				buffer += '>' + that.options.select_one_label + '</option>';
			}

			if (that.options.select_noone) {
				buffer += '<option value="' + that.options.select_noone_val + '"';
				if (that.options.select_noone_val == value)
					buffer += 'selected ';
				buffer += '>' + that.options.select_noone_label + '</option>';
			}
		}

		return buffer;
	};
	
	function constructor() { // NB: called below publics
		jQuery.extend(true, that, ff.ffEvents());
	}
	
	constructor();
	return that;
}; // actex object end

var that = { /* publics */
__ff : true, /* used to recognize ff'objects*/
//"clickTarget" : null,

"init" : function (params) {
	if (!initialized) {
		initialized = true;

		innerURL			= params.innerURL;
		theme_dir			= params.theme_dir;
		loading_markup		= params.loading_markup || 'Loading..&nbsp;&nbsp;<img src="' + ff.fixPath(theme_dir + '/ajax-loader.gif') + '" />';

		/* inits*/
		ff.addEvent({"event_name" : "onClearField", "func_name" : that.onClearField});
		ff.pluginAddInit("ff.ajax", function () {
			ff.ajax.addEvent({"event_name" : "onUpdateField", "func_name" : that.onUpdateField});
		});
	}
},

"setLoadingMarkup" : function (markup) {
	loading_markup = markup;
	return that;
},

"getInstance" : function (id, avoid_undef) {
	var tmp = instances.get(id);
	if (tmp === undefined && !avoid_undef)
		throw "ff.ffField.actex - instance does not exists [" + id + "]";
	
	return tmp;
},

"debug" : function () {
	return instances;
},

"exists" : function (id) {
	if (instances.get(id) === undefined)
		return false
	else
		return true;
},

"resetCache" : function (id) {
	var inst = that.getInstance(id);
	var id_data_src = inst.getCacheDataSrc();
	
	if (!sources.isset(id_data_src))
		return;
			
	var father_value = inst.getFatherValue();
	sources.get(id_data_src).unset(father_value);
},

"factory" : function (params) {
	if (that.exists(params.id))
		return;
		
	var tmp = activecombo(params);
	instances.set(params.id, tmp);

	that.doEvent({
		"event_name"	: "factory",
		"event_params"	: [tmp]
	});

	return tmp;
},

"deleteCombo" : function (id) {
	var inst = that.getInstance(id);
	
	sources.unset(inst.getCacheDataSrc());
	//sources.unset(id); // extra

	instances.unset(id);
},

"change" : function (id, reset_childs) {
	return that.getInstance(id).change(reset_childs);
},

"update" : function (id, new_value, reset_childs) {
	that.getInstance(id).update(new_value, reset_childs);
},

"recalc" : function (id, countCheck, separator) {
	that.getInstance(id).recalc(countCheck, separator);
},

"refill" : function (id, selected_value, father_value, force_refresh) {
	that.getInstance(id).refill(selected_value, father_value, force_refresh);
},

"onClearField" : function (component, field_id, field_data, inst_id) {
	if (field_data.widget !== "actex")
		return;

	if (that.exists(inst_id)) {
		ff.ffField.actex.deleteCombo(inst_id);
	}
},

"insertModeOn" : function (id, dialog) {
	var inst = that.getInstance(id);
	
	inst.insert_mode = true;
	
	if (dialog !== undefined) {
		inst.insert_mode = ff.ffPage.dialog.addEvent({
			"event_name" : "onClose"
			, "func_name" : function (dialog_id) {
				if (dialog_id === dialog) {
					that.insertModeOff(id);
				}
			}
		});
	}
},

"insertModeOff" : function (id) {
	var inst = that.getInstance(id);
	
	if (inst.insert_mode !== false && inst.insert_mode !== true) {
		inst.insert_mode.remove();
	}
	
	inst.insert_mode = false;
},

"onUpdateField" : function (component, key, field, retData) {
	if (field.widget != "actex")
		return;

	if (component !== undefined) {
		switch (ff.struct.get("comps").get(component).type) {
			case "ffDetails":
				var rows = parseInt(jQuery.fn.escapeGet(component + "_rows").val());
				for (var r = 0; r < rows; r++) {
					var tmp_id = component + "_recordset[" + r + "][" + key + "]";
					if (that.exists(tmp_id)) ajaxUpdate(tmp_id, retData);
				}
				break;

			default:
				if (that.exists(component + "_" + key)) ajaxUpdate(component + "_" + key, retData);
		} 
	} else {
		if (that.exists(key)) ajaxUpdate(key, retData);
	}
}

}; /* publics' end*/

/* privates */

function ajaxUpdate (id, retData) {
	var inst = that.getInstance(id);

	ajaxChainBlock(inst);
	inst.refill((inst.insert_mode ? retData.insert_id : undefined), undefined, true);
	
	if (inst.insert_mode !== false)
		that.insertModeOff(id);
};

function ajaxChainBlock (inst) {
	if (inst.childs.length) {
		inst.childs.each(function (a, child) {
			ff.ajax.chainupdate.updated.set(child);
			ajaxChainBlock(that.getInstance(child));
		});
	}
};

/*jQuery(document).mousedown(function (event) {
	ff.ffField.actex.clickTarget = event.target;
	var parent = null;
	if (event.target && (parent = jQuery(event.target).parents(".ui-button")) && parent.length) {
		ff.ffField.actex.clickTarget = parent.get(0);
	}
});

jQuery(document).mouseup(function (event) {
	ff.ffField.actex.clickTarget = null;
});*/

return that;

/* code's end.*/
})();

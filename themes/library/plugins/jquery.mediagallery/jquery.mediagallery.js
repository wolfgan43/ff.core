(function($) {
	var defaults = {
		autoplay:1
	}
	var options;
	var med_list_cont;
	var vert_scroll;
	var hor_scroll;
	var title,descr;
	var video;
	$.fn.extend({
		mediaGallery: function(options) {
			return this.each( function() {

				var basicHTML = '<div id="mediagallery_video_wrap"><div id="mediagallery_video"><div id="media_gallery_inner_vid"></div></div><div id="mediagallery_video_descr"><h1></h1><p></p></div></div><div id="mediagallery_more"><div id="mediagallery_list"><ul id="med_list_cont"></ul></div><div id="mediagallery_scroll"></div><div id="mediagallery_scroll_hor"></div></div>';
				$.extend(defaults, options);
				var content = "";
				$(this).append(basicHTML);
				//
				med_list_cont = $("#med_list_cont");
				vert_scroll = $("#mediagallery_scroll");
				hor_scroll = $( "#mediagallery_scroll_hor" );
				title = $("#mediagallery_video_descr").find("h1");
				descr = $("#mediagallery_video_descr").find("p");
				video = $("#mediagallery_video");
				//
				$(this).find("a").each( function() {
					var url = $(this).attr('href');

					var id;
					if(getVimeoId(url)!= null) {
						var a = $("<li></li>");
						med_list_cont.append(a);
						$.getJSON("http://vimeo.com/api/oembed.json/?url="+url+"&callback=?", function(data) {
							var imgUrl = data.thumbnail_url;
							var title = data.title;
							title = title.substring(0,13) +"...";
							var user = data.author_name;
							var id = data.video_id;
							a.replaceWith("<li><p class='hidden'>vimeo"+id+"</p><img src='"+imgUrl+"' alt='thumb'/><h2>"+title+"</h2><p>By <span class='med_spec'>"+user+"</span> On <span class='med_spec'>Vimeo</span></p>");
							makeClickable();
							doSlider();
							first();

						});
					} else if(url.indexOf("vimeo")>-1) {
						var tempUser = url.replace("http://vimeo.com/","");
						$.getJSON("http://vimeo.com/api/v2/"+tempUser+"/videos.json/?callback=?", function(data) {
						
							$.each(data, function() {
								var a = $("<li></li>");
								med_list_cont.append(a);
								$.getJSON("http://vimeo.com/api/oembed.json/?url="+"http://www.vimeo.com/"+this.id+"&callback=?", function(data) {
									var imgUrl = data.thumbnail_url;
									var title = data.title;
									title = title.substring(0,13) +"...";
									var user = data.author_name;
									var id = data.video_id;
									a.replaceWith("<li><p class='hidden'>vimeo"+id+"</p><img src='"+imgUrl+"' alt='thumb'/><h2>"+title+"</h2><p>By <span class='med_spec'>"+user+"</span> On <span class='med_spec'>Vimeo</span></p>");
									makeClickable();
									doSlider();
									first();
								});
							});
						});
					} else {
						if(url.indexOf("user")>-1) {
							var user = url.replace("http://www.youtube.com/user/","");
							var ammount;
							try {
								ammount = user.split("max-results=")[1].split("&")[0];
							} catch(err) {

							}
							if(ammount && parseFloat(ammount)>50) {
								for(var i = 1;i<ammount;i = i+50) {
									$.getJSON("http://gdata.youtube.com/feeds/api/videos?v=2&author="+user.split("&")[0]+"&max-results=50&start-index="+i+"&alt=json&callback=?", function(data) {
										$.each(data.feed.entry, function() {
											var id = this.id.$t;
											var loc = id.indexOf("video:");
											id = id.substr(loc+6,11);
											var a = $("<li></li>");
											
											$.getJSON("http://gdata.youtube.com/feeds/api/videos?q="+id+"&alt=json&callback=?", function(data) {
												if(data.feed.entry !== undefined) {
													med_list_cont.append(a);
													var title = data.feed.entry[0].title.$t;
													title = title.substring(0,25) +"...";
													var imgUrl = data.feed.entry[0].media$group.media$thumbnail[0].url;
													var user = data.feed.entry[0].author[0].name.$t;
													var id = data.feed.entry[0].id.$t.replace("http://gdata.youtube.com/feeds/api/videos/","");

													a.replaceWith("<li><p class='hidden'>youtube"+id+"</p><img src='"+imgUrl+"' alt='thumb'/><h2>"+title+"</h2><p>By <span class='med_spec'>"+user+"</span> On <span class='med_spec'>Youtube</span></p>");
													makeClickable();
													doSlider();
													first();
												}
											});
										});
									});
								}
							} else {
								$.getJSON("http://gdata.youtube.com/feeds/api/videos?v=2&author="+user+"&alt=json&callback=?", function(data) {
									$.each(data.feed.entry, function() {
										var id = this.id.$t;
										var loc = id.indexOf("video:");
										id = id.substr(loc+6,11);
										var a = $("<li></li>");
										$.getJSON("http://gdata.youtube.com/feeds/api/videos?q="+id+"&alt=json&callback=?", function(data) {
											if(data.feed.entry !== undefined) {
												med_list_cont.append(a);

												var title = data.feed.entry[0].title.$t;
												title = title.substring(0,25) +"...";
												var imgUrl = data.feed.entry[0].media$group.media$thumbnail[0].url;
												var user = data.feed.entry[0].author[0].name.$t;
												var id = data.feed.entry[0].id.$t.replace("http://gdata.youtube.com/feeds/api/videos/","");

												a.replaceWith("<li><p class='hidden'>youtube"+id+"</p><img src='"+imgUrl+"' alt='thumb'/><h2>"+title+"</h2><p>By <span class='med_spec'>"+user+"</span> On <span class='med_spec'>Youtube</span></p>");
												makeClickable();
												doSlider();
												first();
											}
										});
									});
								});
							}
						} else {
							id = getYoutubeId(url);
							$.ajaxSetup ({
								cache: false
							});
							var a = $("<li></li>");
							$.getJSON("http://gdata.youtube.com/feeds/api/videos?q="+id+"&alt=json&callback=?", function(data) {
								if(data.feed.entry !== undefined) {
									med_list_cont.append(a);

									var title = data.feed.entry[0].title.$t;
									title = title.substring(0,25) +"...";
									var imgUrl = data.feed.entry[0].media$group.media$thumbnail[0].url;
									var user = data.feed.entry[0].author[0].name.$t;
									var id = data.feed.entry[0].id.$t.replace("http://gdata.youtube.com/feeds/api/videos/","");
									a.replaceWith("<li><p class='hidden'>youtube"+id+"</p><img src='"+imgUrl+"' alt='thumb'/><h2>"+title+"</h2><p>By <span class='med_spec'>"+user+"</span> On <span class='med_spec'>Youtube</span></p>");
									makeClickable();
									doSlider();
									first();
								}
							});
						}
					}
					$(this).remove();
				});
			});
		}
	});
	function doSlider() {
		vert_scroll.slider({
			orientation: "vertical",
			range: "max",
			min: 0,
			value: med_list_cont.height(),
			max: med_list_cont.height(),
			slide: function( event, ui ) {
				med_list_cont.css("margin-top",(((med_list_cont.height()-ui.value)/2)*-1)+"px");
			},
			change: function(event, ui) {
				med_list_cont.css("margin-top",(((med_list_cont.height()-ui.value)/2)*-1)+"px");
			}
		});
		hor_scroll.slider({
			range: "max",
			min: 0,
			value:0,
			max: med_list_cont.width(),
			slide: function( event, ui ) {

				med_list_cont.css("margin-left",(((0+ui.value))*-1)+"px");
			},
			change: function(event, ui) {
				med_list_cont.css("margin-left",(((0+ui.value))*-1)+"px");
			}
		});
		$("#med_list_cont , #mediagallery_more").mousewheel( function(event, delta) {
			var speed = 5;
			var sliderVal = vert_scroll.slider("value");//read current value of the slider
			sliderVal += (delta*speed);//increment the current value
			vert_scroll.slider("value", sliderVal);//and set the new value of the slider
			event.preventDefault();//stop any default behaviour
		});
		if(hor_scroll.css("display")=="none") {

		} else {
			$("#mediagallery_list").css("width",($("#med_list_cont li").size()*130)+"px");
		}
	}

	function first() {
		if(title.text()=="") {
			title.text(" ");
			$("#med_list_cont li:first").find("img").click();
		}
	}

	function getVimeoId(url) {
		var regExp = /http:\/\/(www\.)?vimeo.com\/(\d+)($|\/)/;
		var match = url.match(regExp);
		if (match != null) {
			return match[2];
		} else {
			return null;
		}
	}

	function makeClickable() {
		var listitem = med_list_cont.find("li");
		listitem.unbind("click");
		listitem.unbind("hover");
		listitem.hover( function() {
			$(this).addClass("med_active");
		}, function() {
			$(this).removeClass("med_active");
		});
		listitem.click( function() {
			video.html("");
			video.append("<div id='media_gallery_inner_vid'></div>");
			var d = $(this);
			$(".med_current").removeClass("med_current");
			d.addClass("med_current");
			var id = $(this).find(".hidden").text();
			if(id.indexOf("youtube")>-1) {
				id = id.replace("youtube","");
				var params = {
					allowScriptAccess: "always",
					wmode:'opaque'

				};
				var atts = {
					id: "myytplayer"
				};

				swfobject.embedSWF("http://www.youtube.com/e/"+id+"?enablejsapi=1&playerapiid=ytplayer&autohide=1&autoplay="+defaults.autoplay,
				"media_gallery_inner_vid", video.width()+"", video.height()+"", "8", null, null, params, atts);
				$.getJSON("http://gdata.youtube.com/feeds/api/videos?q="+id+"&alt=json&callback=?", function(data) {
					if(data.feed.entry !== undefined) {
						title.text(data.feed.entry[0].title.$t);
						descr.text(data.feed.entry[0].media$group.media$description.$t);
					}
				});
			} else if(id.indexOf("vimeo")>-1) {
				id = id.replace("vimeo","");
				var flashvars = {
					'clip_id': id,
					'server': 'vimeo.com',
					'show_title': 0,
					'show_byline': 0,
					'show_portrait': 0,
					'fullscreen': 1,
					'js_api': 1,
					'autoplay':defaults.autoplay
				}

				var parObj = {
					'swliveconnect':true,
					'fullscreen': 1,
					'allowscriptaccess': 'always',
					'allowfullscreen':true
				};

				var attObj = {}
				attObj.id="vid";
				swfobject.embedSWF("http://www.vimeo.com/moogaloop.swf", "media_gallery_inner_vid", video.width()+"", video.height()+"", "9.0.28", '',flashvars,parObj, attObj );
				$.getJSON("http://vimeo.com/api/oembed.json/?url=http://www.vimeo.com/"+id+"&callback=?", function(data) {
					title.text(data.title);
					descr.text(data.description);
				});
			}
		});
	}

	function getYoutubeId(url) {
		var ytLengthID = 11;
		var ID;
		var pos = url.indexOf("?v=");
		if(!pos) {
			pos = url.indexOf("&v=");
		}
		if(!pos) {
			return null;
		}
		return url.substr(pos+3,ytLengthID);
	}

})(jQuery);
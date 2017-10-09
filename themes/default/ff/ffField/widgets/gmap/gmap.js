ff.ffField.gmap = (function () {
	var gmap_data	=  new Array();

	var that = { // publics
		"init" : function () {
			if (window.attachEvent)
			{
				window.attachEvent("onunload", function() {
						GUnload();      // Internet Explorer
					});
			}
			else
			{
			    window.addEventListener("unload", function() {
			            GUnload(); // Firefox and standard browsers
    				}, false);
			}
		}, 
		"addData": function(data) {
			gmap_data[data.id] = new Array();
			gmap_data[data.id]["draggable"] = data.draggable;

			if (1 /*GBrowserIsCompatible()*/) { 
				gmap_data[data.id]["map"] = new GMap2(document.getElementById(data.id + "[map]"));
				gmap_data[data.id]["map"].addControl(new GLargeMapControl3D());
				gmap_data[data.id]["map"].addMapType(G_PHYSICAL_MAP);
				gmap_data[data.id]["map"].setCenter(new GLatLng(data.startLat, data.startLng), data.startZoom);

				gmap_data[data.id]["geocoder"] = new GClientGeocoder() ;
				gmap_data[data.id]["icon"] = new GIcon();
				gmap_data[data.id]["icon"].image = "http://labs.google.com/ridefinder/images/mm_20_red.png";
				gmap_data[data.id]["icon"].shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
				gmap_data[data.id]["icon"].iconSize = new GSize(12, 20);
				gmap_data[data.id]["icon"].shadowSize = new GSize(22, 20);
				gmap_data[data.id]["icon"].iconAnchor = new GPoint(6, 20);

				GEvent.addListener(gmap_data[data.id]["map"], 'click', function(overlay, point) {
						if (overlay) {
							ff.ffField.gmap.removeMarker(data.id);
						} else if (point) {
							ff.ffField.gmap.setMarker(data.id, point, "", false);
						}
					});

				GEvent.addListener(gmap_data[data.id]["map"], 'zoomend', function(oldLevel, newLevel) {
						ff.ffField.gmap.setInfo(data.id);
					});

				if(data.setMarker) {
					ff.ffField.gmap.setMarker(data.id, new GLatLng(data.startLat, data.startLng), data.title, true)
				}

				if(data.forceSearch) {
					ff.ffField.gmap.searchAddress(data.id, document.getElementById(data.id + '_search').value);
				}
			}
		}, 
		"removeMarker" : function (id)	{
			if (gmap_data[id]["marker"])
				gmap_data[id]["map"].removeOverlay(gmap_data[id]["marker"]);

			document.getElementById(id + "[lat]").value = "";
			document.getElementById(id + "[lng]").value = "";
			document.getElementById(id + "[zoom]").value = "";

			gmap_data[id]["marker"] = null;
		}, 

		"setMarker" : function (id, point, sTitle, center)	{
			ff.ffField.gmap.removeMarker(id);

			if (center)
			{
				gmap_data[id]["map"].setCenter(point);
			}

			gmap_data[id]["marker"] = new GMarker(point, {icon: gmap_data[id]["icon"], draggable: gmap_data[id]["draggable"], title: sTitle});

			if (gmap_data[id]["draggable"])
			{
				//gmap_data[id]["marker"].enableDragging();
				GEvent.addListener(gmap_data[id]["marker"], 'dragend', function() {ff.ffField.gmap.newPoint(id)});
			}

			gmap_data[id]["map"].addOverlay(gmap_data[id]["marker"]);

			ff.ffField.gmap.setInfo(id);
		}, 
		"searchAddress" : function (id, address) {
			gmap_data[id]["geocoder"].getLatLng( address, function(point) {
				if (!point) {
					alert(address + " not found")
				} else {
					ff.ffField.gmap.setMarker(id, point, address, true)
				}
			});
		}, 
		"newPoint" : function (id) {
			var point = gmap_data[id]["marker"].getPoint();
			gmap_data[id]["map"].setCenter(point);

			ff.ffField.gmap.setInfo(id);
		}, 
		"setInfo" : function (id) {
			if (gmap_data[id]["marker"]) {
				point = gmap_data[id]["marker"].getPoint();
				document.getElementById(id + "[lat]").value = point.lat().toFixed(6);
				document.getElementById(id + "[lng]").value = point.lng().toFixed(6);
		        document.getElementById(id + "[title]").value = document.getElementById(id + "[search]").value;
				document.getElementById(id + "[zoom]").value = gmap_data[id]["map"].getZoom();
			}
		}, 
		"searchViaEnter" : function (evt, id, address) {
		    evt = (evt) ? evt : event;
		    var charCode = (evt.charCode) ? evt.charCode :
		        ((evt.which) ? evt.which : evt.keyCode);
		    if (charCode == 13) {
		        ff.ffField.gmap.searchAddress(id, address);
		        return false;
		    }
		    return true;
		}
	};

	return that;
	
})();
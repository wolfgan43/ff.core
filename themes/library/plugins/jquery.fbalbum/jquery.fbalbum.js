/**
 * jQuery Facebook Album Plugin
 * @name jquery.fb-album.js
 * @version 4.0
 * @category jQuery Plugin
 */

// Check if Gallery is embedded via iFrame
var isInIFrame 				= (window.location != window.parent.location) ? true : false;
var iFrameDetection			= false;
var language                = (navigator.language || navigator.browserLanguage ).slice(0, 2);

// Other Global Variables
var iFrameWidth 			= 0;
var iFrameHeight 			= 0;
var iFrameAdjust			= 0;
var viewPortWidth 			= 0;
var viewPortHeight			= 0;
var scrollBarWidth			= 0;
var galleryWidth			= 0;
var smartAlbumsPerPage		= 0;
var smartPhotosPerPage		= 0;
var totalItems				= 0;
var galleryContainer 		= "";
var galleryResponsive 		= false;
var lightboxEnabled			= true;
var controlBarAdjust		= 0;
var buttonWidthText			= 0;
var buttonWidthImage		= 0;
var currentPageList			= "";
var AlbumThumbWidth			= 0;
var AlbumThumbHeight		= 0;
var AlbumThumbPadding		= 0;
var AlbumThumbMargin 		= 0;
var PhotoThumbWidth			= 0;
var PhotoThumbHeight		= 0;
var PhotoThumbPadding		= 0;
var PhotoThumbMargin 		= 0;
var TotalThumbs 			= 0;
var TotalPages 				= 0;
var TotalTypes 				= 0;
var SortingOrder 			= "";
var SortingType 			= "";

var imageScalerActive		= true;

var infiniteScrollOffset	= 0;
var infiniteAlbums			= 0;
var infiniteAlbumsShow		= 0;
var infiniteAlbumsCount		= 0;
var infinitePhotos			= 0;
var infinitePhotosShow		= 0;
var infinitePhotosCount		= 0;

var MessiContent 			= "";
var MessiCode 				= "";
var MessiTitle 				= "";

// Define Global Variable to Track and Cancel Ajax Requests
var ajaxRequest;

// Functions to retrieve Screen and iFrame Dimensions
function GetScreenDimensions() {
	if (typeof window.innerWidth != 'undefined') {
		// the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
		viewPortWidth = 			parent.window.innerWidth;
		viewPortHeight = 			parent.window.innerHeight;
	} else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0) {
		// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
		viewPortWidth = 			parent.document.documentElement.clientWidth;
		viewPortHeight = 			parent.document.documentElement.clientHeight;
	} else {
		// older versions of IE
		viewPortWidth = 			parent.document.getElementsByTagName('body')[0].clientWidth;
		viewPortHeight = 			parent.document.getElementsByTagName('body')[0].clientHeight;
	};
}
function GetIFrameDimensions() {
	if ((typeof window.innerWidth != 'undefined') && (typeof( window.innerWidth ) == 'number')) {
		//Non-IE
		iFrameWidth = 				window.innerWidth;
		iFrameHeight = 				window.innerHeight;
	} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		//IE 6+ in 'standards compliant mode'
		iFrameWidth = 				document.documentElement.clientWidth;
		iFrameHeight = 				document.documentElement.clientHeight;
	} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		//IE 4 compatible
		iFrameWidth = 				document.body.clientWidth;
		iFrameHeight = 				document.body.clientHeight;
	}
}
function AdjustIFrameDimensions() {
	// Adjust Height of iFrame Container (if applicable)
	setTimeout(function(){
		if ((isInIFrame) && (iFrameDetection)) {
			GetIFrameDimensions();
			var galleryContainerHeight = jQuery("#" + galleryContainer).height() + iFrameAdjust;
			var galleryContainerWidth = jQuery("#" + galleryContainer).width() + 4;
			var IFrameID = getIframeID(this);
			if (IFrameID != "N/A") {
				window.top.document.getElementById("" + IFrameID + "").style.height = "" + galleryContainerHeight + "px";
				parent.document.getElementById("" + IFrameID + "").style.height = "" + galleryContainerHeight + "px";
				/*if (galleryResponsive) {
					window.top.document.getElementById("" + IFrameID + "").style.width = "" + galleryContainerWidth + "px";
					parent.document.getElementById("" + IFrameID + "").style.width = "" + galleryContainerWidth + "px";
				}*/
			};
		};
	}, 100);
}

// Extend the jQuery Array Functions
(function ($) {
	$.ajaxSetup({ cache: false });
	Array.prototype.frequencies = function() {
		var l = this.length, result = {all:[]};
		while (l--){
		   result[this[l]] = result[this[l]] ? ++result[this[l]] : 1;
		}
		// all pairs (label, frequencies) to an array of arrays(2)
		for (var l in result){
		   if (result.hasOwnProperty(l) && l !== 'all'){
			  result.all.push([ l,result[l] ]);
		   }
		}
		return result;
	};
	// Extend arrays to have a clear function
	Array.prototype.clear = function() {
		this.splice(0, this.length);
	};
	//Extend arrays to have a contains method
	if (!("contains" in Array.prototype)){
		Array.prototype.contains = function ( needle ) {
			var i = 0;
			for (i in this) {
				if (this[i] === needle) {
					return true;
				};
			};
			return false;
		};
	};
})( jQuery );

// Custom plugin for a Slide In/Out Animation with a Fade
(function ($) {
	$.fn.slideFade = function (speed, callback) {
		var slideSpeed;
		for (var i = 0; i < arguments.length; i++) {
			if (typeof arguments[i] == "number") {
				slideSpeed  = arguments[i];
			}
			else {
				var callBack = arguments[i];
			}
		}
		if(!slideSpeed) {
			slideSpeed = 500;
		}
		this.animate({
				opacity: 'toggle',
				height: 'toggle'
			}, slideSpeed,
			function(){
				if( typeof callBack != "function" ) { callBack = function(){}; }
				callBack.call(this);
			}
		);
  };
})( jQuery );

// Additional Plugins and Functions used for the Script
(function ($) {
	//case-insensitive version of :contains
	$.extend($.expr[":"], {"containsNC": function(elem, i, match, array) {return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;}});
	// Plugin to check and compare jQuery versions
	//$.isVersion("1.4.2"); --> returns true, if $().jquery == "1.4.2"
	//$.isVersion("1.3.2", ">"); --> returns true if $().jquery > "1.3.2"
	//$.isVersion("1.3", ">", "1.2.6"); --> returns true
	//$.isVersion("1.3.2", "<", "1.3.1"); --> returns false
	//$.isVersion("1.4.0", ">=", "1.3.2"); --> returns true
	//$.isVersion("1.4.1", "<=", "1.4.1"); --> returns true
	//$.isVersion("1.4.2", "<=", "1.4.2pre"); --> returns false
    /**
     * @param {string} left A string containing the version that will become the left hand operand.
     * @param {string} oper The comparison operator to test against. By default, the "==" operator will be used.
     * @param {string} right A string containing the version that will become the right hand operand. By default, the current jQuery version (jQuery.fn.jquery) will be used.
     * @return {boolean} Returns the evaluation of the expression, either true or false.
    */
	$.isVersion = function(left, oper, right) {
		if (left) {
			var pre = /pre/i,
				replace = /[^\d]+/g,
				oper = oper || "==",
				right = right || $().jquery,
				l = left.replace(replace, ''),
				r = right.replace(replace, ''),
				l_len = l.length, r_len = r.length,
				l_pre = pre.test(left), r_pre = pre.test(right);
			l = (r_len > l_len ? parseInt(l) * Math.pow(10, (r_len - l_len)) : parseInt(l));
			r = (l_len > r_len ? parseInt(r) * Math.pow(10, (l_len - r_len)) : parseInt(r));
			switch(oper) {
				case "==": {return (true === (l == r && (l_pre == r_pre)));};
				case ">=": {return (true === (l >= r && (!l_pre || l_pre == r_pre)));};
				case "<=": {return (true === (l <= r && (!r_pre || r_pre == l_pre)));};
				case ">": {return (true === (l > r || (l == r && r_pre)));};
				case "<": {return (true === (l < r || (l == r && l_pre)));};
			};
		};
		return false;
	};
	// jQuery Plugin for event that happens once after a window resize
    var $event = $.event, $special, resizeTimeout;
    $special = $event.special.debouncedresize = {
        setup: function() {$( this ).on( "resize", $special.handler );},
        teardown: function() {$( this ).off( "resize", $special.handler );},
        handler: function( event, execAsap ) {
            // Save the context
            var context = this,
                args = arguments,
                dispatch = function() {
                    // set correct event type
                    event.type = "debouncedresize";
                    $event.dispatch.apply( context, args );
                };
            if ( resizeTimeout ) {clearTimeout( resizeTimeout );}
            execAsap ? dispatch() : resizeTimeout = setTimeout( dispatch, $special.threshold );
        },
        threshold: 150
    };
	// Adjust element width for responsive layout after window resize
	$(window).on("debouncedresize", function( event ) {
		// Reset viewPort Dimensions
		GetScreenDimensions();
		// Adjust Height of iFrame Container (if applicable)
		AdjustIFrameDimensions();
	});
	// Detect if Scrollbar is present
	$.fn.isScrollable = function(){
		var elem = $(this);
		return (
		elem.css('overflow') == 'scroll'
			|| elem.css('overflow') == 'auto'
			|| elem.css('overflow-x') == 'scroll'
			|| elem.css('overflow-x') == 'auto'
			|| elem.css('overflow-y') == 'scroll'
			|| elem.css('overflow-y') == 'auto'
		);
	};
})(jQuery);

// Function to retrieve iFrame ID in which gallery is embedded (if applicable)
function getIframeID(el) {
	var myTop = top;
	var myURL = location.href.split('?')[0];
	var iFs = top.document.getElementsByTagName('iframe');
	var x, i = iFs.length;
	while ( i-- ){
		x = iFs[i];
		if (x.src && x.src == myURL){
			//return 'The iframe ' + ((x.id)? 'has ID=' + x.id : 'is anonymous');
			return ((x.id)? x.id : 'N/A');
		};
	};
	return 'N/A';
};

// Function to retrieve position of Element in Multi-Dimensional Array
function getIndexByAttribute(list, attr, val){
    var result = null;
    $.each(list, function(index, item){
        if(item[attr].toString() == val.toString()){
           result = index;
           return false;     // breaks the $.each() loop
        }
    });
    return result;
}

// jQuery Facebook Gallery
(function($) {
    $.fn.FB_Album = function (opts) {
		// Set Treshold for Window Resizing Watch
		$.event.special.debouncedresize.threshold = 250;

		// Function to check if Logging Console already exists, otherwise Create
		if (!"console" in window || typeof console == "undefined") {
			var methods = ["log", "debug", "info", "warn", "error", "assert", "dir", "dirxml", "group", "groupEnd", "time", "timeEnd", "count", "trace", "profile", "profileEnd"];
			var emptyFn = function () {};
			window.console = {};
			for (var i = 0; i < methods.length; ++i) {window.console[methods[i]] = emptyFn;};
		};

		// Function to retrieve Absolute Path for a File
		function getAbsolutePath() {
			var loc = window.location;
			var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
			return loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));
		};

		// Define Gallery Options
		opts = $.extend({
            facebookID: 					'',											// Define your Facebook ID
				excludeAlbums: 				[],											// ID's of albums that are to be exclude from showing (only applies if "showSelectionOnly" is set to "false")

			facebookToken:					'',											// Facebook Access Token for Personal / Restricted Facebook Pages

			singleAlbumOnly:				false,										// Set to "true" if you want to show only one specific Facebook Album
				singleAlbumID:				'',											// Define the ID of the single album you want to show

			showSelectionOnly:				false,										// Set to "true" if you want to show only specified albums; otherwise all albums will be pulled (minus the ones marked to be excluded)
				includeAlbums:				[],											// ID's of albums that you want to be shown (only applies if "showSelectionOnly" is set to "true")

			maxNumberGalleries:				20,											// Define how many galleries should be pulled from Facebook (0 = all albums; only applies if "showSelectionOnly" is set to "false")
				excludeTimeLine:			true,										// Define if the "Timeline" album should be excluded automatically

			maxNumberImages:				50,											// Define how many images per gallery should be pulled from Facebook (set to "0" (zero) if all images should be pulled)
				excludeImages: 				[],											// ID's of images that are to be exclude from showing

			maxGraphLimit:					1000,										// Internal Setting for Graph Request (only used if "maxNumberGalleries" is "0")

			weservImageScaler:				false,										// If set to true, the script will utilize the Cloud Based weserv.nl Service to scale Thumbnails
			senchaImageScaler:				false,										// If set to true, the script will utilize the Cloud Based Sencha.io Service to scale Thumbnails
			innerImageScaler:				false,										// If set to true, the script will use an internal php function to scale images to size, otherwise a cloud based service must be used
				PathInternalPHP:			'PHP/TimThumb.php',							// Define path and name to internal PHP Image Scaler

			imageLazyLoad:					true,										// Define if lazyload for thumbnails should be utilized (images will only be loaded if in view)
			imageSpinnerAnimation:			true,										// If "true", the gallery will show a spinner animation until the thumbnail image has been fully loaded

			responsiveGallery:				true,										// Define if gallery is supposed to be responsive to window size changes
				responsiveWidth:			90,											// Set percent of window width the responsive frameID container should have; only enter number but no '%' behind number
				fixedWidth:					800,										// Set window width in px for a fixed size frameID container; only enter number but no 'px' behind number

			detectIFrame:					true,										// If set to "true", script will auto adjust width and height of the iFrame the script is embedded in
				iFrameHeightAdjust:			24,											// Define an adjustment in px that will be used to offset the height adjustment for the iFrame the gallery is embedded in (if applicable)

            PathMomentLanguageFile:         'JS/Language/',                             // Define the path to where the individual language files for Moment.js are located
                FromNowLanguage:            'en',                                       // Define the language for the from-now (i.e. 2 months ago) time phrases; please see manual for listing of available languages

			PathNoCoverImage:				'CSS/Images/no_cover.png',					// Set path to image to be used if Facebook doesn't provide a Cover Image for an album
			allowAlbumDescription:			true,										// Set to "true" if you want to allow the album description in album view
				showDescriptionStart:		false,										// Set to "true" if you want to show the album description by default; toggle button can be used to show/hide
				showCommentsLikes:			true,

			cacheAlbumContents:				false,										// Set to "true" if you want to keep albums already loaded in DOM and reuse or "false" for reloading every time

			paginationLayoutAlbums:			true,										// Set to "true" if you want to use pagination for the album thumbnails
				smartAlbumsPerPageAllow:	true,										// If "true", the script will auto paginate the album thumbnails based on screen dimensions
					setAlbumsByPages:		false,										// If "true", you can manually set the total number of pages for album thumbnails
					numberAlbumsPerPage:	9,											// Set the number of albums that should be shown per page (if paginationLayoutAlbums = true)
					numberPagesForAlbums:	3,											// Set the number of pages for album thubmnails that should be shown (if setAlbumsByPages = true)
				albumsPagerControls:		true,										// If "true", a select box with all available pages wil be provided for quicker Album Navigation
			infiniteScrollAlbums:			false,										// Set to "true" if you want to use infinite scroll for album thumbnails
				infiniteScrollAlbumsSmart:	true,										// If "true", the script will automatically determine the number of thumbnails to load per infinite scroll event
				infiniteScrollAlbumsBlock:	9,											// Manually set the number of thumbnails to load per infinite scroll event (applies if "infiniteScrollAlbumsSmart" = false)

			paginationLayoutPhotos:			true,										// Set to "true" if you want to use pagination for the photo thumbnails
				smartPhotosPerPageAllow:	true,										// If "true", the script will auto paginate the photo thumbnails based on screen dimensions
					setPhotosByPages:		false,										// If "true", you can manually set the total number of pages for photo thumbnails
					numberPhotosPerPage:	16,											// Set the number of photos that should be shown per page (if paginationLayoutPhotos = true)
					numberPagesForPhotos:	6,											// Set the number of pages for photo thumbnails that should be shown (if setPhotosByPages = true)
				showBottomControlBar:		true,										// Set to "true" if you want to show a control bar at the bottom of the detailed album view (includes a 2nd "Back" and a "Scroll to Top" Button)
				photosPagerControls:		true,										// If "true", a select box with all available pages will be provided for quicker Photo Navigation
			infiniteScrollPhotos:			false,										// Set to "true" if you want to use infinite scroll for photo thumbnails
				infiniteScrollPhotosSmart:	true,										// If "true", the script will automatically determine the number of thumbnails to load per infinite scroll event
				infiniteScrollPhotosBlock:	16,											// Manually set the number of thumbnails to load per infinite scroll event (applies if "infiniteScrollPhotosBlock" = false)

			infiniteScrollOffset:			100,										// Define an additional offset in px which increases the height the user needs to scroll down before an infinite scroll event get triggered
			infiniteScrollMore:				true,										// If "true", a "Scroll down to show more Items!" message will be shown below the thumbnails

			showTopPaginationBar:			true,										// If "true", a pagination control bar (first / prev / next / last page) will be shown above the thumbnails
			showBottomPaginationBar:		true,										// If "true", a pagination control bar (first / prev / next / last page) will be shown below the thumbnails
			showThumbInfoInPageBar:			true,										// If "true", thumbnail count (i.e. Album 1 to 6 out of 20) will be shown in Pagination Bars

			floatingControlBar:				true,										// If set to "true", the control bar will follow the user while scrolling up/down (only if not in iFrame!)
				controlBarTopOffset:		10,											// Allows for an offset in px for the floating controlbar in order to account for menus or other top-fixed elements
				showFloatingReturnButton:	false,										// If "true", a return button will be shown in album detail view
				showFloatingToTopButton:	true,										// If "true", a Go-To-Top button will be shown in the floating control bar

			// Settings for Tooltips
			// ---------------------
			tooltipTipAnchor:				'title',									// Define what anchor or data-key should be used to store tooltips (i.e. "alt", "title", etc.)
			tooltipUseInternal:				true,										// Define if the internal tooltip script (qTip2) should be utilized
				tooltipDesign:				'qtip-jtools',								// Define which design to choose from for the qTip2 Plugin
			createTooltipsAlbums:			true,										// Add Tooltip class "TipGallery" to Album Thumbnails
			createTooltipsPhotos:			true,										// Add Tooltip class "TipPhoto" to Photo Thumbnails
			createTooltipsLightbox:			true,										// Add Tooltip class "TipLightbox" to Description Text in Lightbox
			customTooltipsClass:			'',											// Add a Custom Class Name to all Items that include Tooltip Item
			tooltipTitleBar:				false,										// Define if a Tooltip Title Bar should be shown
			tooltipCloseButton:				false,										// Define if Tooltips should get a Close Button
			tooltipThemeRoller:				false,										// Define whether or not the UI-Widget classes of the Themeroller UI styles are applied to the Tooltip
			tooltipTipCorner:				false,										// Define if the Tooltips should have a Corner to create a Speech Bubble Effect
			tooltipOffsetX:					0,											// Define an additional X-Scale (Horizontal) Tooltip Offset
			tooltipOffsetY:					30,											// Define an additional Y-Scale (Vertical) Tooltip Offset
			tooltipPositionTarget:			'mouse',									// Define HTML element the tooltip will be positioned in relation to; 'mouse' or the 'event' (position at target that triggered the tooltip)
			tooltipPositionMy:				'top center',								// Define where the corner of the Tooltip should be positioned in relation to the Target Element
			tooltipPositionAt:				'bottom center',							// Define the corner of the Target Element to position the Tooltip corner in relation to

			// General Settings for Filter/Search Feature
			// ------------------------------------------
			albumsFilterAllow:				true,										// If "true", provides a filter to filter albums by either dates created or last updated
				albumsFilterAllEnabled:		false,										// If "true", all album filter selections will be unchecked by default
				useAlbumsUpdated:			true,										// If "true", filter will use date last updated; if "false", filter will use date created
			photosFilterAllow:				true,										// If "true", provides a filter to filter photos by dates last added to the album
				photosFilterAllEnabled:		false,										// If "true", all photo filter selections will be unchecked by default
			sortFilterNewToOld:				true,										// If "true", all filter criteria will be sorted from newest to oldest or reverse when "false"
			albumSearchControls:			true,										// If "true", a album search feature will be provided

			// General Settings for Sorting Feature
			// ------------------------------------
			albumSortControls:				true,										// Allow for Sorting of Album Thumbnails
				albumAllowSortName:			true,										// Allow for Sorting by Album Name
				albumAllowSortItems:		true,										// Allow for Sorting by Number of Images per Album
				albumAllowSortCreated:		true,										// Allow for Sorting by Date Album has been created
				albumAllowSortUpdate:		true,										// Allow for Sorting by Date Album has last been updated
				albumAllowSortFacebook:		false,										// Allow for Sorting by order as provided by Facebook
				albumAllowSortID:			false,										// Allow for Sorting by Facebook ID
				albumAllowSortPreSet:		false,										// Allow for Sorting by order in which preset ID's have been entered
			photoSortControls:				true,										// Allow for Sorting of Photo Thumbnails
				photoAllowSortAdded:		true,										// Allow for Sorting by Date Photo has been added to Album
				photoAllowSortUpdate:		true,										// Allow for Sorting by Date Photo has last been updated
				photoAllowSortFacebook:		false,										// Allow for Sorting by order as provided by Facebook
				photoAllowSortID:			false,										// Allow for Sorting by Facebook ID

			// Settings for Initial Album Thumbnail Sorting Order
			// --------------------------------------------------
			defaultSortDirectionASC:		true,										// Set to "true" for ascending (oldest to newest) and "false" for descending (newest to oldest) default sort direction for album thumbnails
				defaultSortByAlbumTitle:	true,										// Set to "true" if the default sorting criteria should be the album title
				defaultSortByNumberImages:	false,										// Set to "true" if the default sorting criteria should be the number of images per album
				defaultSortByDateCreated:	false,										// Set to "true" if the default sorting criteria should be the date at which album was created
				defaultSortByDateUpdated:	false,										// Set to "true" if the default sorting criteria should be the date at which album was last updated
				defaultSortByFacebookOrder:	false,										// Set to "true" if the default sorting criteria should be the order at which albums were received from Facebook
				defaultSortByFacebookID:	false,										// Set to "true" if the default sorting criteria should be the album ID as assigned by Facebook
				defaultSortByPreSet:		false,										// Set to "true" if the default sorting critetia should be the order in which preset ID's have been entered

			// Settings for Initial Photo Thumbnail Sorting Order
			// --------------------------------------------------
			defaultPhotoDirectionsASC:		true,										// Set to "true" for ascending (oldest to newest) and "false" (newest to oldest) for descending default sort direction for photo thumbnails
				defaultPhotoSortAdded:		true,										// Set to "true" if the default sorting criteria should be the date at which photo was added to the album
				defaultPhotoSortUpdated:	false,										// Set to "true" if the default sorting criteria should be the date at which photo was last updated
				defaultPhotoSortOrder:		false,										// Set to "true" if the default sorting criteria should be the order at which photos were received from Facebook
				defaultPhotoSortID:			false,										// Set to "true" if the default sorting criteria should be the photo ID assigned by Facebook

			// Settings for Text Items in Sorting Controls
			// -------------------------------------------
			PagesButtonText:				'Change Page',								// Define Text for Page Gallery Button
			SortButtonTextAlbums:			'Sort Albums',								// Define Text for Sorting Button (Albums)
			SortButtonTextPhotos:			'Sort Photos',								// Define Text for Sorting Button (Photos)
				SortNameText:				'Album Name',								// Define Text for Sorting Option (Sort by Album Name)
				SortItemsText:				'Number Images',							// Define Text for Sorting Option (Sort by Number Items)
				SortAddedText:				'Date Added',								// Define Text for Sorting Option (Sort by Date Photo Added to Album)
				SortCreatedText:			'Date Created',								// Define Text for Sorting Option (Sort by Date Created)
				SortUpdatedText:			'Last Update',								// Define Text for Sorting Option (Sort by Date Updated)
				SortFacebookText:			'Facebook Order',							// Define Text for Sorting Option (Sort by Order as provided by Facebook)
				SortIDText:					'Facebook ID',								// Define Text for Sorting Option (Sort by Facebook ID)
				SortPreSetText:				'Custom Order',								// Define Text for Sorting Option (Sort by Order in which pre-set ID's have been entered)
			FilterButtonTextAlbums:			'Last Updated',								// Define Text for Filter Button (Albums)
			FilterButtonTextPhotos:			'Last Added',								// Define Text for Filter Button (Photos)
			SearchButtonTextAlbums:			'Search Albums',							// Define Text for Search Button (Albums)
			SearchButtonTextPhotos:			'Search Photos',							// Define Text for Search Button (Photos)
			SearchDefaultText:				'Search ...',								// Define Text for Default Search Term

			// Settings for Text Items in Album Preview
			// ----------------------------------------
			AlbumContentPreText:			'Content:',									// Adjust width of CSS classes .albumCount, .albumCreate, .albumUpdate, .albumNumber if necessary
			AlbumCreatedPreText:			'Created:',									// Adjust width of CSS classes .albumCount, .albumCreate, .albumUpdate, .albumNumber if necessary
			AlbumUpdatedPreText:			'Updated:',									// Adjust width of CSS classes .albumCount, .albumCreate, .albumUpdate, .albumNumber if necessary
			AlbumShareMePreText:			'Share Album:',								// Define text shown before "Share Album" Links
			AlbumSharesPreText:				'Share Views:',								// Define text shown before the number of total Album Shares
			AlbumNumericIDPreText:			'Album ID:',								// Adjust width of CSS classes .albumCount, .albumCreate, .albumUpdate, .albumNumber if necessary
			OutOfTotalImagesPreText:		'out of',									// Define pre text when there are more images in album that the script is allowed to pull
			SingleImageWord:				'Image',									// Define word for a single Image
			MultiImagesWord:				'Images',									// Define word for multiple Images

			// Settings for Text Items in Photo Preview
			// ----------------------------------------
			AlbumBackButtonText:			'Back',										// Define text for back button in album preview
			AlbumTitlePreText:				'Album Name:',								// Define text shown before album name
			AlbumCommentsText:				'Show Comments',							// Define text to be shown in button that shows Album Comments
			AlbumLinkButtonText:			'Click here to view Album on Facebook',		// Define text shown for text link to original Facebook Album
			AlbumNoDescription:				'No Album Description available.',			// Define text to be shown if there is no Album description available
			ImageLocationPreText:			'Picture(s) taken at',						// Define text shown before image location text (actual loaction pulled from Facebook; if available)
			ImageNumberPreText:				'Image ID:',								// Define text shown before Image ID Number
			ImageShareMePreText:			'Share Image:',								// Define text shown before "Share Image" Links
			ImageSharesPreText:				'Share Views:',								// Define text shown before the number of total Photo Shares
			lightBoxNoDescription:			'No Image Description available.',			// Define text to be shown in Lightbox if no Image Description available

            // Settings for Text Items in Pagination Summary Bar
            // -------------------------------------------------
            PaginationShowingText:          'Showing',                                  // Define text part in "SHOWING Albums/Photos x to y out of z"
            PaginationAlbumsText:           'Albums',                                   // Define text part in "Showing ALBUMS/Photos x to y out of z"
            PaginationPhotosText:           'Photos',                                   // Define text part in "Showing Albums/PHOTOS x to y out of z"
            PaginationItemsToText:          'to',                                       // Define text part in "Showing Albums/Photos x TO y out of z"
            PaginationOutOfTotalText:       'out of',                                   // Define text part in "Showing Albums/Photos x to y OUT OF z"
            PaginationPageText:             'Page',                                     // Define text part in "PAGE x of z"
            PaginationPageOfText:           'of',                                       // Define text part in "Page x OF z"

			// Settings for Text Items in Share Buttons
			// ----------------------------------------
			SocialShareAlbumText:			'Check out this Album on Facebook ... ',	// Define text to be shown in Twitter Share Text (before Link to Album)
			SocialSharePhotoText:			'Check out this Photo on Facebook ... ',	// Define text to be shown in Twitter Share Text (before Link to Photo)

			// Settings for Text Items in Infinite Scroll
			InfiniteScrollMore:				'Scroll down to show more Items!',			// Define text to be shown below thumbnails if Infinite Scroll is enabled
			InfiniteScrollLoad:				'Loading ...',								// Define text to be shown on screen when an Infinite Scroll Event has been triggered

			// Settings for Album Thumbnails
			// -----------------------------
			albumNameTitle:					true,										// Add Name / Title of Album to each Album Thumbnail
				albumNameAbove:				true,										// If "true", the album name will be shown above the thumbnail, otherwise below
				albumNameShorten:			true,										// If "true", the album name shown will automatically be shortened to avoid unnecessary linebreaks
			albumImageCount:				true,										// Add Image Count per Album Below Album Thumbnail
			albumDateCreate:				true,										// Add Date Created below Album Thumbnail
				albumCreateFromNow:			true,										// Define if date created should be converted into a "from now" period (i.e. 2 days ago)
			albumDateUpdate:				false,										// Add Date Last Updated below Album Thumbnail
				albumUpdateFromNow:			true,										// Define if date last updated should be converted into a "from now" period (i.e. 2 days ago)
			albumFacebookID:				false,										// Add Album ID below Album Thumbnail; ID can be used to exclude album from showing

			matchAlbumPhotoThumbs:			false,										// Set to true if you want to make the album thumbnails look like the photo thumbnails (photo thumbnail settings will be used)
				albumWrapperWidth:			290,										// Define width for each Album Wrapper (should equal albumThumWidth + 2x albumFrameOffset!)
				albumThumbWidth: 			280,										// Define width for each Album Thumbnail (deduct at least 2x albumFrameOffset from albumWrapperWidth to allow for frame offset)
				albumThumbHeight: 			200,										// Define Height for each Album Thumbnail
				albumFrameOffset:			5,											// Define offset for 2nd Album Thumbnail border to create stacked effect (set to "0" (zero) to disable stack effect)
				albumThumbPadding:			0,											// This is just a placeholder variable; no need to change since it will be automatically filled!
			albumWrapperMargin:				10,											// Define margin for each Album Wrapper
			albumShadowOffset:				12,											// Define additional offset (top) for album shadow to fine-tune shadow position
			albumInfoOffset:				0,											// Define additional offset (top) for album information section (name, content, dates)
			albumThumbOverlay:				true,										// Add Magnifier Overlay to Thumbnail
			albumThumbRotate:				true,										// Add Hover Rotate / Rumble Effect to Album Thumbnail (rotate does not work in IE 8 or less; rumble effect compensates)
				albumRumbleX:				3,											// Define Rumble Movement on X-Scale for Album Thumbnails
				albumRumbleY:				3,											// Define Rumble Movement on Y-Scale for Album Thumbnails
				albumRotate:				3,											// Define Rotation Angle on X+Y-Scale for Album Thumbnails
				albumRumbleSpeed:			150,										// Define Speed for Rumble / Rotate Effect for Album Thumbnails
			albumShowPaperClipL:			true,										// PaperClip on the left
			albumShowPaperClipR:			false,										// PaperClip on the Right
			albumShowPushPin:				false,										// Centered Pushin
			albumShowShadow:				true,										// Show Shadow below Album Thumbnail (use only one shadow type below)
				albumShadowA:				true,										// Images Show Shadow Type 1 (default if none selected and "albumShowShadow" = "true")
				albumShadowB:				false,										// Images Show Shadow Type 2
				albumShadowC:				false,										// Images Show Shadow Type 3
			albumCCS3Shadow:				false,										// CSS3 Show Shadow Type (adds class "ShadowCSS3" to elements; independent from image shadow types)
			albumShowSocialShare:			true,										// Add Section to share album via Facebook, Twitter and Google
				albumSocialSharePopup:		false,										// If "true", the Album Social Share links will open in a popup window instead of a new tab
				albumTitleSummaryLength:	25,											// Define the Length (Number of Characters) of the Album Title to be used in Share Links
			albumThumbSocialShare:			false,										// If "true", the Social Share button will be shown as overlay in the Album Thumbnail, otherwise, below
			albumShortSocialShare:			true,										// If "true", the Album Share URL will be shortened, using the http://safe.mn URL Shortener Service
				albumShowOrder:				false,										// Show Number of album (derived from order as provided by Facebook)

			// Settings for Photo Thumbnails
			// -----------------------------
			photoThumbWidth: 				210,										// Define Width for each Photo Thumbnail
			photoThumbHeight: 				155,										// Define Height for each Photo Thumbnail
			photoThumbMargin: 				10,											// Define Margin (top-left-bottom-right) for each Photo Thumbnail for space between each thumbnails
			photoThumbPadding:				5,											// Define Padding (top-left-bottom-right) for each Photo Thumbnail for photo frame
			photoThumbOverlay:				true,										// Add Magnifier Overlay to Photo Thumbnail
			photoThumbRotate:				true,										// Add Hover Rotate / Rumble Effect to Photo Thumbnail (rotate does not work in IE 8 or less; rumble effect compensates)
				photoRumbleX:				5,											// Define Rumble Movement on X-Scale for Photo Thumbnails
				photoRumbleY:				5,											// Define Rumble Movement on Y-Scale for Photo Thumbnails
				photoRotate:				5,											// Define Rotation Angle on X+Y-Scale for Photo Thumbnails
				photoRumbleSpeed:			150,										// Define Speed for Rumble / Rotate Effect for Photo Thumbnails
			photoShowClearTape:				true,										// Add Clear Tape on Top of Photo Thumbnail
			photoShowYellowTape:			false,										// Add Yellow Tape on Top of Photo Thumbnail
			photoShowPushPin:				false,										// Add Centered Pushin on Top of Photo Thumbnail
			photoShowIconFBLink:			true,										// Set to "true" if you want to show a icon link to the original Facebook Album
			photoShowTextFBLink:			true,										// Set to "true" if you want to show a text link to the original Facebook Album
			photoShowNumber:				false,										// Add Facebook Image ID Number below Thumbnail
			photoShowSocialShare:			true,										// Add Section to share photo via Facebook, Twitter and Google
				photoSocialSharePopup:		false,										// If "true", the Photo Social Share links will open in a popup window instead of a new tab
				photoTitleSummaryLength:	50,											// Define the Length (Number of Characters) of the Photo Description to be used in Share Links
				photoThumbSocialShare:		false,										// If "true", the Social Share button will be shown as overlay in the Photo Thumbnail, otherwise, below
				photoShortSocialShare:		false,										// If "true", the Photo Share URL will be shortened, using the http://safe.mn URL Shortener Service
				photoShowOrder:				false,										// Show Number of photo (derived from order as provided by Facebook)

			// Settings for Optional Lightboxes
			// --------------------------------
			fancyBoxAllow:					true,										// Add fancyBox (Lightbox) to Photo Thumbnails; if not, images will open up in new tab / window
			fancyBoxOptions: 				{},											// Options for fancyBox Lightbox Plugin (currently not active yet; preparation for future update!)
			colorBoxAllow:					false,										// Add colorBox (Lightbox) to Photo Thumbnails; if not, images will open up in new tab / window
			colorBoxOptions: 				{},											// Options for colorBox Lightbox Plugin (currently not active yet; preparation for future update!)
			prettyPhotoAllow:				false,										// Add prettyPhoto (Lightbox) to Photo Thumbnails; if not, images will open up in new tab / window
			prettyPhotoOptions:				{},											// Options for prettyPhoto Lightbox Plugin (currently not active yet; preparation for future update!)
			photoBoxAllow:					false,										// Add photoBox (Lightbox) to Photo Thumbnails; if not, images will open up in new tab / window
			photoPhotoOptions:				{},											// Options for photoBox Lightbox Plugin (currently not active yet; preparation for future update!)
			lightboxCustomClass:			'',											// Add a Custom Class Name to all Items that can be opened with a Lightbox
			lightboxSocialShare:			false,										// If "true", the social share buttons will be shown in the Lightbox (NOT for photoBox!)

			// Debug Settings (Experimental)
			// -----------------------------
			consoleLogging:					true,										// Define if error/success messages and notices should be logged into the browser developer console
			outputGraphAlbums:				false,
			outputGraphPhotos:				false,
			outputCountAlbumID:				false,										// Shows a popup with album counter and album ID for each album found and not excluded while looping

			// Don't change any ID's unless you are also updating the corresponding CSS file
			// -----------------------------------------------------------------------------
			frameID: 						$(this).attr("id"),							// ID of element in which overall gallery script is to be shown
			loaderID: 						'FB_Album_Loader',							// ID of element in which gallery loader animation is to be shown ... ensure ID matches the one used in CSS settings!
			galleryID: 						'FB_Album_Display',							// ID of element in which gallery thumbnails are to be shown ... ensure ID matches the one used in CSS settings!
			errorID: 						'FB_Error_Display',							// ID of element in which error messages are to be shown ... ensure ID matches the one used in CSS settings!
			infiniteAlbumsID:				'FB_Album_Infinite_Albums',
			infinitePhotosID:				'FB_Album_Infinite_Photos',
			infiniteLoadID:					'FB_Album_Infinite_Load',
			infiniteMoreID:					'FB_Album_Infinite_More'
		}, opts);
		
		// Check if Selection Only Mode with no Albums and Switch to Standard Mode
		if ((opts.showSelectionOnly) && (opts.includeAlbums.length == 0)) {
			opts.showSelectionOnly = false;
		} else if ((opts.showSelectionOnly) && (opts.includeAlbums.length > 0)) {
			opts.maxNumberGalleries = opts.includeAlbums.length;
		};

		// Check if Selection Only Mode with 1 Album Only and Switch to Single Mode
		if ((opts.showSelectionOnly) && (opts.includeAlbums.length == 1)) {
			opts.singleAlbumOnly = true;
			opts.showSelectionOnly = false;
			opts.singleAlbumID = opts.includeAlbums;
			if (opts.consoleLogging) {
				console.log('User set script to "album-selection" mode with only one album (' + opts.includeAlbums + ') specified. Script has been reset to "single-album" mode.');
			};
		};

		// Define Some Script Variables
		var counterA = 					0;
		var counterB = 					0;
		var images = 					0;
		var albumCount =				0;
		var albumId = 					opts.singleAlbumID;
		var headerArray = 				new Array();
		var footerArray = 				new Array();
		var graphLimitA =				opts.maxNumberGalleries;
		var graphLimitB =				opts.maxNumberImages;
		var defaultSortTypeAlbums =		'';
		var defaultSortTypePhotos =		'';
		var defaultSortArrayAlbums =	new Array();
		var defaultSortArrayPhotos =	new Array();
		var defaultThumbArrayScale =	new Array();
		var defaultLightboxArray =		new Array();
		var UserIDCleanOut =            opts.facebookID.replace(/[^a-z0-9\s]/gi, '_');
		var albumsAllInfoDisabled =     false;
		var albumsShowControlBar =		true;
		var AlbumPreSetArray =			[];
		var AlbumIDsArray = 			[];
		var PhotoIDsArray = 			[];
		var tooltipClass =				((opts.customTooltipsClass != "") ? (" " + opts.customTooltipsClass) : "");
		AlbumThumbPadding =				opts.albumThumbPadding;
		AlbumThumbMargin =				opts.albumThumbMargin;
		PhotoThumbPadding =				opts.photoThumbPadding;
		PhotoThumbMargin =				opts.photoThumbMargin;
		galleryContainer =				opts.frameID;
		galleryResponsive = 			opts.responsiveGallery;
		controlBarAdjust = 				opts.controlBarTopOffset;
		iFrameDetection =				opts.detectIFrame;
		iFrameAdjust =					opts.iFrameHeightAdjust;
		infiniteScrollOffset =			opts.infiniteScrollOffset;
	
		// Store and Assign Order for PreSet Album ID's
		if (opts.showSelectionOnly) {
			$.each(opts.includeAlbums, function(intIndex, objValue ){
				AlbumPreSetArray.push({id:objValue, order:intIndex});
			});
		};
	
        // Load Language File for Moment.js if a Custom Language has been Defined
        if (opts.FromNowLanguage != "en") {
            var LanguageURL = opts.PathMomentLanguageFile + opts.FromNowLanguage + '.js';
            $.getScript(LanguageURL).done(function(script, textStatus) {
                if (opts.consoleLogging) {
                    console.log("Moment.js Language File (" + LanguageURL + ") has been successfully loaded.");
                };
                moment.lang(opts.FromNowLanguage);
            }).fail(function(jqxhr, settings, exception) {
                if (opts.consoleLogging) {
                    console.log("Moment.js Language File (" + LanguageURL + ") could not be loaded; reverting to default language (EN).");
                };
                moment.lang('en');
            });
        }

		opts.SocialShareAlbumText = opts.SocialShareAlbumText.replace(/\s/g,"%20");
		opts.SocialSharePhotoText = opts.SocialSharePhotoText.replace(/\s/g,"%20");

		// Check if all Contralbar Features have been disabled by User
		if ((!opts.albumSortControls) && (!opts.albumsFilterAllow) && (!opts.albumSearchControls) && (!opts.albumsPagerControls)) {
			opts.floatingControlBar = false;
			albumsShowControlBar = false;
		}

		// Check for Contradicting Default Sort Settings and Auto Correct
		defaultSortArrayAlbums.push(opts.defaultSortByAlbumTitle);
		defaultSortArrayAlbums.push(opts.defaultSortByNumberImages);
		defaultSortArrayAlbums.push(opts.defaultSortByDateCreated);
		defaultSortArrayAlbums.push(opts.defaultSortByDateUpdated);
		defaultSortArrayAlbums.push(opts.defaultSortByFacebookOrder);
		if (opts.showSelectionOnly) {
			defaultSortArrayAlbums.push(opts.defaultSortByPreSet);
		}
		defaultSortArrayPhotos.push(opts.defaultPhotoSortAdded);
		defaultSortArrayPhotos.push(opts.defaultPhotoSortUpdated);
		defaultSortArrayPhotos.push(opts.defaultPhotoSortOrder);
		defaultSortArrayPhotos.push(opts.defaultPhotoSortID);
		var checkSortSettingsAlbums = 	defaultSortArrayAlbums.frequencies();
		var checkSortSettingsPhotos = 	defaultSortArrayPhotos.frequencies();
		if (checkSortSettingsAlbums[true] != 1) {
			opts.defaultSortByAlbumTitle = true;
			opts.defaultSortByNumberImages = false;
			opts.defaultSortByDateCreated = false;
			opts.defaultSortByDateUpdated = false;
			opts.defaultSortByFacebookOrder = false;
			if (opts.showSelectionOnly) {
				opts.defaultSortByPreSet = false;
			}
		};
		if (checkSortSettingsPhotos[true] != 1) {
			opts.defaultPhotoSortAdded = true;
			opts.defaultPhotoSortUpdated = false;
			opts.defaultPhotoSortOrder = false;
			opts.defaultPhotoSortID = false;
		};

		// Check for Contradicting Scaling Settings and Auto Correct
		defaultThumbArrayScale.push(opts.weservImageScaler);
		defaultThumbArrayScale.push(opts.senchaImageScaler);
		defaultThumbArrayScale.push(opts.innerImageScaler);
		var checkScalingSettings = 	defaultThumbArrayScale.frequencies();
		if (checkScalingSettings[true] > 1) {
			opts.weservImageScaler = true;
			opts.senchaImageScaler = false;
			opts.innerImageScaler = false;
			imageScalerActive = true;
		} else if (checkScalingSettings[true] == 1) {
			imageScalerActive = true;
		} else {
			imageScalerActive = false;
		}

		// Determine Screen, Scrollbar & Gallery Size
		GetScreenDimensions();
		scrollBarWidth = 				scrollBarWidth();
		if (isInIFrame) {
			opts.floatingControlBar = 	false;
			GetIFrameDimensions();
			galleryWidth = 				iFrameWidth;
		} else {
			if ($("#" + galleryContainer).parent().prop("tagName").length > 0) {
				var parentElement =		$("#" + galleryContainer).parent();
				galleryWidth =			Math.round((opts.responsiveGallery == true ? ((parentElement.width() - scrollBarWidth) * opts.responsiveWidth / 100) : opts.fixedWidth));
			} else {
				galleryWidth =			Math.round((opts.responsiveGallery == true ? ((viewPortWidth - scrollBarWidth) * opts.responsiveWidth / 100) : opts.fixedWidth));
			}
		}
		if (opts.consoleLogging) {
			console.log("Usable Screen Size Detection: Width = " + viewPortWidth + "px / Height = " + viewPortHeight + "px / Width of Scrollbar: " + scrollBarWidth + "px / Width of Gallery: " + galleryWidth + "px");
		};

		// Determine Default Sort Settings for Albums & Photos
		if (opts.defaultSortByAlbumTitle) {
			if ((!opts.albumAllowSortName)) {opts.albumAllowSortName = true;};
			defaultSortTypeAlbums = 'albumTitle';
		} else if (opts.defaultSortByNumberImages) {
			if (!opts.albumAllowSortItems) {opts.albumAllowSortItems = true;};
			defaultSortTypeAlbums = 'numberItems';
		} else if (opts.defaultSortByDateCreated) {
			if	(!opts.albumAllowSortCreated) {opts.albumAllowSortCreated = true;};
			defaultSortTypeAlbums = 'createDate';
		} else if (opts.defaultSortByDateUpdated) {
			if	(!opts.albumAllowSortUpdate) {opts.albumAllowSortUpdate = true;};
			defaultSortTypeAlbums = 'updateDate';
		} else if (opts.defaultSortByFacebookOrder) {
			if	(!opts.albumAllowSortFacebook) {opts.albumAllowSortFacebook = true;};
			defaultSortTypeAlbums = 'orderFacebook';
		} else if (opts.defaultSortByFacebookID) {
			if	(!opts.albumAllowSortID) {opts.albumAllowSortID = true;};
			defaultSortTypeAlbums = 'FacebookID';
		} else if (opts.defaultSortByPreSet) {
			if	(!opts.albumAllowSortPreSet) {opts.albumAllowSortPreSet = true;};
			defaultSortTypeAlbums = 'orderPreSet';
		};
		if (opts.defaultPhotoSortAdded) {
			if (!opts.photoAllowSortAdded) {opts.photoAllowSortAdded = true;};
			defaultSortTypePhotos = 'addedDate';
		} else if (opts.defaultPhotoSortUpdated) {
			if (!opts.photoAllowSortUpdate) {opts.photoAllowSortUpdate = true;};
			defaultSortTypePhotos = 'updateDate';
		} else if (opts.defaultPhotoSortOrder) {
			if (!opts.photoAllowSortFacebook) {opts.photoAllowSortFacebook = true;};
			defaultSortTypePhotos = 'orderFacebook';
		} else if (opts.defaultPhotoSortID) {
			if (!opts.photoAllowSortID) {opts.photoAllowSortID = true;};
			defaultSortTypePhotos = 'FacebookID';
		};

		if ((!opts.allowAlbumDescription) && (!opts.showBottomControlBar)) {
			opts.showFloatingReturnButton = true;
		}

		if ((opts.innerImageScaler) && (opts.PathInternalPHP.length == 0)) {opts.innerImageScaler = false;};

        if (!opts.albumImageCount && !opts.albumDateCreate && !opts.albumDateUpdate && !opts.albumGoogleID) {albumsAllInfoDisabled = true;};

		// Check if Album Thumbnails should Match Photo Thumbnails and Adjust
		if (opts.matchAlbumPhotoThumbs) {
			opts.albumWrapperWidth = opts.photoThumbWidth;
			opts.albumThumbWidth = opts.photoThumbWidth;
			opts.albumThumbHeight = opts.photoThumbHeight;
			opts.albumWrapperMargin = opts.photoThumbMargin;
			opts.albumFrameOffset = 0;
			opts.albumThumbPadding = opts.photoThumbPadding;
			opts.albumShadowOffset = 0;
		} else {
			opts.albumThumbPadding = 0;
		};

		// Check which Lightbox Plugin should be Used
		defaultLightboxArray.push(opts.colorBoxAllow);
		defaultLightboxArray.push(opts.fancyBoxAllow);
		defaultLightboxArray.push(opts.prettyPhotoAllow);
		defaultLightboxArray.push(opts.photoBoxAllow);
		var checkLightboxSettings = defaultLightboxArray.frequencies();
		if (checkLightboxSettings[true] > 1) {
			opts.fancyBoxAllow = true;
			opts.colorBoxAllow = false;
			opts.prettyPhotoAllow = false;
			opts.photoBoxAllow = false;
			lightboxEnabled = true;
		} else if (checkLightboxSettings[true] == 1) {
			lightboxEnabled = true;
		} else {
			lightboxEnabled = false;
		}

		if (opts.AlbumShareMePreText.substr(opts.AlbumShareMePreText.length - 1) == ":") {opts.AlbumShareMePreText = opts.AlbumShareMePreText.slice(0, -1);};
		if (opts.ImageShareMePreText.substr(opts.ImageShareMePreText.length - 1) == ":") {opts.ImageShareMePreText = opts.ImageShareMePreText.slice(0, -1);};

		// Initialize Album Gallery
		function galleryAlbumsInit() {
			$('#fb-album-header').html("");
			$('#fb-album-footer').html("");
			if ($('#fb-albums-all-paged').length != 0) {
				//alert("Restored from 'galleryAlbumsInit'");
				if ($("#paginationControls-" + opts.facebookID).length != 0) {
					if ((opts.floatingControlBar) && (!isInIFrame)) {
						$("#paginationControls-" + opts.facebookID).unbind('stickyScroll');
						$("#paginationControls-" + opts.facebookID).stickyScroll('reset');
					};
				};
				$("#" + opts.loaderID).slideFade(700);
				$('#fb-albums-all-paged').slideFade(700);
				var $container = $('#fb-albums-all');
				$container.isotope('reloadItems');
				$container.isotope('reLayout');
				if (opts.infiniteScrollAlbums) {
					$('.albumWrapper:visible').each(function(i, elem) {
						$(this).addClass("Showing").addClass("Infinite");
					});
					$('#' + opts.infiniteAlbumsID).unbind('inview');
					infiniteGallery($container, true, false, opts.facebookID);
				}
				if ((opts.floatingControlBar) && (!isInIFrame)) {
					isotopeHeightContainer = $container.height();
					if (!opts.paginationLayoutAlbums) {
						$("#paginationControls-" + opts.facebookID).stickyScroll({ container: $("#fb-albums-all-paged") })
					}
				}
				$('html, body').animate({scrollTop:$("#" + opts.frameID).offset().top - 20}, 'slow', function() {
					if (opts.infiniteScrollAlbums) {
						$("#" + opts.infiniteAlbumsID).show();
					}
				});
				shortLinkAlbumShares();
			} else {
				$("<div>", {
					id : "fb-albums-all"
				}).appendTo("#fb-album-content");
				if (!opts.albumSortControls) {
					$("#fb-albums-all").css("padding-top", "10px");
				};
				galleryAlbumsShow();
			};
		};

		// Load and Show Album Gallery Thumbnails and Data
		function galleryAlbumsShow() {
			if (!opts.singleAlbumOnly) {
				if ((opts.maxNumberGalleries == 0) || (opts.showSelectionOnly)) {
					if (opts.facebookToken.length != 0) {
						var graph = "https://graph.facebook.com/" + opts.facebookID + "/albums?access_token=" + opts.facebookToken + "&fields=id,name,cover_photo,count,created_time,updated_time,description,link,type,location,place,from,privacy&limit=" + opts.maxGraphLimit + "&callback=?";
					} else {
						var graph = "https://graph.facebook.com/" + opts.facebookID + "/albums?fields=id,name,cover_photo,count,created_time,updated_time,description,link,type,location,place,from,privacy&limit=" + opts.maxGraphLimit + "&callback=?";
					}
				} else {
					var graphLimit = opts.excludeAlbums.length + opts.maxNumberGalleries + (opts.excludeTimeLine == true ? 1 : 0);
					if (opts.facebookToken.length != 0) {
						var graph = "https://graph.facebook.com/" + opts.facebookID + "/albums?access_token=" + opts.facebookToken + "&fields=id,name,cover_photo,count,created_time,updated_time,description,link,type,location,place,from,privacy&limit=" + graphLimit + "&callback=?";
					} else {
						var graph = "https://graph.facebook.com/" + opts.facebookID + "/albums?fields=id,name,cover_photo,count,created_time,updated_time,description,link,type,location,place,from,privacy&limit=" + graphLimit + "&callback=?";
					}
				}
				if (opts.outputGraphAlbums) {
					MessiContent = 	"<strong>Albums Overview:</strong><br/>" + graph.replace('&callback=?', '') + "";
					MessiCode = 	"anim success";
					MessiTitle = 	"Graph Links for Facebook ID: " + opts.facebookID;
					showMessiContent(MessiContent, MessiTitle, MessiCode);
				}
				if (opts.consoleLogging) {
					console.log("Usable JSON Feed for Gallery: " + graph);
				};
				$.ajax({
					url: 			graph,
					cache: 			false,
					dataType: 		"jsonp",
					success: function(json) {
						MessiContent = "";
						$.each(json.data, function(k, albums){
							if (typeof albums.cover_photo !== "undefined") {
								if (typeof(albums.count) != "undefined") {
									if (opts.outputCountAlbumID) {
										MessiContent += "<div style='margin: 10px 0px; text-align: justify;'><strong>Album #" + k + " - " + albums.name + "</strong><br/>ID: " + albums.id + " / Photos: " + albums.count + "</div>";
									}
									if (((opts.showSelectionOnly) && ($.inArray(albums.id, opts.includeAlbums) > -1)) || ((!opts.showSelectionOnly) && ($.inArray(albums.id, opts.excludeAlbums) == -1))) {
										counterA = counterA + 1;
										if ((counterA <= opts.maxNumberGalleries) || (opts.maxNumberGalleries === 0) || ((opts.showSelectionOnly) && (counterA <= opts.includeAlbums.length))) {
											if ((albums.count > opts.maxNumberImages) && (opts.maxNumberImages != 0)) {
												var countTxt = opts.maxNumberImages + " ";
											} else {
												var countTxt = albums.count + " ";
											}
											// Convert ISO-8601 Dates into readable Format
											if (opts.albumDateCreate) {
												var timeStampA = new XDate((albums.created_time.length == 10 ? albums.created_time * 1000 : albums.created_time));
												if (opts.albumCreateFromNow) {
													timeStampA = moment(timeStampA).fromNow();
												} else {
													var timeStampA_Zone = Math.abs(timeStampA.clone().toString("z"));
													timeStampA = timeStampA.clone().addHours(timeStampA_Zone).toString("MM/dd/yyyy - hh:mm TT");
												}
											}
											if (opts.albumDateUpdate) {
												var timeStampB = new XDate((albums.updated_time.length == 10 ? albums.updated_time * 1000 : albums.updated_time));
												if (opts.albumUpdateFromNow) {
													timeStampB = moment(timeStampB).fromNow()
												} else {
													var timeStampB_Zone = Math.abs(timeStampB.clone().toString("z"));
													timeStampB = timeStampB.clone().addHours(timeStampB_Zone).toString("MM/dd/yyyy - hh:mm TT");
												}
											}
											if (this.count > 1) {
												countTxt += opts.MultiImagesWord;
											} else {
												countTxt += opts.SingleImageWord;
											}
											if ((this.count > opts.maxNumberImages) && (opts.maxNumberImages != 0)) {
												countTxt += " (" + opts.OutOfTotalImagesPreText + " " + albums.count + " " + opts.MultiImagesWord + ")";
											}
											if (!opts.matchAlbumPhotoThumbs) {
												var clear = 'width: ' + (opts.albumWrapperWidth + opts.albumFrameOffset * 2) + 'px; margin: ' + opts.albumWrapperMargin + 'px; display: none;';
											} else {
												var clear = 'width: ' + (opts.albumWrapperWidth + 10) + 'px; margin: ' + opts.albumWrapperMargin + 'px; display: none;';
											}
											if (opts.createTooltipsAlbums) {var tooltips = " TipGallery";} else {var tooltips = "";};

											var html = "";
											if (albums.name) {
												var nameSummary = truncateString(albums.name, opts.albumTitleSummaryLength, ' ', ' ...');
											} else {
												var nameSummary = truncateString(opts.AlbumNoDescription, opts.albumTitleSummaryLength, ' ', ' ...');
											}

											if ((opts.albumNameTitle) && (opts.albumNameAbove)) {html += '<div class="albumHead fbLink"><span class="albumNameHead' + tooltipClass + '" data-albumid="' + albums.id + '" ' + opts.tooltipTipAnchor + '="' + albums.name + '">' + albums.name + '</span></div>';}

											if (!opts.matchAlbumPhotoThumbs) {
												html += '<div id="' + albums.id + '" class="albumThumb fbLink' + tooltips + (opts.albumCCS3Shadow == true ? " ShadowCSS3" : "") + tooltipClass + '" ' + opts.tooltipTipAnchor + '="' + albums.name + '" data-link="' + albums.link + '" style="width:' + (opts.albumThumbWidth) + 'px; height:' + (opts.albumThumbHeight) + 'px; padding: ' + opts.albumFrameOffset + 'px;" data-href="#album-' + albums.id + '" data-user="' + opts.facebookID + '">';
											} else {
												html += '<div id="' + albums.id + '" class="albumThumb fbLink' + tooltips + (opts.albumCCS3Shadow == true ? " ShadowCSS3" : "") + tooltipClass + '" ' + opts.tooltipTipAnchor + '="' + albums.name + '" data-link="' + albums.link + '" style="width:' + (opts.albumThumbWidth) + 'px; height:' + (opts.albumThumbHeight) + 'px; padding: 5px;" data-href="#album-' + albums.id + '" data-user="' + opts.facebookID + '">';
											}
												if (!opts.matchAlbumPhotoThumbs) {
													if (opts.albumShowPaperClipL) 	{html += '<span class="PaperClipLeft"></span>';}
													if (opts.albumShowPaperClipR) 	{html += '<span class="PaperClipRight" style="left: ' + (opts.albumWrapperWidth - 30) + 'px;"></span>';}
													if (opts.albumShowPushPin) 		{html += '<span class="PushPin" style="left: ' + (Math.ceil(opts.albumWrapperWidth / 2)) + 'px;"></span>';}
													if (opts.albumShowShadow) {
														if ((!opts.albumShadowA) && (!opts.albumShadowB) && (!opts.albumShadowC)) {
															html += '<div class="fb-album-shadow1" style="top: ' + (opts.albumThumbHeight + opts.albumShadowOffset) + 'px;"></div>';
														} else if (opts.albumShadowA){
															html += '<div class="fb-album-shadow1" style="top: ' + (opts.albumThumbHeight + opts.albumShadowOffset) + 'px;"></div>';
														} else if (opts.albumShadowB){
															html += '<div class="fb-album-shadow2" style="top: ' + (opts.albumThumbHeight + opts.albumShadowOffset) + 'px;"></div>';
														} else if (opts.albumShadowC){
															html += '<div class="fb-album-shadow3" style="top: ' + (opts.albumThumbHeight + opts.albumShadowOffset) + 'px;"></div>';
														}
													}
												} else {
													if (opts.photoShowClearTape) 	{html += '<span class="ClearTape" style="left: ' + (Math.ceil((opts.albumThumbWidth + opts.albumWrapperMargin + opts.albumThumbPadding - 77) / 2)) + 'px;"></span>';}
													if (opts.photoShowYellowTape) 	{html += '<span class="YellowTape" style="left: ' + (Math.ceil((opts.albumThumbWidth + opts.albumWrapperMargin + opts.albumThumbPadding - 115) / 2)) + 'px;"></span>';}
													if (opts.photoShowPushPin) 		{html += '<span class="PushPin" style="left: ' + (Math.ceil((opts.albumThumbWidth + opts.albumWrapperMargin + opts.albumThumbPadding) / 2)) + 'px;"></span>';}
												}
												if (opts.albumFrameOffset == 0) 	{html += '<span id="Wrap_' + albums.id + '" style="border: none;" class="albumThumbWrap" style="width:' + opts.albumThumbWidth + 'px; height:' + opts.albumThumbHeight + 'px; padding: ' + opts.albumFrameOffset + 'px; left: ' + opts.albumFrameOffset + 'px; top: ' + opts.albumFrameOffset + 'px;">';
												} else 								{html += '<span id="Wrap_' + albums.id + '" class="albumThumbWrap" style="width:' + opts.albumThumbWidth + 'px; height:' + opts.albumThumbHeight + 'px; padding: ' + opts.albumFrameOffset + 'px; left: ' + opts.albumFrameOffset + 'px; top: ' + opts.albumFrameOffset + 'px;">';}
												if (opts.imageSpinnerAnimation) {
													if (!opts.matchAlbumPhotoThumbs) 	{html += '<i class="fb-album-spinner" id="fb-album-spinner-' + albums.id + '" style="width:' + opts.albumThumbWidth + 'px; height:' + opts.albumThumbHeight + 'px;"></i>';
													} else 								{html += '<i class="fb-album-spinner" id="fb-album-spinner-' + albums.id + '" style="width:' + opts.albumThumbWidth + 'px; height:' + opts.albumThumbHeight + 'px; top: 0px; left: 0px;"></i>';}
												}
												if (imageScalerActive) {
													html += '<i class="fb-album-thumb" id="fb-album-thumb-' + albums.id + '" style="width:' + opts.albumThumbWidth + 'px; height:' + opts.albumThumbHeight + 'px;"></i>';
												} else {
													html += '<i class="fb-album-thumb noscaler" id="fb-album-thumb-' + albums.id + '" style="background-size:' + opts.albumThumbWidth + 'px; width:' + opts.albumThumbWidth + 'px; height:' + opts.albumThumbHeight + 'px;"></i>';
												}
												if (!opts.matchAlbumPhotoThumbs) 	{html += '<i class="fb-album-overlay" id="fb-album-overlay-' + albums.id + '" data-album="' + albums.id + '" style="width:' + opts.albumThumbWidth + 'px; height:' + opts.albumThumbHeight + 'px; padding: ' + opts.albumFrameOffset + 'px;"></i>';
												} else 								{html += '<i class="fb-album-overlay" id="fb-album-overlay-' + albums.id + '" data-album="' + albums.id + '" style="width:' + opts.albumThumbWidth + 'px; height:' + opts.albumThumbHeight + 'px; padding: ' + opts.albumThumbPadding + 'px; left: -' + opts.albumThumbPadding + 'px; top: -' + opts.albumThumbPadding + 'px; "></i>';}
												if ((opts.albumShowSocialShare) && (opts.albumThumbSocialShare)) {
													var AlbumSocialShareLink = albums.link;
													AlbumIDsArray.push({id:albums.id, link:AlbumSocialShareLink, clean:"", thumb:"", summary:nameSummary});
													if (!opts.matchAlbumPhotoThumbs) 	{
														html += '<i class="fb-album-shareme" id="fb-album-shareme-' + albums.id + '" data-album="' + albums.id + '" style="width:' + opts.albumThumbWidth + 'px; padding: ' + opts.albumFrameOffset + 'px;">';
														html += '<ul id="socialcount_' + albums.id + '" class="socialcount" style="float: right; display: ' + (opts.albumShortSocialShare == true ? " none;" : "block;") + '">';
															html += '<li class="stumbleupon"><a id="AlbumSocialShare_Stumble_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="http://www.stumbleupon.com/submit?url=' + AlbumSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li>';
															html += '<li class="googleplus"><a id="AlbumSocialShare_Google_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="https://plus.google.com/share?url=' + AlbumSocialShareLink + '&title=' + opts.SocialShareAlbumText + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Google Plus"><span class="social-icon icon-googleplus"></span></a></li>';
															html += '<li class="twitter"><a id="AlbumSocialShare_Twitter_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="https://twitter.com/intent/tweet?text=' + fixedEncodeURIComponent(opts.SocialShareAlbumText) + AlbumSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Twitter"><span class="social-icon icon-twitter"></span></a></li>';
															html += '<li class="facebook"><a id="AlbumSocialShare_Facebook_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=' + AlbumSocialShareLink + '&p[images][0]=' + AlbumSocialShareLink + '&p[title]=' + opts.SocialShareAlbumText + '&p[summary]=' + nameSummary + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
														html += '</ul>';
														html += '</i>';
													} else {
														html += '<i class="fb-album-shareme" id="fb-album-shareme-' + albums.id + '" data-album="' + albums.id + '" style="width:' + opts.albumThumbWidth + 'px; padding: ' + opts.albumThumbPadding + 'px; left: -' + opts.albumThumbPadding + 'px; top: -' + opts.albumThumbPadding + 'px; ">';
														html += '<ul id="socialcount_' + albums.id + '" class="socialcount" style="float: right; display: ' + (opts.albumShortSocialShare == true ? " none;" : "block;") + '">';
															html += '<li class="stumbleupon"><a id="AlbumSocialShare_Stumble_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="http://www.stumbleupon.com/submit?url=' + AlbumSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li>';
															html += '<li class="googleplus"><a id="AlbumSocialShare_Google_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="https://plus.google.com/share?url=' + AlbumSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Google Plus"><span class="social-icon icon-googleplus"></span></a></li>';
															html += '<li class="twitter"><a id="AlbumSocialShare_Twitter_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="https://twitter.com/intent/tweet?text=' + fixedEncodeURIComponent(opts.SocialShareAlbumText) + AlbumSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Twitter"><span class="social-icon icon-twitter"></span></a></li>';
															html += '<li class="facebook"><a id="AlbumSocialShare_Facebook_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=' + AlbumSocialShareLink + '&p[images][0]=' + AlbumSocialShareLink + '&p[title]=' + opts.SocialShareAlbumText + '&p[summary]=' + nameSummary + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
														html += '</ul>';
														html += '</i>';
													}
												}
												html += '</span>';
											html += '</div>';

											html += '<div id="albumDetails_' + albums.id + '" class="albumDetails' + tooltipClass + '" ' + opts.tooltipTipAnchor + '="' + albums.id + '" style="width:' + (opts.albumWrapperWidth + 2 * opts.albumThumbPadding) + 'px; padding-top: ' + ((opts.albumShowShadow == true ? opts.albumShadowOffset : 0) + opts.albumInfoOffset) + 'px;">';
												if ((opts.albumShowSocialShare) && (!opts.albumThumbSocialShare)) {
													html += '<div id="albumShare_' + albums.id + '" class="albumShare clearFixMe" style="height: 30px; width: ' + (opts.albumWrapperWidth + opts.albumFrameOffset + 2 * opts.albumThumbPadding) + 'px;' + (albumsAllInfoDisabled == true ? " border-bottom: none; margin-bottom: 0px;" : "") + '">';
														if (opts.albumShowOrder) {
															html += '<span id="albumSocial_' + albums.id + '" class="albumSocial">' + opts.AlbumShareMePreText + ' (#' + counterA + '):</span>';
														} else {
															html += '<span id="albumSocial_' + albums.id + '" class="albumSocial">' + opts.AlbumShareMePreText + ':</span>';
														}
														var AlbumSocialShareLink = albums.link;
														AlbumIDsArray.push({id:albums.id, link:AlbumSocialShareLink, clean:"", thumb:"", summary:nameSummary});
														html += '<ul id="socialcount_' + albums.id + '" class="socialcount" style="float: right; display: ' + (opts.albumShortSocialShare == true ? " none;" : "block;") + '">';
															html += '<li class="stumbleupon"><a id="AlbumSocialShare_Stumble_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="http://www.stumbleupon.com/submit?url=' + AlbumSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li>';
															html += '<li class="googleplus"><a id="AlbumSocialShare_Google_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="https://plus.google.com/share?url=' + AlbumSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Google Plus"><span class="social-icon icon-googleplus"></span></a></li>';
															html += '<li class="twitter"><a id="AlbumSocialShare_Twitter_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="https://twitter.com/intent/tweet?text=' + fixedEncodeURIComponent(opts.SocialShareAlbumText) + AlbumSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Twitter"><span class="social-icon icon-twitter"></span></a></li>';
															//html += '<li class="facebook"><a id="AlbumSocialShare_Facebook_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' + AlbumSocialShareLink + '&title=' + opts.SocialShareAlbumText + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
															html += '<li class="facebook"><a id="AlbumSocialShare_Facebook_' + albums.id + '" class="AlbumSocialShare TipSocial' + tooltipClass + '" target="_blank" href="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=' + AlbumSocialShareLink + '&p[images][0]=' + AlbumSocialShareLink + '&p[title]=' + opts.SocialShareAlbumText + '&p[summary]=' + nameSummary + '" ' + opts.tooltipTipAnchor + '="Share Album &#34;' + albums.name + '&#34; on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
														html += '</ul>';
													html += '</div>';
												}

												html += '<div id="albumText_' + albums.id + '" class="albumText">';
													if ((opts.albumNameTitle) && (!opts.albumNameAbove)) 	{html += '<div class="fbLink" style="width: ' + opts.albumWrapperWidth + 'px;"><span class="albumName' + tooltipClass + '" data-albumid="' + albums.id + '" ' + opts.tooltipTipAnchor + '="' + albums.name + '">' + albums.name + '</span></div>';}
													if (opts.albumImageCount) 		{html += '<div class="clearFixMe" style="width: ' + opts.albumWrapperWidth + 'px; display: block;"><div class="albumCount">' + opts.AlbumContentPreText + '</div><div class="albumInfo clearFixMe">' + countTxt + '</div></div>';}
													if (opts.albumDateCreate) 		{html += '<div class="clearFixMe" style="width: ' + opts.albumWrapperWidth + 'px; display: block;"><div class="albumCreate">' + opts.AlbumCreatedPreText + '</div><div class="albumInfo clearFixMe">' + timeStampA + '</div></div>';}
													if (opts.albumDateUpdate) 		{html += '<div class="clearFixMe" style="width: ' + opts.albumWrapperWidth + 'px; display: block;"><div class="albumUpdate">' + opts.AlbumUpdatedPreText + '</div><div class="albumInfo clearFixMe">' + timeStampB + '</div></div>';}
													if (opts.albumFacebookID) 		{html += '<div class="clearFixMe" style="width: ' + opts.albumWrapperWidth + 'px; display: block;"><div class="albumNumber">' + opts.AlbumNumericIDPreText + '</div><div class="albumInfo clearFixMe">' + this.id + '</div></div>';}
												html += '</div>';
											html += '</div>';

											if (((counterA <= opts.maxNumberGalleries) && (opts.maxNumberGalleries > 0)) || (opts.maxNumberGalleries === 0)) {
												var coverType = albums.type;
												if (opts.useAlbumsUpdated) {
													var albumTime = moment(albums.updated_time).fromNow().replace(/ /g,"_");
													var albumUTC = (albums.updated_time.length == 10 ? albums.updated_time * 1000 : albums.updated_time);
												} else {
													var albumTime = moment(albums.created_time).fromNow().replace(/ /g,"_");
													var albumUTC = (albums.created_time.length == 10 ? albums.created_time * 1000 : albums.created_time);
												}
												if (opts.showSelectionOnly) {
													var albumPreSet = getIndexByAttribute(AlbumPreSetArray, "id", albums.id);
												} else {
													var albumPreSet = "";
												}
												if (((!opts.excludeTimeLine) && (coverType == "wall")) || (coverType != "wall")) {
													$("<div>", {
														"class": 		"albumWrapper " + albumTime,
														"id": 			"coverWrapper-" + albums.id,
														"data-title":	albums.name,
														"data-cover":	albums.cover_photo,
														"data-create":	(albums.created_time.length == 10 ? albums.created_time * 1000 : albums.created_time),
														"data-update":	(albums.updated_time.length == 10 ? albums.updated_time * 1000 : albums.updated_time),
														"data-count":	albums.count,
														"data-number":	albums.id,
														"data-order":	counterA,
														"data-preset":	albumPreSet,
														"data-time":	albumTime,
														"data-UTC":		albumUTC,
														"data-id":		albums.id,
														"data-type":	coverType,
														"data-user":	opts.facebookID,
														style: 			clear,
														html : 			html
													}).appendTo("#fb-albums-all").fadeIn(500, function(){});
													if ((opts.albumShowSocialShare) && (opts.albumThumbSocialShare)) {
														$('body').on('click', '#' + albums.id, function(event){
															$(this).find(".fb-album-overlay").stop().animate({opacity: 0}, "slow");
															if (opts.infiniteScrollAlbums) {
																$('#' + opts.infiniteAlbumsID).unbind('inview').hide();
															}
															checkExisting($(this).attr('data-href'));
														}).on('click', '.fb-album-shareme', function(event) {
															event.stopPropagation();
														});
													} else {
														$('body').on('click', '#' + albums.id, function(event){
															$(this).find(".fb-album-overlay").stop().animate({opacity: 0}, "slow");
															if (opts.infiniteScrollAlbums) {
																$('#' + opts.infiniteAlbumsID).unbind('inview').hide();
															}
															checkExisting($(this).attr('data-href'));
														});
													}
													var coverCount = counterA - 1;
													var coverAlbum = "";
													var coverID = "";
													if (opts.consoleLogging) {
														if (albums.count > 1) {
															if ((opts.maxNumberImages > 0) && (albums.count > opts.maxNumberImages)) {
																var coverContent = opts.maxNumberImages + " out of " + albums.count + " Images";
															} else {
																var coverContent = albums.count + " Images";
															}
														} else {
															if ((opts.maxNumberImages > 0) && (albums.count > opts.maxNumberImages)) {
																var coverContent = opts.maxNumberImages + " out of " + albums.count + " Image";
															} else {
																var coverContent = albums.count + " Image";
															}
														}
													};
													if (coverType == "wall") {
														if (opts.facebookToken.length != 0) {
															var cover = "https://graph.facebook.com/" + opts.facebookID + "/albums?access_token=" + opts.facebookToken + "&fields=cover&callback=?";
														} else {
															var cover = "https://graph.facebook.com/" + opts.facebookID + "/albums?fields=cover&callback=?";
														}
														$.ajax({
															url: 			cover,
															cache: 			false,
															dataType: 		"jsonp",
															success: function(data){
																$.each([data], function(i, item){
																	coverAlbum = "Timeline";
																	coverID = item.cover.cover_id;
																	coverCount++;
																	if (opts.innerImageScaler) {
																		var imgcover = '' + opts.PathInternalPHP + '?src=' + (item.cover.source) + '&w=' + (opts.albumThumbWidth) + '&zc=1';
																	} else if (opts.senchaImageScaler) {
																		var imgcover = 'http://src.sencha.io/' + (opts.albumThumbWidth) + '/' + (item.cover.source);
																	} else if (opts.weservImageScaler) {
																		var imgcover = 'http://images.weserv.nl/?url=' + (item.cover.source.replace("https://", "").replace("http://", "")) + '&h=' + (opts.albumThumbHeight) + '&w=' + (opts.albumThumbWidth) + '&t=fit';
																	} else {
																		var imgcover = item.cover.source;
																	}
																	var fileExtension = item.cover.source.substring(item.cover.source.lastIndexOf('.') + 1).toUpperCase();
																	$("#coverWrapper-" + albums.cover_photo).attr("data-type", fileExtension).addClass(fileExtension);
																	$.each(AlbumIDsArray, function() {
																		if (this.id == albums.id) {
																			this.thumb = item.cover.source;
																		}
																	});
																	if ((opts.imageLazyLoad) && ($.isFunction($.fn.lazyloadanything))) {
																		$("#fb-album-thumb-" + albums.id).attr("data-original", imgcover).attr("data-cover", albums.cover_photo).attr("data-album", albums.id).attr("data-loaded", "FALSE");
																	} else {
																		$("#fb-album-thumb-" + albums.id).attr("data-cover", albums.cover_photo).attr("data-album", albums.id).attr("data-loaded", "FALSE");
																		if ((opts.imageSpinnerAnimation) && ($.isFunction($.fn.waitForImages))) {
																			$("#fb-album-thumb-" + albums.id).hide().css("background-image", "url(" + (imgcover) + ")");
																			$("#fb-album-thumb-" + albums.id).waitForImages({
																				waitForAll: 	true,
																				finished: 		function() {},
																				each: 			function() {
																					var $album 	= $(this).attr('data-album');
																					if ($("#fb-album-thumb-" + $album).attr("data-loaded") == "FALSE") {
																						$("#fb-album-spinner-" + $album).hide();
																						$("#fb-album-thumb-" + $album).fadeIn(500);
																						$("#fb-album-thumb-" + $album).attr("data-loaded", "TRUE");
																					};
																				},
																			});
																		} else {
																			$("#fb-album-thumb-" + albums.id).css("background-image", "url(" + (imgcover) + ")");
																			$("#fb-album-spinner-" + albums.id).hide();
																		}
																	}
																});
																if (opts.consoleLogging) {
																	console.log('Update: Cover Photo (' + coverID + ') link for album #' + coverCount + '(' + coverAlbum + ' / ' + coverContent + ') could be successfully retrieved!');
																}
															},
															error: function(jqXHR, textStatus, errorThrown){
																console.log('Error: \njqXHR:' + jqXHR + '\ntextStatus: ' + textStatus + '\nerrorThrown: '  + errorThrown);
															}
														});
													} else {
														if (opts.facebookToken.length != 0) {
															var cover = "https://graph.facebook.com/" + albums.cover_photo + "?access_token=" + opts.facebookToken + "&fields=album,from,created_time,height,width,source,name,link,picture&callback=?";
														} else {
															var cover = "https://graph.facebook.com/" + albums.cover_photo + "?fields=album,from,created_time,height,width,source,name,link,picture&callback=?";
														}
														$.ajax({
															url: 			cover,
															cache: 			false,
															dataType: 		"jsonp",
															success: function(data){
																$.each([data], function(i, item){
																	coverAlbum = albums.id;
																	coverID = item.id;
																	coverCount++;
																	if (typeof(coverID) === "undefined") {
																		if (/^[a-z]+:\/\//i.test(opts.PathNoCoverImage)) {
																			var pathname = opts.PathNoCoverImage;
																		} else {
																			var pathname = getAbsolutePath();
																			pathname += opts.PathNoCoverImage;
																		}
																		if (opts.innerImageScaler) {
																			var imgcover = '' + opts.PathInternalPHP + '?src=' + (pathname) + '&w=' + (opts.albumThumbWidth) + '&zc=1';
																		} else if (opts.senchaImageScaler) {
																			var imgcover = 'http://src.sencha.io/' + (opts.albumThumbWidth) + '/' + (pathname);
																		} else if (opts.weservImageScaler) {
																			var imgcover = 'http://images.weserv.nl/?url=' + (pathname.replace("https://", "").replace("http://", "")) + '&h=' + (opts.albumThumbHeight) + '&w=' + (opts.albumThumbWidth) + '&t=fit';
																		} else {
																			var imgcover = pathname;
																		}
																	} else {
																		var pathname = item.source;
																		if (opts.innerImageScaler) {
																			var imgcover = '' + opts.PathInternalPHP + '?src=' + (item.source) + '&w=' + (opts.albumThumbWidth) + '&zc=1';
																		} else if (opts.senchaImageScaler) {
																			var imgcover = 'http://src.sencha.io/' + (opts.albumThumbWidth) + '/' + (item.source);
																		} else if (opts.weservImageScaler) {
																			var imgcover = 'http://images.weserv.nl/?url=' + (item.source.replace("https://", "").replace("http://", "")) + '&h=' + (opts.albumThumbHeight) + '&w=' + (opts.albumThumbWidth) + '&t=fit';
																		} else {
																			var imgcover = item.source;
																		}
																	}
																	if (typeof(item.source)  === "undefined") {
																		var fileExtension = "N/A";
																	} else {
																		var fileExtension = item.source.substring(item.source.lastIndexOf('.') + 1).toUpperCase();
																	}
																	$("#coverWrapper-" + albums.cover_photo).attr("data-type", fileExtension).addClass(fileExtension);
																	$.each(AlbumIDsArray, function() {
																		if (this.id == albums.id) {
																			this.thumb = pathname;
																		}
																	});
																	if ((opts.imageLazyLoad) && ($.isFunction($.fn.lazyloadanything))) {
																		$("#fb-album-thumb-" + albums.id).attr("data-original", imgcover).attr("data-cover", albums.cover_photo).attr("data-album", albums.id).attr("data-loaded", "FALSE");
																	} else {
																		$("#fb-album-thumb-" + albums.id).attr("data-cover", albums.cover_photo).attr("data-album", albums.id).attr("data-loaded", "FALSE");
																		if ((opts.imageSpinnerAnimation) && ($.isFunction($.fn.waitForImages))) {
																			$("#fb-album-thumb-" + albums.id).hide().css("background-image", "url(" + (imgcover) + ")");
																			$("#fb-album-thumb-" + albums.id).waitForImages({
																				waitForAll: 	true,
																				finished: 		function() {},
																				each: 			function() {
																					var $album 	= $(this).attr('data-album');
																					if ($("#fb-album-thumb-" + $album).attr("data-loaded") == "FALSE") {
																						$("#fb-album-spinner-" + $album).hide();
																						$("#fb-album-thumb-" + $album).fadeIn(500);
																						$("#fb-album-thumb-" + $album).attr("data-loaded", "TRUE");
																					};
																				},
																			});
																		} else {
																			$("#fb-album-thumb-" + albums.id).css("background-image", "url(" + (imgcover) + ")");
																			$("#fb-album-spinner-" + albums.id).hide();
																		}
																	}
																});
																if (opts.consoleLogging) {
																	console.log('Update: Cover Photo (' + coverID + ') link for album #' + coverCount + '(' + coverAlbum + ' / ' + coverContent + ') could be successfully retrieved!');
																}
															},
															error: function(jqXHR, textStatus, errorThrown){
																console.log('Error: \njqXHR:' + jqXHR + '\ntextStatus: ' + textStatus + '\nerrorThrown: '  + errorThrown);
															}
														});
													}
												} else {
													counterA = counterA - 1;
												}
											}
										}
									} else {}
								}
							}
						});
						if (opts.outputCountAlbumID) {
							MessiCode = 	"anim success";
							MessiTitle = 	"Album Information Summary for Facebook User: " + opts.facebookID;
							showMessiContent(MessiContent, MessiTitle, MessiCode);
						}
						if (opts.consoleLogging) {
							console.log('Update: Data for ' + counterA + ' album(s) for Facebook ID "' + opts.facebookID + '" could be successfully retrieved!');
						}
						// Insert Code to Function Repeat if multiple Accounts

						var $container = $('#fb-albums-all');
						$("#" + opts.loaderID).slideFade(700);
						$("#FB_Album_Display").slideFade(700);
						// Initialize Paging Feature
						equalHeightFloat(true, opts.facebookID);
						currentPageList = $container;
						setTimeout(function(){
							if (!opts.paginationLayoutAlbums) {
								if (opts.infiniteScrollAlbums) {
									if (opts.infiniteScrollAlbumsSmart) {
										infiniteAlbums = smartAlbumsPerPage;
										var albumItemsPerPage = smartAlbumsPerPage;
									} else {
										infiniteAlbums = opts.infiniteScrollAlbumsBlock;
										var albumItemsPerPage = opts.infiniteScrollAlbumsBlock;
									}
									infiniteAlbumsCount = counterA;
								} else {
									var albumItemsPerPage = counterA;
								}
							} else if (opts.smartAlbumsPerPageAllow) {
								var albumItemsPerPage = smartAlbumsPerPage;
							} else {
								if (opts.setAlbumsByPages) {
									var albumItemsPerPage = ((opts.paginationLayoutAlbums = true && opts.numberPagesForAlbums > 0) ? (Math.ceil(counterA / opts.numberPagesForAlbums)) : counterA);
								} else {
									var albumItemsPerPage = ((opts.paginationLayoutAlbums = true && opts.numberAlbumsPerPage > 0) ? opts.numberAlbumsPerPage : counterA);
								}
							}
							var AlbumSettings = {
								'searchBoxDefault' 		: 	opts.SearchDefaultText,
								'itemsPerPageDefault' 	: 	albumItemsPerPage,
								'hideToTop'				:	(opts.showFloatingToTopButton == true ? false : true),
								'hideFilter' 			: 	(opts.albumsFilterAllow == true ? false : true),
								'hideSort' 				: 	(opts.albumSortControls == true ? false : true),
								'hideSearch' 			: 	(opts.albumSearchControls == true ? false : true),
								'hidePager'				:	((opts.albumsPagerControls == true && !opts.infiniteScrollAlbums) ? false : true)
							};
							new CallPagination(currentPageList, AlbumSettings, "fb-albums-all-paged", true, true, opts.facebookID, totalItems);
						}, 500);
						// Initialize LazyLoad for Thumbnails
						if ((opts.imageLazyLoad) && ($.isFunction($.fn.lazyloadanything))) {
							$('.fb-album-thumb').lazyloadanything({
								'auto': 			true,
								'repeatLoad':		true,
								'onLoadingStart': 	function(e, LLobjs, indexes) {
									return true
								},
								'onLoad': 			function(e, LLobj) {
									var $img 	= LLobj.$element;
									var $src 	= $img.attr('data-original');
									var $album 	= $img.attr('data-album');
									if (($('#fb-albums-all-paged').is(':visible')) && $("#fb-album-thumb-" + $album).is(':visible')) {
										if ((opts.imageSpinnerAnimation) && ($.isFunction($.fn.waitForImages))) {
											if ($("#fb-album-thumb-" + $album).attr("data-loaded") == "FALSE") {
												$img.hide().css('background-image', 'url("' + $src + '")');
												$img.waitForImages({
													waitForAll: 	true,
													finished: 		function() {},
													each: 			function() {
														if ($("#coverWrapper-" + $album).css('display') != 'none') {
															$("#fb-album-spinner-" + $album).hide();
															$("#fb-album-thumb-" + $album).fadeIn(500);
															$("#fb-album-thumb-" + $album).attr("data-loaded", "TRUE");
														};
													},
												});
											}
										} else {
											if ($("#fb-album-thumb-" + $album).attr("data-loaded") == "FALSE") {
												$img.hide().css('background-image', 'url("' + $src + '")').fadeIn(500);
												$("#fb-album-thumb-" + $album).attr("data-loaded", "TRUE");
											}
										}
									};
								},
								'onLoadComplete':	function(e, LLobjs, indexes) {
									return true
								}
							});
							restartLazyLoad();
						} else {
							if ((opts.imageSpinnerAnimation) && ($.isFunction($.fn.waitForImages))) {
								$('.albumWrapper .fb-album-thumb').waitForImages({
									waitForAll: true,
									finished: function() {},
									each: function() {
										var $album 	= $(this).attr("data-album");
										var $cover 	= $$(this).attr('data-cover');
										if ($("#fb-album-thumb-" + $cover).attr("data-loaded") == "FALSE") {
											$("#fb-album-spinner-" + $cover).hide();
											$("#fb-album-thumb-" + $cover).hide().fadeIn(500);
											$("#fb-album-thumb-" + $cover).attr("data-loaded", "TRUE");
										};
									},
								});
							} else {
								$('.albumWrapper .fb-album-thumb').each(function(index) {
									var $album 	= $(this).attr("data-album");
									var $cover 	= $(this).attr('data-cover');
									if ($("#fb-album-thumb-" + $cover).attr("data-loaded") == "FALSE") {
										$("#fb-album-thumb-" + $cover).hide().fadeIn(500);
										$("#fb-album-thumb-" + $cover).attr("data-loaded", "TRUE");
									};
								});
							}
						}
						shortLinkAlbumShares("");
						// Adjust Height of iFrame Container (if applicable)
						AdjustIFrameDimensions();
					},
					error: function(jqXHR, textStatus, errorThrown){
						if (opts.consoleLogging) {
							console.log('Error: \njqXHR:' + jqXHR + '\ntextStatus: ' + textStatus + '\nerrorThrown: '  + errorThrown);
						}
					}
				});
			} else {
				$("#" + opts.loaderID).show();
				singleAlbumInit();
			}
		};

		// Initialize Single Album Preview
		function singleAlbumInit() {
			if (opts.infiniteScrollPhotos) {
				$('#' + opts.infiniteMoreID).hide();
			}
			$("#" + opts.loaderID).slideFade(700);
			if (opts.cacheAlbumContents) {
				if (($('#fb-album-paged-' + albumId).length != 0) || ($('#fb-album-' + albumId).length != 0)) {
					//alert("Restore from 'singleAlbumInit'");
					if ($("#paginationControls-" + albumId).length != 0) {
						if ((opts.floatingControlBar) && (!isInIFrame)) {
							$("#paginationControls-" + albumId).unbind('stickyScroll');
							$("#paginationControls-" + albumId).stickyScroll('reset');
						};
					};
					$('#fb-album-header').html(headerArray[albumId]);
					if (opts.showBottomControlBar) {
						$('#fb-album-footer').html(footerArray[albumId]);
					};
					$('#Back-' + albumId + '_1').unbind("click").bind('click', function(e){
						if (opts.infiniteScrollPhotos) {
							$('#' + opts.infinitePhotosID).unbind('inview');
						}
						checkExisting($(this).attr('data-href'));
					});
					if (opts.showBottomControlBar) {
						$('#Back-' + albumId + '_2').unbind("click").bind('click', function(e){
							if (opts.infiniteScrollPhotos) {
								$('#' + opts.infinitePhotosID).unbind('inview');
							}
							checkExisting($(this).attr('data-href'));
						});
						$('#Back_To_Top-' + albumId).click(function(e){
							$('html, body').animate({scrollTop:$("#" + opts.frameID).offset().top - 20}, 'slow');
						});
					};
					$('#albumCommentsShow_' + albumId).unbind("click").bind('click', function(e){
						MessiContent = 	$('#albumCommentsFull_' + albumId).html();
						MessiCode = 	"anim success";
						MessiTitle = 	"Comments for Album: " + albumId;
						showMessiContent(MessiContent, MessiTitle, MessiCode);
					});
					$('#Back-' + albumId + '_3').unbind("click").bind('click', function(e){
						if (opts.infiniteScrollPhotos) {
							$('#' + opts.infinitePhotosID).unbind('inview');
						}
						checkExisting($(this).attr('data-href'));
					});
					$('.paginationMain').hide();
					$("#" + opts.loaderID).slideFade(700);
					setTimeout(function(){
						$('#fb-album-paged-' + albumId).show();
						$('#fb-album-' + albumId).show();
						var $albumContainer = $('#fb-album-' + albumId);
						$albumContainer.isotope('reloadItems');
						$albumContainer.isotope('reLayout');
						if (opts.infiniteScrollPhotos) {
							$('.photoWrapper:visible').each(function(i, elem) {
								$(this).addClass("Showing").addClass("Infinite");
							});
							$('#' + opts.infinitePhotosID).unbind('inview');
							infiniteGallery($albumContainer, false, false, albumId);
						}
						if ((opts.floatingControlBar) && (!isInIFrame) && ($("#paginationControls-" + albumId).length != 0)) {
							isotopeHeightContainer = $albumContainer.height();
							$("#paginationControls-" + albumId).stickyScroll({ container: $("#fb-album-paged-" + albumId) });
						};
						$('html, body').animate({scrollTop:$("#" + opts.frameID).offset().top - 20}, 'slow', function() {
							if (opts.infiniteScrollPhotos) {
								$("#" + opts.infinitePhotosID).show();
							}
						});
						if (opts.consoleLogging) {
							console.log('Update: All data for Album ' + albumId + ' has been restored from cache and set to visible!');
						}
						shortLinkPhotoShares(albumId);
					}, 800);
					return;
				}
			} else {
				removeAlbumDOM(albumId);
				if (opts.infiniteScrollPhotos) {
					$("#" + opts.infinitePhotosID).show();
				}
			}
			counterB = 0;
			if (opts.facebookToken.length != 0) {
				var album = "https://graph.facebook.com/" + albumId + "?access_token=" + opts.facebookToken + "&fields=description,count,cover_photo,id,link,location,name,place,from,created_time,updated_time,type,likes&callback=?";
				var comments = "https://graph.facebook.com/" + albumId + "/comments?access_token=" + opts.facebookToken + "&fields=like_count,id,from,message&callback=?";
			} else {
				var album = "https://graph.facebook.com/" + albumId + "?fields=description,count,cover_photo,id,link,location,name,place,from,created_time,updated_time,type,likes&callback=?";
				var comments = "https://graph.facebook.com/" + albumId + "/comments?fields=like_count,id,from,message&callback=?";
			}
			if (opts.outputGraphPhotos) {
				MessiContent = 	"<strong>Album Info:</strong><br/>" + album.replace('&callback=?', '') + "<br/><br/><strong>Album Comments:</strong><br/>" + comments.replace('&callback=?', '') + "<br/><br/>";
				MessiCode = 	"anim success";
				MessiTitle = 	"Graph Links for Album: " + albumId;
			}
			if (opts.consoleLogging) {
				console.log("Usable JSON Feed for Album Information: " + album);
			};

			var commentsCount = 0;
			var commentsUser = "";
			var commentsText = "";
			var commentsOutput = "";

			$.ajax({
				url: 			comments,
				cache: 			false,
				dataType: 		"jsonp",
				success: function(json){
					$.each(json.data, function(j, comments){
						commentsCount = commentsCount + 1;
						commentsUser = comments.from.name;
						commentsText = comments.message;
						commentsOutput += '<div class="albumCommentsUser">' + commentsUser + '</div>';
						commentsOutput += '<div class="albumCommentsText">' + commentsText + "</div>";
					});
				},
				complete: function(){
					if (commentsOutput.length == 0) {
						commentsOutput += "No Comments available for this Album.";
					};
					$.ajax({
						url: 			album,
						cache: 			false,
						dataType: 		"jsonp",
						success: function(data){
							$.each([data], function(i, item){
								var albname = item.name;
								var alblikes = 0;
								if ((item.likes) && (opts.showCommentsLikes)) {
									$.each(item.likes.data, function(j, likes){
										alblikes = alblikes + 1;
									});
								};
								var desc = "";
								if (item.description){desc += item.description;};
								if (item.location){
									if(desc != ""){desc += ' ';};
									desc += '[' + opts.ImageLocationPreText + ' ' + item.location + ']';
								}
								if ((desc!='') && (desc!=' ')){
									desc = '<p>' + desc + '</p>';
								} else {
									desc='<p>' + opts.AlbumNoDescription + '</p>';
								};
								if (!opts.singleAlbumOnly) {
									var headerID = 	'<div data-href="#" id="Back-' + albumId + '_1" class="BackButton fbLink clearFixMe">' + opts.AlbumBackButtonText + '</div>';
								} else {
									var headerID = 	'';
								};
								if (!opts.singleAlbumOnly) {
									var footerID =	'<div class="seperator clearFixMe" style="width: 100%; margin-top: 0px;"></div>';
									footerID += 		'<div data-href="#" id="Back-' + albumId + '_2" class="BackButton fbLink clearFixMe">' + opts.AlbumBackButtonText + '</div>';
									footerID += 		'<ul id="Top-' + albumId + '" class="TopButton fbLink clearFixMe"><li><a style="width: 40px;" id="Back_To_Top-' + albumId + '"><div id="To_Top_' + albumId + '" class="Album_To_Top"></div></a></li></ul>';
								} else {
									var footerID =	'<div class="seperator clearFixMe" style="width: 100%; margin-top: 0px;"></div>';
									footerID += 		'<ul id="Top-' + albumId + '" class="TopButton fbLink clearFixMe"><li><a style="width: 40px;" id="Back_To_Top-' + albumId + '"><div id="To_Top_' + albumId + '" class="Album_To_Top"></div></a></li></ul>';
								};
								if ((opts.photoShowIconFBLink) && (!opts.singleAlbumOnly)) {
									headerID +=		'<div id="Link-' + albumId + '" class="albumFacebook"><a href="' + item.link + '" target="_blank" style="text-decoration: none; border: 0px;"><div class="albumLinkSimple TipGeneric' + tooltipClass + '" style="top: 25px;" ' + opts.tooltipTipAnchor + '="Click here to view the full Album on Facebook!"></div></a></div>';
								} else if ((opts.photoShowIconFBLink) && (opts.singleAlbumOnly)) {
									headerID +=		'<div id="Link-' + albumId + '" class="albumFacebook"><a href="' + item.link + '" target="_blank" style="text-decoration: none; border: 0px;"><div class="albumLinkSimple TipGeneric' + tooltipClass + '" ' + opts.tooltipTipAnchor + '="Click here to view the full Album on Facebook!"></div></a></div>';
								};
								headerID += 			'<div class="albumTitle clearFixMe" style="' + (((opts.photoShowIconFBLink) && (opts.singleAlbumOnly)) ? "margin-top: -20px;" : "") + '">' + opts.AlbumTitlePreText + ' ' + albname + '</div>';
								if (opts.allowAlbumDescription) {
									headerID += 		'<div class="albumDesc clearFixMe"><span id="albumDesc_' + albumId + '">' + desc + '</span>';
									
									if ((commentsCount != 0) && (opts.showCommentsLikes)) {
										headerID +=		'<div data-href="#" id="albumCommentsShow_' + albumId + '" class="albumCommentsShow">' + opts.AlbumCommentsText + '</div>';
										headerID +=		'<div style="margin-top: 15px;">';
									} else {
										headerID +=		'<div style="margin-top: 5px;">';
									}
									
									if (opts.showCommentsLikes) {
										headerID += 	'<div class="albumCommentsSimpleIcon"></div><div id="albumCommentsSimple_' + albumId + '" class="albumCommentsSimple">' + commentsCount + '</div>';
										headerID += 	'<div class="albumLikesSimpleIcon"></div><div id="albumLikesSimple_' + albumId + '" class="albumLikesSimple">' + alblikes + '</div>';
									}
									headerID +=			'</div>';
									
									headerID +=			'</div>';
									if (opts.photoShowTextFBLink) {
										headerID += 		'<div class="albumLinkText clearFixMe"><a class="AlbumLinkButtonText" href="' + item.link + '" target="_blank">' + opts.AlbumLinkButtonText + '</a></div>';
									}
								};
								
								headerID += 		'<div id="albumCommentsFull_' + albumId + '" class="albumCommentsFull clearFixMe"><strong>Comments:</strong><br/>' + commentsOutput + '</div>';
								
								headerID +=			'<div class="seperator clearFixMe' + ((opts.floatingControlBar == true && !isInIFrame) ? " Floater" : "") + '" style="width: 100%; ' + (opts.floatingControlBar == false ? "margin-bottom: 0px;" : "") + '"></div>';
								headerArray[albumId] = headerID;
								footerArray[albumId] = footerID;
								$('#fb-album-header').html(headerID).hide();
								if (opts.showBottomControlBar) {
									$('#fb-album-footer').html(footerID).hide();
								};
								$("<div>", {
									id: 		'fb-album-' + albumId,
									"class": 	'album'
								}).appendTo("#fb-album-content").hide();
								albumCount = item.count;
							});
							singleAlbumShow(albumCount);
							if (!opts.singleAlbumOnly) {
								$('#Back-' + albumId + '_1').unbind("click").bind('click', function(e){
									if (!opts.cacheAlbumContents) {
										removeAlbumDOM(albumId);
									}
									checkExisting($(this).attr('data-href'));
								});
								$('#Back-' + albumId + '_2').unbind("click").bind('click', function(e){
									if (!opts.cacheAlbumContents) {
										removeAlbumDOM(albumId);
									}
									checkExisting($(this).attr('data-href'));
								});
							}
							$('#Back_To_Top-' + albumId).click(function(e){
								$('html, body').animate({scrollTop:$("#" + opts.frameID).offset().top - 20}, 'slow');
							});
							$('#albumCommentsShow_' + albumId).unbind("click").bind('click', function(e){
								MessiContent = 	$('#albumCommentsFull_' + albumId).html();
								MessiCode = 	"anim success";
								MessiTitle = 	"Comments for Album: " + albumId;
								showMessiContent(MessiContent, MessiTitle, MessiCode);
							});
						},
						error: function(jqXHR, textStatus, errorThrown){
							if (opts.consoleLogging) {
								console.log('Error: \njqXHR:' + jqXHR + '\ntextStatus: ' + textStatus + '\nerrorThrown: '  + errorThrown);
							}
						}
					});
				},
				error: function(jqXHR, textStatus, errorThrown){
					if (opts.consoleLogging) {
						console.log('Error: \njqXHR:' + jqXHR + '\ntextStatus: ' + textStatus + '\nerrorThrown: '  + errorThrown);
					}
				}
			})
		}

		// Load and Show Single Album Thumbnails and Data
		function singleAlbumShow(albumCount) {
			var graphLimit = opts.excludeImages.length + opts.maxNumberImages;
			if (opts.facebookToken.length != 0) {
				var pictures = "https://graph.facebook.com/" + albumId + "/photos?access_token=" + opts.facebookToken + "&fields=id,name,picture,created_time,updated_time,source,height,width,album,link,images,comments,likes&limit=" + albumCount + "&callback=?";
			} else {
				var pictures = "https://graph.facebook.com/" + albumId + "/photos?fields=id,name,picture,created_time,updated_time,source,height,width,album,link,images,comments,likes&limit=" + albumCount + "&callback=?";
			}
			if (opts.outputGraphPhotos) {
				MessiContent += "<strong>Photos Info:</strong><br/>" + pictures.replace('&callback=?', '') + "";
				showMessiContent(MessiContent, MessiTitle, MessiCode);
			}
			if (opts.consoleLogging) {
				console.log("Usable JSON Feed for Album Thumbnails: " + pictures);
			};
			$.ajax({
				url: 			pictures,
				cache: 			false,
				dataType: 		"jsonp",
				success: function(json) {
					$.each(json.data, function(j, photos){
						if (typeof photos.picture !== "undefined") {
							if($.inArray(photos.id, opts.excludeImages) == -1) {
								counterB = counterB + 1;
								if ((counterB <= opts.maxNumberImages) || (opts.maxNumberImages == 0)) {
									if (photos.name) {
										var name = photos.name;
										var nameSummary = truncateString(name, opts.photoTitleSummaryLength, ' ', ' ...');
									} else {
										var name = "";
										var nameSummary = truncateString(opts.lightBoxNoDescription, opts.photoTitleSummaryLength, ' ', ' ...');
									}
									if (opts.createTooltipsPhotos) {
										var tooltips = " TipPhoto";
									} else {
										var tooltips = "";
									};
									if (opts.lightboxCustomClass != "") {
										var lightboxClass = " " + opts.lightboxCustomClass;
									} else {
										var lightboxClass = "";
									};
									// Create Hidden Links for Lightbox if Social Share in Photo Thumbnail
									if ((opts.photoShowSocialShare) && (opts.photoThumbSocialShare)) {
										if (opts.prettyPhotoAllow) {
											if (opts.tooltipTipAnchor != "title") {
												var html = '<a id="' + albumId + '_' + counterB + '" class="photoThumbLink ' + albumId + '" data-photo="' + photos.id + '" data-album="' + albumId + '" data-key="' + counterB + '" data-short="" rel="prettyPhoto[' + albumId + ']" style="display: none !important" ' + opts.tooltipTipAnchor + '="' + name + '" href="' + photos.images[0].source + '" title="' + name + '" target="_blank">Lightbox Link</a>';
											} else {
												var html = '<a id="' + albumId + '_' + counterB + '" class="photoThumbLink ' + albumId + '" data-photo="' + photos.id + '" data-album="' + albumId + '" data-key="' + counterB + '" data-short="" rel="prettyPhoto[' + albumId + ']" style="display: none !important" ' + opts.tooltipTipAnchor + '="' + name + '" href="' + photos.images[0].source + '" target="_blank">Lightbox Link</a>';
											}
										} else {
											if ((opts.photoThumbSocialShare) && (opts.photoBoxAllow)) {
												var html = '<a id="' + albumId + '_' + counterB + '" class="photoThumbLink ' + albumId + '" data-photo="' + photos.id + '" data-album="' + albumId + '" data-key="' + counterB + '" data-short="" rel="' + albumId + '" style="display: none;" ' + opts.tooltipTipAnchor + '="' + name + '" href="' + photos.images[0].source + '" target="_blank"><img src="' + photos.images[0].source + '" alt="' + name + '"></a>';
											} else {
												var html = '<a id="' + albumId + '_' + counterB + '" class="photoThumbLink ' + albumId + '" data-photo="' + photos.id + '" data-album="' + albumId + '" data-key="' + counterB + '" data-short="" rel="' + albumId + '" style="display: none;" ' + opts.tooltipTipAnchor + '="' + name + '" href="' + photos.images[0].source + '" target="_blank">Lightbox Link</a>';
											}
										}
									}
									// Create Container for Photo Thumbnails
									var photoThumbWidthSingle = opts.photoThumbWidth;
									var photoThumbHeightSingle = opts.photoThumbHeight;
									
									if (opts.prettyPhotoAllow) {
										if (opts.tooltipTipAnchor != "title") {
											if (((opts.photoShowSocialShare) && (!opts.photoThumbSocialShare)) || (!opts.photoShowSocialShare)) {
												var html = '<a id="' + albumId + '_' + counterB + '" class="photoThumb prettyPhoto ' + albumId + tooltips + lightboxClass + tooltipClass + '" data-photo="' + photos.id + '" data-album="' + albumId + '" data-key="' + counterB + '" data-short="" rel="prettyPhoto[' + albumId + ']" style="width:' + photoThumbWidthSingle + 'px; height:' + photoThumbHeightSingle + 'px; padding:' + opts.photoThumbPadding + 'px;" ' + opts.tooltipTipAnchor + '="' + name + '" title="' + name + '" href="' + photos.images[0].source + '" target="_blank">';
											} else if ((opts.photoShowSocialShare) && (opts.photoThumbSocialShare)) {
												html += '<div id="Call_' + albumId + '_' + counterB + '" class="photoThumb prettyPhoto Call_' + albumId + tooltips + lightboxClass + tooltipClass + '" data-photo="' + photos.id + '" data-album="' + albumId + '" data-key="' + counterB + '" style="width:' + photoThumbWidthSingle + 'px; height:' + photoThumbHeightSingle + 'px; padding:' + opts.photoThumbPadding + 'px;" ' + opts.tooltipTipAnchor + '="' + name + '" title="' + name + '" data-link="' + photos.images[0].source + '">';
											}
										} else {
											if (((opts.photoShowSocialShare) && (!opts.photoThumbSocialShare)) || (!opts.photoShowSocialShare)) {
												var html = '<a id="' + albumId + '_' + counterB + '" class="photoThumb prettyPhoto ' + albumId + tooltips + lightboxClass + tooltipClass + '" data-photo="' + photos.id + '" data-album="' + albumId + '" data-key="' + counterB + '" data-short="" rel="prettyPhoto[' + albumId + ']" style="width:' + photoThumbWidthSingle + 'px; height:' + photoThumbHeightSingle + 'px; padding:' + opts.photoThumbPadding + 'px;" ' + opts.tooltipTipAnchor + '="' + name + '" href="' + photos.images[0].source + '" target="_blank">';
											} else if ((opts.photoShowSocialShare) && (opts.photoThumbSocialShare)) {
												html += '<div id="Call_' + albumId + '_' + counterB + '" class="photoThumb prettyPhoto Call_' + albumId + tooltips + lightboxClass + tooltipClass + '" data-photo="' + photos.id + '" data-album="' + albumId + '" data-key="' + counterB + '" style="width:' + photoThumbWidthSingle + 'px; height:' + photoThumbHeightSingle + 'px; padding:' + opts.photoThumbPadding + 'px;" ' + opts.tooltipTipAnchor + '="' + name + '" data-link="' + photos.images[0].source + '">';
											}
										};
									} else {
										if (((opts.photoShowSocialShare) && (!opts.photoThumbSocialShare)) || (!opts.photoShowSocialShare)) {
											var html = '<a id="' + albumId + '_' + counterB + '" class="photoThumb ' + albumId + tooltips + lightboxClass + tooltipClass + '" data-photo="' + photos.id + '" data-album="' + albumId + '" data-key="' + counterB + '" data-short="" rel="' + albumId + '" style="width:' + photoThumbWidthSingle + 'px; height:' + photoThumbHeightSingle + 'px; padding:' + opts.photoThumbPadding + 'px;" ' + opts.tooltipTipAnchor + '="' + name + '" href="' + photos.images[0].source + '" target="_blank">';
										} else if ((opts.photoShowSocialShare) && (opts.photoThumbSocialShare)) {
											html += '<div id="Call_' + albumId + '_' + counterB + '" class="photoThumb Call_' + albumId + tooltips + lightboxClass + tooltipClass + '" data-photo="' + photos.id + '" data-album="' + albumId + '" data-key="' + counterB + '" rel="' + albumId + '" style="width:' + photoThumbWidthSingle + 'px; height:' + photoThumbHeightSingle + 'px; padding:' + opts.photoThumbPadding + 'px;" ' + opts.tooltipTipAnchor + '="' + name + '" data-link="' + photos.images[0].source + '">';
										}
									};
									
									if (opts.photoShowClearTape) {
										html += '<span class="ClearTape" style="left: ' + (Math.ceil((photoThumbWidthSingle + opts.photoThumbMargin + opts.photoThumbPadding - 77) / 2)) + 'px;"></span>';
									};
									if (opts.photoShowYellowTape) {
										html += '<span class="YellowTape" style="left: ' + (Math.ceil((photoThumbWidthSingle + opts.photoThumbMargin + opts.photoThumbPadding - 115) / 2)) + 'px;"></span>';
									};
									if (opts.photoShowPushPin) {
										html += '<span class="PushPin" style="left: ' + (Math.ceil((photoThumbWidthSingle + opts.photoThumbMargin + opts.photoThumbPadding) / 2)) + 'px;"></span>';
									};
									
									html += '<span class="photoThumbWrap">';
									if (opts.innerImageScaler) {
										var imgthumb = '' + opts.PathInternalPHP + '?src=' + (photos.source) + '&w=' + photoThumbWidthSingle + '&zc=1';
									} else if (opts.senchaImageScaler) {
										var imgthumb = 'http://src.sencha.io/' + photoThumbWidthSingle + '/' + (photos.source);
									} else if (opts.weservImageScaler) {
										var imgthumb = 'http://images.weserv.nl/?url=' + (photos.source.replace("https://", "").replace("http://", "")) + '&h' + (photoThumbHeightSingle) + '&w=' + (photoThumbWidthSingle) + '&t=fit';
									} else {
										var imgthumb = photos.source;
									}
									if (opts.imageSpinnerAnimation) {
										html += '<i class="fb-photo-spinner fb-photo-spinner-' + albumId + '" id="fb-photo-spinner-' + photos.id + '" style="width:' + photoThumbWidthSingle + 'px; height:' + photoThumbHeightSingle + 'px;  padding:' + opts.photoThumbPadding + 'px;"></i>';
									}
									if (opts.imageLazyLoad) {
										if (imageScalerActive) {
											html += '<i class="fb-photo-thumb fb-photo-thumb-' + albumId + '" id="fb-photo-thumb-' + photos.id + '" style="width:' + photoThumbWidthSingle + 'px; height:' + photoThumbHeightSingle + 'px;" data-loaded="FALSE" data-original="' + imgthumb + '" data-album="' + albumId + '" data-photo="' + photos.id + '"></i>';
										} else {
											html += '<i class="fb-photo-thumb noscaler fb-photo-thumb-' + albumId + '" id="fb-photo-thumb-' + photos.id + '" style="background-size:' + photoThumbWidthSingle + 'px; width:' + photoThumbWidthSingle + 'px; height:' + photoThumbHeightSingle + 'px;" data-loaded="FALSE" data-original="' + imgthumb + '" data-album="' + albumId + '" data-photo="' + photos.id + '"></i>';
										}
									} else {
										if (imageScalerActive) {
											html += '<i class="fb-photo-thumb fb-photo-thumb-' + albumId + '" id="fb-photo-thumb-' + photos.id + '" style="width:' + photoThumbWidthSingle + 'px; height:' + photoThumbHeightSingle + 'px; background-image:url(' + (imgthumb) + ')" data-loaded="FALSE" data-album="' + albumId + '" data-photo="' + photos.id + '"></i>';
										} else {
											html += '<i class="fb-photo-thumb noscaler fb-photo-thumb-' + albumId + '" id="fb-photo-thumb-' + photos.id + '" style="background-size:' + photoThumbWidthSingle + 'px; width:' + photoThumbWidthSingle + 'px; height:' + photoThumbHeightSingle + 'px; background-image:url(' + (imgthumb) + ')" data-loaded="FALSE" data-album="' + albumId + '" data-photo="' + photos.id + '"></i>';
										}
									};
									if ((opts.photoBoxAllow) && (!opts.photoThumbSocialShare)) {
										html += '<i class="fb-photo-image fb-photo-image-' + albumId + '" id="fb-photo-image-' + photos.id + '" style="z-index: 0; display: none;"><img src="' + imgthumb + '" alt="' + name + '"></i>';
									}

									html += '<i class="fb-photo-overlay fb-photo-overlay-' + albumId + '" id="fb-photo-overlay-' + photos.id + '" data-photo="' + photos.id + '" style="width:' + photoThumbWidthSingle + 'px; height:' + photoThumbHeightSingle + 'px;  padding:' + opts.photoThumbPadding + 'px;"></i>';

									if ((opts.photoShowSocialShare) && (opts.photoThumbSocialShare)) {
										var PhotoSocialShareLink = photos.images[0].source;
										// Check if Image ID has been added to Array in a prior Call, if not add
										if (!opts.cacheAlbumContents) {
											var params = {searchedID: photos.id, elementFound: null};
											var isCorrectImageID = function(element) {
												if (element.id == this.searchedID) {
													return (this.elementFound = element);
												} else {
													return false;
												}
											};
											var isFound = PhotoIDsArray.some(isCorrectImageID, params);
											if (!isFound) {
												PhotoIDsArray.push({id:photos.id, album:albumId, link:PhotoSocialShareLink, clean:"", thumb:photos.source, summary:fixedEncodeURIComponent(nameSummary)});
											}
										} else {
											PhotoIDsArray.push({id:photos.id, album:albumId, link:PhotoSocialShareLink, clean:"", thumb:photos.source, summary:fixedEncodeURIComponent(nameSummary)});
										}
										html += '<i class="fb-photo-shareme" id="fb-photo-shareme-' + photos.id + '" data-photo="' + albumId + "_" + counterB + '" style="width:' + photoThumbWidthSingle + 'px; padding: ' + opts.photoThumbPadding + 'px;">';
											html += '<ul id="socialcount_' + photos.id + '" data-parent="' + albumId + '_' + counterB + '" class="socialcount" style="float: right; display: ' + (opts.photoShortSocialShare == true ? " none;" : "block;") + '">';
												html += '<li class="stumbleplus"><a id="PhotoSocialShare_Stumble_' + photos.id + '" class="PhotoSocialShare Share_Stumble TipSocial' + tooltipClass + '" target="_blank" href="http://www.stumbleupon.com/submit?url=' + PhotoSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li>';
												html += '<li class="googleplus"><a id="PhotoSocialShare_Google_' + photos.id + '" class="PhotoSocialShare Share_Google TipSocial' + tooltipClass + '" target="_blank" href="https://plus.google.com/share?url=' + PhotoSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Google Plus"><span class="social-icon icon-googleplus"></span></a></li>';
												html += '<li class="twitter"><a id="PhotoSocialShare_Twitter_' + photos.id + '" class="PhotoSocialShare Share_Twitter TipSocial' + tooltipClass + '" target="_blank" href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + PhotoSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Twitter"><span class="social-icon icon-twitter"></span></a></li>';
												//html += '<li class="facebook"><a id="PhotoSocialShare_Facebook_' + photos.id + '" class="PhotoSocialShare Share_Facebook TipSocial' + tooltipClass + '" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' + PhotoSocialShareLink + '&title=' + opts.SocialSharePhotoText + '" ' + opts.tooltipTipAnchor + '="Share this Image on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
												html += '<li class="facebook"><a id="PhotoSocialShare_Facebook_' + photos.id + '" class="PhotoSocialShare Share_Facebook TipSocial' + tooltipClass + '" target="_blank" href="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=' + PhotoSocialShareLink + '&p[images][0]=' + photos.source + '&p[title]=' + opts.SocialSharePhotoText + '&p[summary]=' + nameSummary + '" ' + opts.tooltipTipAnchor + '="Share this Image on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
												html += '<li class="savetodisk"><a id="PhotoSocialShare_Save_' + photos.id + '" class="PhotoSocialShare Share_Save TipSocial' + tooltipClass + '" target="_blank" href="' + PhotoSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Save this Image to disk" data-original="' + photos.images[0].source + '"><span class="social-icon icon-disk"></span></a></li>';
											html += '</ul>';
										html += '</i>';
									}

									html += '</span>';
									if (((opts.photoShowSocialShare) && (!opts.photoThumbSocialShare)) || (!opts.photoShowSocialShare)) {
										html += '</a>';
									} else {
										html += '</div>';
									}

									if (((opts.photoShowSocialShare) && (!opts.photoThumbSocialShare)) || (opts.photoShowNumber)) {
										html += '<div class="photoDetails' + tooltipClass + '" ' + opts.tooltipTipAnchor + '="' + photos.id + '" style="width: ' + (photoThumbWidthSingle + opts.photoThumbMargin) + 'px; margin-top: ' + (((opts.photoShowSocialShare) && (opts.photoThumbSocialShare)) ? 10 : 0) + 'px;">';
										if ((opts.photoShowSocialShare) && (!opts.photoThumbSocialShare)) {
											html += '<div id="photoShare_' + photos.id + '" class="photoShare clearFixMe" style="height: 30px; width: ' + (photoThumbWidthSingle + opts.photoThumbMargin) + 'px; ' + (!opts.photoShowNumber ? "border-bottom: none;" : "") + '">';
												if (opts.photoShowOrder) {
													html += '<span id="photoSocial_' + photos.id + '" class="photoSocial">' + opts.ImageShareMePreText + ' (#' + counterB + '):</span>';
												} else {
													html += '<span id="photoSocial_' + photos.id + '" class="photoSocial">' + opts.ImageShareMePreText + ':</span>';
												};

												var PhotoSocialShareLink = photos.images[0].source;
												// Check if Image ID has been added to Array in a prior Call, if not add
												if (!opts.cacheAlbumContents) {
													var params = {searchedID: photos.id, elementFound: null};
													var isCorrectImageID = function(element) {
														if (element.id == this.searchedID) {
															return (this.elementFound = element);
														} else {
															return false;
														}
													};
													var isFound = PhotoIDsArray.some(isCorrectImageID, params);
													if (!isFound) {
														PhotoIDsArray.push({id:photos.id, album:albumId, link:PhotoSocialShareLink, clean:"", thumb:photos.source, summary:fixedEncodeURIComponent(nameSummary)});
													}
												} else {
													PhotoIDsArray.push({id:photos.id, album:albumId, link:PhotoSocialShareLink, clean:"", thumb:photos.source, summary:fixedEncodeURIComponent(nameSummary)});
												}

												html += '<ul id="socialcount_' + photos.id + '" data-parent="' + albumId + '_' + counterB + '" class="socialcount" style="float: right; display: ' + (opts.photoShortSocialShare == true ? " none;" : "block;") + '">';
													html += '<li class="stumbleplus"><a id="PhotoSocialShare_Stumble_' + photos.id + '" class="PhotoSocialShare Share_Stumble TipSocial' + tooltipClass + '" target="_blank" href="http://www.stumbleupon.com/submit?url=' + PhotoSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li>';
													html += '<li class="googleplus"><a id="PhotoSocialShare_Google_' + photos.id + '" class="PhotoSocialShare Share_Google TipSocial' + tooltipClass + '" target="_blank" href="https://plus.google.com/share?url=' + PhotoSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Google Plus"><span class="social-icon icon-googleplus"></span></a></li>';
													html += '<li class="twitter"><a id="PhotoSocialShare_Twitter_' + photos.id + '" class="PhotoSocialShare Share_Twitter TipSocial' + tooltipClass + '" target="_blank" href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + PhotoSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Twitter"><span class="social-icon icon-twitter"></span></a></li>';
													//html += '<li class="facebook"><a id="PhotoSocialShare_Facebook_' + photos.id + '" class="PhotoSocialShare Share_Facebook TipSocial' + tooltipClass + '" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' + PhotoSocialShareLink + '&title=' + opts.SocialSharePhotoText + '" ' + opts.tooltipTipAnchor + '="Share this Image on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
													html += '<li class="facebook"><a id="PhotoSocialShare_Facebook_' + photos.id + '" class="PhotoSocialShare Share_Facebook TipSocial' + tooltipClass + '" target="_blank" href="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=' + PhotoSocialShareLink + '&p[images][0]=' + photos.source + '&p[title]=' + opts.SocialSharePhotoText + '&p[summary]=' + nameSummary + '" ' + opts.tooltipTipAnchor + '="Share this Image on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
													html += '<li class="savetodisk"><a id="PhotoSocialShare_Save_' + photos.id + '" class="PhotoSocialShare Share_Save TipSocial' + tooltipClass + '" target="_blank" href="' + PhotoSocialShareLink + '" ' + opts.tooltipTipAnchor + '="Save this Image to disk" data-original="' + photos.images[0].source + '"><span class="social-icon icon-disk"></span></a></li>';
												html += '</ul>';
											html += '</div>';
										}
										if (opts.photoShowNumber) {
											html += '<div class="clearFixMe" style="width: ' + photoThumbWidthSingle + 'px; display: block;"><div class="photoNumber">' + opts.ImageNumberPreText + '</div><div class="photoInfo clearFixMe">' + photos.id + '</div></div>';
										}
										html += '</div>';
									}

									var clear = 'width: ' + (photoThumbWidthSingle + opts.photoThumbPadding * 2) + 'px; margin: ' + opts.photoThumbMargin + 'px; display: none;';
									if (((counterB <= albumCount) && (counterB <= opts.maxNumberImages)) || (opts.maxNumberImages == 0)) {
										if (typeof(photos.source)  === "undefined") {
											var fileExtension = "N/A";
										} else {
											var fileExtension = photos.source.substring(photos.source.lastIndexOf('.') + 1).toUpperCase();
										}
										var photoTime = moment(photos.created_time).fromNow().replace(/ /g,"_");
										$("<div>", {
											"class": 			"photoWrapper " + fileExtension + " " + photoTime + " Wrapper_" + albumId,
											"id": 				'fb-photo-' + photos.id,
											"data-title":		'',
											"data-cover":		'',
											"data-added":		photos.created_time,
											"data-update":		photos.updated_time,
											"data-height-s":	photoThumbHeightSingle + 2 * opts.photoThumbMargin,
											"data-height-o":	photos.height,
											"data-width-s":		photoThumbWidthSingle + 2 * opts.photoThumbMargin,
											"data-width-o":		photos.width,
											"data-number":		photos.id,
											"data-order":		counterB,
											"data-type":		fileExtension,
											"data-time":		photoTime,
											"data-ID":			photos.id,
											"data-location":	'',
											"style":			clear,
											html: 				html
										}).appendTo('#fb-album-' + albumId).show();
									};
								};
							};
						};
					});
					if (opts.consoleLogging) {
						console.log('Update: ' + counterB + ' Photo(s) for album ID "' + albumId + '" could be successfully retrieved!');
					}
					if ($('#fb-album-' + albumId + ' > .photoWrapper').length == 0) {
						$('#fb-album-' + albumId).wrap('<div id="fb-album-paged-' + albumId + '" class="paginationMain" />');
						$("<div>", {
							"id": 				'no-fb-photos',
							html: 				"Sorry, there are either no public images in that album or all of the images have been exluded from being shown ... Please check your settings!"
						}).appendTo('#fb-album-' + albumId).fadeIn(500);
						// Remove Loader Animation and Show Album Content
						$("#" + opts.loaderID).hide();
						if ((opts.allowAlbumDescription) && (opts.showDescriptionStart)) {
							$('#fb-album-header').show();
						} else {
							$('#fb-album-header').hide();
						};
						if (opts.showBottomControlBar) {
							$('#fb-album-footer').show();
						};
						//$('#fb-album-' + albumId).show();
						$("#fb-album-" + albumId).slideFade(700);
					} else {
						var $albumContainer = $('#fb-album-' + albumId);
						$("#" + opts.loaderID).hide();
						//$("#fb-album-paged" + albumId).slideFade(700);
						// Initialize Paging Feature
						equalHeightFloat(false, albumId);
						currentPageList = $albumContainer;
						setTimeout(function(){
							if (!opts.paginationLayoutPhotos) {
								if (opts.infiniteScrollAlbums) {
									if (opts.infiniteScrollPhotosSmart) {
										infinitePhotos = smartPhotosPerPage;
										var photoItemsPerPage = smartPhotosPerPage;
									} else {
										infinitePhotos = opts.infiniteScrollPhotosBlock;
										var photoItemsPerPage = opts.infiniteScrollPhotosBlock;
									}
									infinitePhotosCount = counterB;
								} else {
									var photoItemsPerPage = counterB;
								}
							} else if (opts.smartPhotosPerPageAllow) {
								var photoItemsPerPage = smartPhotosPerPage;
							} else {
								if (opts.setPhotosByPages) {
									var photoItemsPerPage = ((opts.paginationLayoutPhotos = true && opts.numberPagesForPhotos > 0) ? (Math.ceil(counterB / opts.numberPagesForPhotos)) : counterB);
								} else {
									var photoItemsPerPage = ((opts.paginationLayoutPhotos = true && opts.numberPhotosPerPage > 0) ? opts.numberPhotosPerPage : counterB);
								};
							};
							var PhotoSettings = {
								'searchBoxDefault' 		: 	opts.SearchDefaultText,
								'itemsPerPageDefault' 	: 	photoItemsPerPage,
								'hideToTop'				:	(opts.showFloatingToTopButton == true ? false : true),
								'hideFilter' 			: 	(opts.photosFilterAllow == true ? false : true),
								'hideSort' 				: 	(opts.photoSortControls == true ? false : true),
								'hideSearch' 			: 	true,
								'hidePager'				:	((opts.photosPagerControls == true && !opts.infiniteScrollPhotos) == true ? false : true)
							};
							new CallPagination($albumContainer, PhotoSettings, "fb-album-paged-" + albumId, false, false, albumId);
						}, 500);
						// Initialize LazyLoad for Thumbnails
						if ((opts.imageLazyLoad) && ($.isFunction($.fn.lazyloadanything))) {
							$('.fb-photo-thumb-' + albumId).lazyloadanything({
								'auto': 			true,
								'repeatLoad':		true,
								'onLoadingStart': 	function(e, LLobjs, indexes) {
									return true
								},
								'onLoad': 			function(e, LLobj) {
									var $img = LLobj.$element;
									var $src = $img.attr('data-original');
									var $lazy = $img.attr('data-photo');
									var $frame = $img.attr('data-album');
									if (($('#fb-album-paged-' + $frame).is(':visible')) && $('#fb-photo-' + $lazy).hasClass('Showing')) {
										if ((opts.imageSpinnerAnimation) && ($.isFunction($.fn.waitForImages))) {
											if ($("#fb-photo-thumb-" + $lazy).attr("data-loaded") == "FALSE") {
												$img.hide().css('background-image', 'url("' + $src + '")');
												$img.waitForImages({
													waitForAll: 	true,
													finished: 		function() {},
													each: 			function() {
														if ($("#fb-photo-" + $lazy).css('display') != 'none') {
															$("#fb-photo-spinner-" + $lazy).hide();
															$("#fb-photo-thumb-" + $lazy).fadeIn(500);
															$("#fb-photo-thumb-" + $lazy).attr("data-loaded", "TRUE");
														};
													},
												});
											};
										} else {
											if ($("#fb-photo-thumb-" + $lazy).attr("data-loaded") == "FALSE") {
												$img.hide().css('background-image', 'url("' + $src + '")').fadeIn(500);
												$("#fb-photo-thumb-" + $lazy).attr("data-loaded", "TRUE");
											}
										}
									};
								},
								'onLoadComplete':	function(e, LLobjs, indexes) {
									return true
								}
							});
							restartLazyLoad();
						} else {
							if ((opts.imageSpinnerAnimation) && ($.isFunction($.fn.waitForImages))) {
								$('#fb-album-' + albumId + ' .fb-photo-thumb').waitForImages({
									waitForAll: true,
									finished: function() {},
									each: function() {
										var $picture 	= $(this).attr("data-photo");
										if ($("#fb-photo-thumb-" + $picture).attr("data-loaded") == "FALSE") {
											$("#fb-photo-spinner-" + $picture).hide();
											$("#fb-photo-thumb-" + $picture).hide().fadeIn(500);
											$("#fb-photo-thumb-" + $picture).attr("data-loaded", "TRUE");
										};
									},
								});
							} else {
								$('#fb-album-' + albumId + ' .fb-photo-thumb').each(function(index) {
									var $picture 	= $(this).attr("data-photo");
									if ($("#fb-photo-thumb-" + $picture).attr("data-loaded") == "FALSE") {
										$("#fb-photo-thumb-" + $picture).hide().fadeIn(500);
										$("#fb-photo-thumb-" + $picture).attr("data-loaded", "TRUE");
									};
								});
							}
						}
						// Remove Loader Animation and Show Album Content
						if (opts.singleAlbumOnly) {
							$("#" + opts.loaderID).hide();
							$("#" + opts.galleryID).show();
							if (opts.showDescriptionStart) {
								$('#fb-album-header').show();
							}
							$('#fb-album-footer').hide();
							$('#fb-album-' + albumId).show();
						} else {
							$("#" + opts.loaderID).hide();
							if (opts.showDescriptionStart) {
								$('#fb-album-header').show();
							}
							$('#fb-album-footer').show();
							$('#fb-album-' + albumId).show();
						}
					}
					shortLinkPhotoShares(albumId);
					// Adjust Height of iFrame Container (if applicable)
					AdjustIFrameDimensions();
				},
				error: function(jqXHR, textStatus, errorThrown){
					if (opts.consoleLogging) {
						console.log('Error: \njqXHR:' + jqXHR + '\ntextStatus: ' + textStatus + '\nerrorThrown: '  + errorThrown);
					}
				}
			});
		}

		// Check if alredy created Gallery or Album Preview can be restored
		function checkExisting(href) {
			if ((typeof href != 'undefined') && (href.length != 0)) {
				var anchor = href.split('-');
				if (anchor[0] == '#album') {
					// Hide Album Thumbnail Gallery
					if ($('#fb-albums-all-paged').length != 0) {
						if (opts.infiniteScrollAlbums) {
							$('#' + opts.infiniteAlbumsID).unbind('inview').hide();
						}
						$('#fb-albums-all-paged').slideFade(700);
						if ((opts.floatingControlBar) && (!isInIFrame)) {
							$("#paginationControls-" + opts.facebookID).unbind('stickyScroll');
							$("#paginationControls-" + opts.facebookID).stickyScroll('reset');
						}
					}
					if (typeof ajaxRequest !== 'undefined') {
						ajaxRequest.abort();
					}
					// Check if selected album has just been viewed or not
					if (albumId != anchor[1]){
						albumId = anchor[1];
						singleAlbumInit();
					} else {
						if (opts.cacheAlbumContents) {
							//alert("Restore from 'checkExisting'");
							if (opts.infiniteScrollPhotos) {
								$('#' + opts.infiniteMoreID).hide();
							}
							$("#" + opts.loaderID).slideFade(700);
							if ($("#paginationControls-" + albumId).length != 0) {
								if ((opts.floatingControlBar) && (!isInIFrame)) {
									$("#paginationControls-" + albumId).unbind('stickyScroll');
									$("#paginationControls-" + albumId).stickyScroll('reset');
								};
							};
							$('#fb-album-header').html(headerArray[albumId]);
							if (opts.showBottomControlBar) {
								$('#fb-album-footer').html(footerArray[albumId]);
							};
							$('#Back-' + albumId + '_1').unbind("click").bind('click', function(e){
								if (opts.infiniteScrollPhotos) {
									$('#' + opts.infinitePhotosID).unbind('inview');
								}
								checkExisting($(this).attr('data-href'));
							});
							if (opts.showBottomControlBar) {
								$('#Back-' + albumId + '_2').unbind("click").bind('click', function(e){
									if (opts.infiniteScrollPhotos) {
										$('#' + opts.infinitePhotosID).unbind('inview');
									}
									checkExisting($(this).attr('data-href'));
								});
								$('#Back_To_Top-' + albumId).click(function(e){
									$('html, body').animate({scrollTop:$("#" + opts.frameID).offset().top - 20}, 'slow');
								});
							};
							$('#albumCommentsShow_' + albumId).unbind("click").bind('click', function(e){
								MessiContent = 	$('#albumCommentsFull_' + albumId).html();
								MessiCode = 	"anim success";
								MessiTitle = 	"Comments for Album: " + albumId;
								showMessiContent(MessiContent, MessiTitle, MessiCode);
							});
							$('#Back-' + albumId + '_3').unbind("click").bind('click', function(e){
								if (opts.infiniteScrollPhotos) {
									$('#' + opts.infinitePhotosID).unbind('inview');
								}
								checkExisting($(this).attr('data-href'));
							});
							$('.paginationMain').hide();
							$("#" + opts.loaderID).slideFade(700);
							setTimeout(function(){
								$('#fb-album-paged-' + albumId).show();
								$('#fb-album-' + albumId).show();
								var $albumContainer = $('#fb-album-' + albumId);
								$albumContainer.isotope('reloadItems');
								$albumContainer.isotope('reLayout');
								if (opts.infiniteScrollPhotos) {
									$('.photoWrapper:visible').each(function(i, elem) {
										$(this).addClass("Showing").addClass("Infinite");
									});
									$('#' + opts.infinitePhotosID).unbind('inview');
									infiniteGallery($albumContainer, false, false, albumId);
								}
								if ((opts.floatingControlBar) && (!isInIFrame) && ($("#paginationControls-" + albumId).length != 0)) {
									isotopeHeightContainer = $albumContainer.height();
									$("#paginationControls-" + albumId).stickyScroll({ container: $("#fb-album-paged-" + albumId) });
								};
								$('html, body').animate({scrollTop:$("#" + opts.frameID).offset().top - 20}, 'slow', function() {
									if (opts.infiniteScrollPhotos) {
										$("#" + opts.infinitePhotosID).show();
									}
								});
								shortLinkPhotoShares(albumId);
								if (opts.consoleLogging) {
									console.log('Update: All data for Album ' + albumId + ' has been restored from cache and set to visible!');
								}
							}, 700);
						} else {
							singleAlbumInit();
						}
					}
				} else {
					if (opts.infiniteScrollPhotos) {
						$('#' + opts.infinitePhotosID).unbind('inview');
						$('#' + opts.infiniteMoreID).hide();
					}
					if (typeof ajaxRequest !== 'undefined') {
						ajaxRequest.abort();
					}
					$(".albumThumb").find(".fb-album-overlay").stop().animate({opacity: 0}, "fast");
					$(".albumThumb").find(".fb-album-shareme").stop().animate({opacity: 0}, "fast");
					$(".albumThumb").find(".fb-album-loading").stop().animate({opacity: 0}, "fast");
					$('.paginationMain').hide();
					$("#" + opts.loaderID).slideFade(700);
					if ((opts.consoleLogging) && (opts.cacheAlbumContents)) {
						console.log('Update: All data for Album ' + albumId + ' has been cached and set to temporarily hidden!');
					}
					if ($("#paginationControls-" + opts.facebookID).length != 0) {
						if ((opts.floatingControlBar) && (!isInIFrame)) {
							$("#paginationControls-" + opts.facebookID).unbind('stickyScroll');
							$("#paginationControls-" + opts.facebookID).stickyScroll('reset');
						};
					};
					galleryAlbumsInit();
				}
				// Adjust Height of iFrame Container (if applicable)
				AdjustIFrameDimensions();
			}
		}
		
		// Function to Check if Key Node exists in JSON Feed
		function returnIfExist(property) {
			try {
				return property;
			} catch (err) {
				return null
			}
		}
		
		// Function to auto generate Thumbnail Sizes within given Limits
		function randomFromInterval(from, to) {
			return Math.floor(Math.random()*(to - from + 1) + from);
		}

		// Function to Clean HTML Strings
		function htmlEntities(str) {
			return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
		}

		// Ensure that all Thumbnails have the same Height
		function equalHeightFloat(Gallery, Identity){
			var heightArray = new Array();
			var widthArray = new Array();
			if (opts.albumNameShorten && Gallery) {
				if ((opts.albumNameTitle) && (!opts.albumNameAbove)) 	{$('.albumName').shorten({tail: '&nbsp;&nbsp;...'})};
				if ((opts.albumNameTitle) && (opts.albumNameAbove)) 	{$('.albumNameHead').shorten({tail: '&nbsp;&nbsp;...'})};
			}
			if (Gallery) {
				$("#fb-albums-all .albumWrapper").each( function(){
					heightArray.push($(this).find(".albumHead").outerHeight(true) + $(this).find(".albumThumb").outerHeight(true) + $(this).find(".albumDetails").outerHeight(true));
					widthArray.push($(this).outerWidth(true));
				});
			} else {
				$("#fb-album-" + Identity + " .photoWrapper").each( function(){
					//heightArray.push(2 * opts.photoThumbMargin + $(this).outerHeight(true) + $(this).find(".photoThumb").outerHeight(true) + $(this).find(".photoDetails").outerHeight(true) + $(this).find(".photoShare").outerHeight(true));
					heightArray.push($(this).find(".photoThumb").outerHeight(true) + $(this).find(".photoDetails").outerHeight(true) + $(this).find(".photoShare").outerHeight(true));
					widthArray.push($(this).outerWidth(true));
				});
			};
			var maxHeight = Math.max.apply(Math, heightArray);
			var maxWidth = Math.max.apply(Math, widthArray);
			totalItems = 0;
			if (Gallery) {
				AlbumThumbHeight = maxHeight;
				AlbumThumbWidth = maxWidth;
				smartAlbumsPerPage = Math.floor(galleryWidth / maxWidth) * (Math.floor(viewPortHeight / maxHeight));
				$("#fb-albums-all .albumWrapper").each( function(){$(this).height(maxHeight + "px"); totalItems++});
			} else {
				PhotoThumbHeight = maxHeight;
				PhotoThumbWidth = maxWidth;
				smartPhotosPerPage = Math.floor(galleryWidth / maxWidth) * ((Math.floor(viewPortHeight / maxHeight) - ((opts.showTopPaginationBar == true && opts.allowAlbumDescription == true) ? 1 : 0)));
				$("#fb-album-" + Identity + " .photoWrapper").each( function(){$(this).height(PhotoThumbHeight + "px"); totalItems++});
			};
		};

		// Determine Width for Scrollbar based on Browser Settings
		function scrollBarWidth() {
			var parent, child, width;
			if (width === undefined) {
				parent = $('<div style="width:50px; height:50px; overflow:auto; position:absolute; top:-200px; left:-200px;"><div/>').appendTo('body');
				child = parent.children();
				width = child.innerWidth() - child.height(99).innerWidth();
				parent.remove();
			}
			return width;
		};

		// Remove Album Data from DOM
		function removeAlbumDOM(albumId) {
			if (($('#fb-album-paged-' + albumId).length != 0) || ($('#fb-album-' + albumId).length != 0)) {
				var $albumContainer = $('#fb-album-' + albumId);
				$albumContainer.isotope('destroy');
				$('#Back-' + albumId + '_1').unbind("click");
				if (opts.showBottomControlBar) {
					$('#Back-' + albumId + '_2').unbind("click");
				};
				$('#albumCommentsShow_' + albumId).unbind("click");
				$('#Back-' + albumId + '_3').unbind("click");
				if (opts.showFloatingToTopButton) {
					$('#Scroll_To_Top-' + albumId).unbind("click");
				};
				if ((opts.floatingControlBar) && (!isInIFrame)) {
					$("#paginationControls-" + albumId).unbind('stickyScroll');
					$("#paginationControls-" + albumId).stickyScroll('reset');
				};
				if ((opts.photoBoxAllow) && ($.isFunction($.fn.photobox))) {
					if (!isInIFrame) {
						if ($("#pbOverlay").attr("data-album") == albumId) {
							$("#pbOverlay").attr("data-album", "").attr("data-photo", "");
							$('#fb-album-' + albumId).data('_photobox').destroy();
						};
					} else {
						if (parent.$("#FaceBookGalleryPhotoBox").length != 0) {
							parent.$("#pbOverlay").attr("data-album", "").attr("data-photo", "");
							parent.$('#FaceBookGalleryPhotoBox').data('_photobox').destroy();
							parent.$("#FaceBookGalleryPhotoBox").remove();
						}
					}
					if (opts.consoleLogging) {
						console.log('Update: PhotoBox Lightbox for Album ' + albumId + ' has been removed from DOM!');
					};
				};
				if (typeof ajaxRequest !== 'undefined') {
					ajaxRequest.abort();
				}
				$('#fb-album-' + albumId).remove();
				$('#fb-album-paged-' + albumId).remove();
				if (opts.consoleLogging) {
					console.log('Update: All Data for Album ' + albumId + ' has been removed from DOM!');
				};
			};
		};

		// Messi Message Overlays
		function showMessiContent(MessiContent, MessiTitle, MessiCode) {
			var currentWindowHeight = jQuery(window).height() * 0.75 + "px";
			new Messi(MessiContent, {
				title:              MessiTitle,
				titleClass: 		MessiCode,
				buttons:            [{id: 0, label: 'Close', val: 'X'}],
				modal: 				true,
				modalOpacity:		0.70,
				width:				'600px',
				maxheight:			currentWindowHeight,
				viewport: 			{top: '50%', left: '50%'}
			});
		}
		
		// Initialize Pagination of Thumbnails
		function CallPagination(currentPageList, options, ID, Gallery, firstRun, Identifier, totalItems){
			var thisFileList 				= this;
			this.timeClassArray 			= new Array();
			TotalThumbs 					= 0;
			TotalPages 						= 0;
			TotalTypes 						= 0;
			TotalTimes						= 0;
			this.settings = {
				'searchBoxDefault' 		: 	"Search ...",
				'itemsPerPageDefault' 	: 	6,
				'hideToTop'				:	false,
				'hideFilter' 			: 	false,
				'hideSort' 				: 	false,
				'hideSearch' 			: 	false,
				'hidePager'				:	false
			};
			if ( options ) {
				$.extend( this.settings, options );
			};

			$('.' + (Gallery == true ? 'albumWrapper' : 'photoWrapper'), currentPageList).each(function(){
				var linkItem = $(this);
				var fileTime = linkItem.attr('data-time');
				var fileUTC = linkItem.attr('data-UTC');
				var added = false;
				TotalThumbs = TotalThumbs + 1;
				$.map(thisFileList.timeClassArray, function(elementOfArray, indexInArray) {
					if (thisFileList.timeClassArray[indexInArray].Time == fileTime) {
						added = true;
						//return false;
					};
				});
				if (!added) {
					TotalTimes = TotalTimes + 1;
					thisFileList.timeClassArray.push({
						Time: fileTime,
						UTC: fileUTC
					});
				}
				linkItem.addClass('paginationItem');
			});
			thisFileList.timeClassArray.sort(sortFilters('UTC'));

			currentPageList.wrap('<div id="' + ID + '" class="paginationMain" />');
			currentPageList.addClass('paginationFrame');
			this.hideToTopDef 				= this.settings.hideToTop;
			this.hideFilterDef 				= this.settings.hideFilter;
			this.hideSearchDef 				= this.settings.hideSearch;
			this.hideSortDef 				= this.settings.hideSort;
			this.hidePagerDef 				= this.settings.hidePager;
			this.PerPageItems 				= this.settings.itemsPerPageDefault;
			this.SearchBox 					= this.settings.searchBoxDefault;

			if ((opts.floatingControlBar == false) && (Gallery == true)) {
				var CSSAdjust = "padding-bottom: 0px;"
			} else if ((opts.floatingControlBar == false) || (Gallery == false)) {
				var CSSAdjust = "padding-bottom: 5px;"
			} else {
				var CSSAdjust = "";
			}

			this.paginationControls 		= $('<div id="paginationControls-' + Identifier +'" class="paginationControls' + ((opts.floatingControlBar == true && !isInIFrame) ? " Floater" : "") + '" style="' + CSSAdjust + '"></div>');
			this.paginationControls.append((this.hideToTopDef === false ? '<a id="Scroll_To_Top-' + Identifier +'" class="Scroll_To_Top float_right defaultPaginationStyle btn TipGeneric' + tooltipClass + '" style="display:none" ' + opts.tooltipTipAnchor + '="Click here to go back to the top of the Facebook gallery."><div id="To_Top-' + Identifier +'" class="To_Top"></div></a>' : ''));
			this.paginationControls.append((((Gallery === false) && (opts.allowAlbumDescription)) ? '<a id="Toggle_Info_Main-' + Identifier +'" class="Toggle_Info_Main TipGeneric ' + ((opts.showDescriptionStart) ? "Toggle_Info_On" : "Toggle_Info_Off") + ' float_right defaultPaginationStyle btn' + tooltipClass + '" ' + opts.tooltipTipAnchor + '="Click here to toggle the Album Description." href="#"><div id="Toggle_Info-' + Identifier +'" class="Toggle_Info"></div></a>' : ''));
			this.paginationControls.append((this.hideSortDef === false ? '<a href="#" id="showSortingBtn-' + Identifier +'" class="showSortingBtn float_right defaultPaginationStyle btn"><div class="AdjustSort">' + (Gallery == true ? opts.SortButtonTextAlbums : opts.SortButtonTextPhotos) + '</div></a>' : ''));
			this.paginationControls.append((((this.hideFilterDef === false) && (TotalTimes > 1)) ? '<a href="#" id="showFilterBtn-' + Identifier +'" class="showFilterBtn float_right defaultPaginationStyle btn"><div class="AdjustType">' + (Gallery == true ? opts.FilterButtonTextAlbums : opts.FilterButtonTextPhotos) + '</div></a>' : ''));
			this.paginationControls.append((this.hidePagerDef === false ? '<a href="#" id="showPagerBtn-' + Identifier +'" class="showPagerBtn float_right defaultPaginationStyle btn"><div class="AdjustPage">' + opts.PagesButtonText + '</div></a>' : ''));
			this.paginationControls.append((this.hideSearchDef === false ? '<div id="paginationSearch-' + Identifier +'" class="paginationSearch"><label style="display:none;">Search</label><input type="text" value="' + this.settings.searchBoxDefault + '" class="paginationSearchValue"><a class="paginationSearchGo btn defaultPaginationStyle"><div class="AdjustSearch">' + (Gallery == true ? opts.SearchButtonTextAlbums : opts.SearchButtonTextPhotos) + '</div></a><a class="clearSearch btn defaultPaginationStyle hidden"><div class="AdjustClear">Clear</div></a></div>' : '<div class="paginationSearch" style="height: 20px;"></div>'));
			this.paginationControls.append((((Gallery === false) && (!opts.singleAlbumOnly)) ? '<div id="Back-' + Identifier + '_3" class="BackButton fblink clearFixMe" style="margin-top: 10px; ' +  (((opts.showFloatingReturnButton == false) && (opts.showDescriptionStart == true)) ? "display: none;" : "") + '" data-href="#">' + opts.AlbumBackButtonText + '</div>' : ""));
			this.paginationControls.append(((opts.showThumbInfoInPageBar == true && (Gallery == true && opts.paginationLayoutAlbums == false) || (Gallery == false && opts.paginationLayoutPhotos == false)) ? '<span id="thumbnailStage-' + Identifier +'" class="thumbnailStage" style="' + (Gallery == false ? "margin: -25px 0px 0px 0px" : "") + '"> (' + opts.PaginationShowingText + ' ' + (Gallery == true ? opts.PaginationAlbumsText : opts.PaginationPhotosText) + ' <span class="thumbnailStageA"></span> ' + opts.PaginationItemsToText + ' <span class="thumbnailStageB"></span> ' + opts.PaginationOutOfTotalText + ' <span class="thumbnailStageC"></span>)</span>' : ''));
			
			// Create and Define Filter List
			var filterList = '<div id="paginationFilters-' + Identifier +'" class="paginationFilters"><ul id="Filter-Selections-' + Identifier +'" class="Selections unstyled">';
			if ((Gallery == true && opts.albumsFilterAllEnabled == true) || (!Gallery == true && opts.photosFilterAllEnabled == true)) {
				var enableAllFilters = true;
			} else {
				var enableAllFilters = false;
			}
			for(var i = 0; i < thisFileList.timeClassArray.length; i++){
				if (thisFileList.timeClassArray[i].Time != "") {
					filterList += '<li data-UTC="' + thisFileList.timeClassArray[i].UTC + '"><a href="" data-filter-type="' + thisFileList.timeClassArray[i].Time + '" class="'+ thisFileList.timeClassArray[i].Time +'Filter ' +  thisFileList.timeClassArray[i].Time + ' ' + (enableAllFilters == true ? "showing" : "")  + ' FilterA">' + thisFileList.timeClassArray[i].Time.replace(/_/g, ' ') + '</a></li>';
				}
			};
			filterList += '</ul><p class="bar"><a href="#" class="Closer">Close</a></p></div>';
			this.paginationControls.append((((this.hideFilterDef === false) && ((TotalTypes > 1) || (TotalTimes > 1))) ? filterList : ''));

			// Create and Define Paging List
			TotalPages = Math.ceil(TotalThumbs / this.PerPageItems);
			if (this.hidePagerDef === false) {
				this.paginationContainer = $('<div id="paginationPagers-' + Identifier +'" class="paginationPagers"></div>');
				this.paginationControls.append(this.paginationContainer);
				this.paginationListing = $('<ul id="Pager-Selections-' + Identifier +'" class="Selections unstyled"></ul>');
				this.paginationContainer.append(this.paginationListing);
				for (var i = 1; i < TotalPages+1; i++){
					this.paginationListing.append('<li><a id="Page_' + i + '_' + Identifier + '" class="" href="" data-filter-type="Page ' + i + '">' + i +'</a></li>');
				};
				this.paginationContainer.append('<p class="bar"><a href="#" class="Closer">Close</a></p>');
			}

			// Create and Define Sorting List
			if (Gallery) {
				var initialSort = (opts.defaultSortDirectionASC == true ? "asc" : "dec");
				if (defaultSortTypeAlbums == 'albumTitle') {SortByName = initialSort} else {SortByName = ""};
				if (defaultSortTypeAlbums == 'numberItems') {SortBySize = initialSort} else {SortBySize = ""};
				if (defaultSortTypeAlbums == 'createDate') {SortByCreated = initialSort} else {SortByCreated = ""};
				if (defaultSortTypeAlbums == 'updateDate') {SortByUpdated = initialSort} else {SortByUpdated = ""};
				if (defaultSortTypeAlbums == 'orderFacebook') {SortByOrder = initialSort} else {SortByOrder = ""};
				if (defaultSortTypeAlbums == 'FacebookID') {SortByID = initialSort} else {SortByID = ""};
				if (opts.showSelectionOnly) {
					if (defaultSortTypeAlbums == 'orderPreSet') {SortByPreSet = initialSort} else {SortByPreSet = ""};
				};
			} else {
				var initialSort = (opts.defaultPhotoDirectionsASC == true ? "asc" : "dec");
				if (defaultSortTypePhotos == 'addedDate') {SortByAdded = initialSort} else {SortByAdded = ""};
				if (defaultSortTypePhotos == 'updateDate') {SortByUpdated = initialSort} else {SortByUpdated = ""};
				if (defaultSortTypePhotos == 'orderFacebook') {SortByOrder = initialSort} else {SortByOrder = ""};
				if (defaultSortTypePhotos == 'FacebookID') {SortByID = initialSort} else {SortByID = ""};
			}

			if (this.hideSortDef === false) {
				this.paginationSorting = $('<div id="paginationSorting-' + Identifier +'" class="paginationSorting" style="' + (this.hideFilterDef === true ? 'right:0;' : '') + '">');
				this.paginationCriteria = $('<ul id="Sort-Selections-' + Identifier +'" class="Selections unstyled"></ul>');
				this.paginationCriteria.append(((TotalThumbs > 1 && Gallery && opts.albumAllowSortName) ? '<li><a id="SortByName" class="' + SortByName + '" href="" data-sort-type="bytitle" data-sort-direction="' + SortByName + '">' + opts.SortNameText + '</a></li>' : ''));
				this.paginationCriteria.append(((TotalThumbs > 1 && Gallery && opts.albumAllowSortItems) ? '<li><a id="SortBySize" class="' + SortBySize + '" href="" data-sort-type="bysize" data-sort-direction="' + SortBySize + '">' + opts.SortItemsText + '</a></li>' : ''));
				this.paginationCriteria.append(((TotalThumbs > 1 && Gallery && opts.albumAllowSortCreated) ? '<li><a id="SortByCreated" class="' + SortByCreated + '" href="" data-sort-type="bycreate" data-sort-direction="' + SortByCreated + '">' + opts.SortCreatedText + '</a></li>' : ''));
				this.paginationCriteria.append(((TotalThumbs > 1 && !Gallery && opts.photoAllowSortAdded) ? '<li><a id="SortByAdded" class="' + SortByAdded + '" href="" data-sort-type="byadded" data-sort-direction="' + SortByAdded + '">' + opts.SortAddedText + '</a></li>' : ''));
				this.paginationCriteria.append(((TotalThumbs > 1 && ((Gallery && opts.albumAllowSortUpdate) || (!Gallery && opts.photoAllowSortUpdate))) ? '<li><a id="SortByUpdated" class="' + SortByUpdated + '" href="" data-sort-type="byupdate" data-sort-direction="' + SortByUpdated + '">' + opts.SortUpdatedText + '</a></li>' : ''));
				this.paginationCriteria.append(((TotalThumbs > 1 && ((Gallery && opts.albumAllowSortID) || (!Gallery && opts.photoAllowSortID))) ? '<li><a id="SortByID" class="' + SortByID + '" href="" data-sort-type="byID" data-sort-direction="' + SortByID + '">' + opts.SortIDText + '</a></li>' : ''));
				this.paginationCriteria.append(((TotalThumbs > 1 && ((Gallery && opts.albumAllowSortFacebook) || (!Gallery && opts.photoAllowSortFacebook))) ? '<li><a id="SortByOrder" class="' + SortByOrder + '" href="" data-sort-type="byorder" data-sort-direction="' + SortByOrder + '">' + opts.SortFacebookText + '</a></li>' : ''));
				if (opts.showSelectionOnly) {
					this.paginationCriteria.append(((TotalThumbs > 1 && Gallery && opts.albumAllowSortPreSet) ? '<li><a id="SortByPreSet" class="' + SortByPreSet + '" href="" data-sort-type="bypreset" data-sort-direction="' + SortByPreSet + '">' + opts.SortPreSetText + '</a></li>' : ''));
				};
				this.paginationSorting.append(this.paginationCriteria);
				this.paginationSorting.append('<p class="bar"><a href="#" class="Closer">Close</a></p>');
				this.paginationControls.append(this.paginationSorting);
			}

			currentPageList.before('<div id="paginationBar-' + Identifier +'" class="paginationBar"></div>');

			if (opts.showTopPaginationBar) {
				if (((Gallery) && (!opts.infiniteScrollAlbums)) || ((!Gallery) && (!opts.infiniteScrollPhotos))) {
					$(".paginationBar", $("#" + ID)).append('<div id="paginationButtonsTop-' + Identifier +'" class="paginationButtons ControlsTop" style="' + (opts.floatingControlBar == false ? "padding-top: 15px;" : "") + '">'
															+ '<a href="#" class="pfl_first btn defaultPaginationStyle"><div id="FirstPage"></div></a>'
															+ '<a href="#" class="pfl_prev btn disabled defaultPaginationStyle"><div id="PrevPage"></div></a>'
															+ '<a href="#" class="pfl_last btn defaultPaginationStyle"><div id="LastPage"></div></a>'
															+ '<a href="#" class="pfl_next btn defaultPaginationStyle"><div id="NextPage"></div></a>'
															+ '<span class="pagingInfo" style="' + (opts.showThumbInfoInPageBar == false ? "margin-top: 5px;" : "") + '">' + opts.PaginationPageText + ' <span class="currentPage"></span> ' + opts.PaginationPageOfText + ' <span class="totalPages"></span></span>'
															+ (opts.showThumbInfoInPageBar == true ? '<span class="thumbnailStage"> (' + opts.PaginationShowingText + ' ' + (Gallery == true ? opts.PaginationAlbumsText : opts.PaginationPhotosText) + ' <span class="thumbnailStageA"></span> ' + opts.PaginationItemsToText + ' <span class="thumbnailStageB"></span> ' + opts.PaginationOutOfTotalText + ' <span class="thumbnailStageC"></span>)</span>' : '')
															+ '</div>');
				}
			}

			if (((Gallery == true) && (albumsShowControlBar)) || (Gallery == false)){
				$(".paginationBar", $("#" + ID)).append(this.paginationControls);
			}
			$(".paginationBar", $("#" + ID)).append('<div style="display:none" class="paginationMessage"><div id="ErrorMessage"></div><a class="btn defaultPaginationStyle" href="#">Show All Albums</a></div>');
			$(".paginationBar", $("#" + ID)).append('<div style="display:none" class="paginationEmpty"><span></span></div>');

			if (opts.showBottomPaginationBar) {
				if (((Gallery) && (!opts.infiniteScrollAlbums)) || ((!Gallery) && (!opts.infiniteScrollPhotos))) {
					currentPageList.after('<div id="paginationButtonsBottom-' + Identifier +'" class="paginationButtons ControlsBottom" style="' + (opts.floatingControlBar == false ? "padding-top: 15px;" : "") + '">'
															+ '<a href="#" class="pfl_first btn defaultPaginationStyle"><div id="FirstPage"></div></a>'
															+ '<a href="#" class="pfl_prev btn disabled defaultPaginationStyle"><div id="PrevPage"></div></a>'
															+ '<a href="#" class="pfl_last btn defaultPaginationStyle"><div id="LastPage"></div></a>'
															+ '<a href="#" class="pfl_next btn defaultPaginationStyle"><div id="NextPage"></div></a>'
															+ '<span class="pagingInfo" style="' + (opts.showThumbInfoInPageBar == false ? "margin-top: 5px;" : "") + '">' + opts.PaginationPageText + ' <span class="currentPage"></span> ' + opts.PaginationPageOfText + ' <span class="totalPages"></span></span>'
															+ (opts.showThumbInfoInPageBar == true ? '<span class="thumbnailStage"> (' + opts.PaginationShowingText + ' ' + (Gallery == true ? opts.PaginationAlbumsText : opts.PaginationPhotosText) + ' <span class="thumbnailStageA"></span> ' + opts.PaginationItemsToText + ' <span class="thumbnailStageB"></span> ' + opts.PaginationOutOfTotalText + ' <span class="thumbnailStageC"></span>)</span>' : '')
															+ '</div>');
				}
			}

			// Assign and Define Variables
			this.allThumbsContainer = 		$("#" + ID);
			this.messageBox = 				$('.paginationMessage', this.allThumbsContainer);
			this.messageText = 				$('span', this.messageBox);
			this.emptyThumbsBox = 			$('.paginationEmpty', this.allThumbsContainer);
			this.emptyThumbsText = 			$('span', this.emptyThumbsBox);
			this.filteredThumbs = 			$('.paginationItem', this.allThumbsContainer);
			this.allThumbs = 				$('.paginationItem', this.allThumbsContainer);
			this.fileContainer = 			$('.paginationFrame', this.allThumbsContainer);
			this.firstButton = 				$('.pfl_first', this.allThumbsContainer);
			this.lastButton = 				$('.pfl_last', this.allThumbsContainer);
			this.nextButton = 				$('.pfl_next', this.allThumbsContainer);
			this.prevButton = 				$('.pfl_prev', this.allThumbsContainer);
			this.pageAt = 					currentPageList.data('itemsperpage') !== undefined ? currentPageList.data('itemsperpage') : this.settings.itemsPerPageDefault;
			this.paginationContainer = 		$('.paginationButtons', this.allThumbsContainer);
			this.currentPageCounter = 		$('.currentPage', this.allThumbsContainer);
			this.totalPageCounter = 		$('.totalPages', this.allThumbsContainer);
			this.searchAndFilterContainer = this.paginationControls;
			this.showPagerBtn = 			$('.showPagerBtn', this.allThumbsContainer);
			this.pagerLinksContainer = 		$('.paginationPagers',this.allThumbsContainer);
			this.pagerLinks = 				$('li a', this.pagerLinksContainer);
			this.showFilterBtn = 			$('.showFilterBtn', this.allThumbsContainer);
			this.filterLinksContainer = 	$('.paginationFilters', this.allThumbsContainer);
			this.filterLinks = 				$('li a', this.filterLinksContainer);
			this.showSortingBtn = 			$('.showSortingBtn', this.allThumbsContainer);
			this.sortingLinksContainer = 	$('.paginationSorting', this.allThumbsContainer);
			this.sortingLinks = 			$('.paginationSorting li a', this.allThumbsContainer);
			this.searchBoxContainer = 		$('.paginationSearch', this.allThumbsContainer);
			this.searchBox = 				$('.paginationSearchValue', this.searchBoxContainer);
			this.searchButton = 			$('.paginationSearchGo', this.searchBoxContainer);
			this.clearSearchButton = 		$('.clearSearch', this.searchBoxContainer);
			this.currentPage = 				0;
			this.totalFiles = 				this.filteredThumbs.length;
			if (((Gallery) && (opts.infiniteScrollAlbums)) || ((!Gallery) && (opts.infiniteScrollPhotos))) {
				this.totalPages =			1;
			} else {
				this.totalPages = 			(Math.ceil(this.totalFiles / this.pageAt));
			}
			this.itemsLastPage = 			this.totalFiles - ((this.totalPages - 1) * this.PerPageItems);

			// Scroll To Top Click
			$('#Scroll_To_Top-' + Identifier).click(function(e){
				$('html, body').animate({scrollTop:$("#" + opts.frameID).offset().top - 20}, 'slow');
			});
			// Info Panel Toggle Button Click
			$('#Back-' + Identifier + '_3').unbind("click").bind('click', function(e){
				checkExisting($(this).attr('data-href'));
			});
			$('#Toggle_Info_Main-' + Identifier).click(function(e){
				$('#fb-album-header').slideFade();
				if ($(this).hasClass('Toggle_Info_On')) {
					$(this).addClass('Toggle_Info_Off');
					$(this).removeClass('Toggle_Info_On');
					if ((!opts.showFloatingReturnButton)) {$('#Back-' + Identifier + '_3').show();}
				} else {
					$(this).addClass('Toggle_Info_On');
					$(this).removeClass('Toggle_Info_Off');
					if ((!opts.showFloatingReturnButton)) {$('#Back-' + Identifier + '_3').hide();}
				}
				if (isInIFrame && iFrameDetection) {
					AdjustIFrameDimensions();
				}
			});
			// Paging Click Events
			if(this.pageAt < this.totalFiles){
				this.nextButton.click(function(event) {
					event.preventDefault();
					if($(this).hasClass('disabled')){return false;}
					//currentPageList.isotope('destroy');
					thisFileList.currentPage++;
					showHidePages(thisFileList, Gallery, Identifier);
					isotopeGallery(currentPageList, Gallery, false, Identifier);
					return false;
				});
				this.prevButton.click(function(event) {
					event.preventDefault();
					if($(this).hasClass('disabled')){return false;}
					//currentPageList.isotope('destroy');
					thisFileList.currentPage--;
					showHidePages(thisFileList, Gallery, Identifier);
					isotopeGallery(currentPageList, Gallery, false, Identifier);
					return false;
				});
				this.firstButton.click(function(event) {
					event.preventDefault();
					if($(this).hasClass('disabled')){return false;}
					//currentPageList.isotope('destroy');
					thisFileList.currentPage = 0;
					showHidePages(thisFileList, Gallery, Identifier);
					isotopeGallery(currentPageList, Gallery, false, Identifier);
					return false;
				});
				this.lastButton.click(function(event) {
					event.preventDefault();
					if($(this).hasClass('disabled')){return false;}
					//currentPageList.isotope('destroy');
					thisFileList.currentPage = thisFileList.totalPages - 1;
					showHidePages(thisFileList, Gallery, Identifier);
					isotopeGallery(currentPageList, Gallery, false, Identifier);
					return false;
				});
			};
			$('.paginationPagers li a').click(function(event){
				event.preventDefault();
				//currentPageList.isotope('destroy');
				thisFileList.currentPage = $(this).text() - 1;
				showHidePages(thisFileList, Gallery, Identifier);
				thisFileList.pagerLinksContainer.toggle();
				isotopeGallery(currentPageList, Gallery, false, Identifier);
				return false;
			});
			// Filter Click Events
			this.showFilterBtn.click(function(event){
				event.preventDefault();
				$('#paginationFilters-' + Identifier, this.allThumbsContainer).css("left", $('#showFilterBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
				thisFileList.sortingLinksContainer.hide();
				thisFileList.pagerLinksContainer.hide();
				thisFileList.filterLinksContainer.toggle();
				return false;
			});
			$('.Closer', this.filterLinksContainer).click(function(event){
				event.preventDefault();
				thisFileList.sortingLinksContainer.hide();
				thisFileList.pagerLinksContainer.hide();
				thisFileList.filterLinksContainer.toggle();
				return false;
			});
			this.filterLinks.click(function(event){
				event.preventDefault();
				//currentPageList.isotope('destroy');
				if ((Gallery) && (opts.infiniteScrollAlbums)) {
					if ($('.albumWrapper.Showing.Infinite').length > infiniteAlbumsShow) {
						infiniteAlbumsShow = $('.albumWrapper.Showing.Infinite').length;
					}
				} else if ((!Gallery) && (opts.infiniteScrollPhotos)) {
					if ($('.photoWrapper.Showing.Infinite').length > infinitePhotosShow) {
						infinitePhotosShow = $('.photoWrapper.Showing.Infinite').length;
					}
				}
				$(this).toggleClass('showing');
				var typeToShowA = $('.paginationFilters a.showing', thisFileList.allThumbsContainer);
				var typesString = "";
				if (typeToShowA.length > 0){
					thisFileList.fileContainer.show();
					$.each(typeToShowA, function(){
						typesString += "." + $(this).data('filter-type') + ',';
					});
					thisFileList.filteredThumbs.remove();
					thisFileList.filteredThumbs = thisFileList.allThumbs.filter(typesString.slice(0,-1));
					if(thisFileList.filteredThumbs.length === 0){
						showHideMessage("Sorry, no images of selected type(s) could be found.", thisFileList, Gallery, Identifier);
					} else {
						thisFileList.fileContainer.append(thisFileList.filteredThumbs);
						sortGallery(SortingOrder, SortingType, thisFileList, Gallery);
						thisFileList.currentPage = 0;
						resetPaging(thisFileList);
						showHidePages(thisFileList, Gallery, Identifier);
						showHideMessage("", thisFileList, Gallery, Identifier);
					};
					$(".Selections").css("max-height", 300);
				} else {
					if (((Gallery) && (!opts.albumsFilterAllEnabled)) || ((!Gallery) && (!opts.photosFilterAllEnabled))) {
						thisFileList.filteredThumbs.remove();
						thisFileList.filteredThumbs = thisFileList.allThumbs;
						thisFileList.fileContainer.append(thisFileList.filteredThumbs);
						sortGallery(SortingOrder, SortingType, thisFileList, Gallery);
						thisFileList.currentPage = 0;
						resetPaging(thisFileList);
						showHidePages(thisFileList, Gallery, Identifier);
						showHideMessage("", thisFileList, Gallery, Identifier);
						$(".Selections").css("max-height", 300);
					} else {
						showHideMessage('No types selected.', thisFileList, Gallery, Identifier);
						$(".Selections").css("max-height", 75);
						thisFileList.fileContainer.hide();
					}
				};
				if ((opts.showThumbInfoInPageBar == true && opts.paginationLayoutAlbums == false)) {
					$('.thumbnailStageA', thisFileList.allThumbsContainer).text(((parseInt(thisFileList.currentPage)) * parseInt(thisFileList.PerPageItems) + 1));
					$('.thumbnailStageB', thisFileList.allThumbsContainer).text(((parseInt(thisFileList.currentPage) + 1) * thisFileList.PerPageItems < parseInt(thisFileList.filteredThumbs.length) ? ((parseInt(thisFileList.currentPage) + 1) * thisFileList.PerPageItems) : parseInt(thisFileList.filteredThumbs.length)));
					$('.thumbnailStageC', thisFileList.allThumbsContainer).text(parseInt(thisFileList.filteredThumbs.length));
				}
				isotopeGallery(currentPageList, Gallery, false, Identifier);
				if ($('#paginationFilters-' + Identifier, this.allThumbsContainer).length != 0) {
					$('#paginationFilters-' + Identifier, this.allThumbsContainer).css("left", $('#showFilterBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
				}
				if ($('#paginationSorting-' + Identifier, this.allThumbsContainer).length !=0) {
					$('#paginationSorting-' + Identifier, this.allThumbsContainer).css("left", $('#showSortingBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
				}
				if ($('#paginationPagers-' + Identifier, this.allThumbsContainer).length !=0) {
					$('#paginationPagers-' + Identifier, this.allThumbsContainer).css("left", $('#showPagerBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
				}
				// Adjust Height of iFrame Container (if applicable)
				AdjustIFrameDimensions();
				return false;
			});
			// Search Click Events
			this.searchBox.focus(function(e){
				$(this).addClass("active");
				if($(this).val() === thisFileList.settings.searchBoxDefault){
					$(this).val("");
				};
			});
			this.searchBox.blur(function(e){
				$(this).removeClass("active");
				if($(this).val() === "") {
					$(this).val(thisFileList.settings.searchBoxDefault);
				};
				if ((opts.imageLazyLoad) && ($.isFunction($.fn.lazyloadanything))) {
					$.fn.lazyloadanything('load');
				}
			});
			this.searchBox.keydown(function (e){
				if(e.keyCode === 13){
					thisFileList.searchButton.click();
				};
			});
			this.searchButton.click(function(e){
				e.preventDefault();
				if (thisFileList.searchBox.val() !== "" && thisFileList.searchBox.val() !== thisFileList.settings.searchBoxDefault){
					thisFileList.searchBox.removeClass("error");
					thisFileList.filteredThumbs.remove();
					thisFileList.filteredThumbs = thisFileList.allThumbs.filter(':containsNC(' + thisFileList.searchBox.val() + ')');
					if(thisFileList.filteredThumbs.length > 0){
						//currentPageList.isotope('destroy');
						$('#' + opts.infiniteAlbumsID).unbind('inview');
						thisFileList.fileContainer.append(thisFileList.filteredThumbs);
						thisFileList.currentPage = 0;
						resetPaging(thisFileList);
						showHidePages(thisFileList, Gallery, Identifier);
						showHideMessage("", thisFileList, Gallery, Identifier);
						thisFileList.clearSearchButton.removeClass('hidden');
						thisFileList.fileContainer.show();
						isotopeGallery(currentPageList, Gallery, false, Identifier);
					} else {
						thisFileList.clearSearchButton.removeClass('hidden');
						showHideMessage("No albums matching your keyword(s) could be found.", thisFileList, Gallery, Identifier);
						thisFileList.fileContainer.hide();
					};
				} else {
					thisFileList.searchBox.addClass("error");
				};
				if ($('#paginationFilters-' + Identifier, this.allThumbsContainer).length != 0) {
					$('#paginationFilters-' + Identifier, this.allThumbsContainer).css("left", $('#showFilterBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
				}
				if ($('#paginationSorting-' + Identifier, this.allThumbsContainer).length !=0) {
					$('#paginationSorting-' + Identifier, this.allThumbsContainer).css("left", $('#showSortingBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
				}
				if ($('#paginationPagers-' + Identifier, this.allThumbsContainer).length !=0) {
					$('#paginationPagers-' + Identifier, this.allThumbsContainer).css("left", $('#showPagerBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
				}
				// Adjust Height of iFrame Container (if applicable)
				AdjustIFrameDimensions();
			});
			this.clearSearchButton.click(function(e){
				e.preventDefault();
				//currentPageList.isotope('destroy');
				thisFileList.searchBox.val(thisFileList.SearchBox);
				resetWholeList(thisFileList, currentPageList, Identifier, Gallery, firstRun);
				isotopeGallery(currentPageList, Gallery, false, Identifier);
				return false;
			});
			// Show Options - Sorting Button
			this.showSortingBtn.click(function(event){
				event.preventDefault();
				$('#paginationSorting-' + Identifier, this.allThumbsContainer).css("left", $('#showSortingBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
				thisFileList.filterLinksContainer.hide();
				thisFileList.pagerLinksContainer.hide();
				thisFileList.sortingLinksContainer.toggle();
				return false;
			});
			// Show Options - Paging Button
			this.showPagerBtn.click(function(event){
				event.preventDefault();
				$('#paginationPagers-' + Identifier, this.allThumbsContainer).css("left", $('#showPagerBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
				thisFileList.filterLinksContainer.hide();
				thisFileList.sortingLinksContainer.hide();
				if ((Gallery) || (!Gallery && opts.cacheAlbumContents)) {
					showHidePages(thisFileList, Gallery, Identifier);
				}
				thisFileList.pagerLinksContainer.toggle();
				return false;
			});
			$('.Closer', this.sortingLinksContainer).click(function(){
				thisFileList.filterLinksContainer.hide();
				thisFileList.pagerLinksContainer.hide();
				thisFileList.sortingLinksContainer.toggle();
				return false;
			});
			$('.Closer', this.pagerLinksContainer).click(function(){
				thisFileList.filterLinksContainer.hide();
				thisFileList.sortingLinksContainer.hide();
				thisFileList.pagerLinksContainer.toggle();
				return false;
			});
			// Sorting Links Click Events
			this.sortingLinks.click(function(event){
				event.preventDefault();
				//currentPageList.isotope('destroy');
				if (((Gallery) && (opts.infiniteScrollAlbums)) || ((!Gallery) && (opts.infiniteScrollPhotos))) {
					$('#' + opts.infiniteAlbumsID).unbind('inview');
				}
				var clicked = $(this);
				var sortType = clicked.attr("data-sort-type");
				var sortDirection = "";
				var sortLinks = thisFileList.sortingLinks;
				sortDirection = clicked.attr("data-sort-direction");
				sortLinks.attr('class', '');
				if (sortDirection === "" || sortDirection === undefined){
					sortLinks.attr('data-sort-direction', '');
					sortDirection = 'asc';
				} else {
					if (clicked.attr('data-sort-direction') === 'asc'){
						sortDirection = 'dec';
					} else{
						sortDirection = 'asc';
					};
				};
				clicked.attr('data-sort-direction', sortDirection);
				clicked.addClass(sortDirection);
				SortingOrder = sortDirection;
				SortingType = sortType;
				sortGallery(sortDirection, sortType, thisFileList, Gallery);
				thisFileList.currentPage = 0;
				resetPaging(thisFileList);
				showHidePages(thisFileList, Gallery, Identifier);
				showHideMessage("", thisFileList, Gallery, Identifier);
				if (thisFileList.sortingLinksContainer.is(":hidden") == false){
					thisFileList.sortingLinksContainer.toggle();
				};
				isotopeGallery(currentPageList, Gallery, false, Identifier);
				return false;
			});
			// Show All Files Button
			$('.btn', thisFileList.messageBox).click(function(event){
				event.preventDefault();
				//currentPageList.isotope('destroy');
				thisFileList.searchBox.val(thisFileList.SearchBox);
				resetWholeList(thisFileList, currentPageList, Identifier, Gallery, firstRun);
				isotopeGallery(currentPageList, Gallery, false, Identifier);
				if ($('#paginationFilters-' + Identifier, this.allThumbsContainer).length != 0) {
					$('#paginationFilters-' + Identifier, this.allThumbsContainer).css("left", $('#showFilterBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
				}
				if ($('#paginationSorting-' + Identifier, this.allThumbsContainer).length !=0) {
					$('#paginationSorting-' + Identifier, this.allThumbsContainer).css("left", $('#showSortingBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
				}
				if ($('#paginationPagers-' + Identifier, this.allThumbsContainer).length !=0) {
					$('#paginationPagers-' + Identifier, this.allThumbsContainer).css("left", $('#showPagerBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
				}
				return false;
			});
			initialSorting(thisFileList, currentPageList, Gallery);
			showHidePages(thisFileList, Gallery, Identifier);
			isotopeGallery(currentPageList, Gallery, true, Identifier);
			if (!Gallery) {
				initializeLightboxes(thisFileList, currentPageList, Gallery, true, Identifier);
			}
			// Restart LazyLoad
			restartLazyLoad();
		}

		// Other Functions used for Pagination & Layout
		function showHideMessage (message, thisFileList, Gallery, Identifier){
			$("#ErrorMessage", thisFileList.messageBox).html(message);
			if (message.length > 0){
				thisFileList.filteredThumbs.remove();
				thisFileList.currentPage = 0;
				$('.thumbnailStageA', thisFileList.allThumbsContainer).text(0);
				$('.thumbnailStageB', thisFileList.allThumbsContainer).text(0);
				thisFileList.totalPages = 1;
				showHidePages(thisFileList, Gallery, Identifier);
				thisFileList.messageBox.show();
			} else {
				thisFileList.messageBox.hide();
			};
		};
		function showHidePages(thisFileList, Gallery, Identifier){
			thisFileList.filteredThumbs.hide();
			if (((Gallery) && (opts.infiniteScrollAlbums)) || ((!Gallery) && (opts.infiniteScrollPhotos))) {
				thisFileList.filteredThumbs.filter(':lt(' + ((parseInt(thisFileList.currentPage) + 1) * parseInt(thisFileList.pageAt)) + ')').addClass("Showing").addClass("Infinite").removeClass("Hiding").show();
			} else {
				thisFileList.filteredThumbs.filter(':lt(' + ((parseInt(thisFileList.currentPage) + 1) * parseInt(thisFileList.pageAt)) + ')').addClass("Showing").removeClass("Hiding").show();
			}
			thisFileList.filteredThumbs.filter(':lt(' + ((parseInt(thisFileList.currentPage) + 0) * parseInt(thisFileList.pageAt)) + ')').addClass("Hiding").removeClass("Showing").removeClass("Infinite").removeClass("isotope-item").hide();
			thisFileList.fileContainer.removeClass('loading');
			if (thisFileList.currentPage === 0){
				thisFileList.prevButton.addClass('disabled');
				thisFileList.firstButton.addClass('disabled');
			} else {
				thisFileList.prevButton.removeClass('disabled');
				thisFileList.firstButton.removeClass('disabled');
			};
			if (thisFileList.currentPage === (thisFileList.totalPages - 1)){
				thisFileList.nextButton.addClass('disabled');
				thisFileList.lastButton.addClass('disabled');
			} else {
				thisFileList.nextButton.removeClass('disabled');
				thisFileList.lastButton.removeClass('disabled');
			};
			if (thisFileList.totalPages > 1){
				thisFileList.paginationContainer.show();
				$('.currentPage', thisFileList.allThumbsContainer).text(parseInt(thisFileList.currentPage) + 1);
				$('.thumbnailStageA', thisFileList.allThumbsContainer).text(((parseInt(thisFileList.currentPage)) * parseInt(thisFileList.PerPageItems) + 1));
				$('.thumbnailStageB', thisFileList.allThumbsContainer).text(((parseInt(thisFileList.currentPage) + 1) * thisFileList.PerPageItems < parseInt(thisFileList.filteredThumbs.length) ? ((parseInt(thisFileList.currentPage) + 1) * thisFileList.PerPageItems) : parseInt(thisFileList.filteredThumbs.length)));
				$('.thumbnailStageC', thisFileList.allThumbsContainer).text(parseInt(thisFileList.filteredThumbs.length));
				$('.paginationPagers > ul > li > a').each(function() {
					if (this.innerHTML == (thisFileList.currentPage + 1)) {
						$(this).addClass('Active');
						$(this).removeClass('InActive');
						$(this).removeClass('Disabled');
					} else if (this.innerHTML > thisFileList.totalPages) {
						$(this).removeClass('Active');
						$(this).removeClass('InActive');
						$(this).addClass('Disabled');
					} else {
						$(this).removeClass('Active');
						$(this).removeClass('Disabled');
						$(this).addClass('InActive');
					};
				});
				thisFileList.showPagerBtn.show();
				resetPaging(thisFileList);
			} else {
				thisFileList.paginationContainer.hide();
				thisFileList.showPagerBtn.hide();
				if (TotalThumbs == 1) {
					thisFileList.showSortingBtn.hide();
				} else {
					thisFileList.showSortingBtn.show();
				};
			};
			var CurrentFiles = 0;
			$('.paginationItem', this.allThumbsContainer).each(function(index) {
				if ($(this).is(":visible")) {
					CurrentFiles = CurrentFiles + 1;
					$(this).addClass("Showing");
				} else {
					$(this).removeClass("Showing");
				}
			});
			if ((thisFileList.hideToTopDef == true) && (thisFileList.hideFilterDef == true) && (thisFileList.hideSearchDef == true) && (thisFileList.hideSortDef == true) && (thisFileList.hidePagerDef == true) && (opts.singleAlbumOnly == true)) {
				thisFileList.paginationControls.hide();
			} else {
				thisFileList.paginationControls.show();
			}
			thisFileList.fileContainer.show();
			$('span', thisFileList.emptyThumbsBox).html("");
			$('span', thisFileList.messageBox).html("");
			thisFileList.emptyThumbsBox.hide();
			if (Gallery) {
				// Add Rotate Effect to Album Thumbnails
				if ((opts.albumThumbRotate) && ($.isFunction($.fn.jrumble))) {
					$('.albumThumb').jrumble({
						x: 				opts.albumRumbleX,
						y: 				opts.albumRumbleY,
						rotation: 		opts.albumRotate,
						speed: 			opts.albumRumbleSpeed,
						opacity:		false,
						opacityMin:		0.6
					});
					$('.albumThumb').hover(function(){
						$(this).trigger('startRumble');
					}, function(){
						$(this).trigger('stopRumble');
					});
				};
				// Add Overlay Effect for Album Thumbnails
				if (opts.albumThumbOverlay) {
					$(".fb-album-overlay").css("opacity", "0");
					$(".fb-album-shareme").css("opacity", "0");
					$(".fb-album-overlay").hover(function () {
						$(this).stop().animate({opacity: .5}, "slow");
						var albumShareMe = $(this).attr("data-album");
						$("#fb-album-shareme-" + albumShareMe).stop().animate({opacity: .6}, "slow");
					}, function () {
						$(this).stop().animate({opacity: 0}, "slow");
						var albumShareMe = $(this).attr("data-album");
						$("#fb-album-shareme-" + albumShareMe).stop().animate({opacity: 0}, "slow");
					});
				} else {
					$(".fb-album-overlay").css("display", "none");
				}
				if ((opts.albumShowSocialShare) && (opts.albumThumbSocialShare)) {
					$(".fb-album-shareme").hover(function () {
						var albumShareMe = $(this).attr("data-album");
						if ((opts.albumThumbRotate) && ($.isFunction($.fn.jrumble))) {
							$('#' + albumShareMe).trigger('stopRumble');
						}
						$(this).stop().animate({opacity: .9}, "slow");
					}, function () {
						var albumShareMe = $(this).attr("data-album");
						if ((opts.albumThumbRotate) && ($.isFunction($.fn.jrumble))) {
							$('#' + albumShareMe).trigger('stopRumble');
						}
						$(this).stop().animate({opacity: 0}, "slow");
					});
				} else {
					$(".fb-album-shareme").css("display", "none");
				}
				/*$('.albumThumb').hover(function(){
					var albumInfo = $(this).attr("id");
					$("#albumDetails_" + albumInfo).css("display", "block");
				}, function(){
					var albumInfo = $(this).attr("id");
					$("#albumDetails_" + albumInfo).css("display", "none");
				});*/
			} else {
				// Add Rotate Effect to Photo Thumbnails
				if ((opts.photoThumbRotate) && ($.isFunction($.fn.jrumble))) {
					$('.photoThumb').jrumble({
						x: 				opts.photoRumbleX,
						y: 				opts.photoRumbleY,
						rotation: 		opts.photoRotate,
						speed: 			opts.photoRumbleSpeed,
						opacity:		false,
						opacityMin:		0.6
					});
					$('.photoThumb').hover(function(){
						$(this).trigger('startRumble');
					}, function(){
						$(this).trigger('stopRumble');
					});
				}
				// Add Overlay Effect for Photo Thumbnails
				if (opts.photoThumbOverlay) {
					$(".fb-photo-overlay").css("opacity", "0");
					$(".fb-photo-overlay").hover(function () {
						$(this).stop().animate({opacity: .5}, "slow");
						var photoShareMe = $(this).attr("data-photo");
						$("#fb-photo-shareme-" + photoShareMe).stop().animate({opacity: .6}, "slow");
					}, function () {
						$(this).stop().animate({opacity: 0}, "slow");
						var photoShareMe = $(this).attr("data-photo");
						$("#fb-photo-shareme-" + photoShareMe).stop().animate({opacity: 0}, "slow");
					});
				} else {
					$(".fb-photo-overlay").css("display", "none");
				}
				if ((opts.photoShowSocialShare) && (opts.photoThumbSocialShare)) {
					$(".fb-photo-shareme").hover(function () {
						var photoShareMe = $(this).attr("data-photo");
						if ((opts.photoThumbRotate) && ($.isFunction($.fn.jrumble))) {
							$('#Call_' + photoShareMe).trigger('stopRumble');
						}
						$(this).stop().animate({opacity: .9}, "slow");
					}, function () {
						var photoShareMe = $(this).attr("data-photo");
						if ((opts.photoThumbRotate) && ($.isFunction($.fn.jrumble))) {
							$('#Call_' + photoShareMe).trigger('stopRumble');
						}
						$(this).stop().animate({opacity: 0}, "slow");
					});
				} else {
					$(".fb-photo-shareme").css("display", "none");
				}
			}
			if ((opts.showThumbInfoInPageBar == true && (Gallery == true && opts.paginationLayoutAlbums == false) || (Gallery == false && opts.paginationLayoutPhotos == false))) {
				$('.thumbnailStageA', thisFileList.allThumbsContainer).text(((parseInt(thisFileList.currentPage)) * parseInt(thisFileList.PerPageItems) + 1));
				$('.thumbnailStageB', thisFileList.allThumbsContainer).text(((parseInt(thisFileList.currentPage) + 1) * thisFileList.PerPageItems < parseInt(thisFileList.filteredThumbs.length) ? ((parseInt(thisFileList.currentPage) + 1) * thisFileList.PerPageItems) : parseInt(thisFileList.filteredThumbs.length)));
				$('.thumbnailStageC', thisFileList.allThumbsContainer).text(parseInt(thisFileList.filteredThumbs.length));
			}

			if (((Gallery) && (opts.infiniteScrollAlbums)) || ((!Gallery) && (opts.infiniteScrollPhotos))) {
				//$("#" + opts.infiniteAlbumsID).show();
			}

			// Restart LazyLoad
			restartLazyLoad();
			// Adjust Height of iFrame Container (if applicable)
			AdjustIFrameDimensions();
		};
		function resetWholeList(thisFileList, currentPageList, Identifier, Gallery, firstRun){
			thisFileList.filteredThumbs.remove();
			thisFileList.filteredThumbs = thisFileList.allThumbs;
			thisFileList.fileContainer.append(thisFileList.filteredThumbs);
			if ((Gallery) && (opts.albumsFilterAllEnabled)) {
				thisFileList.filterLinks.addClass('showing');
			} else if ((!Gallery) && (opts.photosFilterAllEnabled)) {
				thisFileList.filterLinks.addClass('showing');
			}
			thisFileList.currentPage = 0;
			resetPaging(thisFileList);
			//showHidePages(thisFileList, Gallery, Identifier);
			showHideMessage("", thisFileList, Gallery, Identifier);
			thisFileList.clearSearchButton.addClass('hidden');
			if (typeof $("#Sort-Selections-" + Identifier).find(".asc").attr("data-sort-type") != 'undefined') {
				sortGallery('asc', $("#Sort-Selections-" + Identifier).find(".asc").attr("data-sort-type"), thisFileList, Gallery);
			} else {
				sortGallery('dec', $("#Sort-Selections-" + Identifier).find(".dec").attr("data-sort-type"), thisFileList, Gallery);
			}
			$(".Selections").css("max-height", 300);
			//currentPageList.isotope('flush');
			currentPageList.isotope('reloadItems');
			currentPageList.isotope({
				itemSelector: 				'.albumWrapper',
				animationEngine:			'best-available',
				itemPositionDataEnabled: 	false,
				transformsEnabled:			true,
				resizesContainer: 			true,
				masonry: {
					columnOffset: 			0
				},
				layoutMode: 				'masonry',
				filter:						'.Showing',
				onLayout: function( $elems, instance ) {
					if (!firstRun) {
						$('html, body').animate({scrollTop:$("#" + opts.frameID).offset().top - 20}, 'slow');
						// Restart LazyLoad
						restartLazyLoad();
					}
				}
			}, function($elems){});
			showHidePages(thisFileList, Gallery, Identifier);
			if ($('#paginationFilters-' + Identifier, this.allThumbsContainer).length != 0) {
				$('#paginationFilters-' + Identifier, this.allThumbsContainer).css("left", $('#showFilterBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
			}
			if ($('#paginationSorting-' + Identifier, this.allThumbsContainer).length !=0) {
				$('#paginationSorting-' + Identifier, this.allThumbsContainer).css("left", $('#showSortingBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
			}
			if ($('#paginationPagers-' + Identifier, this.allThumbsContainer).length !=0) {
				$('#paginationPagers-' + Identifier, this.allThumbsContainer).css("left", $('#showPagerBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
			}
			// Adjust Height of iFrame Container (if applicable)
			AdjustIFrameDimensions();
			// Restart LazyLoad
			restartLazyLoad();
		};
		function resetPaging(thisFileList){
			thisFileList.totalPages = Math.ceil(thisFileList.filteredThumbs.length / thisFileList.pageAt);
			$('.totalPages', thisFileList.allThumbsContainer).text(thisFileList.totalPages);
		};
		function infiniteGallery(currentPageList, Gallery, firstRun, Identifier) {
			if (opts.infiniteScrollMore) {
				$("#" + opts.infiniteMoreID).hide();
			};
			if ((Gallery) && (opts.infiniteScrollAlbums)) {
				var hiddenItemsCountView = $('.albumWrapper.isotope-hidden').length;
				if (hiddenItemsCountView != 0) {
					if (opts.infiniteScrollMore) {
						$("#" + opts.infiniteMoreID).show();
					};
					$('#' + opts.infiniteAlbumsID).unbind('inview').bind('inview', function(event, isInView, visiblePartX, visiblePartY) {
						if (isInView) {
							var nextItemsCount = 0;
							var totalItemsCount = $('.albumWrapper').length;
							var hiddenItemsCount = $('.albumWrapper.isotope-hidden').length;
							if (hiddenItemsCount != 0) {
								$("#" + opts.infiniteLoadID).show();
								$('.albumWrapper.isotope-hidden').each(function(i, elem) {
									if ($(this).is(":hidden") == true) {
										nextItemsCount++;
										if (nextItemsCount <= infiniteAlbums) {
											$(this).addClass("Showing").addClass("Infinite").show();
										}
									}
								});
								isotopeGallery(currentPageList, Gallery, false, Identifier);
								var visibleItemsCount = $('.albumWrapper.Showing.Infinite').length;
								$("#thumbnailStage-" + Identifier + " .thumbnailStageB").html(visibleItemsCount);
								if (visibleItemsCount == totalItemsCount) {
									if (opts.infiniteScrollMore) {
										$("#" + opts.infiniteMoreID).hide();
									};
									$('#' + opts.infiniteAlbumsID).unbind('inview');
								} else {
									if (opts.infiniteScrollMore) {
										$("#" + opts.infiniteMoreID).show();
									};
								}
							} else {
								$('#' + opts.infiniteAlbumsID).unbind('inview');
								if (opts.infiniteScrollMore) {
									$("#" + opts.infiniteMoreID).hide();
								};
							}
						}
					});
				} else {
					$('#' + opts.infiniteAlbumsID).unbind('inview');
					if (opts.infiniteScrollMore) {
						$("#" + opts.infiniteMoreID).hide();
					};
				}
			} else if ((!Gallery) && (opts.infiniteScrollPhotos)) {
				var hiddenItemsCountView = $('.photoWrapper.Wrapper_' + Identifier + '.isotope-hidden').length;
				if (hiddenItemsCountView != 0) {
					if (opts.infiniteScrollMore) {
						$("#" + opts.infiniteMoreID).show();
					};
					$('#' + opts.infinitePhotosID).unbind('inview').bind('inview', function(event, isInView, visiblePartX, visiblePartY) {
						if (isInView) {
							var nextItemsCount = 0;
							var totalItemsCount = $('.photoWrapper.Wrapper_' + Identifier).length;
							var hiddenItemsCount = $('.photoWrapper.Wrapper_' + Identifier + '.isotope-hidden').length;
							if (hiddenItemsCount != 0) {
								$("#" + opts.infiniteLoadID).show();
								$('#fb-album-' + Identifier + ' .photoWrapper.Wrapper_' + Identifier + '.isotope-hidden').each(function(i, elem) {
									if ($(this).is(":hidden") == true) {
										nextItemsCount++;
										if (nextItemsCount <= infinitePhotos) {
											$(this).addClass("Showing").addClass("Infinite").show();
										}
									}
								});
								isotopeGallery(currentPageList, Gallery, false, Identifier);
								var visibleItemsCount = $('.photoWrapper.Wrapper_' + Identifier + '.Showing.Infinite').length;
								$("#thumbnailStage-" + Identifier + " .thumbnailStageB").html(visibleItemsCount);
								if (visibleItemsCount == totalItemsCount) {
									if (opts.infiniteScrollMore) {
										$("#" + opts.infiniteMoreID).hide();
									};
									$('#' + opts.infinitePhotosID).unbind('inview');
								} else {
									if (opts.infiniteScrollMore) {
										$("#" + opts.infiniteMoreID).show();
									};
								}
							} else {
								$('#' + opts.infinitePhotosID).unbind('inview');
								if (opts.infiniteScrollMore) {
									$("#" + opts.infiniteMoreID).hide();
								};
							}
						}
					});
				} else {
					$('#' + opts.infinitePhotosID).unbind('inview');
					if (opts.infiniteScrollMore) {
						$("#" + opts.infiniteMoreID).hide();
					};
				}
			}
		}
		function isotopeGallery(currentPageList, Gallery, firstRun, Identifier) {
			if (Gallery) {
				if (opts.infiniteScrollAlbums) {
					var isotopeFilter = '.Showing.Infinite';
				} else {
					var isotopeFilter = '.Showing';
				}
				if (firstRun) {
					setTimeout(function(){
						currentPageList.isotope({
							itemSelector: 				'.albumWrapper',
							animationEngine:			'best-available',
							itemPositionDataEnabled: 	false,
							transformsEnabled:			true,
							resizesContainer: 			true,
							masonry: {
								columnOffset: 			0
							},
							layoutMode: 				'masonry',
							filter:						isotopeFilter,
							onLayout: function( $elems, instance ) {
								isotopeHeightContainer = currentPageList.height();
								if ((opts.floatingControlBar) && (!isInIFrame)) {
									$("#paginationControls-" + Identifier).stickyScroll({ container: $("#fb-albums-all-paged") })
								};
								if ($('#paginationFilters-' + Identifier, this.allThumbsContainer).length != 0) {
									$('#paginationFilters-' + Identifier, this.allThumbsContainer).css("left", $('#showFilterBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
								};
								if ($('#paginationSorting-' + Identifier, this.allThumbsContainer).length !=0) {
									$('#paginationSorting-' + Identifier, this.allThumbsContainer).css("left", $('#showSortingBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
								};
								if ($('#paginationPagers-' + Identifier, this.allThumbsContainer).length !=0) {
									$('#paginationPagers-' + Identifier, this.allThumbsContainer).css("left", $('#showPagerBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
								};
								infiniteGallery(currentPageList, Gallery, true, Identifier);
								$("#" + opts.infiniteLoadID).hide();
							}
						}, function($elems){});
					}, 100);
				} else {
					//currentPageList.isotope('flush');
					currentPageList.isotope('reloadItems');
					currentPageList.isotope({
						itemSelector: 				'.albumWrapper',
						animationEngine:			'best-available',
						itemPositionDataEnabled: 	false,
						transformsEnabled:			true,
						resizesContainer: 			true,
						masonry: {
							columnOffset: 			0
						},
						layoutMode: 				'masonry',
						filter:						isotopeFilter,
						onLayout: function( $elems, instance ) {
							isotopeHeightContainer = currentPageList.height();
							if (!opts.infiniteScrollAlbums) {
								$('html, body').animate({scrollTop:$("#" + opts.frameID).offset().top - 20}, 'slow');
							}
							if ($('#paginationFilters-' + Identifier, this.allThumbsContainer).length != 0) {
								$('#paginationFilters-' + Identifier, this.allThumbsContainer).css("left", $('#showFilterBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
							}
							if ($('#paginationSorting-' + Identifier, this.allThumbsContainer).length !=0) {
								$('#paginationSorting-' + Identifier, this.allThumbsContainer).css("left", $('#showSortingBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
							}
							if ($('#paginationPagers-' + Identifier, this.allThumbsContainer).length !=0) {
								$('#paginationPagers-' + Identifier, this.allThumbsContainer).css("left", $('#showPagerBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
							}
							infiniteGallery(currentPageList, Gallery, false, Identifier);
							$("#" + opts.infiniteLoadID).hide();
						}
					}, function($elems){});
				}
			} else {
				if (opts.infiniteScrollPhotos) {
					var isotopeFilter = '.Showing.Infinite';
				} else {
					var isotopeFilter = '.Showing';
				}
				if (firstRun) {
					setTimeout(function(){
						currentPageList.isotope({
							itemSelector: 				'.photoWrapper',
							animationEngine:			'best-available',
							itemPositionDataEnabled: 	false,
							transformsEnabled:			true,
							resizesContainer: 			true,
							masonry: {
								columnOffset: 			0
							},
							layoutMode: 				'masonry',
							filter:						isotopeFilter,
							onLayout: function( $elems, instance ) {
								isotopeHeightContainer = currentPageList.height();
								if ((opts.floatingControlBar) && (!isInIFrame)) {
									$("#paginationControls-" + Identifier).stickyScroll({ container: $("#fb-album-paged-" + Identifier) });
								};
								$('#Back-' + albumId + '_3').unbind("click").bind('click', function(e){
									if (!opts.cacheAlbumContents) {
										removeAlbumDOM(Identifier);
									}
									checkExisting($(this).attr('data-href'));
								});
								if ($('#paginationFilters-' + Identifier, this.allThumbsContainer).length != 0) {
									$('#paginationFilters-' + Identifier, this.allThumbsContainer).css("left", $('#showFilterBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
								};
								if ($('#paginationSorting-' + Identifier, this.allThumbsContainer).length !=0) {
									$('#paginationSorting-' + Identifier, this.allThumbsContainer).css("left", $('#showSortingBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
								};
								if ($('#paginationPagers-' + Identifier, this.allThumbsContainer).length !=0) {
									$('#paginationPagers-' + Identifier, this.allThumbsContainer).css("left", $('#showPagerBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
								};
								infiniteGallery(currentPageList, Gallery, true, Identifier);
								$("#" + opts.infiniteLoadID).hide();
							}
						}, function($elems){
							//currentPageList.isotope('reLayout');
						});
					}, 100);
				} else {
					//currentPageList.isotope('flush');
					currentPageList.isotope('reloadItems');
					currentPageList.isotope({
						itemSelector: 				'.photoWrapper',
						animationEngine:			'best-available',
						itemPositionDataEnabled: 	false,
						transformsEnabled:			true,
						resizesContainer: 			true,
						masonry: {
							columnOffset: 			0
						},
						layoutMode: 				'masonry',
						filter:						isotopeFilter,
						onLayout: function( $elems, instance ) {
							isotopeHeightContainer = currentPageList.height();
							if (!opts.infiniteScrollPhotos) {
								$('html, body').animate({scrollTop:$("#" + opts.frameID).offset().top - 20}, 'slow');
							}
							if ($('#paginationFilters-' + Identifier, this.allThumbsContainer).length != 0) {
								$('#paginationFilters-' + Identifier, this.allThumbsContainer).css("left", $('#showFilterBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
							};
							if ($('#paginationSorting-' + Identifier, this.allThumbsContainer).length !=0) {
								$('#paginationSorting-' + Identifier, this.allThumbsContainer).css("left", $('#showSortingBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
							};
							if ($('#paginationPagers-' + Identifier, this.allThumbsContainer).length !=0) {
								$('#paginationPagers-' + Identifier, this.allThumbsContainer).css("left", $('#showPagerBtn-' + Identifier, this.allThumbsContainer).position().left + 0);
							};
							if ((opts.photoBoxAllow) && ($.isFunction($.fn.photobox))) {
								if (!isInIFrame) {
									if ($("#pbOverlay").attr("data-album") == Identifier) {
										$("#pbOverlay").attr("data-album", "").attr("data-photo", "");
										$('#fb-album-' + Identifier).data('_photobox').destroy();
									};
								} else {
									if (parent.$("#FaceBookGalleryPhotoBox").length != 0) {
										parent.$("#pbOverlay").attr("data-album", "").attr("data-photo", "");
										parent.$('#FaceBookGalleryPhotoBox').data('_photobox').destroy();
										parent.$("#FaceBookGalleryPhotoBox").remove();
									}
								}
								initializeLightboxes(currentPageList, Gallery, firstRun, Identifier);
							}
							infiniteGallery(currentPageList, Gallery, false, Identifier);
							$("#" + opts.infiniteLoadID).hide();
						}
					}, function($elems){
						//currentPageList.isotope('reLayout');
					});
				}
			}
			// Restart LazyLoad
			restartLazyLoad();
			//return false;
		}
		function initializeLightboxes(thisFileList, currentPageList, Gallery, firstRun, Identifier) {
			// Assign Intermediary Click Event if Social Share is embedded in Thumbnail
			if ((opts.photoShowSocialShare) && (opts.photoThumbSocialShare)) {
				if (!lightboxEnabled) {
					$("a." + albumId).unbind("click").on("click", function(event){
						event.preventDefault();
						var link = $(this).attr("id");
						var target = $("#" + link).attr("target");
						if($.trim(target).length > 0) {
							window.open($("#" + link).attr("href"), target);
						} else {
							window.location = $("#" + link).attr("href", "_blank");
						}
					});
				}
				$(".Call_" + albumId).unbind("click").on("click", function(event){
					var retrieveCounter = $(this).attr("data-key");
					$('a#' + albumId + "_" + retrieveCounter).click();
				}).on('click', '.fb-photo-shareme', function(event) {
					event.stopPropagation();
				}).on("click", "a.PhotoSocialShare", function(event) {
					event.stopPropagation();
					if (opts.photoSocialSharePopup) {
						event.preventDefault();
						window.open(this.href, 'Share Photo', 'width=500, height=500');
					}
				});
			}
			// Initialize fancyBox Plugin for Photo Thumbnails
			if ((opts.fancyBoxAllow) && ($.isFunction($.fn.fancybox))) {
				if (isInIFrame) {
					$('a.' + albumId).click(function(e) {
						e.preventDefault();
						$(this).trigger('stopRumble');
						$(this).stop().animate({opacity: 1}, "slow");
						var currentImage = $(this).attr('data-key') - 1;
						$(".fb-photo-overlay").animate({opacity: 0}, "fast");
						var galleryArray = [];
						$('a.' + albumId).each(function(index){
							if ($(this).attr('href') != "undefined") {
								galleryArray.push({
									href: 	$(this).attr('href'),
									title:  $(this).attr(opts.tooltipTipAnchor),
									id:		$(this).attr('id'),
									key:	$(this).attr('data-key'),
									rel:	$(this).attr('rel'),
									short:	$(this).attr('data-short'),
									photo:	$(this).attr('data-photo')
								});
							}
						});
						if (parent.$("#FaceBookGalleryFancyBox").length > 0) {
							parent.$("#FaceBookGalleryFancyBox").empty();
							var $gallery = parent.$("#FaceBookGalleryFancyBox");
						} else {
							var $gallery = parent.$('<div id="FaceBookGalleryFancyBox">').hide().appendTo('body');
						}
						$.each(galleryArray, function(i){
							$('<a id="' + galleryArray[i].id + '" class="fancyBoxOutSource ' + albumId + '" rel="' + albumId + '" data-key="' + galleryArray[i].key + '" data-photo="' + galleryArray[i].photo + '" data-short="' + galleryArray[i].short + '" href="' + galleryArray[i].href + '" ' + opts.tooltipTipAnchor + '="' + galleryArray[i].title + '">' + galleryArray[i].id + '</a>').appendTo($gallery);
						});
						$gallery.find('a.fancyBoxOutSource').fancybox({
							padding: 			15,
							scrolling: 			'auto',
							autosize: 			true,
							fitToView: 			true,
							openEffect: 		'fade',
							openEasing: 		'swing',
							openSpeed: 			500,
							//closeBtn:			false,
							closeEffect: 		'fade',
							closeEasing: 		'swing',
							closeSpeed : 		500,
							closeClick: 		true,
							arrows: 			true,
							nextClick: 			false,
							playSpeed:			8000,
							afterLoad: function(){
								//this.title = (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
							},
							beforeShow: function(){
								this.title = $(this.element).attr(opts.tooltipTipAnchor);
								if (opts.lightboxSocialShare) {
									var thisPhotoID = $(this.element).attr("data-photo");
									if ($(this.element).attr("data-short").length != 0) {
										var thisPhotoLink = $(this.element).attr("data-short");
									} else {
										var thisPhotoLink = $(this.element).attr("href");
									}
									if (!this.title) {
										this.title = opts.lightBoxNoDescription;
									} else {
										this.title += '<br />';
									}
									this.title += '<ul id="light_socialcount_' + thisPhotoID + '" class="socialcount" style="float: right; height: 25px;">';
										this.title += '<li class="stumbleplus"><a id="Light_PhotoSocialShare_Stumble_' + thisPhotoID + '" class="Share_Stumble TipSocial' + tooltipClass + '" target="_blank" href="http://www.stumbleupon.com/submit?url=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li>';
										this.title += '<li class="googleplus"><a id="Light_PhotoSocialShare_Google_' + thisPhotoID + '" class="Share_Google TipSocial' + tooltipClass + '" target="_blank" href="https://plus.google.com/share?url=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Google Plus"><span class="social-icon icon-googleplus"></span></a></li>';
										this.title += '<li class="twitter"><a id="Light_PhotoSocialShare_Twitter_' + thisPhotoID + '" class="Share_Twitter TipSocial' + tooltipClass + '" target="_blank" href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Twitter"><span class="social-icon icon-twitter"></span></a></li>';
										this.title += '<li class="facebook"><a id="Light_PhotoSocialShare_Facebook_' + thisPhotoID + '" class="Share_Facebook TipSocial' + tooltipClass + '" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
										this.title += '<li class="savetodisk"><a id="Light_PhotoSocialShare_Save_' + thisPhotoID + '" class="Share_Save TipSocial' + tooltipClass + '" target="_blank" href="' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Save this Image to disk" data-original="' + $(this.element).attr("href") + '"><span class="social-icon icon-disk"></span></a></li>';
									this.title += '</ul>';
									this.title += '<br />';
								} else {
									if (!this.title) {
										this.title = opts.lightBoxNoDescription;
									}
								}
							},
							afterShow: function(){
								//this.title = (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
							},
							onUpdate: function(){
								//this.title = (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
							},
							helpers:  {
								overlay : {
									speedIn  : 		300,
									speedOut : 		300,
									opacity  : 		0.8,
									css      : {
										cursor : 	'pointer'
									},
									closeClick: 	true
								},
								title : {
										type : 		'inside'
								},
								buttons	: {
									position: 		'top'
								},
								thumbs	: {
									width	: 		50,
									height	: 		50
								}
							}
						});
						parent.$('a#' + $(this).attr('id')).click();
					});
				} else {
					var fancyBoxLink = "";
					$('a.' + albumId).fancybox({
						padding: 			15,
						scrolling: 			'auto',
						autosize: 			true,
						fitToView: 			true,
						openEffect: 		'fade',
						openEasing: 		'swing',
						openSpeed: 			500,
						//closeBtn:			false,
						closeEffect: 		'fade',
						closeEasing: 		'swing',
						closeSpeed : 		500,
						closeClick: 		true,
						arrows: 			true,
						nextClick: 			false,
						playSpeed:			8000,
						afterLoad: function(){
							//this.title = $(this.element).attr(opts.tooltipTipAnchor);
						},
						beforeShow: function(){
							this.title = $(this.element).attr(opts.tooltipTipAnchor);
							if (opts.lightboxSocialShare) {
								var thisPhotoID = $(this.element).attr("data-photo");
								if ($(this.element).attr("data-short").length != 0) {
									var thisPhotoLink = $(this.element).attr("data-short");
								} else {
									var thisPhotoLink = $(this.element).attr("href");
								}
								if (!this.title) {
									this.title = opts.lightBoxNoDescription;
								} else {
									this.title += '<br />';
								}
								this.title += '<ul id="light_socialcount_' + thisPhotoID + '" class="socialcount" style="float: right; height: 25px;">';
									this.title += '<li class="stumbleplus"><a id="Light_PhotoSocialShare_Stumble_' + thisPhotoID + '" class="Share_Stumble TipSocial' + tooltipClass + '" target="_blank" href="http://www.stumbleupon.com/submit?url=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li>';
									this.title += '<li class="googleplus"><a id="Light_PhotoSocialShare_Google_' + thisPhotoID + '" class="Share_Google TipSocial' + tooltipClass + '" target="_blank" href="https://plus.google.com/share?url=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Google Plus"><span class="social-icon icon-googleplus"></span></a></li>';
									this.title += '<li class="twitter"><a id="Light_PhotoSocialShare_Twitter_' + thisPhotoID + '" class="Share_Twitter TipSocial' + tooltipClass + '" target="_blank" href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Twitter"><span class="social-icon icon-twitter"></span></a></li>';
									this.title += '<li class="facebook"><a id="Light_PhotoSocialShare_Facebook_' + thisPhotoID + '" class="Share_Facebook TipSocial' + tooltipClass + '" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
									this.title += '<li class="savetodisk"><a id="Light_PhotoSocialShare_Save_' + thisPhotoID + '" class="Share_Save TipSocial' + tooltipClass + '" target="_blank" href="' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Save this Image to disk" data-original="' + $(this.element).attr("href") + '"><span class="social-icon icon-disk"></span></a></li>';
								this.title += '</ul>';
								this.title += '<br />';
							} else {
								if (!this.title) {
									this.title = opts.lightBoxNoDescription;
								}
							}
						},
						afterShow: function(){
							//this.title = $(this.element).attr(opts.tooltipTipAnchor);
						},
						onUpdate: function(){
							//this.title = $(this.element).attr(opts.tooltipTipAnchor);
						},
						helpers:  {
							overlay : {
								speedIn  : 		300,
								speedOut : 		300,
								opacity  : 		0.8,
								css      : {
									cursor : 	'pointer'
								},
								closeClick: 	true
							},
							title : {
									type : 		'inside'
							},
							buttons	: {
								position: 		'top'
							},
							thumbs	: {
								width	: 		50,
								height	: 		50
							}
						}
					});
				}
			}
			// Initialize colorBox Plugin for Photo Thumbnails
			if ((opts.colorBoxAllow) && ($.isFunction($.fn.colorbox))) {
				if (isInIFrame) {
					$('a.' + albumId).click(function(e) {
						e.preventDefault();
						$(this).trigger('stopRumble');
						$(this).stop().animate({opacity: 1}, "slow");
						var currentImage = $(this).attr('data-key') - 1;
						$(".fb-photo-overlay").animate({opacity: 0}, "fast");
						var galleryArray = [];
						$('a.' + albumId).each(function(index){
							if ($(this).attr('href') != "undefined") {
								galleryArray.push({
									href: 	$(this).attr('href'),
									title:  $(this).attr(opts.tooltipTipAnchor),
									id:		$(this).attr('id'),
									key:	$(this).attr('data-key'),
									rel:	$(this).attr('rel'),
									short:	$(this).attr('data-short'),
									photo:	$(this).attr('data-photo')
								});
							}
						});
						if (parent.$("#FaceBookGalleryColorBox").length > 0) {
							parent.$("#FaceBookGalleryColorBox").empty();
							var $gallery = parent.$("#FaceBookGalleryColorBox");
						} else {
							var $gallery = parent.$('<div id="FaceBookGalleryColorBox">').hide().appendTo('body');
						}
						$.each(galleryArray, function(i){
							$('<a id="' + galleryArray[i].id + '" class="ColorBoxOutSource" rel="' + galleryArray[i].rel + '" data-key="' + galleryArray[i].key + '" data-photo="' + galleryArray[i].photo + '" data-short="' + galleryArray[i].short + '" href="' + galleryArray[i].href + '" title="' + galleryArray[i].title + '">' + galleryArray[i].id + '</a>').appendTo($gallery);
						});
						$gallery.find('a.ColorBoxOutSource').colorbox({
							rel:				albumId,
							scalePhotos: 		true,
							maxWidth:			'100%',
							maxHeight:			'100%',
							scrolling:			false,
							returnFocus:		false,
							slideshow:			true,
							slideshowSpeed:		6000,
							slideshowAuto:		false,
							slideshowStart:		'<span id="cboxPlay"></span>',
							slideshowStop:		'<span id="cboxStop"></span>',
							current: 			'Image {current} of {total}',
							title: function(){
								var thisPhotoText = 	$(this).attr(opts.tooltipTipAnchor);
								var thisPhotoInfo = 	"";
								var thisPhotoID = 		$(this).attr("data-photo");
								if ($(this).attr("data-short").length != 0) {
									var thisPhotoLink = $(this).attr("data-short");
								} else {
									var thisPhotoLink = $(this).attr("href");
								}
								if (thisPhotoText.length != 0) {
									if (opts.createTooltipsLightbox) {
										thisPhotoInfo = '<div class="TipLightbox ' + tooltipClass + '" style="cursor: pointer;" ' + opts.tooltipTipAnchor + '="' + thisPhotoText + '">' + thisPhotoText + '</div>';
									} else {
										thisPhotoInfo = '<span>' + thisPhotoText + '</span>';
									}
									thisPhotoInfo += '<br />';
									thisPhotoInfo += '<ul id="light_socialcount_' + thisPhotoID + '" class="socialcount" style="float: right; height: 25px; margin-right: 10px;">';
										thisPhotoInfo += '<li class="stumbleplus"><a id="Light_PhotoSocialShare_Stumble_' + thisPhotoID + '" class="Share_Stumble TipSocial' + tooltipClass + '" target="_blank" href="http://www.stumbleupon.com/submit?url=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li>';
										thisPhotoInfo += '<li class="googleplus"><a id="Light_PhotoSocialShare_Google_' + thisPhotoID + '" class="Share_Google TipSocial' + tooltipClass + '" target="_blank" href="https://plus.google.com/share?url=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Google Plus"><span class="social-icon icon-googleplus"></span></a></li>';
										thisPhotoInfo += '<li class="twitter"><a id="Light_PhotoSocialShare_Twitter_' + thisPhotoID + '" class="Share_Twitter TipSocial' + tooltipClass + '" target="_blank" href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Twitter"><span class="social-icon icon-twitter"></span></a></li>';
										thisPhotoInfo += '<li class="facebook"><a id="Light_PhotoSocialShare_Facebook_' + thisPhotoID + '" class="Share_Facebook TipSocial' + tooltipClass + '" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
										thisPhotoInfo += '<li class="savetodisk"><a id="Light_PhotoSocialShare_Save_' + thisPhotoID + '" class="Share_Save TipSocial' + tooltipClass + '" target="_blank" href="' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Save this Image to disk" data-original="' + $(this.element).attr("href") + '"><span class="social-icon icon-disk"></span></a></li>';
									thisPhotoInfo += '</ul>';
									thisPhotoInfo += '<br />';
								} else {
									thisPhotoInfo = '<span>' + opts.lightBoxNoDescription + '</span>';
									thisPhotoInfo += '<br />';
									thisPhotoInfo += '<ul id="light_socialcount_' + thisPhotoID + '" class="socialcount" style="float: right; height: 25px; margin-right: 10px;">';
										thisPhotoInfo += '<li class="stumbleplus"><a id="Light_PhotoSocialShare_Stumble_' + thisPhotoID + '" class="Share_Stumble TipSocial' + tooltipClass + '" target="_blank" href="http://www.stumbleupon.com/submit?url=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share Image &#34;' + thisPhotoID + '&#34; on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li>';
										thisPhotoInfo += '<li class="googleplus"><a id="Light_PhotoSocialShare_Google_' + thisPhotoID + '" class="Share_Google TipSocial' + tooltipClass + '" target="_blank" href="https://plus.google.com/share?url=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share Image &#34;' + thisPhotoID + '&#34; on Google Plus"><span class="social-icon icon-googleplus"></span></a></li>';
										thisPhotoInfo += '<li class="twitter"><a id="Light_PhotoSocialShare_Twitter_' + thisPhotoID + '" class="Share_Twitter TipSocial' + tooltipClass + '" target="_blank" href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share Image &#34;' + thisPhotoID + '&#34; on Twitter"><span class="social-icon icon-twitter"></span></a></li>';
										thisPhotoInfo += '<li class="facebook"><a id="Light_PhotoSocialShare_Facebook_' + thisPhotoID + '" class="Share_Facebook TipSocial' + tooltipClass + '" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share Image &#34;' + thisPhotoID + '&#34; on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
										thisPhotoInfo += '<li class="savetodisk"><a id="Light_PhotoSocialShare_Save_' + thisPhotoID + '" class="Share_Save TipSocial' + tooltipClass + '" target="_blank" href="' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Save Image &#34;' + thisPhotoID + '&#34; to disk" data-original="' + $(this.element).attr("href") + '"><span class="social-icon icon-disk"></span></a></li>';
									thisPhotoInfo += '</ul>';
									thisPhotoInfo += '<br />';
								}
								return thisPhotoInfo;
							}
						});
						parent.$('a#' + $(this).attr('id')).click();
					});
				} else {
					$('a.' + albumId).colorbox({
						rel: 				albumId,
						scalePhotos: 		true,
						maxWidth:			'100%',
						maxHeight:			'100%',
						scrolling:			false,
						returnFocus:		false,
						slideshow:			true,
						slideshowSpeed:		6000,
						slideshowAuto:		false,
						slideshowStart:		'<span id="cboxPlay"></span>',
						slideshowStop:		'<span id="cboxStop"></span>',
						current: 			'Image {current} of {total}',
						title: function(){
							var thisPhotoText = 	$(this).attr(opts.tooltipTipAnchor);
							var thisPhotoInfo = 	"";
							var thisPhotoID = 		$(this).attr("data-photo");
							if ($(this).attr("data-short").length != 0) {
								var thisPhotoLink = $(this).attr("data-short");
							} else {
								var thisPhotoLink = $(this).attr("href");
							}
							if (thisPhotoText.length != 0) {
								if (opts.createTooltipsLightbox) {
									thisPhotoInfo = '<div class="TipLightbox ' + tooltipClass + '" style="cursor: pointer;" ' + opts.tooltipTipAnchor + '="' + thisPhotoText + '">' + thisPhotoText + '</div>';
								} else {
									thisPhotoInfo = '<span>' + thisPhotoText + '</span>';
								}
								thisPhotoInfo += '<br />';
								thisPhotoInfo += '<ul id="light_socialcount_' + thisPhotoID + '" class="socialcount" style="float: right; height: 25px; margin-right: 10px;">';
									thisPhotoInfo += '<li class="stumbleplus"><a id="Light_PhotoSocialShare_Stumble_' + thisPhotoID + '" class="Share_Stumble TipSocial' + tooltipClass + '" target="_blank" href="http://www.stumbleupon.com/submit?url=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li>';
									thisPhotoInfo += '<li class="googleplus"><a id="Light_PhotoSocialShare_Google_' + thisPhotoID + '" class="Share_Google TipSocial' + tooltipClass + '" target="_blank" href="https://plus.google.com/share?url=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Google Plus"><span class="social-icon icon-googleplus"></span></a></li>';
									thisPhotoInfo += '<li class="twitter"><a id="Light_PhotoSocialShare_Twitter_' + thisPhotoID + '" class="Share_Twitter TipSocial' + tooltipClass + '" target="_blank" href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Twitter"><span class="social-icon icon-twitter"></span></a></li>';
									thisPhotoInfo += '<li class="facebook"><a id="Light_PhotoSocialShare_Facebook_' + thisPhotoID + '" class="Share_Facebook TipSocial' + tooltipClass + '" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
									thisPhotoInfo += '<li class="savetodisk"><a id="Light_PhotoSocialShare_Save_' + thisPhotoID + '" class="Share_Save TipSocial' + tooltipClass + '" target="_blank" href="' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Save this Image to disk" data-original="' + $(this.element).attr("href") + '"><span class="social-icon icon-disk"></span></a></li>';
								thisPhotoInfo += '</ul>';
								thisPhotoInfo += '<br />';
							} else {
								thisPhotoInfo = '<span>' + opts.lightBoxNoDescription + '</span>';
								thisPhotoInfo += '<br />';
								thisPhotoInfo += '<ul id="light_socialcount_' + thisPhotoID + '" class="socialcount" style="float: right; height: 25px; margin-right: 10px;">';
									thisPhotoInfo += '<li class="stumbleplus"><a id="Light_PhotoSocialShare_Stumble_' + thisPhotoID + '" class="Share_Stumble TipSocial' + tooltipClass + '" target="_blank" href="http://www.stumbleupon.com/submit?url=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share Image &#34;' + thisPhotoID + '&#34; on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li>';
									thisPhotoInfo += '<li class="googleplus"><a id="Light_PhotoSocialShare_Google_' + thisPhotoID + '" class="Share_Google TipSocial' + tooltipClass + '" target="_blank" href="https://plus.google.com/share?url=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share Image &#34;' + thisPhotoID + '&#34; on Google Plus"><span class="social-icon icon-googleplus"></span></a></li>';
									thisPhotoInfo += '<li class="twitter"><a id="Light_PhotoSocialShare_Twitter_' + thisPhotoID + '" class="Share_Twitter TipSocial' + tooltipClass + '" target="_blank" href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share Image &#34;' + thisPhotoID + '&#34; on Twitter"><span class="social-icon icon-twitter"></span></a></li>';
									thisPhotoInfo += '<li class="facebook"><a id="Light_PhotoSocialShare_Facebook_' + thisPhotoID + '" class="Share_Facebook TipSocial' + tooltipClass + '" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Share Image &#34;' + thisPhotoID + '&#34; on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
									thisPhotoInfo += '<li class="savetodisk"><a id="Light_PhotoSocialShare_Save_' + thisPhotoID + '" class="Share_Save TipSocial' + tooltipClass + '" target="_blank" href="' + thisPhotoLink + '" ' + opts.tooltipTipAnchor + '="Save Image &#34;' + thisPhotoID + '&#34; to disk" data-original="' + $(this.element).attr("href") + '"><span class="social-icon icon-disk"></span></a></li>';
								thisPhotoInfo += '</ul>';
								thisPhotoInfo += '<br />';
							}
							return thisPhotoInfo;
						}
					});
				}
			}
			// Initialize prettyPhoto Plugin for Photo Thumbnails
			if ((opts.prettyPhotoAllow) && ($.isFunction($.fn.prettyPhoto))) {
				if (isInIFrame) {
					$('a.' + albumId).click(function(e) {
						e.preventDefault();
						$(this).trigger('stopRumble');
						$(this).stop().animate({opacity: 1}, "slow");
						var currentImage = $(this).attr('data-key') - 1;
						$(".fb-photo-overlay").animate({opacity: 0}, "fast");
						var galleryArray = [];
						$('a.' + albumId).each(function(index){
							if ($(this).attr('href') != "undefined") {
								galleryArray.push({
									href: 	$(this).attr('href'),
									title:  $(this).attr(opts.tooltipTipAnchor),
									id:		$(this).attr('id'),
									key:	$(this).attr('data-key'),
									rel:	$(this).attr('rel'),
									short:	$(this).attr('data-short'),
									photo:	$(this).attr('data-photo')
								});
							}
						});
						if (parent.$("#FaceBookGalleryPrettyPhoto").length > 0) {
							parent.$("#FaceBookGalleryPrettyPhoto").empty();
							var $gallery = parent.$("#FaceBookGalleryPrettyPhoto");
						} else {
							var $gallery = parent.$('<div id="FaceBookGalleryPrettyPhoto">').hide().appendTo('body');
						}
						$.each(galleryArray, function(i){
							$('<a id="' + galleryArray[i].id + '" class="prettyPhotoOutSource" rel="prettyPhoto[' + albumId + ']" data-key="' + galleryArray[i].key + '" data-photo="' + galleryArray[i].photo + '" data-short="' + galleryArray[i].short + '" href="' + galleryArray[i].href + '" title="' + galleryArray[i].title + '">' + galleryArray[i].id + '</a>').appendTo($gallery);
						});
						$gallery.find('a.prettyPhotoOutSource[rel^="prettyPhoto"]').prettyPhoto({
							animation_speed: 			'fast', 			/* fast/slow/normal */
							slideshow: 					8000, 				/* false OR interval time in ms */
							autoplay_slideshow: 		false, 				/* true/false */
							opacity: 					0.80, 				/* Value between 0 and 1 */
							show_title: 				true, 				/* true/false */
							allow_resize: 				true, 				/* Resize the photos bigger than viewport. true/false */
							counter_separator_label: 	'/', 				/* The separator for the gallery counter 1 "of" 2 */
							theme: 						'facebook', 		/* light_rounded / dark_rounded / light_square / dark_square / facebook */
							horizontal_padding: 		20, 				/* The padding on each side of the picture */
							hideflash: 					false, 				/* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
							wmode: 						'opaque', 			/* Set the flash wmode attribute */
							autoplay: 					true, 				/* Automatically start videos: True/False */
							modal: 						false, 				/* If set to true, only the close button will close the window */
							deeplinking: 				false, 				/* Allow prettyPhoto to update the url to enable deeplinking. */
							overlay_gallery: 			true, 				/* If set to true, a gallery will overlay the fullscreen image on mouse over */
							keyboard_shortcuts: 		true, 				/* Set to false if you open forms inside prettyPhoto */
							changepicturecallback: 		function(){			/* Called everytime a photo is shown/changed */
								if (opts.lightboxSocialShare) {
									parent.$(".Share_PrettyPhoto").each(function(index, value) {
										var currentHREF = $(this).attr("data-href");
										currentHREF += parent.$('#fullResImage').attr("src");
										$(this).attr("href", currentHREF)
									});
								}
								if (parent.$(".pp_description").html().length == 0) {
									parent.$(".pp_description").html(opts.lightBoxNoDescription).show();
								} else {
									parent.$(".pp_description").show();
								}
							},
							callback: 					function(){}, 		/* Called when prettyPhoto is closed */
							ie6_fallback: 				false,
							markup: 					'<div class="pp_pic_holder"> \
															<div class="ppt">&nbsp;</div> \
															<div class="pp_top"> \
																<div class="pp_left"></div> \
																<div class="pp_middle"></div> \
																<div class="pp_right"></div> \
															</div> \
															<div class="pp_content_container"> \
																<div class="pp_left"> \
																<div class="pp_right"> \
																	<div class="pp_content"> \
																		<div class="pp_loaderIcon"></div> \
																		<div class="pp_fade"> \
																			<a href="#" class="pp_expand" title="Expand the Image">Expand</a> \
																			<div class="pp_hoverContainer"> \
																				<a class="pp_next" href="#">next</a> \
																				<a class="pp_previous" href="#">previous</a> \
																			</div> \
																			<div id="pp_full_res"></div> \
																			<div class="pp_details"> \
																				<div class="pp_nav"> \
																					<a href="#" class="pp_arrow_previous">Previous</a> \
																					<p class="currentTextHolder">0/0</p> \
																					<a href="#" class="pp_arrow_next">Next</a> \
																				</div> \
																				<ul id="light_socialcount" class="socialcount clearFixMe" style="float: right; height: 25px; margin-right: 40px; display: ' + (opts.lightboxSocialShare == true ? "block;" : "none;") + '"> \
																					<li class="share_stumbleplus"><a id="Light_PhotoSocialShare_Stumble" class="Share_PrettyPhoto Share_Stumble TipSocial" target="_blank" data-href="http://www.stumbleupon.com/submit?url=" href="http://www.stumbleupon.com/submit?url=" ' + opts.tooltipTipAnchor + '="Share this Image on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li> \
																					<li class="share_googleplus"><a id="Light_PhotoSocialShare_Google" class="Share_PrettyPhoto Share_Google TipSocial" target="_blank" data-href="https://plus.google.com/share?url=" href="https://plus.google.com/share?url=" ' + opts.tooltipTipAnchor + '="Share this Image on Google Plus"><span class="social-icon icon-googleplus"></span></a></li> \
																					<li class="share_twitter"><a id="Light_PhotoSocialShare_Twitter" class="Share_PrettyPhoto Share_Twitter TipSocial" target="_blank" data-href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + '" href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + '" ' + opts.tooltipTipAnchor + '="Share this Image on Twitter"><span class="social-icon icon-twitter"></span></a></li> \
																					<li class="share_facebook"><a id="Light_PhotoSocialShare_Facebook" class="Share_PrettyPhoto Share_Facebook TipSocial" target="_blank" data-href="https://www.facebook.com/sharer/sharer.php?u=" href="https://www.facebook.com/sharer/sharer.php?u=" ' + opts.tooltipTipAnchor + '="Share this Image on Facebook"><span class="social-icon icon-facebook"></span></a></li> \
																					<li class="share_savetodisk"><a id="Light_PhotoSocialShare_Save" class="Share_PrettyPhoto Share_Save TipSocial" target="_blank" data-href="" href="" ' + opts.tooltipTipAnchor + '="Save this Image to disk" data-original="' + location.href + '"><span class="social-icon icon-disk"></span></a></li> \
																				</ul> \
																				{pp_social} \
																				<a class="pp_close" href="#">Close</a> \
																				<p class="pp_description"></p> \
																			</div> \
																		</div> \
																	</div> \
																</div> \
																</div> \
															</div> \
															<div class="pp_bottom"> \
																<div class="pp_left"></div> \
																<div class="pp_middle"></div> \
																<div class="pp_right"></div> \
															</div> \
														</div> \
														<div class="pp_overlay"></div>',
							social_tools: 				''
						});
						parent.$('a#' + $(this).attr('id')).click();
					});
				} else {
					$('a.' + albumId + '[rel^="prettyPhoto"]').prettyPhoto({
						animation_speed: 			'fast', 			/* fast/slow/normal */
						slideshow: 					8000, 				/* false OR interval time in ms */
						autoplay_slideshow: 		false, 				/* true/false */
						opacity: 					0.80, 				/* Value between 0 and 1 */
						show_title: 				true, 				/* true/false */
						allow_resize: 				true, 				/* Resize the photos bigger than viewport. true/false */
						counter_separator_label: 	'/', 				/* The separator for the gallery counter 1 "of" 2 */
						theme: 						'facebook', 		/* light_rounded / dark_rounded / light_square / dark_square / facebook */
						horizontal_padding: 		20, 				/* The padding on each side of the picture */
						hideflash: 					false, 				/* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
						wmode: 						'opaque', 			/* Set the flash wmode attribute */
						autoplay: 					true, 				/* Automatically start videos: True/False */
						modal: 						false, 				/* If set to true, only the close button will close the window */
						deeplinking: 				false, 				/* Allow prettyPhoto to update the url to enable deeplinking. */
						overlay_gallery: 			true, 				/* If set to true, a gallery will overlay the fullscreen image on mouse over */
						keyboard_shortcuts: 		true, 				/* Set to false if you open forms inside prettyPhoto */
						changepicturecallback: 		function(){			/* Called everytime a photo is shown/changed */
							if (opts.lightboxSocialShare) {
								$("a.Share_PrettyPhoto").each(function(index, value) {
									var currentHREF = $(this).attr("data-href");
									currentHREF += $('#fullResImage').attr("src");
									$(this).attr("href", currentHREF)
								});
							}
							if ($(".pp_description").html().length == 0) {
								$(".pp_description").html(opts.lightBoxNoDescription).show();
							} else {
								$(".pp_description").show();
							}
						},
						callback: 					function(){}, 		/* Called when prettyPhoto is closed */
						ie6_fallback: 				false,
						markup: 					'<div class="pp_pic_holder"> \
														<div class="ppt">&nbsp;</div> \
														<div class="pp_top"> \
															<div class="pp_left"></div> \
															<div class="pp_middle"></div> \
															<div class="pp_right"></div> \
														</div> \
														<div class="pp_content_container"> \
															<div class="pp_left"> \
															<div class="pp_right"> \
																<div class="pp_content"> \
																	<div class="pp_loaderIcon"></div> \
																	<div class="pp_fade"> \
																		<a href="#" class="pp_expand" title="Expand the Image">Expand</a> \
																		<div class="pp_hoverContainer"> \
																			<a class="pp_next" href="#">next</a> \
																			<a class="pp_previous" href="#">previous</a> \
																		</div> \
																		<div id="pp_full_res"></div> \
																		<div class="pp_details"> \
																			<div class="pp_nav"> \
																				<a href="#" class="pp_arrow_previous">Previous</a> \
																				<p class="currentTextHolder">0/0</p> \
																				<a href="#" class="pp_arrow_next">Next</a> \
																			</div> \
																			<ul id="light_socialcount" class="socialcount clearFixMe" style="float: right; height: 25px; margin-right: 40px; display: ' + (opts.lightboxSocialShare == true ? "block;" : "none;") + '"> \
																				<li class="share_stumbleplus"><a id="Light_PhotoSocialShare_Stumble" class="Share_PrettyPhoto Share_Stumble TipSocial" target="_blank" data-href="http://www.stumbleupon.com/submit?url=" href="http://www.stumbleupon.com/submit?url=" ' + opts.tooltipTipAnchor + '="Share this Image on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li> \
																				<li class="share_googleplus"><a id="Light_PhotoSocialShare_Google" class="Share_PrettyPhoto Share_Google TipSocial" target="_blank" data-href="https://plus.google.com/share?url=" href="https://plus.google.com/share?url=" ' + opts.tooltipTipAnchor + '="Share this Image on Google Plus"><span class="social-icon icon-googleplus"></span></a></li> \
																				<li class="share_twitter"><a id="Light_PhotoSocialShare_Twitter" class="Share_PrettyPhoto Share_Twitter TipSocial" target="_blank" data-href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + '" href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + '" ' + opts.tooltipTipAnchor + '="Share this Image on Twitter"><span class="social-icon icon-twitter"></span></a></li> \
																				<li class="share_facebook"><a id="Light_PhotoSocialShare_Facebook" class="Share_PrettyPhoto Share_Facebook TipSocial" target="_blank" data-href="https://www.facebook.com/sharer/sharer.php?u=" href="https://www.facebook.com/sharer/sharer.php?u=" ' + opts.tooltipTipAnchor + '="Share this Image on Facebook"><span class="social-icon icon-facebook"></span></a></li> \
																				<li class="share_savetodisk"><a id="Light_PhotoSocialShare_Save" class="Share_PrettyPhoto Share_Save TipSocial" target="_blank" data-href="" href="" ' + opts.tooltipTipAnchor + '="Save this Image to disk" data-original="' + location.href + '"><span class="social-icon icon-disk"></span></a></li> \
																			</ul> \
																			{pp_social} \
																			<a class="pp_close" href="#">Close</a> \
																			<p class="pp_description"></p> \
																		</div> \
																	</div> \
																</div> \
															</div> \
															</div> \
														</div> \
														<div class="pp_bottom"> \
															<div class="pp_left"></div> \
															<div class="pp_middle"></div> \
															<div class="pp_right"></div> \
														</div> \
													</div> \
													<div class="pp_overlay"></div>',
						social_tools: 				''
					});
				}
			}
			// Initialize photoBox Plugin for Photo Thumbnails
			if ((opts.photoBoxAllow) && ($.isFunction($.fn.photobox))) {
				if (isInIFrame) {
					$('a.' + albumId).click(function(e) {
						e.preventDefault();
						$(this).trigger('stopRumble');
						$(this).stop().animate({opacity: 1}, "slow");
						var currentImage = $(this).attr('data-key') - 1;
						$(".fb-photo-overlay").animate({opacity: 0}, "fast");
						var galleryArray = [];
						$('a.' + albumId).each(function(index){
							if ($(this).attr('href') != "undefined") {
								galleryArray.push({
									href: 	$(this).attr('href'),
									title:  $(this).attr(opts.tooltipTipAnchor),
									id:		$(this).attr('id'),
									key:	$(this).attr('data-key'),
									rel:	$(this).attr('rel'),
									short:	$(this).attr('data-short'),
									photo:	$(this).attr('data-photo')
								});
							}
						});
						if (parent.$("#FaceBookGalleryPhotoBox").length > 0) {
							parent.$("#FaceBookGalleryPhotoBox").empty();
							var $gallery = parent.$("#FaceBookGalleryPhotoBox");
						} else {
							var $gallery = parent.$('<div id="FaceBookGalleryPhotoBox">').hide().appendTo('body');
						}
						$.each(galleryArray, function(i){
							$('<a id="' + galleryArray[i].id + '" class="photoBoxOutSource ' + albumId + '" rel="' + albumId + '" data-key="' + galleryArray[i].key + '" data-photo="' + galleryArray[i].photo + '" data-short="' + galleryArray[i].short + '" href="' + galleryArray[i].href + '" ' + opts.tooltipTipAnchor + '="' + galleryArray[i].title + '"><img style="width: 50px; height: auto;" src="' + galleryArray[i].href + '"></a>').appendTo($gallery);
						});
						parent.$('#FaceBookGalleryPhotoBox').photobox('a.' + albumId, {
							history:			false,
							time:				5000,
							autoplay:			false,
							thumbs:				true
						}, photoBoxCallback);
						parent.$('a#' + $(this).attr('id')).click();
					});
				} else {
					$('#fb-album-' + albumId).photobox('.photoWrapper a.' + albumId, {
						history:			false,
						time:				5000,
						autoplay:			false,
						thumbs:				true
					}, photoBoxCallback);
				}
			}
		}
		function photoBoxCallback(){
			var currentTitle = $("#pbOverlay #pbCaption .pbCaptionText .title").html();
			var currentAlbum = $(this).attr("data-album");
			var currentPhoto = $(this).attr("data-photo");
			if (currentTitle.length == 0) {
				$("#pbOverlay #pbCaption .pbCaptionText .title").html(opts.lightBoxNoDescription);
			}
			$("#pbOverlay").attr("data-album", currentAlbum).attr("data-photo", currentPhoto);
			if (opts.lightboxSocialShare) {
				var currentKey = $(this).attr("data-key");
				var curentLink = $(this).attr("href");
				var socialTitle = '<div class="photoBoxSocial clearFixMe" style="margin: 10px auto; width: 130px; z-index: 999999;">';
				socialTitle += '<ul id="socialcount_' + currentPhoto + '" data-parent="' + currentAlbum + '_' + currentKey + '" class="socialcount" style="">';
					socialTitle += '<li class="stumbleplus"><a id="PhotoSocialShare_Stumble_' + currentPhoto + '" class="Share_Stumble TipSocial' + tooltipClass + '" target="_blank" href="http://www.stumbleupon.com/submit?url=' + curentLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Stumble Upon"><span class="social-icon icon-stumbleupon"></span></a></li>';
					socialTitle += '<li class="googleplus"><a id="PhotoSocialShare_Google_' + currentPhoto + '" class="Share_Google TipSocial' + tooltipClass + '" target="_blank" href="https://plus.google.com/share?url=' + curentLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Google Plus"><span class="social-icon icon-googleplus"></span></a></li>';
					socialTitle += '<li class="twitter"><a id="PhotoSocialShare_Twitter_' + currentPhoto + '" class="Share_Twitter TipSocial' + tooltipClass + '" target="_blank" href="https://twitter.com/intent/tweet?text=' + opts.SocialSharePhotoText + curentLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Twitter"><span class="social-icon icon-twitter"></span></a></li>';
					socialTitle += '<li class="facebook"><a id="PhotoSocialShare_Facebook_' + currentPhoto + '" class="Share_Facebook TipSocial' + tooltipClass + '" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' + curentLink + '" ' + opts.tooltipTipAnchor + '="Share this Image on Facebook"><span class="social-icon icon-facebook"></span></a></li>';
					socialTitle += '<li class="savetodisk"><a id="PhotoSocialShare_Save_' + currentPhoto + '" class="Share_Save TipSocial' + tooltipClass + '" target="_blank" href="' + curentLink + '" ' + opts.tooltipTipAnchor + '="Save this Image to disk"><span class="social-icon icon-disk"></span></a></li>';
				socialTitle += '</ul>';
				socialTitle += '</div>';
				$("#pbOverlay #pbCaption .pbCaptionText .photoBoxSocial").remove();
				$("#pbOverlay #pbCaption .pbCaptionText").prepend(socialTitle);
			}
		}
		function initialSorting(thisFileList, currentPageList, Gallery) {
			if (Gallery) {
				if (defaultSortTypeAlbums == 'albumTitle') 				{sortType = "bytitle"
				} else if (defaultSortTypeAlbums == 'numberItems') 		{sortType = "bysize"
				} else if (defaultSortTypeAlbums == 'createDate') 		{sortType = "bycreate"
				} else if (defaultSortTypeAlbums == 'updateDate') 		{sortType = "byupdate"
				} else if (defaultSortTypeAlbums == 'orderFacebook') 	{sortType = "byorder"
				} else if (defaultSortTypeAlbums == 'FacebookID') 		{sortType = "byID"
				} else if (defaultSortTypeAlbums == 'orderPreSet') 		{
					if (opts.showSelectionOnly) {
						sortType = "bypreset"
					} else {
						sortType = "bytitle"
					}
				}
				sortGallery((opts.defaultSortDirectionASC == true ? "asc" : "dec"), sortType, thisFileList, Gallery);
			} else {
				if (defaultSortTypePhotos == 'addedDate') 				{sortType = "byadded"
				} else if (defaultSortTypePhotos == 'updateDate') 		{sortType = "byupdate"
				} else if (defaultSortTypePhotos == 'orderFacebook') 	{sortType = "byorder"
				} else if (defaultSortTypePhotos == 'FacebookID') 		{sortType = "byID"
				}
				sortGallery((opts.defaultPhotoDirectionsASC == true ? "asc" : "dec"), sortType, thisFileList, Gallery);
			}
		}
		function sortGallery(direction, sortType, thisFileList, Gallery){
			thisFileList.filteredThumbs.remove();
			var sortAlg = direction + "_" + sortType;
			switch (sortAlg){
				case 'asc_byorder':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(asc_byorder);
					break;
				case 'dec_byorder':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(dec_byorder);
					break;
				case 'asc_bysize':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(asc_bysize);
					break;
				case 'dec_bysize':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(dec_bysize);
					break;
				case 'asc_bycreate':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(asc_bycreate);
					break;
				case 'dec_bycreate':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(dec_bycreate);
					break;
				case 'asc_byupdate':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(asc_byupdate);
					break;
				case 'dec_byupdate':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(dec_byupdate);
					break;
				case 'asc_bytitle':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(asc_bytitle);
					break;
				case 'dec_bytitle':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(dec_bytitle);
					break;
				case 'asc_byadded':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(asc_byadded);
					break;
				case 'dec_byadded':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(dec_byadded);
					break;
				case 'asc_byID':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(asc_byID);
					break;
				case 'dec_byID':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(dec_byID);
					break;
				case 'asc_bypreset':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(asc_bypreset);
					break;
				case 'dec_bypreset':
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(dec_bypreset);
					break;
				default:
					thisFileList.filteredThumbs = thisFileList.filteredThumbs.sort(asc_bytitle);
					break;
			};
			thisFileList.fileContainer.hide().append(thisFileList.filteredThumbs).fadeIn('slow');
			// Restart LazyLoad
			restartLazyLoad();
		};
		function restartLazyLoad() {
			if ((opts.imageLazyLoad) && ($.isFunction($.fn.lazyloadanything))) {
				$.fn.lazyloadanything('load')
			}
		}
		function shortLinkAlbumShares(Identifier) {
			// Check for and implement URL Shortener Service for Album Share URLs
			if ((opts.albumShowSocialShare) && (opts.albumShortSocialShare)) {
				if (typeof ajaxRequest !== 'undefined') {
					ajaxRequest.abort();
				}
				$.each(AlbumIDsArray, function(i, albumShare){
					var ShortURL = AlbumIDsArray[i].clean;
					var SocialShareText = opts.SocialShareAlbumText;
					if (ShortURL.length == 0) {
						ajaxRequest = $.ajax({
							url: 			"http://safe.mn/api/shorten?format=jsonp&callback=?&url=" + fixedEncodeURIComponent(AlbumIDsArray[i].link),
							cache: 			false,
							dataType: 		"jsonp",
							success: function(data, textStatus){
								if (data.url) {
									ShortURL = data.url;
									var shareLinkTwitter = 	'https://twitter.com/intent/tweet?text=' + SocialShareText + ShortURL;
									var shareLinkGoogle = 	'https://plus.google.com/share?url=' + ShortURL;
									var shareLinkFacebook = 'http://www.facebook.com/sharer/sharer.php?s=100&p[url]=' + ShortURL + '&p[images][0]=' + AlbumIDsArray[i].thumb + '&p[title]=' + SocialShareText + '&p[summary]=' + AlbumIDsArray[i].summary;
									var shareLinkStumble = 	'http://www.stumbleupon.com/submit?url=' + ShortURL;
									$("#AlbumSocialShare_Twitter_" + AlbumIDsArray[i].id + "").attr("href", shareLinkTwitter);
									$("#AlbumSocialShare_Google_" + AlbumIDsArray[i].id + "").attr("href", shareLinkGoogle);
									$("#AlbumSocialShare_Facebook_" + AlbumIDsArray[i].id + "").attr("href", shareLinkFacebook);
									$("#AlbumSocialShare_Stumble_" + AlbumIDsArray[i].id + "").attr("href", shareLinkStumble);
									AlbumIDsArray[i].clean = ShortURL;
									if (opts.consoleLogging) {
										console.log('The Share URL for Album #' + AlbumIDsArray[i].id + ' has been shortened to: ' + ShortURL);
									}
								} else {
									if (opts.consoleLogging) {
										console.log('The Share URL for Album #' + AlbumIDsArray[i].id + ' could not be shortened! Error: ' + data.error);
									}
								}
							},
							error: function(jqXHR, textStatus, errorThrown){
								if (opts.consoleLogging) {
									console.log('No connection to the URL Shortener Service could be established! Error: \njqXHR:' + jqXHR + '\ntextStatus: ' + textStatus + '\nerrorThrown: '  + errorThrown);
								}
							}
						});
					} else {
						var shareLinkTwitter = 'https://twitter.com/intent/tweet?text=' + SocialShareText + ShortURL;
						var shareLinkGoogle = 'https://plus.google.com/share?url=' + ShortURL;
						var shareLinkFacebook = 'http://www.facebook.com/sharer/sharer.php?s=100&p[url]=' + ShortURL + '&p[images][0]=' + AlbumIDsArray[i].thumb + '&p[title]=' + SocialShareText + '&p[summary]=' + AlbumIDsArray[i].summary;
						var shareLinkStumble = 'http://www.stumbleupon.com/submit?url=' + ShortURL;
						$("#AlbumSocialShare_Twitter_" + AlbumIDsArray[i].id + "").attr("href", shareLinkTwitter);
						$("#AlbumSocialShare_Google_" + AlbumIDsArray[i].id + "").attr("href", shareLinkGoogle);
						$("#AlbumSocialShare_Facebook_" + AlbumIDsArray[i].id + "").attr("href", shareLinkFacebook);
						$("#AlbumSocialShare_Stumble_" + AlbumIDsArray[i].id + "").attr("href", shareLinkStumble);
					}
					$("#socialcount_" + AlbumIDsArray[i].id).show();
				});
			}
			// Check if Album Social Share should open as Popup
			$("body").on("click", "a.AlbumSocialShare", function(event) {
				if (opts.albumSocialSharePopup) {
					event.preventDefault();
					window.open(this.href, 'Share Album', 'width=500, height=500');
				}
			});
		}
		function shortLinkPhotoShares(Identifier) {
			// Check for and implement URL Shortener Service for Photo Share URLs
			if ((opts.photoShowSocialShare) && (opts.photoShortSocialShare)) {
				if (typeof ajaxRequest !== 'undefined') {
					ajaxRequest.abort();
				}
				$.each(PhotoIDsArray, function(i, photoShare){
					if (PhotoIDsArray[i].album == Identifier) {
						var ShortURL = PhotoIDsArray[i].clean;
						var SocialShareText = opts.SocialSharePhotoText;
						if (ShortURL.length == 0) {
							ajaxRequest = $.ajax({
								url: 			"http://safe.mn/api/shorten?format=jsonp&callback=?&url=" + fixedEncodeURIComponent(PhotoIDsArray[i].link),
								cache: 			false,
								dataType: 		"jsonp",
								success: function(data){
									if (data.url) {
										ShortURL = data.url;
										var shareLinkTwitter = 'https://twitter.com/intent/tweet?text=' + SocialShareText + ShortURL;
										var shareLinkGoogle = 'https://plus.google.com/share?url=' + ShortURL;
										var shareLinkFacebook = 'https://www.facebook.com/sharer/sharer.php?u=' + ShortURL + '&title=' + SocialShareText;
										var shareLinkStumble = 'http://www.stumbleupon.com/submit?url=' + ShortURL;
										$("#PhotoSocialShare_Twitter_" + PhotoIDsArray[i].id + "").attr("href", shareLinkTwitter);
										$("#PhotoSocialShare_Google_" + PhotoIDsArray[i].id + "").attr("href", shareLinkGoogle);
										$("#PhotoSocialShare_Facebook_" + PhotoIDsArray[i].id + "").attr("href", shareLinkFacebook);
										$("#PhotoSocialShare_Stumble_" + PhotoIDsArray[i].id + "").attr("href", shareLinkStumble);
										$("#PhotoSocialShare_Save_" + PhotoIDsArray[i].id + "").attr("href", ShortURL);
										$("#" + $("#socialcount_" + PhotoIDsArray[i].id + "").attr("data-parent")).attr("data-short", ShortURL);
										PhotoIDsArray[i].clean = ShortURL;
										if (opts.consoleLogging) {
											console.log('The Share URL for Photo #' + PhotoIDsArray[i].id + ' has been shortened to: ' + ShortURL);
										}
									} else {
										if (opts.consoleLogging) {
											console.log('The Share URL for Photo #' + PhotoIDsArray[i].id + ' could not be shortened! Error: ' + data.error);
										}
									}
								},
								error: function(jqXHR, textStatus, errorThrown){
									if (opts.consoleLogging) {
										console.log('No connection to the URL Shortener Service could be established! Error: \njqXHR:' + jqXHR + '\ntextStatus: ' + textStatus + '\nerrorThrown: '  + errorThrown);
									}
								}
							});
						} else {
							var shareLinkTwitter = 'https://twitter.com/intent/tweet?text=' + SocialShareText + ShortURL;
							var shareLinkGoogle = 'https://plus.google.com/share?url=' + ShortURL;
							var shareLinkFacebook = 'https://www.facebook.com/sharer/sharer.php?u=' + ShortURL + '&title=' + SocialShareText;
							var shareLinkStumble = 'http://www.stumbleupon.com/submit?url=' + ShortURL;
							$("#PhotoSocialShare_Twitter_" + PhotoIDsArray[i].id + "").attr("href", shareLinkTwitter);
							$("#PhotoSocialShare_Google_" + PhotoIDsArray[i].id + "").attr("href", shareLinkGoogle);
							$("#PhotoSocialShare_Facebook_" + PhotoIDsArray[i].id + "").attr("href", shareLinkFacebook);
							$("#PhotoSocialShare_Stumble_" + PhotoIDsArray[i].id + "").attr("href", shareLinkStumble);
							$("#PhotoSocialShare_Save_" + PhotoIDsArray[i].id + "").attr("href", ShortURL);
							$("#" + $("#socialcount_" + PhotoIDsArray[i].id + "").attr("data-parent")).attr("data-short", ShortURL);
						}
						$("#socialcount_" + PhotoIDsArray[i].id).show();
					};
				});
			};
			// Check if Photo Social Share should open as Popup
			$("body").on("click", "a.PhotoSocialShare", function(event) {
				if (opts.photoSocialSharePopup) {
					event.preventDefault();
					window.open(this.href, 'Share Photo', 'width=500, height=500');
				}
			});
		}
		function fixedEncodeURIComponent(str) {
			return encodeURIComponent(str).replace(/[!'()]/g, escape).replace(/\*/g, "%2A");
		}
		function truncateString(string, limit, breakChar, rightPad) {
			//truncateString(text, 52, ' ', '...')
			if (string.length <= limit) return string;
			var substr = string.substr(0, limit);
			if ((breakPoint = substr.lastIndexOf(breakChar)) >= 0) {
				if (breakPoint < string.length -1) {
					return string.substr(0, breakPoint) + rightPad;
				} else {
					return string
				}
			} else {
				return string
			}
		}
		function sortFilters(propName) {
			return function (a,b) {
				// return a[propName] - b[propName];
				var aVal = a[propName], bVal = b[propName];
				if (opts.sortFilterNewToOld) {
					return aVal < bVal ? 1 : (aVal > bVal ?  - 1 : 0);
				} else {
					return aVal > bVal ? 1 : (aVal < bVal ?  - 1 : 0);
				}

			};
		}
		function asc_byorder(a, b){
			var compA = parseInt($(a).attr("data-order"));
			var compB = parseInt($(b).attr("data-order"));
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		};
		function dec_byorder(a, b){
			var compA = parseInt($(a).attr("data-order"));
			var compB = parseInt($(b).attr("data-order"));
			return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
		};
		function asc_bysize(a, b){
			var compA = parseInt($(a).attr("data-count"));
			var compB = parseInt($(b).attr("data-count"));
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		};
		function dec_bysize(a, b){
			var compA = parseInt($(a).attr("data-count"));
			var compB = parseInt($(b).attr("data-count"));
			return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
		};
		function asc_bycreate(a, b){
			var compA = $(a).attr("data-create").toUpperCase();
			var compB = $(b).attr("data-create").toUpperCase();
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		};
		function dec_bycreate(a, b){
			var compA = $(a).attr("data-create").toUpperCase();
			var compB = $(b).attr("data-create").toUpperCase();
			return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
		};
		function asc_byupdate(a, b){
			var compA = $(a).attr("data-update").toUpperCase();
			var compB = $(b).attr("data-update").toUpperCase();
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		};
		function dec_byupdate(a, b){
			var compA = $(a).attr("data-update").toUpperCase();
			var compB = $(b).attr("data-update").toUpperCase();
			return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
		};
		function asc_byadded(a, b){
			var compA = $(a).attr("data-added").toUpperCase();
			var compB = $(b).attr("data-added").toUpperCase();
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		};
		function dec_byadded(a, b){
			var compA = $(a).attr("data-added").toUpperCase();
			var compB = $(b).attr("data-added").toUpperCase();
			return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
		};
		function asc_bytitle(a, b){
			var compA = $(a).attr("data-title").toUpperCase();
			var compB = $(b).attr("data-title").toUpperCase();
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		};
		function dec_bytitle(a, b){
			var compA = $(a).attr("data-title").toUpperCase();
			var compB = $(b).attr("data-title").toUpperCase();
			return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
		};
		function asc_byID(a, b){
			var compA = $(a).attr("data-id").toUpperCase();
			var compB = $(b).attr("data-id").toUpperCase();
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		};
		function dec_byID(a, b){
			var compA = $(a).attr("data-id").toUpperCase();
			var compB = $(b).attr("data-id").toUpperCase();
			return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
		};
		function asc_bypreset(a, b){
			var compA = $(a).attr("data-preset").toUpperCase();
			var compB = $(b).attr("data-preset").toUpperCase();
			return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		};
		function dec_bypreset(a, b){
			var compA = $(a).attr("data-preset").toUpperCase();
			var compB = $(b).attr("data-preset").toUpperCase();
			return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
		};
		
		// Create Necessary HTML Markup for Gallery
		$("#" + opts.frameID).append("<div id='" + opts.loaderID + "' class='clearFixMe'></div>");
		$("#" + opts.frameID).append("<div id='" + opts.galleryID + "' class='clearFixMe'></div>");

		if ((opts.infiniteScrollAlbums) || (opts.infiniteScrollPhotos)) {
			$("#" + opts.frameID).append("<div id='" + opts.infiniteLoadID + "' class='clearFixMe' style='display: none;'><div id='FB_Album_Infinite_Image'></div>" + opts.InfiniteScrollLoad + "</div>");
			if (opts.infiniteScrollMore) {
				$("#" + opts.frameID).append("<div id='" + opts.infiniteMoreID + "' class='clearFixMe' style='display: none;'><div id='FB_Album_Infinite_More_Arrow_Left'></div><div id='FB_Album_Infinite_More_Text'>" + opts.InfiniteScrollMore + "</div><div id='FB_Album_Infinite_More_Arrow_Right'></div></div>");
			}
			$("#" + opts.frameID).append("<div id='" + opts.infiniteAlbumsID + "' class='clearFixMe' style='display: block;'></div>");
			$("#" + opts.frameID).append("<div id='" + opts.infinitePhotosID + "' class='clearFixMe' style='display: block;'></div>");
		}

		if (opts.responsiveGallery) {
			$("#" + opts.frameID).css("width", opts.responsiveWidth + "%").css("padding", (opts.floatingControlBar == true ? "0px 0px 5px 0px" : "5px 0px"));
		} else {
			$("#" + opts.frameID).css("width", opts.fixedWidth + "px").css("padding", (opts.floatingControlBar == true ? "0px 0px 5px 0px" : "5px 0px"));
		}

		$("<div>", {id : "fb-album-header"}).appendTo("#" + opts.galleryID);
		$("<div>", {id : "fb-album-content"}).appendTo("#" + opts.galleryID);

		if (opts.showBottomControlBar) {$("<div>", {id : "fb-album-footer"}).appendTo("#" + opts.galleryID);}

		//if (!opts.paginationLayoutAlbums) {$('.album').hide();} else {$('.paginationMain').hide();}
		$('.paginationMain').hide();

		// Initialize qTip2 Tooltips as live mouseover event
		if ((opts.tooltipUseInternal) && ($.isFunction($.fn.qtip))) {
			var qTipShared = {
				prerender: 			false,
				overwrite: 			true,
				hide: {
					target: 		false,
					event: 			'mouseleave mousedown unfocus click',
					effect: 		true,
					delay: 			0,
					fixed: 			true,
					inactive: 		5000,
					leave: 			"window"
				},
				events: {
					render: 		null,
					move: 			null,
					show: 			null,
					hide: 			null,
					toggle: 		null,
					focus: 			null,
					blur: 			null
				},
				show: {
					target: 		false,
					event: 			'mouseenter',
					effect: 		true,
					delay: 			90,
					solo: 			true,
					ready: 			false,
					modal: 			false
				}
			};
			// Initialize Tooltips for Gallery
			$('body').on('mouseover', '.TipGallery, .TipLightbox, .TipPhoto, .TipSocial, .TipGeneric', function() {
				// Make sure to only apply one Tooltip per Element!
				if( typeof( $(this).data('qtip') ) == 'object' ) {
					return;
				};
				if (($(this).hasClass("TipGallery")) || ($(this).hasClass("TipSocial"))) {
					var tooltipWidth = 250;
				} else if ($(this).hasClass("TipGeneric")) {
					var tooltipWidth = 220;
				} else if (($(this).hasClass("TipPhoto")) || ($(this).hasClass("TipLightbox"))) {
					var tooltipWidth = 400;
				}
				$(this).qtip( $.extend({}, qTipShared, {
					style: {
						classes: 		opts.tooltipDesign,
						def: 			true,
						widget: 		opts.tooltipThemeRoller,
						width: 			tooltipWidth,
						tip: {
							corner: 	opts.tooltipTipCorner,
							mimic: 		false,
							width: 		8,
							height: 	8,
							border: 	true,
							offset: 	0
						}
					},
					content: {
						text: 			true,
						attr: 			opts.tooltipTipAnchor,
						title: {
							text: 		(opts.tooltipTitleBar == true ? 'Album Title:' : false),
							button: 	(opts.tooltipTitleBar == true ? opts.tooltipCloseButton : false)
						}
					},
					position: {
						my: 			opts.tooltipPositionMy,
						at: 			opts.tooltipPositionAt,
						target: 		opts.tooltipPositionTarget,
						container: 		false,
						viewport: 		$(window),
						adjust: {
							x: 			opts.tooltipOffsetX,
							y: 			opts.tooltipOffsetY,
							mouse: 		true,
							resize: 	true,
							method: 	'flipinvert flipinvert'
						},
						effect: 		true
					}
				}));
				$(this).qtip('show');
			});
		}

		galleryAlbumsInit();
    };
})(jQuery);


// -------------------------------
// Other External REQUIRED Plugins
// -------------------------------

// jQuery Messi Plugin 1.3 (Docs & Licensing: http://marcosesperon.es/apps/messi/)
function Messi(data, options) {
    var _this = this;
    _this.options = jQuery.extend({}, Messi.prototype.options, options || {});
    // preparamos el elemento
    _this.messi = jQuery(_this.template);
    _this.setContent(data);
    // ajustamos el t�tulo
    if(_this.options.title == null) {
        jQuery('.messi-titlebox', _this.messi).remove();
    } else {
        jQuery('.messi-title', _this.messi).append(_this.options.title);
        if(_this.options.buttons.length === 0 && !_this.options.autoclose) {
            if(_this.options.closeButton) {
                var close = jQuery('<span class="messi-closebtn"></span>');
                close.bind('click', function() {
                    _this.hide();
                });
                jQuery('.messi-titlebox', this.messi).prepend(close);
            };
        };
        if(_this.options.titleClass != null) jQuery('.messi-titlebox', this.messi).addClass(_this.options.titleClass);
    };
    // ajustamos el ancho
    if(_this.options.width != null) jQuery('.messi-box', _this.messi).css('width', _this.options.width);
    // preparamos los botones
    if(_this.options.buttons.length > 0) {
        for (var i = 0; i < _this.options.buttons.length; i++) {
            var cls = (_this.options.buttons[i]["class"]) ? _this.options.buttons[i]["class"] : '';
            var btn = jQuery('<div class="btnbox"><button class="btn ' + cls + '" href="#" data-value="' + _this.options.buttons[i].val + '">' + _this.options.buttons[i].label + '</button></div>');
            btn.on('click', 'button', function() {
                var value = jQuery.attr(this, 'data-value');
                var after = (_this.options.callback != null) ? function() { _this.options.callback(value); } : null;
                _this.hide(after);
            });
            jQuery('.messi-actions', this.messi).append(btn);
        };
    } else {
        jQuery('.messi-footbox', this.messi).remove();
    };
    // preparamos el bot�n de cerrar autom�ticamente
    if(_this.options.buttons.length === 0 && _this.options.title == null && !_this.options.autoclose) {
        if (_this.options.closeButton) {
            var close = jQuery('<span class="messi-closebtn"></span>');
            close.on('click', function() {
                _this.hide();
            });
            jQuery('.messi-content', this.messi).prepend(close);
        };
    };
    // activamos la pantalla modal
    _this.modal = (_this.options.modal) ? jQuery('<div class="messi-modal"></div>').css({
        opacity: _this.options.modalOpacity,
        width: jQuery(document).width(),
        height: jQuery(document).height(),
        'z-index': _this.options.zIndex + jQuery('.messi').length
    }).appendTo(document.body) : null;
    if ((_this.options.modal) && (_this.options.modalClick)) {
        _this.modal.on('click', function() {_this.hide();})
    }
    // mostramos el mensaje
    if(_this.options.show) _this.show();
    // controlamos el redimensionamiento de la pantalla
    jQuery(window).bind('resize', function(){ _this.resize(); });
    // configuramos el cierre autom�tico
    if(_this.options.autoclose != null) {
        setTimeout(function(_this) {
            _this.hide();
        }, _this.options.autoclose, this);
    };
    return _this;
};
Messi.prototype = {
    options: {
        autoclose:              null,                           // autoclose message after 'x' miliseconds, i.e: 5000
        buttons:                [],                             // array of buttons, i.e: [{id: 'ok', label: 'OK', val: 'OK'}]
        callback:               null,                           // callback function after close message
        center:                 true,                           // center message on screen
        closeButton:            true,                           // show close button in header title (or content if buttons array is empty).
        height:                 'auto',                         // content height
		maxheight:				'90%',							// content max height
        title:                  null,                           // message title
        titleClass:             null,                           // title style: info, warning, success, error
        modal:                  false,                          // shows message in modal (loads background)
        modalClick:             true,                           // close modal on click in modal area
        modalOpacity:           .2,                             // modal background opacity
        padding:                '10px',                         // content padding
        show:                   true,                           // show message after load
        unload:                 true,                           // unload message after hide
        viewport:               {top: '0px', left: '0px'},      // if not center message, sets X and Y position
        width:                  '500px',                        // message width
        zIndex:                  99999                          // message z-index
    },
    template: '<div class="messi"><div class="messi-box"><div class="messi-wrapper"><div class="messi-titlebox"><span class="messi-title"></span></div><div class="messi-content"></div><div class="messi-footbox"><div class="messi-actions"></div></div></div></div></div>',
    content: '<div></div>',
    visible: false,
    setContent: function(data) {
        jQuery('.messi-content', this.messi).css({padding: this.options.padding, height: this.options.height, maxHeight: this.options.maxheight}).empty().append(data);
    },
    viewport: function() {
        return {
            //top:    ((jQuery(window).height() - this.messi.height()) / 2) +  jQuery(window).scrollTop() + "px",
            //left:   ((jQuery(window).width() - this.messi.width()) / 2) + jQuery(window).scrollLeft() + "px"
            top:    ((jQuery(window).height() - this.messi.height()) / 2) + "px",
            left:   ((jQuery(window).width() - this.messi.width()) / 2) + "px"
        };
    },
    show: function() {
        if(this.visible) return;
        if(this.options.modal && this.modal != null) this.modal.show();
        this.messi.appendTo(document.body);
        // obtenemos el centro de la pantalla si la opci�n de centrar est� activada
        if(this.options.center) this.options.viewport = this.viewport(jQuery('.messi-box', this.messi));
        this.messi.css({
            top:            this.options.viewport.top,
            left:           this.options.viewport.left,
            'z-index':      this.options.zIndex + jQuery('.messi').length
        }).show().animate({opacity: 1}, 300);
        // cancelamos el scroll
        //document.documentElement.style.overflow = "hidden";
        //jQuery("body").css("overflow", "hidden");
        this.visible = true;
    },
    hide: function(after) {
        if (!this.visible) return;
        var _this = this;
        this.messi.animate({opacity: 0}, 300, function() {
            if(_this.options.modal && _this.modal != null) _this.modal.remove();
            _this.messi.css({display: 'none'}).remove();
            // reactivamos el scroll
            //document.documentElement.style.overflow = "visible";
            //jQuery("body").css("overflow", "visible");
            _this.visible = false;
            if (after) after.call();
            if(_this.options.unload) _this.unload();
        });
        return this;
    },
    resize: function() {
        if(this.options.modal) {
            jQuery('.messi-modal').css({
                width: jQuery(document).width(),
                height: jQuery(document).height()
            });
        };
        if(this.options.center) {
            this.options.viewport = this.viewport(jQuery('.messi-box', this.messi));
            this.messi.css({top: this.options.viewport.top, left: this.options.viewport.left});
        };
    },
    toggle: function() {
        this[this.visible ? 'hide' : 'show']();
        return this;
    },
    unload: function() {
        if (this.visible) this.hide();
        jQuery(window).unbind('resize', function () { this.resize(); });
        this.messi.remove();
    },
};
jQuery.extend(Messi, {
    alert: function(data, callback, options) {
        var buttons = [
            {id: 'ok',      label: 'OK',    val: 'OK'}
        ];
        options = jQuery.extend({closeButton: false, buttons: buttons, callback:function() {}}, options || {}, {show: true, unload: true, callback: callback});
        return new Messi(data, options);
    },
    ask: function(data, callback, options) {
        var buttons = [
            {id: 'yes',     label: 'Yes',   val: 'Y',   "class": 'btn-success'},
            {id: 'no',      label: 'No',    val: 'N',   "class": 'btn-danger'},
        ];
        options = jQuery.extend({closeButton: false, modal: true, buttons: buttons, callback:function() {}}, options || {}, {show: true, unload: true, callback: callback});
        return new Messi(data, options);
    },
    img: function(src, options) {
        var img = new Image();
        jQuery(img).load(function() {
            var vp = {width: jQuery(window).width() - 50, height: jQuery(window).height() - 50};
            var ratio = (this.width > vp.width || this.height > vp.height) ? Math.min(vp.width / this.width, vp.height / this.height) : 1;
            jQuery(img).css({width: this.width * ratio, height: this.height * ratio});
            options = jQuery.extend(options || {}, {show: true, unload: true, closeButton: true, width: this.width * ratio, height: this.height * ratio, padding: 0});
            new Messi(img, options);
        }).error(function() {
            console.log('Error loading ' + src);
        }).attr('src', src);
    },
    load: function(url, options) {
        options = jQuery.extend(options || {}, {show: true, unload: true, params: {}});
        var request = {
            url: url,
            data: options.params,
            dataType: 'html',
            cache: false,
            error: function (request, status, error) {
                console.log(request.responseText);
            },
            success: function(html) {
                //html = jQuery(html);
                new Messi(html, options);
            }
        };
        jQuery.ajax(request);
    }
});

// jQuery inView (Docs & Licensing: https://github.com/protonet/jquery.inview)
(function ($) {
    var inviewObjects = {}, viewportSize, viewportOffset, d = document, w = window, documentElement = d.documentElement, expando = $.expando;
    $.event.special.inview = {
        add: function(data) {
            inviewObjects[data.guid + "-" + this[expando]] = { data: data, $element: $(this) };
        },
        remove: function(data) {
            try { delete inviewObjects[data.guid + "-" + this[expando]]; } catch(e) {}
        }
    };
    function getViewportSize() {
        var mode, domObject, size = { height: w.innerHeight, width: w.innerWidth };
        // if this is correct then return it. iPad has compat Mode, so will
        // go into check clientHeight/clientWidth (which has the wrong value).
        if (!size.height) {
            mode = d.compatMode;
            if (mode || !$.support.boxModel) { // IE, Gecko
                domObject = mode === 'CSS1Compat' ?
                    documentElement : // Standards
                    d.body; // Quirks
                size = {
                    height: domObject.clientHeight,
                    width:  domObject.clientWidth
                };
            }
        }
        return size;
    }
    function getViewportOffset() {
        return {
            top:  w.pageYOffset || documentElement.scrollTop   || d.body.scrollTop,
            left: w.pageXOffset || documentElement.scrollLeft  || d.body.scrollLeft
        };
    }
    function checkInView() {
        var $elements = $(), elementsLength, i = 0;
        $.each(inviewObjects, function(i, inviewObject) {
            var selector  = inviewObject.data.selector,
                $element  = inviewObject.$element;
            $elements = $elements.add(selector ? $element.find(selector) : $element);
        });
        elementsLength = $elements.length;
        if (elementsLength) {
            viewportSize   = viewportSize   || getViewportSize();
            viewportOffset = viewportOffset || getViewportOffset();
            for (; i<elementsLength; i++) {
                // Ignore elements that are not in the DOM tree
                if (!$.contains(documentElement, $elements[i])) {
                    continue;
                }
                var $element      = $($elements[i]),
                    elementSize   = { height: $element.height(), width: $element.width() },
                    elementOffset = $element.offset(),
                    inView        = $element.data('inview'),
                    visiblePartX,
                    visiblePartY,
                    visiblePartsMerged;
                // Don't ask me why because I haven't figured out yet:
                // viewportOffset and viewportSize are sometimes suddenly null in Firefox 5.
                // Even though it sounds weird:
                // It seems that the execution of this function is interferred by the onresize/onscroll event
                // where viewportOffset and viewportSize are unset
                if (!viewportOffset || !viewportSize) {
                    return;
                }
                if (elementOffset.top + elementSize.height - infiniteScrollOffset > viewportOffset.top &&
                    elementOffset.top < viewportOffset.top + viewportSize.height - infiniteScrollOffset &&
                    elementOffset.left + elementSize.width > viewportOffset.left &&
                    elementOffset.left < viewportOffset.left + viewportSize.width) {
                    visiblePartX = (viewportOffset.left > elementOffset.left ?
                        'right' : (viewportOffset.left + viewportSize.width) < (elementOffset.left + elementSize.width) ?
                        'left' : 'both');
                    visiblePartY = (viewportOffset.top > elementOffset.top ?
                        'bottom' : (viewportOffset.top + viewportSize.height) < (elementOffset.top + elementSize.height) ?
                        'top' : 'both');
                    visiblePartsMerged = visiblePartX + "-" + visiblePartY;
                    if (!inView || inView !== visiblePartsMerged) {
                        $element.data('inview', visiblePartsMerged).trigger('inview', [true, visiblePartX, visiblePartY]);
                    }
                } else if (inView) {
                    $element.data('inview', false).trigger('inview', [false]);
                }
            }
        }
    }
    $(w).bind("scroll resize", function() {
        viewportSize = viewportOffset = null;
    });
    // IE < 9 scrolls to focused elements without firing the "scroll" event
    if (!documentElement.addEventListener && documentElement.attachEvent) {
        documentElement.attachEvent("onfocusin", function() {
            viewportOffset = null;
        });
    }
    // Use setInterval in order to also make sure this captures elements within
    // "overflow:scroll" elements or elements that appeared in the dom tree due to
    // dom manipulation and reflow
    // old: $(window).scroll(checkInView);
    //
    // By the way, iOS (iPad, iPhone, ...) seems to not execute, or at least delays
    // intervals while the user scrolls. Therefore the inview event might fire a bit late there
    setInterval(checkInView, 250);
})(jQuery);

// jQuery Shorten (Docs & Licensing: https://github.com/MarcDiethelm/jQuery-Shorten)
(function(a){function s(g,c){return c.measureText(g).width}function t(g,c){c.text(g);return c.width()}var q=false,o,j,k;a.fn.shorten=function(){var g={},c=arguments,r=c.callee;if(c.length)if(c[0].constructor==Object)g=c[0];else if(c[0]=="options")return a(this).eq(0).data("shorten-options");else g={width:parseInt(c[0]),tail:c[1]};this.css("visibility","hidden");var h=a.extend({},r.defaults,g);return this.each(function(){var e=a(this),d=e.text(),p=d.length,i,f=a("<span/>").html(h.tail).text(),l={shortened:false, textOverflow:false};i=e.css("float")!="none"?h.width||e.width():h.width||e.parent().width();if(i<0)return true;e.data("shorten-options",h);this.style.display="block";this.style.whiteSpace="nowrap";if(o){var b=a(this),n=document.createElement("canvas");ctx=n.getContext("2d");b.html(n);ctx.font=b.css("font-style")+" "+b.css("font-variant")+" "+b.css("font-weight")+" "+Math.ceil(parseFloat(b.css("font-size")))+"px "+b.css("font-family");j=ctx;k=s}else{b=a('<table style="padding:0; margin:0; border:none; font:inherit;width:auto;zoom:1;position:absolute;"><tr style="padding:0; margin:0; border:none; font:inherit;"><td style="padding:0; margin:0; border:none; font:inherit;white-space:nowrap;"></td></tr></table>'); $td=a("td",b);a(this).html(b);j=$td;k=t}b=k.call(this,d,j);if(b<i){e.text(d);this.style.visibility="visible";e.data("shorten-info",l);return true}h.tooltip&&this.setAttribute("title",d);if(r._native&&!g.width){n=a("<span>"+h.tail+"</span>").text();if(n.length==1&&n.charCodeAt(0)==8230){e.text(d);this.style.overflow="hidden";this.style[r._native]="ellipsis";this.style.visibility="visible";l.shortened=true;l.textOverflow="ellipsis";e.data("shorten-info",l);return true}}f=k.call(this,f,j);i-=f;f=i*1.15; if(b-f>0){f=d.substring(0,Math.ceil(p*(f/b)));if(k.call(this,f,j)>i){d=f;p=d.length}}do{p--;d=d.substring(0,p)}while(k.call(this,d,j)>=i);e.html(a.trim(a("<span/>").text(d).html())+h.tail);this.style.visibility="visible";l.shortened=true;e.data("shorten-info",l);return true})};var m=document.documentElement.style;if("textOverflow"in m)q="textOverflow";else if("OTextOverflow"in m)q="OTextOverflow";if(typeof Modernizr!="undefined"&&Modernizr.canvastext)o=Modernizr.canvastext;else{m=document.createElement("canvas"); o=!!(m.getContext&&m.getContext("2d")&&typeof m.getContext("2d").fillText==="function")}a.fn.shorten._is_canvasTextSupported=o;a.fn.shorten._native=q;a.fn.shorten.defaults={tail:"&hellip;",tooltip:true}})(jQuery);

// jQuery LazyLoadAnything (Docs & Licensing: https://github.com/shrimpwagon/jquery-lazyloadanything)
(function($) {
	// Cache jQuery window
	var $window = $(window);
	// Element to listen to scroll event
	var $listenTo;
	// Force load flag
	var force_load_flag = false;
	// Plugin methods
	var methods = {
		'init': function(options) {
			var defaults = {
				'auto': 			true,
				'cache': 			false,
				'timeout': 			1000,
				'includeMargin': 	false,
				'viewportMargin': 	0,
				'repeatLoad': 		false,
				'listenTo': 		window,
				'onLoadingStart': function(e, llelements, indexes) {
					return true;
				},
				'onLoad': function(e, llelement) {
					return true;
				},
				'onLoadingComplete': function(e, llelements, indexes) {
					return true;
				}
			};
			var settings = $.extend({}, defaults, options);
			$listenTo = $(settings.listenTo);
			var timeout = 0;
			var llelements = [];
			var $selector = this;
			// Scroll listener
			$listenTo.bind('scroll.lla', function(e) {
				// Check for manually/auto load
				if(!force_load_flag && !settings.auto) return false;
				force_load_flag = false;
				// Clear timeout if scrolling continues
				clearTimeout(timeout);
				// Set the timeout for onLoad
				timeout = setTimeout(function() {
					var viewport_left = $listenTo.scrollLeft();
					var viewport_top = $listenTo.scrollTop();
					var viewport_width = $listenTo.innerWidth();
					var viewport_height = $listenTo.innerHeight();
					var viewport_x1 = viewport_left - settings.viewportMargin;
					var viewport_x2 = viewport_left + viewport_width + settings.viewportMargin;
					var viewport_y1 = viewport_top - settings.viewportMargin;
					var viewport_y2 = viewport_top + viewport_height + settings.viewportMargin;
					var load_elements = [];
					var i, llelem_top, llelem_bottom;
					// Cycle through llelements and check if they are within viewpane
					for(i = 0; i < llelements.length; i++) {
						// Get top and bottom of llelem
						llelem_x1 = llelements[i].getLeft();
						llelem_x2 = llelements[i].getRight();
						llelem_y1 = llelements[i].getTop();
						llelem_y2 = llelements[i].getBottom();
						if(llelements[i].$element.is(':visible')) {
							if ((viewport_x1 < llelem_x2) && (viewport_x2 > llelem_x1) && (viewport_y1 < llelem_y2) && (viewport_y2 > llelem_y1)) {
								// Grab index of llelements that will be loaded
								if(settings.repeatLoad || !llelements[i].loaded) load_elements.push(i);
							}
						}
					};
					// Call onLoadingStart event
					if(settings.onLoadingStart.call(undefined, e, llelements, load_elements)) {
						// Cycle through array of indexes that will be loaded
						for(i = 0; i < load_elements.length; i++) {
							// Set loaded flag
							llelements[load_elements[i]].loaded = true;
							// Call the individual element onLoad
							if(settings.onLoad.call(undefined, e, llelements[load_elements[i]]) === false) break;
						}
						// Call onLoadingComplete event
						settings.onLoadingComplete.call(undefined, e, llelements, load_elements);
					}
				}, settings.timeout);
			});
			// LazyLoadElement class
			function LazyLoadElement($element) {
				var self = this;
				this.loaded = false;
				this.$element = $element;
				this.top = undefined;
				this.bottom = undefined;
				this.left = undefined;
				this.right = undefined;
				this.getTop = function() {
					if (self.top) return self.top;
					//return self.$element.position().top;
					return self.$element.offset().top;
				};
				this.getBottom = function() {
					if(self.bottom) return self.bottom;
					var top = (self.top) ? self.top : this.getTop();
					return top + self.$element.outerHeight(settings.includeMargin);
				};
				this.getLeft = function() {
					if(self.left) return self.left;
					return self.$element.position().left;
				};
				this.getRight = function() {
					if(self.right) return self.right;
					var left = (self.left) ? self.left : this.getLeft();
					return left + self.$element.outerWidth(settings.includeMargin);
				};
				// Cache the top and bottom of set
				if(settings.cache) {
					this.top = this.getTop();
					this.bottom = this.getBottom();
					this.left = this.getLeft();
					this.right = this.getRight();
				};
			};
			// Cycle throught the selector(s)
			var chain = $selector.each(function() {
				// Add LazyLoadElement classes to the main array
				llelements.push(new LazyLoadElement($(this)));
			});
			return chain;
		},
		'destroy': function() {
			$listenTo.unbind('scroll.lla');
		},
		'load': function() {
			force_load_flag = true;
			$listenTo.trigger('scroll.lla');
		}
	};
	$.fn.lazyloadanything = function(method) {
		// Method calling logic
		if (methods[method]) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if (typeof method === 'object' || ! method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' + method + ' does not exist on jQuery.lazyloadanything');
		}
	};
})( jQuery );

// waitForImages 1.5.0 (Docs & Licensing: https://github.com/alexanderdickson/waitForImages)
!function(a){var b="waitForImages";a.waitForImages={hasImageProperties:["backgroundImage","listStyleImage","borderImage","borderCornerImage","cursor"]},a.expr[":"].uncached=function(b){if(!a(b).is('img[src!=""]'))return!1;var c=new Image;return c.src=b.src,!c.complete},a.fn.waitForImages=function(c,d,e){var f=0,g=0;if(a.isPlainObject(arguments[0])&&(e=arguments[0].waitForAll,d=arguments[0].each,c=arguments[0].finished),c=c||a.noop,d=d||a.noop,e=!!e,!a.isFunction(c)||!a.isFunction(d))throw new TypeError("An invalid callback was supplied.");return this.each(function(){var h=a(this),i=[],j=a.waitForImages.hasImageProperties||[],k=/url\(\s*(['"]?)(.*?)\1\s*\)/g;e?h.find("*").addBack().each(function(){var b=a(this);b.is("img:uncached")&&i.push({src:b.attr("src"),element:b[0]}),a.each(j,function(a,c){var d,e=b.css(c);if(!e)return!0;for(;d=k.exec(e);)i.push({src:d[2],element:b[0]})})}):h.find("img:uncached").each(function(){i.push({src:this.src,element:this})}),f=i.length,g=0,0===f&&c.call(h[0]),a.each(i,function(e,i){var j=new Image;a(j).on("load."+b+" error."+b,function(a){return g++,d.call(i.element,g,f,"load"==a.type),g==f?(c.call(h[0]),!1):void 0}),j.src=i.src})})}}(jQuery);

// Custom Layout Modes for Isotope
(function ($) {
	if ($.isFunction($.fn.isotope)) {
		$.Isotope.prototype.flush = function() {
			this.$allAtoms = $();
			this.$filteredAtoms = $();
			//this.element.children().remove();
			//this.reLayout();
		};
		// Centered Masonry
		$.Isotope.prototype._getCenteredMasonryColumns = function() {
			// Assign equal height to all elements to match fitRows effect
			var maxHeight = -1;
			$('.albumWrapper').each(function() {
				maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
			});
			$('.albumWrapper').each(function() {
				$(this).height(maxHeight);
			});
			var columnOffset = this.options.masonry && this.options.masonry.columnOffset || 0;
			this.width = this.element.width();
			var parentWidth = this.element.parent().width();
			// i.e. options.masonry && options.masonry.columnWidth
			var colW = this.options.masonry && this.options.masonry.columnWidth ||
			// or use the size of the first item
			this.$filteredAtoms.outerWidth(true) ||
			// if there's no items, use size of container
			parentWidth;
			var cols = Math.floor( parentWidth / colW );
			cols = Math.max( cols, 1 );
			// i.e. this.masonry.cols = ....
			this.masonry.cols = cols;
			// i.e. this.masonry.columnWidth = ...
			this.masonry.columnWidth = colW ;
		};
		$.Isotope.prototype._masonryReset = function() {
			// layout-specific props
			this.masonry = {};
			// FIXME shouldn't have to call this again
			this._getCenteredMasonryColumns();
			var i = this.masonry.cols;
			this.masonry.colYs = [];
			while (i--) {
				this.masonry.colYs.push( 0 );
			}
		};
		$.Isotope.prototype._masonryResizeChanged = function() {
			var prevColCount = this.masonry.cols;
			// get updated colCount
			this._getCenteredMasonryColumns();
			return ( this.masonry.cols !== prevColCount );
		};
		$.Isotope.prototype._masonryGetContainerSize = function() {
			var unusedCols = 0, i = this.masonry.cols;
			var gutter = this.options.masonry && this.options.masonry.gutterWidth || 0;
			// count unused columns
			while ( --i ) {
				if ( this.masonry.colYs[i] !== 0 ) {
					break;
				}
				unusedCols++;
			}
			return {
				height : Math.max.apply( Math, this.masonry.colYs ),
				// fit container to columns that have been used;
				width : (this.masonry.cols - unusedCols) * this.masonry.columnWidth
			};
		};
	};
})( jQuery );

// moment.js 2.1.0 (Docs & Licensing: http://momentjs.com/)
!function(t){function e(t,e){return function(n){return u(t.call(this,n),e)}}function n(t,e){return function(n){return this.lang().ordinal(t.call(this,n),e)}}function s(){}function i(t){a(this,t)}function r(t){var e=t.years||t.year||t.y||0,n=t.months||t.month||t.M||0,s=t.weeks||t.week||t.w||0,i=t.days||t.day||t.d||0,r=t.hours||t.hour||t.h||0,a=t.minutes||t.minute||t.m||0,o=t.seconds||t.second||t.s||0,u=t.milliseconds||t.millisecond||t.ms||0;this._input=t,this._milliseconds=u+1e3*o+6e4*a+36e5*r,this._days=i+7*s,this._months=n+12*e,this._data={},this._bubble()}function a(t,e){for(var n in e)e.hasOwnProperty(n)&&(t[n]=e[n]);return t}function o(t){return 0>t?Math.ceil(t):Math.floor(t)}function u(t,e){for(var n=t+"";n.length<e;)n="0"+n;return n}function h(t,e,n,s){var i,r,a=e._milliseconds,o=e._days,u=e._months;a&&t._d.setTime(+t._d+a*n),(o||u)&&(i=t.minute(),r=t.hour()),o&&t.date(t.date()+o*n),u&&t.month(t.month()+u*n),a&&!s&&H.updateOffset(t),(o||u)&&(t.minute(i),t.hour(r))}function d(t){return"[object Array]"===Object.prototype.toString.call(t)}function c(t,e){var n,s=Math.min(t.length,e.length),i=Math.abs(t.length-e.length),r=0;for(n=0;s>n;n++)~~t[n]!==~~e[n]&&r++;return r+i}function f(t){return t?ie[t]||t.toLowerCase().replace(/(.)s$/,"$1"):t}function l(t,e){return e.abbr=t,x[t]||(x[t]=new s),x[t].set(e),x[t]}function _(t){if(!t)return H.fn._lang;if(!x[t]&&A)try{require("./lang/"+t)}catch(e){return H.fn._lang}return x[t]}function m(t){return t.match(/\[.*\]/)?t.replace(/^\[|\]$/g,""):t.replace(/\\/g,"")}function y(t){var e,n,s=t.match(E);for(e=0,n=s.length;n>e;e++)s[e]=ue[s[e]]?ue[s[e]]:m(s[e]);return function(i){var r="";for(e=0;n>e;e++)r+=s[e]instanceof Function?s[e].call(i,t):s[e];return r}}function M(t,e){function n(e){return t.lang().longDateFormat(e)||e}for(var s=5;s--&&N.test(e);)e=e.replace(N,n);return re[e]||(re[e]=y(e)),re[e](t)}function g(t,e){switch(t){case"DDDD":return V;case"YYYY":return X;case"YYYYY":return $;case"S":case"SS":case"SSS":case"DDD":return I;case"MMM":case"MMMM":case"dd":case"ddd":case"dddd":return R;case"a":case"A":return _(e._l)._meridiemParse;case"X":return B;case"Z":case"ZZ":return j;case"T":return q;case"MM":case"DD":case"YY":case"HH":case"hh":case"mm":case"ss":case"M":case"D":case"d":case"H":case"h":case"m":case"s":return J;default:return new RegExp(t.replace("\\",""))}}function p(t){var e=(j.exec(t)||[])[0],n=(e+"").match(ee)||["-",0,0],s=+(60*n[1])+~~n[2];return"+"===n[0]?-s:s}function D(t,e,n){var s,i=n._a;switch(t){case"M":case"MM":i[1]=null==e?0:~~e-1;break;case"MMM":case"MMMM":s=_(n._l).monthsParse(e),null!=s?i[1]=s:n._isValid=!1;break;case"D":case"DD":case"DDD":case"DDDD":null!=e&&(i[2]=~~e);break;case"YY":i[0]=~~e+(~~e>68?1900:2e3);break;case"YYYY":case"YYYYY":i[0]=~~e;break;case"a":case"A":n._isPm=_(n._l).isPM(e);break;case"H":case"HH":case"h":case"hh":i[3]=~~e;break;case"m":case"mm":i[4]=~~e;break;case"s":case"ss":i[5]=~~e;break;case"S":case"SS":case"SSS":i[6]=~~(1e3*("0."+e));break;case"X":n._d=new Date(1e3*parseFloat(e));break;case"Z":case"ZZ":n._useUTC=!0,n._tzm=p(e)}null==e&&(n._isValid=!1)}function Y(t){var e,n,s=[];if(!t._d){for(e=0;7>e;e++)t._a[e]=s[e]=null==t._a[e]?2===e?1:0:t._a[e];s[3]+=~~((t._tzm||0)/60),s[4]+=~~((t._tzm||0)%60),n=new Date(0),t._useUTC?(n.setUTCFullYear(s[0],s[1],s[2]),n.setUTCHours(s[3],s[4],s[5],s[6])):(n.setFullYear(s[0],s[1],s[2]),n.setHours(s[3],s[4],s[5],s[6])),t._d=n}}function w(t){var e,n,s=t._f.match(E),i=t._i;for(t._a=[],e=0;e<s.length;e++)n=(g(s[e],t).exec(i)||[])[0],n&&(i=i.slice(i.indexOf(n)+n.length)),ue[s[e]]&&D(s[e],n,t);i&&(t._il=i),t._isPm&&t._a[3]<12&&(t._a[3]+=12),t._isPm===!1&&12===t._a[3]&&(t._a[3]=0),Y(t)}function k(t){var e,n,s,r,o,u=99;for(r=0;r<t._f.length;r++)e=a({},t),e._f=t._f[r],w(e),n=new i(e),o=c(e._a,n.toArray()),n._il&&(o+=n._il.length),u>o&&(u=o,s=n);a(t,s)}function v(t){var e,n=t._i,s=K.exec(n);if(s){for(t._f="YYYY-MM-DD"+(s[2]||" "),e=0;4>e;e++)if(te[e][1].exec(n)){t._f+=te[e][0];break}j.exec(n)&&(t._f+=" Z"),w(t)}else t._d=new Date(n)}function T(e){var n=e._i,s=G.exec(n);n===t?e._d=new Date:s?e._d=new Date(+s[1]):"string"==typeof n?v(e):d(n)?(e._a=n.slice(0),Y(e)):e._d=n instanceof Date?new Date(+n):new Date(n)}function b(t,e,n,s,i){return i.relativeTime(e||1,!!n,t,s)}function S(t,e,n){var s=W(Math.abs(t)/1e3),i=W(s/60),r=W(i/60),a=W(r/24),o=W(a/365),u=45>s&&["s",s]||1===i&&["m"]||45>i&&["mm",i]||1===r&&["h"]||22>r&&["hh",r]||1===a&&["d"]||25>=a&&["dd",a]||45>=a&&["M"]||345>a&&["MM",W(a/30)]||1===o&&["y"]||["yy",o];return u[2]=e,u[3]=t>0,u[4]=n,b.apply({},u)}function F(t,e,n){var s,i=n-e,r=n-t.day();return r>i&&(r-=7),i-7>r&&(r+=7),s=H(t).add("d",r),{week:Math.ceil(s.dayOfYear()/7),year:s.year()}}function O(t){var e=t._i,n=t._f;return null===e||""===e?null:("string"==typeof e&&(t._i=e=_().preparse(e)),H.isMoment(e)?(t=a({},e),t._d=new Date(+e._d)):n?d(n)?k(t):w(t):T(t),new i(t))}function z(t,e){H.fn[t]=H.fn[t+"s"]=function(t){var n=this._isUTC?"UTC":"";return null!=t?(this._d["set"+n+e](t),H.updateOffset(this),this):this._d["get"+n+e]()}}function C(t){H.duration.fn[t]=function(){return this._data[t]}}function L(t,e){H.duration.fn["as"+t]=function(){return+this/e}}for(var H,P,U="2.1.0",W=Math.round,x={},A="undefined"!=typeof module&&module.exports,G=/^\/?Date\((\-?\d+)/i,Z=/(\-)?(\d*)?\.?(\d+)\:(\d+)\:(\d+)\.?(\d{3})?/,E=/(\[[^\[]*\])|(\\)?(Mo|MM?M?M?|Do|DDDo|DD?D?D?|ddd?d?|do?|w[o|w]?|W[o|W]?|YYYYY|YYYY|YY|gg(ggg?)?|GG(GGG?)?|e|E|a|A|hh?|HH?|mm?|ss?|SS?S?|X|zz?|ZZ?|.)/g,N=/(\[[^\[]*\])|(\\)?(LT|LL?L?L?|l{1,4})/g,J=/\d\d?/,I=/\d{1,3}/,V=/\d{3}/,X=/\d{1,4}/,$=/[+\-]?\d{1,6}/,R=/[0-9]*['a-z\u00A0-\u05FF\u0700-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+|[\u0600-\u06FF\/]+(\s*?[\u0600-\u06FF]+){1,2}/i,j=/Z|[\+\-]\d\d:?\d\d/i,q=/T/i,B=/[\+\-]?\d+(\.\d{1,3})?/,K=/^\s*\d{4}-\d\d-\d\d((T| )(\d\d(:\d\d(:\d\d(\.\d\d?\d?)?)?)?)?([\+\-]\d\d:?\d\d)?)?/,Q="YYYY-MM-DDTHH:mm:ssZ",te=[["HH:mm:ss.S",/(T| )\d\d:\d\d:\d\d\.\d{1,3}/],["HH:mm:ss",/(T| )\d\d:\d\d:\d\d/],["HH:mm",/(T| )\d\d:\d\d/],["HH",/(T| )\d\d/]],ee=/([\+\-]|\d\d)/gi,ne="Date|Hours|Minutes|Seconds|Milliseconds".split("|"),se={Milliseconds:1,Seconds:1e3,Minutes:6e4,Hours:36e5,Days:864e5,Months:2592e6,Years:31536e6},ie={ms:"millisecond",s:"second",m:"minute",h:"hour",d:"day",w:"week",M:"month",y:"year"},re={},ae="DDD w W M D d".split(" "),oe="M D H h m s w W".split(" "),ue={M:function(){return this.month()+1},MMM:function(t){return this.lang().monthsShort(this,t)},MMMM:function(t){return this.lang().months(this,t)},D:function(){return this.date()},DDD:function(){return this.dayOfYear()},d:function(){return this.day()},dd:function(t){return this.lang().weekdaysMin(this,t)},ddd:function(t){return this.lang().weekdaysShort(this,t)},dddd:function(t){return this.lang().weekdays(this,t)},w:function(){return this.week()},W:function(){return this.isoWeek()},YY:function(){return u(this.year()%100,2)},YYYY:function(){return u(this.year(),4)},YYYYY:function(){return u(this.year(),5)},gg:function(){return u(this.weekYear()%100,2)},gggg:function(){return this.weekYear()},ggggg:function(){return u(this.weekYear(),5)},GG:function(){return u(this.isoWeekYear()%100,2)},GGGG:function(){return this.isoWeekYear()},GGGGG:function(){return u(this.isoWeekYear(),5)},e:function(){return this.weekday()},E:function(){return this.isoWeekday()},a:function(){return this.lang().meridiem(this.hours(),this.minutes(),!0)},A:function(){return this.lang().meridiem(this.hours(),this.minutes(),!1)},H:function(){return this.hours()},h:function(){return this.hours()%12||12},m:function(){return this.minutes()},s:function(){return this.seconds()},S:function(){return~~(this.milliseconds()/100)},SS:function(){return u(~~(this.milliseconds()/10),2)},SSS:function(){return u(this.milliseconds(),3)},Z:function(){var t=-this.zone(),e="+";return 0>t&&(t=-t,e="-"),e+u(~~(t/60),2)+":"+u(~~t%60,2)},ZZ:function(){var t=-this.zone(),e="+";return 0>t&&(t=-t,e="-"),e+u(~~(10*t/6),4)},z:function(){return this.zoneAbbr()},zz:function(){return this.zoneName()},X:function(){return this.unix()}};ae.length;)P=ae.pop(),ue[P+"o"]=n(ue[P],P);for(;oe.length;)P=oe.pop(),ue[P+P]=e(ue[P],2);for(ue.DDDD=e(ue.DDD,3),s.prototype={set:function(t){var e,n;for(n in t)e=t[n],"function"==typeof e?this[n]=e:this["_"+n]=e},_months:"January_February_March_April_May_June_July_August_September_October_November_December".split("_"),months:function(t){return this._months[t.month()]},_monthsShort:"Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_"),monthsShort:function(t){return this._monthsShort[t.month()]},monthsParse:function(t){var e,n,s;for(this._monthsParse||(this._monthsParse=[]),e=0;12>e;e++)if(this._monthsParse[e]||(n=H([2e3,e]),s="^"+this.months(n,"")+"|^"+this.monthsShort(n,""),this._monthsParse[e]=new RegExp(s.replace(".",""),"i")),this._monthsParse[e].test(t))return e},_weekdays:"Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),weekdays:function(t){return this._weekdays[t.day()]},_weekdaysShort:"Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_"),weekdaysShort:function(t){return this._weekdaysShort[t.day()]},_weekdaysMin:"Su_Mo_Tu_We_Th_Fr_Sa".split("_"),weekdaysMin:function(t){return this._weekdaysMin[t.day()]},weekdaysParse:function(t){var e,n,s;for(this._weekdaysParse||(this._weekdaysParse=[]),e=0;7>e;e++)if(this._weekdaysParse[e]||(n=H([2e3,1]).day(e),s="^"+this.weekdays(n,"")+"|^"+this.weekdaysShort(n,"")+"|^"+this.weekdaysMin(n,""),this._weekdaysParse[e]=new RegExp(s.replace(".",""),"i")),this._weekdaysParse[e].test(t))return e},_longDateFormat:{LT:"h:mm A",L:"MM/DD/YYYY",LL:"MMMM D YYYY",LLL:"MMMM D YYYY LT",LLLL:"dddd, MMMM D YYYY LT"},longDateFormat:function(t){var e=this._longDateFormat[t];return!e&&this._longDateFormat[t.toUpperCase()]&&(e=this._longDateFormat[t.toUpperCase()].replace(/MMMM|MM|DD|dddd/g,function(t){return t.slice(1)}),this._longDateFormat[t]=e),e},isPM:function(t){return"p"===(t+"").toLowerCase()[0]},_meridiemParse:/[ap]\.?m?\.?/i,meridiem:function(t,e,n){return t>11?n?"pm":"PM":n?"am":"AM"},_calendar:{sameDay:"[Today at] LT",nextDay:"[Tomorrow at] LT",nextWeek:"dddd [at] LT",lastDay:"[Yesterday at] LT",lastWeek:"[Last] dddd [at] LT",sameElse:"L"},calendar:function(t,e){var n=this._calendar[t];return"function"==typeof n?n.apply(e):n},_relativeTime:{future:"in %s",past:"%s ago",s:"a few seconds",m:"a minute",mm:"%d minutes",h:"an hour",hh:"%d hours",d:"a day",dd:"%d days",M:"a month",MM:"%d months",y:"a year",yy:"%d years"},relativeTime:function(t,e,n,s){var i=this._relativeTime[n];return"function"==typeof i?i(t,e,n,s):i.replace(/%d/i,t)},pastFuture:function(t,e){var n=this._relativeTime[t>0?"future":"past"];return"function"==typeof n?n(e):n.replace(/%s/i,e)},ordinal:function(t){return this._ordinal.replace("%d",t)},_ordinal:"%d",preparse:function(t){return t},postformat:function(t){return t},week:function(t){return F(t,this._week.dow,this._week.doy).week},_week:{dow:0,doy:6}},H=function(t,e,n){return O({_i:t,_f:e,_l:n,_isUTC:!1})},H.utc=function(t,e,n){return O({_useUTC:!0,_isUTC:!0,_l:n,_i:t,_f:e})},H.unix=function(t){return H(1e3*t)},H.duration=function(t,e){var n,s,i=H.isDuration(t),a="number"==typeof t,o=i?t._input:a?{}:t,u=Z.exec(t);return a?e?o[e]=t:o.milliseconds=t:u&&(n="-"===u[1]?-1:1,o={y:0,d:~~u[2]*n,h:~~u[3]*n,m:~~u[4]*n,s:~~u[5]*n,ms:~~u[6]*n}),s=new r(o),i&&t.hasOwnProperty("_lang")&&(s._lang=t._lang),s},H.version=U,H.defaultFormat=Q,H.updateOffset=function(){},H.lang=function(t,e){return t?(e?l(t,e):x[t]||_(t),H.duration.fn._lang=H.fn._lang=_(t),void 0):H.fn._lang._abbr},H.langData=function(t){return t&&t._lang&&t._lang._abbr&&(t=t._lang._abbr),_(t)},H.isMoment=function(t){return t instanceof i},H.isDuration=function(t){return t instanceof r},H.fn=i.prototype={clone:function(){return H(this)},valueOf:function(){return+this._d+6e4*(this._offset||0)},unix:function(){return Math.floor(+this/1e3)},toString:function(){return this.format("ddd MMM DD YYYY HH:mm:ss [GMT]ZZ")},toDate:function(){return this._offset?new Date(+this):this._d},toISOString:function(){return M(H(this).utc(),"YYYY-MM-DD[T]HH:mm:ss.SSS[Z]")},toArray:function(){var t=this;return[t.year(),t.month(),t.date(),t.hours(),t.minutes(),t.seconds(),t.milliseconds()]},isValid:function(){return null==this._isValid&&(this._isValid=this._a?!c(this._a,(this._isUTC?H.utc(this._a):H(this._a)).toArray()):!isNaN(this._d.getTime())),!!this._isValid},utc:function(){return this.zone(0)},local:function(){return this.zone(0),this._isUTC=!1,this},format:function(t){var e=M(this,t||H.defaultFormat);return this.lang().postformat(e)},add:function(t,e){var n;return n="string"==typeof t?H.duration(+e,t):H.duration(t,e),h(this,n,1),this},subtract:function(t,e){var n;return n="string"==typeof t?H.duration(+e,t):H.duration(t,e),h(this,n,-1),this},diff:function(t,e,n){var s,i,r=this._isUTC?H(t).zone(this._offset||0):H(t).local(),a=6e4*(this.zone()-r.zone());return e=f(e),"year"===e||"month"===e?(s=432e5*(this.daysInMonth()+r.daysInMonth()),i=12*(this.year()-r.year())+(this.month()-r.month()),i+=(this-H(this).startOf("month")-(r-H(r).startOf("month")))/s,i-=6e4*(this.zone()-H(this).startOf("month").zone()-(r.zone()-H(r).startOf("month").zone()))/s,"year"===e&&(i/=12)):(s=this-r,i="second"===e?s/1e3:"minute"===e?s/6e4:"hour"===e?s/36e5:"day"===e?(s-a)/864e5:"week"===e?(s-a)/6048e5:s),n?i:o(i)},from:function(t,e){return H.duration(this.diff(t)).lang(this.lang()._abbr).humanize(!e)},fromNow:function(t){return this.from(H(),t)},calendar:function(){var t=this.diff(H().startOf("day"),"days",!0),e=-6>t?"sameElse":-1>t?"lastWeek":0>t?"lastDay":1>t?"sameDay":2>t?"nextDay":7>t?"nextWeek":"sameElse";return this.format(this.lang().calendar(e,this))},isLeapYear:function(){var t=this.year();return 0===t%4&&0!==t%100||0===t%400},isDST:function(){return this.zone()<this.clone().month(0).zone()||this.zone()<this.clone().month(5).zone()},day:function(t){var e=this._isUTC?this._d.getUTCDay():this._d.getDay();return null!=t?"string"==typeof t&&(t=this.lang().weekdaysParse(t),"number"!=typeof t)?this:this.add({d:t-e}):e},month:function(t){var e,n=this._isUTC?"UTC":"";return null!=t?"string"==typeof t&&(t=this.lang().monthsParse(t),"number"!=typeof t)?this:(e=this.date(),this.date(1),this._d["set"+n+"Month"](t),this.date(Math.min(e,this.daysInMonth())),H.updateOffset(this),this):this._d["get"+n+"Month"]()},startOf:function(t){switch(t=f(t)){case"year":this.month(0);case"month":this.date(1);case"week":case"day":this.hours(0);case"hour":this.minutes(0);case"minute":this.seconds(0);case"second":this.milliseconds(0)}return"week"===t&&this.weekday(0),this},endOf:function(t){return this.startOf(t).add(t,1).subtract("ms",1)},isAfter:function(t,e){return e="undefined"!=typeof e?e:"millisecond",+this.clone().startOf(e)>+H(t).startOf(e)},isBefore:function(t,e){return e="undefined"!=typeof e?e:"millisecond",+this.clone().startOf(e)<+H(t).startOf(e)},isSame:function(t,e){return e="undefined"!=typeof e?e:"millisecond",+this.clone().startOf(e)===+H(t).startOf(e)},min:function(t){return t=H.apply(null,arguments),this>t?this:t},max:function(t){return t=H.apply(null,arguments),t>this?this:t},zone:function(t){var e=this._offset||0;return null==t?this._isUTC?e:this._d.getTimezoneOffset():("string"==typeof t&&(t=p(t)),Math.abs(t)<16&&(t=60*t),this._offset=t,this._isUTC=!0,e!==t&&h(this,H.duration(e-t,"m"),1,!0),this)},zoneAbbr:function(){return this._isUTC?"UTC":""},zoneName:function(){return this._isUTC?"Coordinated Universal Time":""},daysInMonth:function(){return H.utc([this.year(),this.month()+1,0]).date()},dayOfYear:function(t){var e=W((H(this).startOf("day")-H(this).startOf("year"))/864e5)+1;return null==t?e:this.add("d",t-e)},weekYear:function(t){var e=F(this,this.lang()._week.dow,this.lang()._week.doy).year;return null==t?e:this.add("y",t-e)},isoWeekYear:function(t){var e=F(this,1,4).year;return null==t?e:this.add("y",t-e)},week:function(t){var e=this.lang().week(this);return null==t?e:this.add("d",7*(t-e))},isoWeek:function(t){var e=F(this,1,4).week;return null==t?e:this.add("d",7*(t-e))},weekday:function(t){var e=(this._d.getDay()+7-this.lang()._week.dow)%7;return null==t?e:this.add("d",t-e)},isoWeekday:function(t){return null==t?this.day()||7:this.day(this.day()%7?t:t-7)},lang:function(e){return e===t?this._lang:(this._lang=_(e),this)}},P=0;P<ne.length;P++)z(ne[P].toLowerCase().replace(/s$/,""),ne[P]);z("year","FullYear"),H.fn.days=H.fn.day,H.fn.months=H.fn.month,H.fn.weeks=H.fn.week,H.fn.isoWeeks=H.fn.isoWeek,H.fn.toJSON=H.fn.toISOString,H.duration.fn=r.prototype={_bubble:function(){var t,e,n,s,i=this._milliseconds,r=this._days,a=this._months,u=this._data;u.milliseconds=i%1e3,t=o(i/1e3),u.seconds=t%60,e=o(t/60),u.minutes=e%60,n=o(e/60),u.hours=n%24,r+=o(n/24),u.days=r%30,a+=o(r/30),u.months=a%12,s=o(a/12),u.years=s},weeks:function(){return o(this.days()/7)},valueOf:function(){return this._milliseconds+864e5*this._days+2592e6*(this._months%12)+31536e6*~~(this._months/12)},humanize:function(t){var e=+this,n=S(e,!t,this.lang());return t&&(n=this.lang().pastFuture(e,n)),this.lang().postformat(n)},add:function(t,e){var n=H.duration(t,e);return this._milliseconds+=n._milliseconds,this._days+=n._days,this._months+=n._months,this._bubble(),this},subtract:function(t,e){var n=H.duration(t,e);return this._milliseconds-=n._milliseconds,this._days-=n._days,this._months-=n._months,this._bubble(),this},get:function(t){return t=f(t),this[t.toLowerCase()+"s"]()},as:function(t){return t=f(t),this["as"+t.charAt(0).toUpperCase()+t.slice(1)+"s"]()},lang:H.fn.lang};for(P in se)se.hasOwnProperty(P)&&(L(P,se[P]),C(P.toLowerCase()));L("Weeks",6048e5),H.duration.fn.asMonths=function(){return(+this-31536e6*this.years())/2592e6+12*this.years()},H.lang("en",{ordinal:function(t){var e=t%10,n=1===~~(t%100/10)?"th":1===e?"st":2===e?"nd":3===e?"rd":"th";return t+n}}),A&&(module.exports=H),"undefined"==typeof ender&&(this.moment=H),"function"==typeof define&&define.amd&&define("moment",[],function(){return H})}.call(this);

// StickyScroll Plugin (Docs & Licensing: https://github.com/rickharris/stickyscroll)
(function($) {
    $.fn.stickyScroll = function(options) {
        var methods = {
            init : function(options) {
                var settings;
                if (options.mode !== 'auto' && options.mode !== 'manual') {
                    if (options.container) {
                        options.mode = 'auto';
                    }
                    if (options.bottomBoundary) {
                        options.mode = 'manual';
                    }
                };
                settings = $.extend({
                    mode: 				'auto', // 'auto' or 'manual'
                    container: 			$('body'),
                    topBoundary: 		null,
                    bottomBoundary: 	null
                }, options);
                function bottomBoundary() {
					return settings.container.offset().top + isotopeHeightContainer;
                };
                function topBoundary() {
                    return settings.container.offset().top
                };
                function elHeight(el) {
                    return $(el).attr('offsetHeight');
                };
                // make sure user input is a jQuery object
                settings.container = $(settings.container);
                if(!settings.container.length) {
                    if(console) {
                        console.log('StickyScroll: the element ' + options.container + ' does not exist, we\'re throwing in the towel.');
                    };
                    return;
                };
                // calculate automatic bottomBoundary
                if(settings.mode === 'auto') {
                    settings.topBoundary 	= topBoundary();
                    settings.bottomBoundary = bottomBoundary();
                };
                return this.each(function(index) {
                    var el = $(this), win = $(window), id = XDate.now() + index, height = elHeight(el);
                    el.data('sticky-id', id);
                    win.bind('scroll.stickyscroll-' + id, function() {
                        var top = $(document).scrollTop();
						var bottom = top - isotopeHeightContainer;
						//$("#PositionControl").html("Offset: " + settings.topBoundary + "</br></br>Container: " + isotopeHeightContainer + "</br></br>Limit: " + settings.bottomBoundary + "</br></br>Top: " + top + "</br></br>Bottom: " + (bottom - settings.topBoundary));
                        if (bottom - settings.topBoundary >= 0) {
							// Don't follow mouse further once bottom of container has been reached
                            el.offset({
                                top: $(document).height() - settings.bottomBoundary - height
                            }).removeClass('sticky-active').removeClass('sticky-inactive').addClass('sticky-stopped');
							//$(".Scroll_To_Top").show().css("marginLeft", "10px");
							$(".Scroll_To_Top").show();
                        } else if (top > settings.topBoundary) {
							// Follow mouse as long as container is still visible
                            el.offset({
                                top: $(window).scrollTop() + controlBarAdjust
                            }).removeClass('sticky-stopped').removeClass('sticky-inactive').addClass('sticky-active');
							//$(".Scroll_To_Top").show().css("marginLeft", "10px");
							$(".Scroll_To_Top").show();
                        } else if (top < settings.topBoundary) {
							// Return to original position as after page load
                            el.css({
                                position: 	'',
                                top: 		'',
                                bottom: 	''
                            })
                            .removeClass('sticky-stopped')
                            .removeClass('sticky-active')
                            .addClass('sticky-inactive');
							//$(".Scroll_To_Top").hide().css("marginLeft", "0px");
							$(".Scroll_To_Top").hide();
                        }
                    });
                    win.bind('resize.stickyscroll-' + id, function() {
                        if (settings.mode === 'auto') {
                            settings.topBoundary 		= topBoundary();
                            settings.bottomBoundary 	= bottomBoundary();
                        };
                        height = elHeight(el);
                        $(this).scroll();
                    });
                    el.addClass('sticky-processed');
                    // start it off
                    win.scroll();
                });
            },
            reset : function() {
                return this.each(function() {
                    var el = $(this), id = el.data('sticky-id');
                    el.css({
                        position: '',
                        top: '',
                        bottom: ''
                    }).removeClass('sticky-stopped').removeClass('sticky-active').removeClass('sticky-inactive').removeClass('sticky-processed');
                    $(window).unbind('.stickyscroll-' + id);
                });
            }
        };
        // if options is a valid method, execute it
        if (methods[options]) {
            return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof options === 'object' || !options) {
            return methods.init.apply(this, arguments);
        } else if(console) {
            console.log('Method' + options + ' does not exist on jQuery.stickyScroll');
        };
    };
})(jQuery);

// XDate v0.8 (Docs & Licensing: http://arshaw.com/xdate/)
var XDate=function(g,n,A,p){function f(){var a=this instanceof f?this:new f,c=arguments,b=c.length,d;typeof c[b-1]=="boolean"&&(d=c[--b],c=q(c,0,b));if(b)if(b==1)if(b=c[0],b instanceof g||typeof b=="number")a[0]=new g(+b);else if(b instanceof f){var c=a,h=new g(+b[0]);if(l(b))h.toString=v;c[0]=h}else{if(typeof b=="string"){a[0]=new g(0);a:{for(var c=b,b=d||!1,h=f.parsers,w=0,e;w<h.length;w++)if(e=h[w](c,b,a)){a=e;break a}a[0]=new g(c)}}}else a[0]=new g(m.apply(g,c)),d||(a[0]=r(a[0]));else a[0]=new g;
typeof d=="boolean"&&B(a,d);return a}function l(a){return a[0].toString===v}function B(a,c,b){if(c){if(!l(a))b&&(a[0]=new g(m(a[0].getFullYear(),a[0].getMonth(),a[0].getDate(),a[0].getHours(),a[0].getMinutes(),a[0].getSeconds(),a[0].getMilliseconds()))),a[0].toString=v}else l(a)&&(a[0]=b?r(a[0]):new g(+a[0]));return a}function C(a,c,b,d,h){var e=k(j,a[0],h),a=k(D,a[0],h),h=!1;d.length==2&&typeof d[1]=="boolean"&&(h=d[1],d=[b]);b=c==1?(b%12+12)%12:e(1);a(c,d);h&&e(1)!=b&&(a(1,[e(1)-1]),a(2,[E(e(0),
e(1))]))}function F(a,c,b,d){var b=Number(b),h=n.floor(b);a["set"+o[c]](a["get"+o[c]]()+h,d||!1);h!=b&&c<6&&F(a,c+1,(b-h)*G[c],d)}function H(a,c,b){var a=a.clone().setUTCMode(!0,!0),c=f(c).setUTCMode(!0,!0),d=0;if(b==0||b==1){for(var h=6;h>=b;h--)d/=G[h],d+=j(c,!1,h)-j(a,!1,h);b==1&&(d+=(c.getFullYear()-a.getFullYear())*12)}else b==2?(b=a.toDate().setUTCHours(0,0,0,0),d=c.toDate().setUTCHours(0,0,0,0),d=n.round((d-b)/864E5)+(c-d-(a-b))/864E5):d=(c-a)/[36E5,6E4,1E3,1][b-3];return d}function s(a){var c=
a(0),b=a(1),d=a(2),a=new g(m(c,b,d)),c=t(I(c,b,d));return n.floor(n.round((a-c)/864E5)/7)+1}function I(a,c,b){c=new g(m(a,c,b));if(c<t(a))return a-1;else if(c>=t(a+1))return a+1;return a}function t(a){a=new g(m(a,0,4));a.setUTCDate(a.getUTCDate()-(a.getUTCDay()+6)%7);return a}function J(a,c,b,d){var h=k(j,a,d),e=k(D,a,d);b===p&&(b=I(h(0),h(1),h(2)));b=t(b);d||(b=r(b));a.setTime(+b);e(2,[h(2)+(c-1)*7])}function K(a,c,b,d,h){var e=f.locales,g=e[f.defaultLocale]||{},i=k(j,a,h),b=(typeof b=="string"?
e[b]:b)||{};return x(a,c,function(a){if(d)for(var b=(a==7?2:a)-1;b>=0;b--)d.push(i(b));return i(a)},function(a){return b[a]||g[a]},h)}function x(a,c,b,d,e){for(var f,g,i="";f=c.match(N);){i+=c.substr(0,f.index);if(f[1]){g=i;for(var i=a,j=f[1],l=b,m=d,n=e,k=j.length,o=void 0,q="";k>0;)o=O(i,j.substr(0,k),l,m,n),o!==p?(q+=o,j=j.substr(k),k=j.length):k--;i=g+(q+j)}else f[3]?(g=x(a,f[4],b,d,e),parseInt(g.replace(/\D/g,""),10)&&(i+=g)):i+=f[7]||"'";c=c.substr(f.index+f[0].length)}return i+c}function O(a,
c,b,d,e){var g=f.formatters[c];if(typeof g=="string")return x(a,g,b,d,e);else if(typeof g=="function")return g(a,e||!1,d);switch(c){case "fff":return i(b(6),3);case "s":return b(5);case "ss":return i(b(5));case "m":return b(4);case "mm":return i(b(4));case "h":return b(3)%12||12;case "hh":return i(b(3)%12||12);case "H":return b(3);case "HH":return i(b(3));case "d":return b(2);case "dd":return i(b(2));case "ddd":return d("dayNamesShort")[b(7)]||"";case "dddd":return d("dayNames")[b(7)]||"";case "M":return b(1)+
1;case "MM":return i(b(1)+1);case "MMM":return d("monthNamesShort")[b(1)]||"";case "MMMM":return d("monthNames")[b(1)]||"";case "yy":return(b(0)+"").substring(2);case "yyyy":return b(0);case "t":return u(b,d).substr(0,1).toLowerCase();case "tt":return u(b,d).toLowerCase();case "T":return u(b,d).substr(0,1);case "TT":return u(b,d);case "z":case "zz":case "zzz":return e?c="Z":(d=a.getTimezoneOffset(),a=d<0?"+":"-",b=n.floor(n.abs(d)/60),d=n.abs(d)%60,e=b,c=="zz"?e=i(b):c=="zzz"&&(e=i(b)+":"+i(d)),c=
a+e),c;case "w":return s(b);case "ww":return i(s(b));case "S":return c=b(2),c>10&&c<20?"th":["st","nd","rd"][c%10-1]||"th"}}function u(a,c){return a(3)<12?c("amDesignator"):c("pmDesignator")}function y(a){return!isNaN(+a[0])}function j(a,c,b){return a["get"+(c?"UTC":"")+o[b]]()}function D(a,c,b,d){a["set"+(c?"UTC":"")+o[b]].apply(a,d)}function r(a){return new g(a.getUTCFullYear(),a.getUTCMonth(),a.getUTCDate(),a.getUTCHours(),a.getUTCMinutes(),a.getUTCSeconds(),a.getUTCMilliseconds())}function E(a,
c){return 32-(new g(m(a,c,32))).getUTCDate()}function z(a){return function(){return a.apply(p,[this].concat(q(arguments)))}}function k(a){var c=q(arguments,1);return function(){return a.apply(p,c.concat(q(arguments)))}}function q(a,c,b){return A.prototype.slice.call(a,c||0,b===p?a.length:b)}function L(a,c){for(var b=0;b<a.length;b++)c(a[b],b)}function i(a,c){c=c||2;for(a+="";a.length<c;)a="0"+a;return a}var o="FullYear,Month,Date,Hours,Minutes,Seconds,Milliseconds,Day,Year".split(","),M=["Years",
"Months","Days"],G=[12,31,24,60,60,1E3,1],N=/(([a-zA-Z])\2*)|(\((('.*?'|\(.*?\)|.)*?)\))|('(.*?)')/,m=g.UTC,v=g.prototype.toUTCString,e=f.prototype;e.length=1;e.splice=A.prototype.splice;e.getUTCMode=z(l);e.setUTCMode=z(B);e.getTimezoneOffset=function(){return l(this)?0:this[0].getTimezoneOffset()};L(o,function(a,c){e["get"+a]=function(){return j(this[0],l(this),c)};c!=8&&(e["getUTC"+a]=function(){return j(this[0],!0,c)});c!=7&&(e["set"+a]=function(a){C(this,c,a,arguments,l(this));return this},c!=
8&&(e["setUTC"+a]=function(a){C(this,c,a,arguments,!0);return this},e["add"+(M[c]||a)]=function(a,d){F(this,c,a,d);return this},e["diff"+(M[c]||a)]=function(a){return H(this,a,c)}))});e.getWeek=function(){return s(k(j,this,!1))};e.getUTCWeek=function(){return s(k(j,this,!0))};e.setWeek=function(a,c){J(this,a,c,!1);return this};e.setUTCWeek=function(a,c){J(this,a,c,!0);return this};e.addWeeks=function(a){return this.addDays(Number(a)*7)};e.diffWeeks=function(a){return H(this,a,2)/7};f.parsers=[function(a,
c,b){if(a=a.match(/^(\d{4})(-(\d{2})(-(\d{2})([T ](\d{2}):(\d{2})(:(\d{2})(\.(\d+))?)?(Z|(([-+])(\d{2})(:?(\d{2}))?))?)?)?)?$/)){var d=new g(m(a[1],a[3]?a[3]-1:0,a[5]||1,a[7]||0,a[8]||0,a[10]||0,a[12]?Number("0."+a[12])*1E3:0));a[13]?a[14]&&d.setUTCMinutes(d.getUTCMinutes()+(a[15]=="-"?1:-1)*(Number(a[16])*60+(a[18]?Number(a[18]):0))):c||(d=r(d));return b.setTime(+d)}}];f.parse=function(a){return+f(""+a)};e.toString=function(a,c,b){return a===p||!y(this)?this[0].toString():K(this,a,c,b,l(this))};
e.toUTCString=e.toGMTString=function(a,c,b){return a===p||!y(this)?this[0].toUTCString():K(this,a,c,b,!0)};e.toISOString=function(){return this.toUTCString("yyyy-MM-dd'T'HH:mm:ss(.fff)zzz")};f.defaultLocale="";f.locales={"":{monthNames:"January,February,March,April,May,June,July,August,September,October,November,December".split(","),monthNamesShort:"Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec".split(","),dayNames:"Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday".split(","),dayNamesShort:"Sun,Mon,Tue,Wed,Thu,Fri,Sat".split(","),
amDesignator:"AM",pmDesignator:"PM"}};f.formatters={i:"yyyy-MM-dd'T'HH:mm:ss(.fff)",u:"yyyy-MM-dd'T'HH:mm:ss(.fff)zzz"};L("getTime,valueOf,toDateString,toTimeString,toLocaleString,toLocaleDateString,toLocaleTimeString,toJSON".split(","),function(a){e[a]=function(){return this[0][a]()}});e.setTime=function(a){this[0].setTime(a);return this};e.valid=z(y);e.clone=function(){return new f(this)};e.clearTime=function(){return this.setHours(0,0,0,0)};e.toDate=function(){return new g(+this[0])};f.now=function(){return+new g};
f.today=function(){return(new f).clearTime()};f.UTC=m;f.getDaysInMonth=E;if(typeof module!=="undefined"&&module.exports)module.exports=f;typeof define==="function"&&define.amd&&define([],function(){return f});return f}(Date,Math,Array);

// jRumble v1.3 (Docs & Licensing: http://jackrugile.com/jrumble)
(function (f) {
    f.fn.jrumble = function (g) {
        var a = f.extend({
            x: 2,
            y: 2,
            rotation: 1,
            speed: 15,
            opacity: false,
            opacityMin: 0.5
        }, g);
        return this.each(function () {
            var b = f(this),
                h = a.x * 2,
                i = a.y * 2,
                k = a.rotation * 2,
                g = a.speed === 0 ? 1 : a.speed,
                m = a.opacity,
                n = a.opacityMin,
                l, j, o = function () {
                    var e = Math.floor(Math.random() * (h + 1)) - h / 2,
                        a = Math.floor(Math.random() * (i + 1)) - i / 2,
                        c = Math.floor(Math.random() * (k + 1)) - k / 2,
                        d = m ? Math.random() + n : 1,
                        e = e === 0 && h !== 0 ? Math.random() < 0.5 ? 1 : -1 : e,
                        a = a === 0 && i !== 0 ? Math.random() < 0.5 ? 1 : -1 : a;
                    b.css("display") === "inline" && (l = true, b.css("display", "inline-block"));
                    b.css({
                        position: "relative",
                        left: e + "px",
                        top: a + "px",
                        "-ms-filter": "progid:DXImageTransform.Microsoft.Alpha(Opacity=" + d * 100 + ")",
                        filter: "alpha(opacity=" + d * 100 + ")",
                        "-moz-opacity": d,
                        "-khtml-opacity": d,
                        opacity: d,
                        "-webkit-transform": "rotate(" + c + "deg)",
                        "-moz-transform": "rotate(" + c + "deg)",
                        "-ms-transform": "rotate(" + c + "deg)",
                        "-o-transform": "rotate(" + c + "deg)",
                        transform: "rotate(" + c + "deg)"
                    })
                }, p = {
                    left: 0,
                    top: 0,
                    "-ms-filter": "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)",
                    filter: "alpha(opacity=100)",
                    "-moz-opacity": 1,
                    "-khtml-opacity": 1,
                    opacity: 1,
                    "-webkit-transform": "rotate(0deg)",
                    "-moz-transform": "rotate(0deg)",
                    "-ms-transform": "rotate(0deg)",
                    "-o-transform": "rotate(0deg)",
                    transform: "rotate(0deg)"
                };
            b.bind({
                startRumble: function (a) {
                    a.stopPropagation();
                    clearInterval(j);
                    j = setInterval(o, g)
                },
                stopRumble: function (a) {
                    a.stopPropagation();
                    clearInterval(j);
                    l && b.css("display", "inline");
                    b.css(p)
                }
            })
        })
    }
})(jQuery);

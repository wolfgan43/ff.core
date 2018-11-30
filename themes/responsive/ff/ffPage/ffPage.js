/**
 * Forms Framework Javascript Handling Object
 *	ffPage' namespace
 */

ff.ffPage = (function () {
//privates

var that = { // publics
    __ff : "ff.ffPage", // used to recognize ff'objects
    "goToWithRetUrl" : function (href, ret) {
        var url = href + (href.indexOf("?") < 0 ? "?" : "&") + "ret_url=" + encodeURIComponent(window.location.pathname + window.location.search + window.location.hash);
        if(ret) {
            return url;
        } else {
            window.location.href = url;
            return false;

        }

    }
}; // publics' end

    window.addEventListener('load', function () {
        ff.initExt(that);
    });

return that;

// code's end.
})();

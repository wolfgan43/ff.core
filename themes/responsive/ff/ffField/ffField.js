/**
 * Forms Framework Javascript Handling Object
 *	ffField' namespace
 */

ff.ffField = (function () {
//privates

var that = { // publics
__ff : "ff.ffField" // used to recognize ff'objects
}; // publics' end

    window.addEventListener('load', function () {
        ff.initExt(that);
    });

return that;

// code's end.
})();

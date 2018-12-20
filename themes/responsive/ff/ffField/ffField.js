/**
 * Forms Framework Javascript Handling Object
 *	ffField' namespace
 */

ff.ffField = (function () {
//privates

var that = { // publics
__ff : "ff.ffField" // used to recognize ff'objects
}; // publics' end

    /* Init obj */
    function constructor() { // NB: called below publics
        ff.initExt(that);
    }

    if(document.readyState == "complete") {
        //  constructor(); //va in contrasto con libLoaded
    } else {
        window.addEventListener('load', function () {
            constructor();
        });
    }

return that;

// code's end.
})();

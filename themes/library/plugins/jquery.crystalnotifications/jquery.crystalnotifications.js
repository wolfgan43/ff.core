// =======================================================================================
// 
// Crystal Notifications v1.1
// Changelog:
//    v1.1 Some bug fixes on IE9-IE10
//
// Author: Klerith
// Page: http://codecanyon.net/user/klerith
// Email: fernando.herrera85@gmail.com BUT, first send me a message through codecanyon page.
//        That's because some people stole the code and ask me support when they are not a customer :(
//
// =======================================================================================



(function ($) 
{

var cnEmptyMessage = "You don't have any notifications.";


//=== Swiper JS
var Swiper=function(f,b){function g(a){return document.querySelectorAll?document.querySelectorAll(a):jQuery(a)}function h(){var c=y-l;b.freeMode&&(c=y-l);b.slidesPerView>a.slides.length&&(c=0);0>c&&(c=0);return c}function n(){function c(c){var d=new Image;d.onload=function(){a.imagesLoaded++;if(a.imagesLoaded==a.imagesToLoad.length&&(a.reInit(),b.onImagesReady))b.onImagesReady(a)};d.src=c}a.browser.ie10?(a.h.addEventListener(a.wrapper,a.touchEvents.touchStart,z,!1),a.h.addEventListener(document,a.touchEvents.touchMove,
A,!1),a.h.addEventListener(document,a.touchEvents.touchEnd,B,!1)):(a.support.touch&&(a.h.addEventListener(a.wrapper,"touchstart",z,!1),a.h.addEventListener(a.wrapper,"touchmove",A,!1),a.h.addEventListener(a.wrapper,"touchend",B,!1)),b.simulateTouch&&(a.h.addEventListener(a.wrapper,"mousedown",z,!1),a.h.addEventListener(document,"mousemove",A,!1),a.h.addEventListener(document,"mouseup",B,!1)));b.autoResize&&a.h.addEventListener(window,"resize",a.resizeFix,!1);t();a._wheelEvent=!1;if(b.mousewheelControl){void 0!==
document.onmousewheel&&(a._wheelEvent="mousewheel");try{WheelEvent("wheel"),a._wheelEvent="wheel"}catch(d){}a._wheelEvent||(a._wheelEvent="DOMMouseScroll");a._wheelEvent&&a.h.addEventListener(a.container,a._wheelEvent,N,!1)}b.keyboardControl&&a.h.addEventListener(document,"keydown",O,!1);if(b.updateOnImagesReady){document.querySelectorAll?a.imagesToLoad=a.container.querySelectorAll("img"):window.jQuery&&(a.imagesToLoad=g(a.container).find("img"));for(var e=0;e<a.imagesToLoad.length;e++)c(a.imagesToLoad[e].getAttribute("src"))}}
function t(){if(b.preventLinks){var c=[];document.querySelectorAll?c=a.container.querySelectorAll("a"):window.jQuery&&(c=g(a.container).find("a"));for(var d=0;d<c.length;d++)a.h.addEventListener(c[d],"click",P,!1)}if(b.releaseFormElements)for(c=document.querySelectorAll?a.container.querySelectorAll("input, textarea, select"):g(a.container).find("input, textarea, select"),d=0;d<c.length;d++)a.h.addEventListener(c[d],a.touchEvents.touchStart,Q,!0);if(b.onSlideClick)for(d=0;d<a.slides.length;d++)a.h.addEventListener(a.slides[d],
"click",R,!1);if(b.onSlideTouch)for(d=0;d<a.slides.length;d++)a.h.addEventListener(a.slides[d],a.touchEvents.touchStart,S,!1)}function v(){if(b.onSlideClick)for(var c=0;c<a.slides.length;c++)a.h.removeEventListener(a.slides[c],"click",R,!1);if(b.onSlideTouch)for(c=0;c<a.slides.length;c++)a.h.removeEventListener(a.slides[c],a.touchEvents.touchStart,S,!1);if(b.releaseFormElements)for(var d=document.querySelectorAll?a.container.querySelectorAll("input, textarea, select"):g(a.container).find("input, textarea, select"),
c=0;c<d.length;c++)a.h.removeEventListener(d[c],a.touchEvents.touchStart,Q,!0);if(b.preventLinks)for(d=[],document.querySelectorAll?d=a.container.querySelectorAll("a"):window.jQuery&&(d=g(a.container).find("a")),c=0;c<d.length;c++)a.h.removeEventListener(d[c],"click",P,!1)}function O(c){var b=c.keyCode||c.charCode;if(37==b||39==b||38==b||40==b){for(var e=!1,f=a.h.getOffset(a.container),h=a.h.windowScroll().left,g=a.h.windowScroll().top,m=a.h.windowWidth(),l=a.h.windowHeight(),f=[[f.left,f.top],[f.left+
a.width,f.top],[f.left,f.top+a.height],[f.left+a.width,f.top+a.height]],p=0;p<f.length;p++){var r=f[p];r[0]>=h&&(r[0]<=h+m&&r[1]>=g&&r[1]<=g+l)&&(e=!0)}if(!e)return}if(k){if(37==b||39==b)c.preventDefault?c.preventDefault():c.returnValue=!1;39==b&&a.swipeNext();37==b&&a.swipePrev()}else{if(38==b||40==b)c.preventDefault?c.preventDefault():c.returnValue=!1;40==b&&a.swipeNext();38==b&&a.swipePrev()}}function N(c){var d=a._wheelEvent,e;c.detail?e=-c.detail:"mousewheel"==d?e=c.wheelDelta:"DOMMouseScroll"==
d?e=-c.detail:"wheel"==d&&(e=Math.abs(c.deltaX)>Math.abs(c.deltaY)?-c.deltaX:-c.deltaY);b.freeMode?(k?a.getWrapperTranslate("x"):a.getWrapperTranslate("y"),k?(d=a.getWrapperTranslate("x")+e,e=a.getWrapperTranslate("y"),0<d&&(d=0),d<-h()&&(d=-h())):(d=a.getWrapperTranslate("x"),e=a.getWrapperTranslate("y")+e,0<e&&(e=0),e<-h()&&(e=-h())),a.setWrapperTransition(0),a.setWrapperTranslate(d,e,0),k?a.updateActiveSlide(d):a.updateActiveSlide(e)):0>e?a.swipeNext():a.swipePrev();b.autoplay&&a.stopAutoplay();
c.preventDefault?c.preventDefault():c.returnValue=!1;return!1}function T(a){for(var d=!1;!d;)-1<a.className.indexOf(b.slideClass)&&(d=a),a=a.parentElement;return d}function R(c){a.allowSlideClick&&(c.target?(a.clickedSlide=this,a.clickedSlideIndex=a.slides.indexOf(this)):(a.clickedSlide=T(c.srcElement),a.clickedSlideIndex=a.slides.indexOf(a.clickedSlide)),b.onSlideClick(a))}function S(c){a.clickedSlide=c.target?this:T(c.srcElement);a.clickedSlideIndex=a.slides.indexOf(a.clickedSlide);b.onSlideTouch(a)}
function P(b){if(!a.allowLinks)return b.preventDefault?b.preventDefault():b.returnValue=!1,!1}function Q(a){a.stopPropagation?a.stopPropagation():a.returnValue=!1;return!1}function z(c){b.preventLinks&&(a.allowLinks=!0);if(a.isTouched||b.onlyExternal)return!1;var d;if(d=b.noSwiping)if(d=c.target||c.srcElement){d=c.target||c.srcElement;var e=!1;do-1<d.className.indexOf(b.noSwipingClass)&&(e=!0),d=d.parentElement;while(!e&&d.parentElement&&-1==d.className.indexOf(b.wrapperClass));!e&&(-1<d.className.indexOf(b.wrapperClass)&&
-1<d.className.indexOf(b.noSwipingClass))&&(e=!0);d=e}if(d)return!1;G=!1;a.isTouched=!0;u="touchstart"==c.type;if(!u||1==c.targetTouches.length){b.loop&&a.fixLoop();a.callPlugins("onTouchStartBegin");u||(c.preventDefault?c.preventDefault():c.returnValue=!1);d=u?c.targetTouches[0].pageX:c.pageX||c.clientX;c=u?c.targetTouches[0].pageY:c.pageY||c.clientY;a.touches.startX=a.touches.currentX=d;a.touches.startY=a.touches.currentY=c;a.touches.start=a.touches.current=k?d:c;a.setWrapperTransition(0);a.positions.start=
a.positions.current=k?a.getWrapperTranslate("x"):a.getWrapperTranslate("y");k?a.setWrapperTranslate(a.positions.start,0,0):a.setWrapperTranslate(0,a.positions.start,0);a.times.start=(new Date).getTime();x=void 0;0<b.moveStartThreshold&&(M=!1);if(b.onTouchStart)b.onTouchStart(a);a.callPlugins("onTouchStartEnd")}}function A(c){if(a.isTouched&&!b.onlyExternal&&(!u||"mousemove"!=c.type)){var d=u?c.targetTouches[0].pageX:c.pageX||c.clientX,e=u?c.targetTouches[0].pageY:c.pageY||c.clientY;"undefined"===
typeof x&&k&&(x=!!(x||Math.abs(e-a.touches.startY)>Math.abs(d-a.touches.startX)));"undefined"!==typeof x||k||(x=!!(x||Math.abs(e-a.touches.startY)<Math.abs(d-a.touches.startX)));if(x)a.isTouched=!1;else if(c.assignedToSwiper)a.isTouched=!1;else if(c.assignedToSwiper=!0,a.isMoved=!0,b.preventLinks&&(a.allowLinks=!1),b.onSlideClick&&(a.allowSlideClick=!1),b.autoplay&&a.stopAutoplay(),!u||1==c.touches.length){a.callPlugins("onTouchMoveStart");c.preventDefault?c.preventDefault():c.returnValue=!1;a.touches.current=
k?d:e;a.positions.current=(a.touches.current-a.touches.start)*b.touchRatio+a.positions.start;if(0<a.positions.current&&b.onResistanceBefore)b.onResistanceBefore(a,a.positions.current);if(a.positions.current<-h()&&b.onResistanceAfter)b.onResistanceAfter(a,Math.abs(a.positions.current+h()));b.resistance&&"100%"!=b.resistance&&(0<a.positions.current&&(c=1-a.positions.current/l/2,a.positions.current=0.5>c?l/2:a.positions.current*c),a.positions.current<-h()&&(d=(a.touches.current-a.touches.start)*b.touchRatio+
(h()+a.positions.start),c=(l+d)/l,d=a.positions.current-d*(1-c)/2,e=-h()-l/2,a.positions.current=d<e||0>=c?e:d));b.resistance&&"100%"==b.resistance&&(0<a.positions.current&&(!b.freeMode||b.freeModeFluid)&&(a.positions.current=0),a.positions.current<-h()&&(!b.freeMode||b.freeModeFluid)&&(a.positions.current=-h()));if(b.followFinger){b.moveStartThreshold?Math.abs(a.touches.current-a.touches.start)>b.moveStartThreshold||M?(M=!0,k?a.setWrapperTranslate(a.positions.current,0,0):a.setWrapperTranslate(0,
a.positions.current,0)):a.positions.current=a.positions.start:k?a.setWrapperTranslate(a.positions.current,0,0):a.setWrapperTranslate(0,a.positions.current,0);(b.freeMode||b.watchActiveIndex)&&a.updateActiveSlide(a.positions.current);b.grabCursor&&(a.container.style.cursor="move",a.container.style.cursor="grabbing",a.container.style.cursor="-moz-grabbin",a.container.style.cursor="-webkit-grabbing");D||(D=a.touches.current);H||(H=(new Date).getTime());a.velocity=(a.touches.current-D)/((new Date).getTime()-
H)/2;2>Math.abs(a.touches.current-D)&&(a.velocity=0);D=a.touches.current;H=(new Date).getTime();a.callPlugins("onTouchMoveEnd");if(b.onTouchMove)b.onTouchMove(a);return!1}}}}function B(c){x&&a.swipeReset();if(!b.onlyExternal&&a.isTouched){a.isTouched=!1;b.grabCursor&&(a.container.style.cursor="move",a.container.style.cursor="grab",a.container.style.cursor="-moz-grab",a.container.style.cursor="-webkit-grab");a.positions.current||0===a.positions.current||(a.positions.current=a.positions.start);b.followFinger&&
(k?a.setWrapperTranslate(a.positions.current,0,0):a.setWrapperTranslate(0,a.positions.current,0));a.times.end=(new Date).getTime();a.touches.diff=a.touches.current-a.touches.start;a.touches.abs=Math.abs(a.touches.diff);a.positions.diff=a.positions.current-a.positions.start;a.positions.abs=Math.abs(a.positions.diff);var d=a.positions.diff,e=a.positions.abs;c=a.times.end-a.times.start;5>e&&(300>c&&!1==a.allowLinks)&&(b.freeMode||0==e||a.swipeReset(),b.preventLinks&&(a.allowLinks=!0),b.onSlideClick&&
(a.allowSlideClick=!0));setTimeout(function(){b.preventLinks&&(a.allowLinks=!0);b.onSlideClick&&(a.allowSlideClick=!0)},100);if(a.isMoved){a.isMoved=!1;var f=h();if(0<a.positions.current)a.swipeReset();else if(a.positions.current<-f)a.swipeReset();else if(b.freeMode){if(b.freeModeFluid){var e=1E3*b.momentumRatio,d=a.positions.current+a.velocity*e,g=!1,F,m=20*Math.abs(a.velocity)*b.momentumBounceRatio;d<-f&&(b.momentumBounce&&a.support.transitions?(d+f<-m&&(d=-f-m),F=-f,G=g=!0):d=-f);0<d&&(b.momentumBounce&&
a.support.transitions?(d>m&&(d=m),F=0,G=g=!0):d=0);0!=a.velocity&&(e=Math.abs((d-a.positions.current)/a.velocity));k?a.setWrapperTranslate(d,0,0):a.setWrapperTranslate(0,d,0);a.setWrapperTransition(e);b.momentumBounce&&g&&a.wrapperTransitionEnd(function(){if(G){if(b.onMomentumBounce)b.onMomentumBounce(a);k?a.setWrapperTranslate(F,0,0):a.setWrapperTranslate(0,F,0);a.setWrapperTransition(300)}});a.updateActiveSlide(d)}(!b.freeModeFluid||300<=c)&&a.updateActiveSlide(a.positions.current)}else{E=0>d?"toNext":
"toPrev";"toNext"==E&&300>=c&&(30>e||!b.shortSwipes?a.swipeReset():a.swipeNext(!0));"toPrev"==E&&300>=c&&(30>e||!b.shortSwipes?a.swipeReset():a.swipePrev(!0));f=0;if("auto"==b.slidesPerView){for(var d=Math.abs(k?a.getWrapperTranslate("x"):a.getWrapperTranslate("y")),q=g=0;q<a.slides.length;q++)if(m=k?a.slides[q].getWidth(!0):a.slides[q].getHeight(!0),g+=m,g>d){f=m;break}f>l&&(f=l)}else f=p*b.slidesPerView;"toNext"==E&&300<c&&(e>=0.5*f?a.swipeNext(!0):a.swipeReset());"toPrev"==E&&300<c&&(e>=0.5*f?
a.swipePrev(!0):a.swipeReset())}if(b.onTouchEnd)b.onTouchEnd(a);a.callPlugins("onTouchEnd")}else{a.isMoved=!1;if(b.onTouchEnd)b.onTouchEnd(a);a.callPlugins("onTouchEnd");a.swipeReset()}}}function I(c,d,e){function f(){g+=m;if(p="toNext"==l?g>c:g<c)k?a.setWrapperTranslate(Math.round(g),0):a.setWrapperTranslate(0,Math.round(g)),a._DOMAnimating=!0,window.setTimeout(function(){f()},1E3/60);else{if(b.onSlideChangeEnd)b.onSlideChangeEnd(a);k?a.setWrapperTranslate(c,0):a.setWrapperTranslate(0,c);a._DOMAnimating=
!1}}if(a.support.transitions||!b.DOMAnimation){k?a.setWrapperTranslate(c,0,0):a.setWrapperTranslate(0,c,0);var h="to"==d&&0<=e.speed?e.speed:b.speed;a.setWrapperTransition(h)}else{var g=k?a.getWrapperTranslate("x"):a.getWrapperTranslate("y"),h="to"==d&&0<=e.speed?e.speed:b.speed,m=Math.ceil((c-g)/h*(1E3/60)),l=g>c?"toNext":"toPrev",p="toNext"==l?g>c:g<c;if(a._DOMAnimating)return;f()}a.updateActiveSlide(c);if(b.onSlideNext&&"next"==d)b.onSlideNext(a,c);if(b.onSlidePrev&&"prev"==d)b.onSlidePrev(a,c);
if(b.onSlideReset&&"reset"==d)b.onSlideReset(a,c);("next"==d||"prev"==d||"to"==d&&!0==e.runCallbacks)&&W()}function W(){a.callPlugins("onSlideChangeStart");if(b.onSlideChangeStart)if(b.queueStartCallbacks&&a.support.transitions){if(a._queueStartCallbacks)return;a._queueStartCallbacks=!0;b.onSlideChangeStart(a);a.wrapperTransitionEnd(function(){a._queueStartCallbacks=!1})}else b.onSlideChangeStart(a);b.onSlideChangeEnd&&(a.support.transitions?b.queueEndCallbacks?a._queueEndCallbacks||(a._queueEndCallbacks=
!0,a.wrapperTransitionEnd(b.onSlideChangeEnd)):a.wrapperTransitionEnd(b.onSlideChangeEnd):b.DOMAnimation||setTimeout(function(){b.onSlideChangeEnd(a)},10))}function U(){for(var b=a.paginationButtons,d=0;d<b.length;d++)a.h.removeEventListener(b[d],"click",V,!1)}function V(b){var d;b=b.target||b.srcElement;for(var e=a.paginationButtons,f=0;f<e.length;f++)b===e[f]&&(d=f);a.swipeTo(d)}if(document.body.__defineGetter__&&HTMLElement){var s=HTMLElement.prototype;s.__defineGetter__&&s.__defineGetter__("outerHTML",
function(){return(new XMLSerializer).serializeToString(this)})}window.getComputedStyle||(window.getComputedStyle=function(a,b){this.el=a;this.getPropertyValue=function(b){var d=/(\-([a-z]){1})/g;"float"===b&&(b="styleFloat");d.test(b)&&(b=b.replace(d,function(a,b,c){return c.toUpperCase()}));return a.currentStyle[b]?a.currentStyle[b]:null};return this});Array.prototype.indexOf||(Array.prototype.indexOf=function(a,b){for(var e=b||0,f=this.length;e<f;e++)if(this[e]===a)return e;return-1});if((document.querySelectorAll||
window.jQuery)&&"undefined"!==typeof f&&(f.nodeType||0!==g(f).length)){var a=this;a.touches={start:0,startX:0,startY:0,current:0,currentX:0,currentY:0,diff:0,abs:0};a.positions={start:0,abs:0,diff:0,current:0};a.times={start:0,end:0};a.id=(new Date).getTime();a.container=f.nodeType?f:g(f)[0];a.isTouched=!1;a.isMoved=!1;a.activeIndex=0;a.activeLoaderIndex=0;a.activeLoopIndex=0;a.previousIndex=null;a.velocity=0;a.snapGrid=[];a.slidesGrid=[];a.imagesToLoad=[];a.imagesLoaded=0;a.wrapperLeft=0;a.wrapperRight=
0;a.wrapperTop=0;a.wrapperBottom=0;var J,p,y,E,x,l,s={mode:"horizontal",touchRatio:1,speed:300,freeMode:!1,freeModeFluid:!1,momentumRatio:1,momentumBounce:!0,momentumBounceRatio:1,slidesPerView:1,slidesPerGroup:1,simulateTouch:!0,followFinger:!0,shortSwipes:!0,moveStartThreshold:!1,autoplay:!1,onlyExternal:!1,createPagination:!0,pagination:!1,paginationElement:"span",paginationClickable:!1,paginationAsRange:!0,resistance:!0,scrollContainer:!1,preventLinks:!0,noSwiping:!1,noSwipingClass:"swiper-no-swiping",
initialSlide:0,keyboardControl:!1,mousewheelControl:!1,mousewheelDebounce:600,useCSS3Transforms:!0,loop:!1,loopAdditionalSlides:0,calculateHeight:!1,updateOnImagesReady:!0,releaseFormElements:!0,watchActiveIndex:!1,visibilityFullFit:!1,offsetPxBefore:0,offsetPxAfter:0,offsetSlidesBefore:0,offsetSlidesAfter:0,centeredSlides:!1,queueStartCallbacks:!1,queueEndCallbacks:!1,autoResize:!0,resizeReInit:!1,DOMAnimation:!0,loader:{slides:[],slidesHTMLType:"inner",surroundGroups:1,logic:"reload",loadAllSlides:!1},
slideElement:"div",slideClass:"swiper-slide",slideActiveClass:"swiper-slide-active",slideVisibleClass:"swiper-slide-visible",wrapperClass:"swiper-wrapper",paginationElementClass:"swiper-pagination-switch",paginationActiveClass:"swiper-active-switch",paginationVisibleClass:"swiper-visible-switch"};b=b||{};for(var q in s)if(q in b&&"object"===typeof b[q])for(var C in s[q])C in b[q]||(b[q][C]=s[q][C]);else q in b||(b[q]=s[q]);a.params=b;b.scrollContainer&&(b.freeMode=!0,b.freeModeFluid=!0);b.loop&&(b.resistance=
"100%");var k="horizontal"===b.mode;a.touchEvents={touchStart:a.support.touch||!b.simulateTouch?"touchstart":a.browser.ie10?"MSPointerDown":"mousedown",touchMove:a.support.touch||!b.simulateTouch?"touchmove":a.browser.ie10?"MSPointerMove":"mousemove",touchEnd:a.support.touch||!b.simulateTouch?"touchend":a.browser.ie10?"MSPointerUp":"mouseup"};for(q=a.container.childNodes.length-1;0<=q;q--)if(a.container.childNodes[q].className)for(C=a.container.childNodes[q].className.split(" "),s=0;s<C.length;s++)C[s]===
b.wrapperClass&&(J=a.container.childNodes[q]);a.wrapper=J;a._extendSwiperSlide=function(c){c.append=function(){b.loop?(c.insertAfter(a.slides.length-a.loopedSlides),a.removeLoopedSlides(),a.calcSlides(),a.createLoop()):a.wrapper.appendChild(c);a.reInit();return c};c.prepend=function(){b.loop?(a.wrapper.insertBefore(c,a.slides[a.loopedSlides]),a.removeLoopedSlides(),a.calcSlides(),a.createLoop()):a.wrapper.insertBefore(c,a.wrapper.firstChild);a.reInit();return c};c.insertAfter=function(d){if("undefined"===
typeof d)return!1;b.loop?(d=a.slides[d+1+a.loopedSlides],a.wrapper.insertBefore(c,d),a.removeLoopedSlides(),a.calcSlides(),a.createLoop()):(d=a.slides[d+1],a.wrapper.insertBefore(c,d));a.reInit();return c};c.clone=function(){return a._extendSwiperSlide(c.cloneNode(!0))};c.remove=function(){a.wrapper.removeChild(c);a.reInit()};c.html=function(a){if("undefined"===typeof a)return c.innerHTML;c.innerHTML=a;return c};c.index=function(){for(var b,e=a.slides.length-1;0<=e;e--)c===a.slides[e]&&(b=e);return b};
c.isActive=function(){return c.index()===a.activeIndex?!0:!1};c.swiperSlideDataStorage||(c.swiperSlideDataStorage={});c.getData=function(a){return c.swiperSlideDataStorage[a]};c.setData=function(a,b){c.swiperSlideDataStorage[a]=b;return c};c.data=function(a,b){return b?(c.setAttribute("data-"+a,b),c):c.getAttribute("data-"+a)};c.getWidth=function(b){return a.h.getWidth(c,b)};c.getHeight=function(b){return a.h.getHeight(c,b)};c.getOffset=function(){return a.h.getOffset(c)};return c};a.calcSlides=function(c){var d=
a.slides?a.slides.length:!1;a.slides=[];a.displaySlides=[];for(var e=0;e<a.wrapper.childNodes.length;e++)if(a.wrapper.childNodes[e].className)for(var f=a.wrapper.childNodes[e].className.split(" "),g=0;g<f.length;g++)f[g]===b.slideClass&&a.slides.push(a.wrapper.childNodes[e]);for(e=a.slides.length-1;0<=e;e--)a._extendSwiperSlide(a.slides[e]);d&&(d!==a.slides.length||c)&&(v(),t(),a.updateActiveSlide(),b.createPagination&&a.params.pagination&&a.createPagination(),a.callPlugins("numberOfSlidesChanged"))};
a.createSlide=function(c,d,e){d=d||a.params.slideClass;e=e||b.slideElement;e=document.createElement(e);e.innerHTML=c||"";e.className=d;return a._extendSwiperSlide(e)};a.appendSlide=function(b,d,e){if(b)return b.nodeType?a._extendSwiperSlide(b).append():a.createSlide(b,d,e).append()};a.prependSlide=function(b,d,e){if(b)return b.nodeType?a._extendSwiperSlide(b).prepend():a.createSlide(b,d,e).prepend()};a.insertSlideAfter=function(b,d,e,f){return"undefined"===typeof b?!1:d.nodeType?a._extendSwiperSlide(d).insertAfter(b):
a.createSlide(d,e,f).insertAfter(b)};a.removeSlide=function(c){if(a.slides[c]){if(b.loop){if(!a.slides[c+a.loopedSlides])return!1;a.slides[c+a.loopedSlides].remove();a.removeLoopedSlides();a.calcSlides();a.createLoop()}else a.slides[c].remove();return!0}return!1};a.removeLastSlide=function(){return 0<a.slides.length?(b.loop?(a.slides[a.slides.length-1-a.loopedSlides].remove(),a.removeLoopedSlides(),a.calcSlides(),a.createLoop()):a.slides[a.slides.length-1].remove(),!0):!1};a.removeAllSlides=function(){for(var b=
a.slides.length-1;0<=b;b--)a.slides[b].remove()};a.getSlide=function(b){return a.slides[b]};a.getLastSlide=function(){return a.slides[a.slides.length-1]};a.getFirstSlide=function(){return a.slides[0]};a.activeSlide=function(){return a.slides[a.activeIndex]};var K=[],L;for(L in a.plugins)b[L]&&(q=a.plugins[L](a,b[L]))&&K.push(q);a.callPlugins=function(a,b){b||(b={});for(var e=0;e<K.length;e++)if(a in K[e])K[e][a](b)};a.browser.ie10&&!b.onlyExternal&&(k?a.wrapper.classList.add("swiper-wp8-horizontal"):
a.wrapper.classList.add("swiper-wp8-vertical"));b.freeMode&&(a.container.className+=" swiper-free-mode");a.initialized=!1;a.init=function(c,d){var e=a.h.getWidth(a.container),f=a.h.getHeight(a.container);if(e!==a.width||f!==a.height||c){a.width=e;a.height=f;l=k?e:f;e=a.wrapper;c&&a.calcSlides(d);if("auto"===b.slidesPerView){var g=0,h=0;0<b.slidesOffset&&(e.style.paddingLeft="",e.style.paddingRight="",e.style.paddingTop="",e.style.paddingBottom="");e.style.width="";e.style.height="";0<b.offsetPxBefore&&
(k?a.wrapperLeft=b.offsetPxBefore:a.wrapperTop=b.offsetPxBefore);0<b.offsetPxAfter&&(k?a.wrapperRight=b.offsetPxAfter:a.wrapperBottom=b.offsetPxAfter);b.centeredSlides&&(k?(a.wrapperLeft=(l-this.slides[0].getWidth(!0))/2,a.wrapperRight=(l-a.slides[a.slides.length-1].getWidth(!0))/2):(a.wrapperTop=(l-a.slides[0].getHeight(!0))/2,a.wrapperBottom=(l-a.slides[a.slides.length-1].getHeight(!0))/2));k?(0<=a.wrapperLeft&&(e.style.paddingLeft=a.wrapperLeft+"px"),0<=a.wrapperRight&&(e.style.paddingRight=a.wrapperRight+
"px")):(0<=a.wrapperTop&&(e.style.paddingTop=a.wrapperTop+"px"),0<=a.wrapperBottom&&(e.style.paddingBottom=a.wrapperBottom+"px"));var m=0,q=0;a.snapGrid=[];a.slidesGrid=[];for(var n=0,r=0;r<a.slides.length;r++){var f=a.slides[r].getWidth(!0),s=a.slides[r].getHeight(!0);b.calculateHeight&&(n=Math.max(n,s));var t=k?f:s;if(b.centeredSlides){var u=r===a.slides.length-1?0:a.slides[r+1].getWidth(!0),w=r===a.slides.length-1?0:a.slides[r+1].getHeight(!0),u=k?u:w;if(t>l){for(w=0;w<=Math.floor(t/(l+a.wrapperLeft));w++)0===
w?a.snapGrid.push(m+a.wrapperLeft):a.snapGrid.push(m+a.wrapperLeft+l*w);a.slidesGrid.push(m+a.wrapperLeft)}else a.snapGrid.push(q),a.slidesGrid.push(q);q+=t/2+u/2}else{if(t>l)for(w=0;w<=Math.floor(t/l);w++)a.snapGrid.push(m+l*w);else a.snapGrid.push(m);a.slidesGrid.push(m)}m+=t;g+=f;h+=s}b.calculateHeight&&(a.height=n);k?(y=g+a.wrapperRight+a.wrapperLeft,e.style.width=g+"px",e.style.height=a.height+"px"):(y=h+a.wrapperTop+a.wrapperBottom,e.style.width=a.width+"px",e.style.height=h+"px")}else if(b.scrollContainer)e.style.width=
"",e.style.height="",n=a.slides[0].getWidth(!0),g=a.slides[0].getHeight(!0),y=k?n:g,e.style.width=n+"px",e.style.height=g+"px",p=k?n:g;else{if(b.calculateHeight){g=n=0;k||(a.container.style.height="");e.style.height="";for(r=0;r<a.slides.length;r++)a.slides[r].style.height="",n=Math.max(a.slides[r].getHeight(!0),n),k||(g+=a.slides[r].getHeight(!0));s=n;a.height=s;k?g=s:(l=s,a.container.style.height=l+"px")}else s=k?a.height:a.height/b.slidesPerView,g=k?a.height:a.slides.length*s;f=k?a.width/b.slidesPerView:
a.width;n=k?a.slides.length*f:a.width;p=k?f:s;0<b.offsetSlidesBefore&&(k?a.wrapperLeft=p*b.offsetSlidesBefore:a.wrapperTop=p*b.offsetSlidesBefore);0<b.offsetSlidesAfter&&(k?a.wrapperRight=p*b.offsetSlidesAfter:a.wrapperBottom=p*b.offsetSlidesAfter);0<b.offsetPxBefore&&(k?a.wrapperLeft=b.offsetPxBefore:a.wrapperTop=b.offsetPxBefore);0<b.offsetPxAfter&&(k?a.wrapperRight=b.offsetPxAfter:a.wrapperBottom=b.offsetPxAfter);b.centeredSlides&&(k?(a.wrapperLeft=(l-p)/2,a.wrapperRight=(l-p)/2):(a.wrapperTop=
(l-p)/2,a.wrapperBottom=(l-p)/2));k?(0<a.wrapperLeft&&(e.style.paddingLeft=a.wrapperLeft+"px"),0<a.wrapperRight&&(e.style.paddingRight=a.wrapperRight+"px")):(0<a.wrapperTop&&(e.style.paddingTop=a.wrapperTop+"px"),0<a.wrapperBottom&&(e.style.paddingBottom=a.wrapperBottom+"px"));y=k?n+a.wrapperRight+a.wrapperLeft:g+a.wrapperTop+a.wrapperBottom;e.style.width=n+"px";e.style.height=g+"px";m=0;a.snapGrid=[];a.slidesGrid=[];for(r=0;r<a.slides.length;r++)a.snapGrid.push(m),a.slidesGrid.push(m),m+=p,a.slides[r].style.width=
f+"px",a.slides[r].style.height=s+"px"}if(a.initialized){if(a.callPlugins("onInit"),b.onFirstInit)b.onInit(a)}else if(a.callPlugins("onFirstInit"),b.onFirstInit)b.onFirstInit(a);a.initialized=!0}};a.reInit=function(b){a.init(!0,b)};a.resizeFix=function(c){a.callPlugins("beforeResizeFix");a.init(b.resizeReInit||c);if(!b.freeMode)b.loop?a.swipeTo(a.activeLoopIndex,0,!1):a.swipeTo(a.activeIndex,0,!1);else if((k?a.getWrapperTranslate("x"):a.getWrapperTranslate("y"))<-h()){c=k?-h():0;var d=k?0:-h();a.setWrapperTransition(0);
a.setWrapperTranslate(c,d,0)}a.callPlugins("afterResizeFix")};a.destroy=function(c){a.browser.ie10?(a.h.removeEventListener(a.wrapper,a.touchEvents.touchStart,z,!1),a.h.removeEventListener(document,a.touchEvents.touchMove,A,!1),a.h.removeEventListener(document,a.touchEvents.touchEnd,B,!1)):(a.support.touch&&(a.h.removeEventListener(a.wrapper,"touchstart",z,!1),a.h.removeEventListener(a.wrapper,"touchmove",A,!1),a.h.removeEventListener(a.wrapper,"touchend",B,!1)),b.simulateTouch&&(a.h.removeEventListener(a.wrapper,
"mousedown",z,!1),a.h.removeEventListener(document,"mousemove",A,!1),a.h.removeEventListener(document,"mouseup",B,!1)));b.autoResize&&a.h.removeEventListener(window,"resize",a.resizeFix,!1);v();b.paginationClickable&&U();b.mousewheelControl&&a._wheelEvent&&a.h.removeEventListener(a.container,a._wheelEvent,N,!1);b.keyboardControl&&a.h.removeEventListener(document,"keydown",O,!1);b.autoplay&&a.stopAutoplay();a.callPlugins("onDestroy");a=null};b.grabCursor&&(a.container.style.cursor="move",a.container.style.cursor=
"grab",a.container.style.cursor="-moz-grab",a.container.style.cursor="-webkit-grab");a.allowSlideClick=!0;a.allowLinks=!0;var u=!1,M,G=!0,D,H;a.swipeNext=function(c){!c&&b.loop&&a.fixLoop();a.callPlugins("onSwipeNext");var d=c=k?a.getWrapperTranslate("x"):a.getWrapperTranslate("y");if("auto"==b.slidesPerView)for(var e=0;e<a.snapGrid.length;e++){if(-c>=a.snapGrid[e]&&-c<a.snapGrid[e+1]){d=-a.snapGrid[e+1];break}}else d=p*b.slidesPerGroup,d=-(Math.floor(Math.abs(c)/Math.floor(d))*d+d);d<-h()&&(d=-h());
if(d==c)return!1;I(d,"next");return!0};a.swipePrev=function(c){!c&&b.loop&&a.fixLoop();!c&&b.autoplay&&a.stopAutoplay();a.callPlugins("onSwipePrev");c=Math.ceil(k?a.getWrapperTranslate("x"):a.getWrapperTranslate("y"));var d;if("auto"==b.slidesPerView){d=0;for(var e=1;e<a.snapGrid.length;e++){if(-c==a.snapGrid[e]){d=-a.snapGrid[e-1];break}if(-c>a.snapGrid[e]&&-c<a.snapGrid[e+1]){d=-a.snapGrid[e];break}}}else d=p*b.slidesPerGroup,d*=-(Math.ceil(-c/d)-1);0<d&&(d=0);if(d==c)return!1;I(d,"prev");return!0};
a.swipeReset=function(){a.callPlugins("onSwipeReset");var c=k?a.getWrapperTranslate("x"):a.getWrapperTranslate("y"),d=p*b.slidesPerGroup;h();if("auto"==b.slidesPerView){for(var e=d=0;e<a.snapGrid.length;e++){if(-c===a.snapGrid[e])return;if(-c>=a.snapGrid[e]&&-c<a.snapGrid[e+1]){d=0<a.positions.diff?-a.snapGrid[e+1]:-a.snapGrid[e];break}}-c>=a.snapGrid[a.snapGrid.length-1]&&(d=-a.snapGrid[a.snapGrid.length-1]);c<=-h()&&(d=-h())}else d=0>c?Math.round(c/d)*d:0;b.scrollContainer&&(d=0>c?c:0);d<-h()&&
(d=-h());b.scrollContainer&&l>p&&(d=0);if(d==c)return!1;I(d,"reset");return!0};a.swipeTo=function(c,d,e){c=parseInt(c,10);a.callPlugins("onSwipeTo",{index:c,speed:d});b.loop&&(c+=a.loopedSlides);var f=k?a.getWrapperTranslate("x"):a.getWrapperTranslate("y");if(!(c>a.slides.length-1||0>c)){var g;g="auto"==b.slidesPerView?-a.slidesGrid[c]:-c*p;g<-h()&&(g=-h());if(g==f)return!1;I(g,"to",{index:c,speed:d,runCallbacks:!1===e?!1:!0});return!0}};a._queueStartCallbacks=!1;a._queueEndCallbacks=!1;a.updateActiveSlide=
function(c){if(a.initialized&&0!=a.slides.length){a.previousIndex=a.activeIndex;0<c&&(c=0);"undefined"==typeof c&&(c=k?a.getWrapperTranslate("x"):a.getWrapperTranslate("y"));if("auto"==b.slidesPerView){if(a.activeIndex=a.slidesGrid.indexOf(-c),0>a.activeIndex){for(var d=0;d<a.slidesGrid.length-1&&!(-c>a.slidesGrid[d]&&-c<a.slidesGrid[d+1]);d++);var e=Math.abs(a.slidesGrid[d]+c),f=Math.abs(a.slidesGrid[d+1]+c);a.activeIndex=e<=f?d:d+1}}else a.activeIndex=b.visibilityFullFit?Math.ceil(-c/p):Math.round(-c/
p);a.activeIndex==a.slides.length&&(a.activeIndex=a.slides.length-1);0>a.activeIndex&&(a.activeIndex=0);if(a.slides[a.activeIndex]){a.calcVisibleSlides(c);e=RegExp("\\s*"+b.slideActiveClass);f=RegExp("\\s*"+b.slideVisibleClass);for(d=0;d<a.slides.length;d++)a.slides[d].className=a.slides[d].className.replace(e,"").replace(f,""),0<=a.visibleSlides.indexOf(a.slides[d])&&(a.slides[d].className+=" "+b.slideVisibleClass);a.slides[a.activeIndex].className+=" "+b.slideActiveClass;b.loop?(d=a.loopedSlides,
a.activeLoopIndex=a.activeIndex-d,a.activeLoopIndex>=a.slides.length-2*d&&(a.activeLoopIndex=a.slides.length-2*d-a.activeLoopIndex),0>a.activeLoopIndex&&(a.activeLoopIndex=a.slides.length-2*d+a.activeLoopIndex)):a.activeLoopIndex=a.activeIndex;b.pagination&&a.updatePagination(c)}}};a.createPagination=function(c){b.paginationClickable&&a.paginationButtons&&U();var d="",e=a.slides.length;b.loop&&(e-=2*a.loopedSlides);for(var f=0;f<e;f++)d+="<"+b.paginationElement+' class="'+b.paginationElementClass+
'"></'+b.paginationElement+">";a.paginationContainer=b.pagination.nodeType?b.pagination:g(b.pagination)[0];a.paginationContainer.innerHTML=d;a.paginationButtons=[];document.querySelectorAll?a.paginationButtons=a.paginationContainer.querySelectorAll("."+b.paginationElementClass):window.jQuery&&(a.paginationButtons=g(a.paginationContainer).find("."+b.paginationElementClass));c||a.updatePagination();a.callPlugins("onCreatePagination");if(b.paginationClickable)for(c=a.paginationButtons,d=0;d<c.length;d++)a.h.addEventListener(c[d],
"click",V,!1)};a.updatePagination=function(c){if(b.pagination&&!(1>a.slides.length)){if(document.querySelectorAll)var d=a.paginationContainer.querySelectorAll("."+b.paginationActiveClass);else window.jQuery&&(d=g(a.paginationContainer).find("."+b.paginationActiveClass));if(d&&(d=a.paginationButtons,0!=d.length)){for(var e=0;e<d.length;e++)d[e].className=b.paginationElementClass;var f=b.loop?a.loopedSlides:0;if(b.paginationAsRange){a.visibleSlides||a.calcVisibleSlides(c);c=[];for(e=0;e<a.visibleSlides.length;e++){var h=
a.slides.indexOf(a.visibleSlides[e])-f;b.loop&&0>h&&(h=a.slides.length-2*a.loopedSlides+h);b.loop&&h>=a.slides.length-2*a.loopedSlides&&(h=a.slides.length-2*a.loopedSlides-h,h=Math.abs(h));c.push(h)}for(e=0;e<c.length;e++)d[c[e]]&&(d[c[e]].className+=" "+b.paginationVisibleClass);b.loop?d[a.activeLoopIndex].className+=" "+b.paginationActiveClass:d[a.activeIndex].className+=" "+b.paginationActiveClass}else b.loop?d[a.activeLoopIndex].className+=" "+b.paginationActiveClass+" "+b.paginationVisibleClass:
d[a.activeIndex].className+=" "+b.paginationActiveClass+" "+b.paginationVisibleClass}}};a.calcVisibleSlides=function(c){var d=[],e=0,f=0,g=0;k&&0<a.wrapperLeft&&(c+=a.wrapperLeft);!k&&0<a.wrapperTop&&(c+=a.wrapperTop);for(var h=0;h<a.slides.length;h++){var e=e+f,f="auto"==b.slidesPerView?k?a.h.getWidth(a.slides[h],!0):a.h.getHeight(a.slides[h],!0):p,g=e+f,m=!1;b.visibilityFullFit?(e>=-c&&g<=-c+l&&(m=!0),e<=-c&&g>=-c+l&&(m=!0)):(g>-c&&g<=-c+l&&(m=!0),e>=-c&&e<-c+l&&(m=!0),e<-c&&g>-c+l&&(m=!0));m&&
d.push(a.slides[h])}0==d.length&&(d=[a.slides[a.activeIndex]]);a.visibleSlides=d};a.autoPlayIntervalId=void 0;a.startAutoplay=function(){if("undefined"!==typeof a.autoPlayIntervalId)return!1;b.autoplay&&!b.loop&&(a.autoPlayIntervalId=setInterval(function(){a.swipeNext(!0)||a.swipeTo(0)},b.autoplay));b.autoplay&&b.loop&&(a.autoPlayIntervalId=setInterval(function(){a.swipeNext()},b.autoplay));a.callPlugins("onAutoplayStart")};a.stopAutoplay=function(){a.autoPlayIntervalId&&clearInterval(a.autoPlayIntervalId);
a.autoPlayIntervalId=void 0;a.callPlugins("onAutoplayStop")};a.loopCreated=!1;a.removeLoopedSlides=function(){if(a.loopCreated)for(var b=0;b<a.slides.length;b++)!0===a.slides[b].getData("looped")&&a.wrapper.removeChild(a.slides[b])};a.createLoop=function(){if(0!=a.slides.length){a.loopedSlides=b.slidesPerView+b.loopAdditionalSlides;for(var c="",d="",e=0;e<a.loopedSlides;e++)c+=a.slides[e].outerHTML;for(e=a.slides.length-a.loopedSlides;e<a.slides.length;e++)d+=a.slides[e].outerHTML;J.innerHTML=d+J.innerHTML+
c;a.loopCreated=!0;a.calcSlides();for(e=0;e<a.slides.length;e++)(e<a.loopedSlides||e>=a.slides.length-a.loopedSlides)&&a.slides[e].setData("looped",!0);a.callPlugins("onCreateLoop")}};a.fixLoop=function(){if(a.activeIndex<a.loopedSlides){var c=a.slides.length-3*a.loopedSlides+a.activeIndex;a.swipeTo(c,0,!1)}else a.activeIndex>a.slides.length-2*b.slidesPerView&&(c=-a.slides.length+a.activeIndex+a.loopedSlides,a.swipeTo(c,0,!1))};a.loadSlides=function(){var c="";a.activeLoaderIndex=0;for(var d=b.loader.slides,
e=b.loader.loadAllSlides?d.length:b.slidesPerView*(1+b.loader.surroundGroups),f=0;f<e;f++)c="outer"==b.loader.slidesHTMLType?c+d[f]:c+("<"+b.slideElement+' class="'+b.slideClass+'" data-swiperindex="'+f+'">'+d[f]+"</"+b.slideElement+">");a.wrapper.innerHTML=c;a.calcSlides(!0);b.loader.loadAllSlides||a.wrapperTransitionEnd(a.reloadSlides,!0)};a.reloadSlides=function(){var c=b.loader.slides,d=parseInt(a.activeSlide().data("swiperindex"),10);if(!(0>d||d>c.length-1)){a.activeLoaderIndex=d;var e=Math.max(0,
d-b.slidesPerView*b.loader.surroundGroups),f=Math.min(d+b.slidesPerView*(1+b.loader.surroundGroups)-1,c.length-1);0<d&&(d=-p*(d-e),k?a.setWrapperTranslate(d,0,0):a.setWrapperTranslate(0,d,0),a.setWrapperTransition(0));if("reload"===b.loader.logic){for(var g=a.wrapper.innerHTML="",d=e;d<=f;d++)g+="outer"==b.loader.slidesHTMLType?c[d]:"<"+b.slideElement+' class="'+b.slideClass+'" data-swiperindex="'+d+'">'+c[d]+"</"+b.slideElement+">";a.wrapper.innerHTML=g}else{for(var g=1E3,h=0,d=0;d<a.slides.length;d++){var l=
a.slides[d].data("swiperindex");l<e||l>f?a.wrapper.removeChild(a.slides[d]):(g=Math.min(l,g),h=Math.max(l,h))}for(d=e;d<=f;d++)d<g&&(e=document.createElement(b.slideElement),e.className=b.slideClass,e.setAttribute("data-swiperindex",d),e.innerHTML=c[d],a.wrapper.insertBefore(e,a.wrapper.firstChild)),d>h&&(e=document.createElement(b.slideElement),e.className=b.slideClass,e.setAttribute("data-swiperindex",d),e.innerHTML=c[d],a.wrapper.appendChild(e))}a.reInit(!0)}};a.calcSlides();0<b.loader.slides.length&&
0==a.slides.length&&a.loadSlides();b.loop&&a.createLoop();a.init();n();b.pagination&&b.createPagination&&a.createPagination(!0);b.loop||0<b.initialSlide?a.swipeTo(b.initialSlide,0,!1):a.updateActiveSlide(0);b.autoplay&&a.startAutoplay()}};
Swiper.prototype={plugins:{},wrapperTransitionEnd:function(f,b){function g(){f(h);h.params.queueEndCallbacks&&(h._queueEndCallbacks=!1);if(!b)for(var v=0;v<t.length;v++)h.h.removeEventListener(n,t[v],g,!1)}var h=this,n=h.wrapper,t=["webkitTransitionEnd","transitionend","oTransitionEnd","MSTransitionEnd","msTransitionEnd"];if(f)for(var v=0;v<t.length;v++)h.h.addEventListener(n,t[v],g,!1)},getWrapperTranslate:function(f){var b=this.wrapper,g,h,n=window.WebKitCSSMatrix?new WebKitCSSMatrix(window.getComputedStyle(b,
null).webkitTransform):window.getComputedStyle(b,null).MozTransform||window.getComputedStyle(b,null).OTransform||window.getComputedStyle(b,null).MsTransform||window.getComputedStyle(b,null).msTransform||window.getComputedStyle(b,null).transform||window.getComputedStyle(b,null).getPropertyValue("transform").replace("translate(","matrix(1, 0, 0, 1,");g=n.toString().split(",");this.params.useCSS3Transforms?("x"==f&&(h=16==g.length?parseFloat(g[12]):window.WebKitCSSMatrix?n.m41:parseFloat(g[4])),"y"==
f&&(h=16==g.length?parseFloat(g[13]):window.WebKitCSSMatrix?n.m42:parseFloat(g[5]))):("x"==f&&(h=parseFloat(b.style.left,10)||0),"y"==f&&(h=parseFloat(b.style.top,10)||0));return h||0},setWrapperTranslate:function(f,b,g){var h=this.wrapper.style;f=f||0;b=b||0;g=g||0;this.params.useCSS3Transforms?this.support.transforms3d?h.webkitTransform=h.MsTransform=h.msTransform=h.MozTransform=h.OTransform=h.transform="translate3d("+f+"px, "+b+"px, "+g+"px)":(h.webkitTransform=h.MsTransform=h.msTransform=h.MozTransform=
h.OTransform=h.transform="translate("+f+"px, "+b+"px)",this.support.transforms||(h.left=f+"px",h.top=b+"px")):(h.left=f+"px",h.top=b+"px");this.callPlugins("onSetWrapperTransform",{x:f,y:b,z:g})},setWrapperTransition:function(f){var b=this.wrapper.style;b.webkitTransitionDuration=b.MsTransitionDuration=b.msTransitionDuration=b.MozTransitionDuration=b.OTransitionDuration=b.transitionDuration=f/1E3+"s";this.callPlugins("onSetWrapperTransition",{duration:f})},h:{getWidth:function(f,b){var g=window.getComputedStyle(f,
null).getPropertyValue("width"),h=parseFloat(g);if(isNaN(h)||0<g.indexOf("%"))h=f.offsetWidth-parseFloat(window.getComputedStyle(f,null).getPropertyValue("padding-left"))-parseFloat(window.getComputedStyle(f,null).getPropertyValue("padding-right"));b&&(h+=parseFloat(window.getComputedStyle(f,null).getPropertyValue("padding-left"))+parseFloat(window.getComputedStyle(f,null).getPropertyValue("padding-right")));return h},getHeight:function(f,b){if(b)return f.offsetHeight;var g=window.getComputedStyle(f,
null).getPropertyValue("height"),h=parseFloat(g);if(isNaN(h)||0<g.indexOf("%"))h=f.offsetHeight-parseFloat(window.getComputedStyle(f,null).getPropertyValue("padding-top"))-parseFloat(window.getComputedStyle(f,null).getPropertyValue("padding-bottom"));b&&(h+=parseFloat(window.getComputedStyle(f,null).getPropertyValue("padding-top"))+parseFloat(window.getComputedStyle(f,null).getPropertyValue("padding-bottom")));return h},getOffset:function(f){var b=f.getBoundingClientRect(),g=document.body,h=f.clientTop||
g.clientTop||0,g=f.clientLeft||g.clientLeft||0,n=window.pageYOffset||f.scrollTop;f=window.pageXOffset||f.scrollLeft;document.documentElement&&!window.pageYOffset&&(n=document.documentElement.scrollTop,f=document.documentElement.scrollLeft);return{top:b.top+n-h,left:b.left+f-g}},windowWidth:function(){if(window.innerWidth)return window.innerWidth;if(document.documentElement&&document.documentElement.clientWidth)return document.documentElement.clientWidth},windowHeight:function(){if(window.innerHeight)return window.innerHeight;
if(document.documentElement&&document.documentElement.clientHeight)return document.documentElement.clientHeight},windowScroll:function(){if("undefined"!=typeof pageYOffset)return{left:window.pageXOffset,top:window.pageYOffset};if(document.documentElement)return{left:document.documentElement.scrollLeft,top:document.documentElement.scrollTop}},addEventListener:function(f,b,g,h){f.addEventListener?f.addEventListener(b,g,h):f.attachEvent&&f.attachEvent("on"+b,g)},removeEventListener:function(f,b,g,h){f.removeEventListener?
f.removeEventListener(b,g,h):f.detachEvent&&f.detachEvent("on"+b,g)}},setTransform:function(f,b){var g=f.style;g.webkitTransform=g.MsTransform=g.msTransform=g.MozTransform=g.OTransform=g.transform=b},setTranslate:function(f,b){var g=f.style,h=b.x||0,n=b.y||0,t=b.z||0;g.webkitTransform=g.MsTransform=g.msTransform=g.MozTransform=g.OTransform=g.transform=this.support.transforms3d?"translate3d("+h+"px,"+n+"px,"+t+"px)":"translate("+h+"px,"+n+"px)";this.support.transforms||(g.left=h+"px",g.top=n+"px")},
setTransition:function(f,b){var g=f.style;g.webkitTransitionDuration=g.MsTransitionDuration=g.msTransitionDuration=g.MozTransitionDuration=g.OTransitionDuration=g.transitionDuration=b+"ms"},support:{touch:window.Modernizr&&!0===Modernizr.touch||function(){return!!("ontouchstart"in window||window.DocumentTouch&&document instanceof DocumentTouch)}(),transforms3d:window.Modernizr&&!0===Modernizr.csstransforms3d||function(){var f=document.createElement("div");return"webkitPerspective"in f.style||"MozPerspective"in
f.style||"OPerspective"in f.style||"MsPerspective"in f.style||"perspective"in f.style}(),transforms:window.Modernizr&&!0===Modernizr.csstransforms||function(){var f=document.createElement("div").style;return"transform"in f||"WebkitTransform"in f||"MozTransform"in f||"msTransform"in f||"MsTransform"in f||"OTransform"in f}(),transitions:window.Modernizr&&!0===Modernizr.csstransitions||function(){var f=document.createElement("div").style;return"transition"in f||"WebkitTransition"in f||"MozTransition"in
f||"msTransition"in f||"MsTransition"in f||"OTransition"in f}()},browser:{ie8:function(){var f=-1;"Microsoft Internet Explorer"==navigator.appName&&null!=/MSIE ([0-9]{1,}[.0-9]{0,})/.exec(navigator.userAgent)&&(f=parseFloat(RegExp.$1));return-1!=f&&9>f}(),ie10:window.navigator.msPointerEnabled}};(window.jQuery||window.Zepto)&&function(f){f.fn.swiper=function(b){b=new Swiper(f(this)[0],b);f(this).data("swiper",b);return b}}(window.jQuery||window.Zepto);
"undefined"!==typeof module&&(module.exports=Swiper);

var mySwiper;
var TagId = 0;
var CrystalCount = 0;
var CrystalPanelItemCount = 0;
var cnCenterInit = 0;
var isOpen = 0;


$(window).on("resize",function(){

	if (isOpen ==1){

	}	
	else{
		var WindowHeigth = window.innerHeight;
	 	WindowHeigth = (WindowHeigth * -1)-18;
		$("#cnNotificationCenter").animate({
			top: WindowHeigth,
		},0)
	}
	
	ReziseCrystalNotificiation();
	ResizeCrystalPanel();


});




$(document).ready(function(){

	// Prepare the space for the notification center
  	var WindowHeigth = window.innerHeight;
 	WindowHeigth = (WindowHeigth * -1)-18;
  	// NotificationCenter.css("top",WindowHeigth - 15);

	var NotificationCenter ='<div id="cnNotificationCenter" style="top:'+ WindowHeigth +'px"><div align="center" class="cnFull"><div class="cnEmptySpace">'+ cnEmptyMessage +'</div><div id="cnNotificationBody"><div class="swiper-containerCrystalNotification"><div class="swiper-wrapper"></div> </div> <div class="cnBottomBar">__________</div></div></div>';
	$("body").prepend(NotificationCenter);

	mySwiper = new Swiper('.swiper-containerCrystalNotification',{
	    mode:'vertical',
	    loop: false,
	    freeMode: true,
	    freeModeFluid:true,
	    slidesPerView: 'auto',
	    mousewheelControl:true,
	  });  

	TagId = 0;


	$(".cnBottomBar").bind("click",function(){
		CloseNotificationCenter();
	});


	$('body').keyup(function(e){
        if (e.keyCode == 27){
           if(isOpen == 1){
           	isOpen = 0;
           		var WindowHeigth = window.innerHeight;
			 	WindowHeigth = (WindowHeigth * -1)-18;

			 	$("#cnNotificationCenter").animate({
					top: WindowHeigth,
				},550);
           }
        }
    });   

});


$.CrystalNotification = function(settings,callback){

	CloseNotificationCenter();

	CrystalCount +=1;

	settings = $.extend({
        	title: "Information",
        	image: undefined,
        	content: "",
        	sound: true,
        	panelbutton: true,
        	closebutton: true,
        	timeout: undefined,
        	addtopanel: true,
        }, settings);

	var ID = "#CrystalNotification"+CrystalCount;


	$(".cnLastOneContainer").slideUp(300,function(){
		$(".cnLastOneContainer:not("+ ID +")").remove();
	});

	var CloseButtonId = "cnCloseButton" + CrystalCount;

	var content ="";
		content += '<div class="cnLastOneContainer" id="CrystalNotification'+ CrystalCount +'">';

		if(settings.closebutton){
			content += '<div class="cnNotiClose" id="cnCloseButton'+ CrystalCount +'" crystalnumber="'+ CrystalCount +'">';
			content += 'X';
			content += '</div>';	
		}

		content += '<div align="center">';
		content += '<div class="cnContainer">';
		content += '<div class="cnNotiImageContainer">';

		if(settings.image != undefined)
			content += '<img src="'+ settings.image +'" class="cnNotiImg">';

		content += '</div>';
		content += '<div class="cnNotiContentContainer animated fadeIn">';
		content += '<span class="cnNotiTitulo">';
		content += settings.title;
		content += '</span>';
		content += settings.content;
		content += '</div>';

		if(settings.panelbutton){

			content += '<div class="cnParteBaja">';
			content += '__________';
			content += '</div>';
		}


		content += '</div>	';
		content += '</div>';
		content += '</div>';

	$("body").prepend(content);
	$(ID).slideDown(300);
	ReziseCrystalNotificiation();

// Sound
if(settings.sound===true)
    {
        if(isIE8orlower() == 0)
        {
            var audioElement = document.createElement('audio');

            if (navigator.userAgent.match('Firefox/'))
                audioElement.setAttribute('src', 'static/sound/crystal.ogg');
            else
                audioElement.setAttribute('src', 'static/sound/crystal.mp3');

            
            $.get();
            audioElement.addEventListener("load", function() {
            audioElement.play();
            }, true);


            audioElement.pause();
            audioElement.play();
        }
    }




// Timeout close
	if(settings.timeout !=undefined){
		setTimeout(function(){
			if (typeof callback == "function") 
            {   
                if(callback) callback();
            }

			$(ID).slideUp(300,function(){
				$(ID).remove();
			});
		},settings.timeout);
	}

	var call = undefined;
	if (typeof callback == "function") 
    {   
        call = callback;
    }

	if(settings.addtopanel){
		$.CrystalNotificationPanel({
			title: settings.title,
			image: settings.image,
			content: settings.content,
		},call);
	}	

	


	if(settings.closebutton){
		$("#" + CloseButtonId).bind("click",function(){
			var CrystalNumber = $(this).attr("crystalnumber");
			var ID = "#CrystalNotification" + CrystalNumber;

			$(ID).slideUp(300);
			if (typeof callback == "function") 
            {   
                if(callback) callback(true);
            }
		});

	}

}


// Crystal Notification Panel
$.CrystalNotificationPanel = function(settings,callback){

	if(settings == undefined){
		return;
	}

	CrystalPanelItemCount +=1;

	$(".cnEmptySpace").fadeOut(200);

	settings = $.extend({
        	title: "Information",
        	image: undefined,
        	content: "",
        }, settings);


	var SlideContent  = "";
	var TitleExist = false;


	$(".cnTitleArray").each(function(idx){

		var cnTitle = $(this).html();

		if(settings.title == cnTitle){
			//Title exist.
			TitleExist =true;
			TagId = $(this).next().attr("tagid");
		}

	});


	if(!TitleExist){

		TagId +=1;
		SlideContent  = "";
		SlideContent += '<div class="cnItemDescripcion animated fadeIn fast" ID="CrystalPanelItem'+ CrystalPanelItemCount +'" tagid="'+ TagId +'">';
		SlideContent += '<span class="cnItemTitleDescription">'+ settings.title +'</span>';
		SlideContent += settings.content;
		SlideContent += '</div>';

		var newSlide = mySwiper.createSlide(SlideContent);
		newSlide.prepend();


		SlideContent  = "";
		SlideContent += '<div class="cnItem animated fadeIn fast" tagid="'+ TagId +'">';
		SlideContent += '<div class="cnItemImageContainer">';

		if(settings.image != undefined)
			SlideContent += '<img src="'+ settings.image +'" class="cnItemImage">';

		SlideContent += '</div>';
		SlideContent += '<div class="cnItemTitleContainer">';
		SlideContent += '<span class="cnTitleArray">'+ settings.title +'</span>';
		SlideContent += '<span class="cnTitleClose" tagid="'+ TagId +'">X</span>'
		SlideContent += '</div>';
		SlideContent += '</div>';

		var newSlide = mySwiper.createSlide(SlideContent);
		newSlide.prepend();


	}
	else
	{
		// If Exists, We need to insert the slide under the corresponding group

		for(i=0; i< mySwiper.slides.length; i++){

			var InThisGroup = false;
			var myDiv =  $("<div>" + mySwiper.slides[i].html() + "</div>");

			if(myDiv.find(".cnItem").length >0){

				var InternalTitle = myDiv.find(".cnTitleArray").html();
				if(InternalTitle == settings.title){

					var TagContainer = myDiv.find(".cnItem");//.attr("tagid");
					
					SlideContent  = "";
					SlideContent += '<div class="cnItemDescripcion animated fadeIn fast" ID="CrystalPanelItem'+ CrystalPanelItemCount +'" tagid="'+ TagId +'">';
					SlideContent += '<span class="cnItemTitleDescription">'+ settings.title +'</span>';
					SlideContent += settings.content;
					SlideContent += '</div>';

					
					var newSlide = mySwiper.createSlide(SlideContent);
					mySwiper.insertSlideAfter(i, newSlide);

					i = mySwiper.slides.length;
				}


			}
				

		}

	}


	$("#CrystalPanelItem"+ CrystalPanelItemCount).bind("click",function(){

		if (typeof callback == "function") 
	    {   
	        if(callback) callback();
	    }

	})
	

	ResizeCrystalPanel();
	mySwiper.reInit();

}

// Show Notification Panel
function ShowCrystalNotificationPanel(){


	isOpen = 1;
	CloseAllCrystalsInternal();

	var WindowHeigth = window.innerHeight;
 	WindowHeigth = (WindowHeigth * -1)-18;
 	WindowHeigth = WindowHeigth*-1;

 	

	$("#cnNotificationCenter").animate({
		top: 0,
	},750);

}


// Close all Crystal Notifications
function CloseAllCrystalsInternal(){

	$(".cnLastOneContainer").slideUp(400,function(){
		$(this).remove();
	});

}

// .CrystalNotificationPanel
function CloseNotificationCenter(){

	isOpen = 0;

	var WindowHeigth = window.innerHeight;
 	WindowHeigth = (WindowHeigth * -1)-18;
	$("#cnNotificationCenter").animate({
		top: WindowHeigth,
	})
}

// Remove all the items inside the section of notification panel
$(document).on("click",".cnTitleClose",function(){


	var TagID = $(this).attr("tagid");
	var SlidesCount =  mySwiper.slides.length-1;

	
	var CurrentTitle = "";

	for (var i = SlidesCount; i >= 0; i--) {

		var InThisGroup = false;
		var myDiv =  $("<div>" + mySwiper.slides[i].html() + "</div>");


		var CurrentTag = myDiv.find(".cnItemDescripcion").attr("tagid");
		

		// console.log("Tag a borrar: " + TagID + ", Tag del slide: " + CurrentTag + ", del indice: "+ i);

		// if(TagID == CurrentTag){
		// 	mySwiper.removeSlide(i);
		// }

		// Esto elimina la cabecera
		CurrentTag = myDiv.find(".cnItem").attr("tagid");
		if(CurrentTag == TagID){
			CurrentTitle = myDiv.find(".cnTitleArray").text();
			mySwiper.removeSlide(i);
		}
	};

	// Titles are now deleted, continue with the childs
	SlidesCount =  mySwiper.slides.length-1;

	for(i=SlidesCount; i >=0; i--){

		var InThisGroup = false;
		var myDiv =  $("<div>" + mySwiper.slides[i].html() + "</div>");


		var CurrentTag = myDiv.find(".cnItemDescripcion").attr("tagid");
		var CurrentTitle = "";

		
		if(TagID == CurrentTag){
			mySwiper.removeSlide(i);
		}
	}



	mySwiper.swipeTo(0);
	mySwiper.reInit();

	if(mySwiper.slides.length == 0){
		$(".cnEmptySpace").fadeIn(300);
	}



});

// Show Notification Panel on click
$(document).on("click",".cnParteBaja",function(){

	ShowCrystalNotificationPanel();
})



// Nuke all the slides in the notification panel
$.CrystalNuke = function(){
	mySwiper.removeAllSlides();
	mySwiper.reInit();
	$(".cnEmptySpace").fadeIn(300);
}



function getInternetExplorerVersion() {
    var rv = -1; // Return value assumes failure.
    if (navigator.appName == 'Microsoft Internet Explorer') {
        var ua = navigator.userAgent;
        var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) != null)
            rv = parseFloat(RegExp.$1);
    }
    return rv;
}
function checkVersion() {
    var msg = "You're not using Windows Internet Explorer.";
    var ver = getInternetExplorerVersion();
    if (ver > -1) {
        if (ver >= 8.0)
            msg = "You're using a recent copy of Windows Internet Explorer."
        else
            msg = "You should upgrade your copy of Windows Internet Explorer.";
    }
    alert(msg);
}

function isIE8orlower() {
    var msg = "0";
    var ver = getInternetExplorerVersion();
    if (ver > -1) {
        if (ver >= 9.0)
            msg = 0
        else
            msg = 1;
    }
    return msg;
}


$.ShowNotificationPanel = function(){
	isOpen = 1;
	CloseAllCrystalsInternal();
	ResizeCrystalPanel();
}


function ReziseCrystalNotificiation(){
	var windowWidth = window.innerWidth;
	if(windowWidth <= 546){


		//Screen Ajust
		$(".cnContainer").css("width","100%");
		$(".cnNotiImageContainer").css("width","40px");

		var Diferencia = windowWidth - 43;
		var ContentSize = windowWidth - 100;

		$(".cnNotiContentContainer").css("width", ContentSize + "px");
		$(".cnNotiContentContainer").css("font-size","12px");

		$(".cnNotiTitulo").css("font-size","12px");

	}
	else{

		$(".cnNotiTitulo").css("font-size","16px");
		$(".cnNotiContentContainer").css("font-size","14px");
		$(".cnContainer").css("width","500px");
		$(".cnNotiContentContainer").css("width","440px");
		$(".cnNotiImageContainer").css("width","55px");
	}
}



function ResizeCrystalPanel(){

	var windowWidth = window.innerWidth;

	var EmtySpace =  $(".cnEmptySpace").width();
	$(".cnEmptySpace").css("left", ((windowWidth- EmtySpace) /2)+"px");

	if(windowWidth <= 506){
		var ContentSize = windowWidth - 60;
		var CloseController = (windowWidth-100) /2;

		$(".cnBottomBar").css({
			"position":"absolute",
			"left": CloseController + "px",
		});


		
		$(".cnItemDescripcion").css("font-size","12px");



		$(".cnNotiContentContainer").css("font-size","12px");
		
		$(".swiper-containerCrystalNotification").css("width","100%");
		$(".cnItemTitleContainer").css("width", ContentSize + "px");
		$(".cnItemDescripcion").css("width", ContentSize + "px");

		

	}
	else
	{
		$(".cnBottomBar").css({
			"position":"relative",
			"left":"0px",
		});


		$(".cnItemDescripcion").css("font-size","14px");

		$(".cnNotiContentContainer").css("font-size","14px");
		$(".swiper-containerCrystalNotification").css("width","500px");
		$(".cnItemTitleContainer").css("width", "440px");
		$(".cnItemDescripcion").css("width", "440px");

		$(".cnEmptySpace").css("left","40%");
	}


}



})(jQuery);



// Show the notification Panel
function ShowCrystalNotificationPanel(){

	$.ShowNotificationPanel();

var WindowHeigth = window.innerHeight;
 	WindowHeigth = (WindowHeigth * -1)-18;
 	WindowHeigth = WindowHeigth*-1;

	$("#cnNotificationCenter").animate({
		top: 0,
	},750);
}


function CloseCrystalNotificationPanel(){

	isOpen = 0;

	var WindowHeigth = window.innerHeight;
 	WindowHeigth = (WindowHeigth * -1)-18;

 	$("#cnNotificationCenter").animate({
		top: WindowHeigth,
	},550);
}

// This function close all the small notifications
function CloseAllCrystals(){
	

	$(".cnLastOneContainer").slideUp(300,function(){
		$(this).remove();
	});

}

// Remove all de Notification in the notification center
function NukeAllTheNotifications(){
	$.CrystalNuke();
}



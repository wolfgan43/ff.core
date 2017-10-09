jQuery(document).ready(function() {
	jQuery("img").addClass("glossy iradius16");
});
/*
* Initialisation class "glossy"
* vary the radius by adding iradius followed by the desired radius in percent of half of the smaller picture dimension:
  Corner radius class "iradius25" - min=20 max=50 default=25
* vary the shadow by adding noshadow:
  Noshadow class "noshadow" - default=false
* vary the background by adding ibgcolor followed by the desired color as hex:
  Background color class "ibgcolor" - min=000000 max=ffffff default=0
* vary the background by adding igradient followed by the desired color as hex:
  Gradient color class "igradient" - min=000000 max=ffffff default=0
* vary the color gradient direction by adding horizontal:
  Horizontal gradient class "horizontal" - default=false
*/
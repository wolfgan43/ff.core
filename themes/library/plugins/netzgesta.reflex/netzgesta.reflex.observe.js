jQuery(document).ready(function() {
	jQuery("img").addClass("reflex iheight30");
});

/*
* Initialisation class "reflex"
* vary the reflection by adding idistance followed by the desired height in pixel:
  Reflection distance class "idistance" - min=0 max=100 default=0
* vary the reflection by adding iheight followed by the desired height in percent:
  Reflection height class "iheight" - min=10 max=100 default=33
* vary the reflection by adding iopacity followed by the desired opacity in percent:
  Reflection opacity class "iopacity" - min=0 max=100 default=33
* vary the border by adding iborder followed by the desired wide in pixel:
  Border wide class "iborder" - min=0 max=100 default=0
* vary the border by adding icolor followed by the desired color as hex:
  Border color class "icolor" - min=000000 max=ffffff default=f0f4ff
* vary the 3D Z-Tilt direction by adding one of the following:
  Z-Tilt direction class "itiltright" or "itiltnone" or "itiltleft"
  by default "reflex.js" cycles through [right|none|left].
*/
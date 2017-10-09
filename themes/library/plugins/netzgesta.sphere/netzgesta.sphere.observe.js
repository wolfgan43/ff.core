jQuery(document).ready(function() {
	jQuery("img").addClass("sphere");
});

/*
* Initialisation class "sphere"
* define the sphere size by adding isize followed by the desired size in pixel:
  Sphere size class "isize" - min=32 max=n default=auto
* vary the image by adding izoom followed by the desired zoom in percent:
  Image zoom class "izoom" - min=100 max=200 default=100
* vary the image by adding ishift followed by the desired shift in percent:
  Image shift class "ishift" - min=1 max=100 default=50
* vary the image by adding ialpha followed by the desired opacity in percent:
  Image opacity class "ialpha" - min=1 max=100 default=100
* vary the shading by adding ishade followed by the desired opacity in percent:
  Shading opacity class "ishade" - min=1 max=100 default=100
* vary the shining by adding ishine followed by the desired opacity in percent:
  Shining opacity class "ishine" - min=1 max=100 default=100
* vary the sphere background by adding icolor followed by the desired color as hex:
  Sphere color class "icolor" - min=000000 max=ffffff default=0
* vary the sphere background by adding igradient followed by the desired color as hex:
  Sphere color class "igradient" - min=000000 max=ffffff default=0
*/
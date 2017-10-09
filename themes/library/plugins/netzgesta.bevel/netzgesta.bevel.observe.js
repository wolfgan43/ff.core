jQuery(document).ready(function() {
	jQuery("img").addClass("bevel iradius16");
});

/*    
    * Initialisation class "bevel"
    * vary the radius by adding iradius followed by the desired radius in percent:
      Image radius class "iradius20" - min=20 max=40 default=20
    * vary the masking by adding usemask:
      Image masking class "usemask"
    * vary the masking by adding ibackcol followed by the color:
      Mask color class "ibackcol" - min=000000 max=ffffff default=0080ff
    * vary the masking by adding ifillcol followed by the color:
      Mask color class "ifillcol" - min=000000 max=ffffff default=ibackcol
    * vary the glowing by adding noglow:
      Image glowing class "noglow"
    * vary the glowing by adding iglowopac followed by the desired opacity in percent:
      Glow opacity class "iglowopac" - min=1 max=100 default=33
    * vary the glowcolor by adding iglowcol followed by the color:
      Glow color class "iglowcol" - min=000000 max=ffffff default=000000
    * vary the shining by adding noshine:
      Image shining class "noshine"
    * vary the shining by adding ishineopac followed by the desired opacity in percent:
      Shine opacity class "ishineopac" - min=1 max=100 default=40
    * vary the shinecolor by adding ishinecol followed by the color:
      Shine color class "ishinecol" - min=000000 max=ffffff default=ffffff
    * vary the shading by adding noshade:
      Image shading class "noshade"
    * vary the shading by adding ishadeopac followed by the desired opacity in percent:
      Shade opacity class "ishadeopac" - min=1 max=100 default=50
    * vary the shading by adding islinear:
      Shade gradient class "islinear"
    * vary the shadecolor by adding ishadecol followed by the color:
      Shade color class "ishadecol" - min=000000 max=ffffff default=000000
*/

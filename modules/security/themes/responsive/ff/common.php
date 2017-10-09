<?php
  function mod_sec_get_framework_css() {
	$framework_css = array(
        "component" => array(
            "class" => "loginBox security nopadding"
            , "type" => null        //null OR '' OR "-inline"
            , "grid" => "row-fluid"  //false OR array(xs, sm, md, lg) OR 'row' OR 'row-fluid'
        )
        , "inner-wrap" => array( 
            "col" => array(
                            "xs" => 12
                            , "sm" => 12
                            , "md" => 12
                            , "lg" => 12 
                        )
        )
        , "logo" => array(
            "class" => "logo-login" 
            , "col" => array( //false OR array(xs, sm, md, lg) OR 'row' OR 'row-fluid'
                            "xs" => 0
                            , "sm" => 0
                            , "md" => 6
                            , "lg" => 7
                        )   
        )        
        , "login" => array(
        	"def" => array(
	            "class" => "login" 
	            , "col" => array( //false OR array(xs, sm, md, lg) OR 'row' OR 'row-fluid'
	                            "xs" => 12
	                            , "sm" => 12
	                            , "md" => 6
	                            , "lg" => 5
	                        ) 
	        )
	        , "standard" => array(
	        	"def" => array(
		            "class" => "standard-login" 
		            , "col" => false
		        )
		        , "record" => array(
			        "class" => "login-field"
			        , "form" => null
		        )
		        , "field" => array(
	        		"form" => "control"
		        )
		        , "recover" => array(
		            "class" => "recover"
		            , "util" => "align-right" 
		        )
	        )        
	        , "social" => array(
	        	"def" => array(
		            "class" => "social-login" 
		            , "col" => false
		        )
				, "google" => array(
	                "class" => "google"
	                , "button" => array(
	                    "value" => "primary"
	                    , "params" => array(
	                        "width" => "full"
	                    )
	                )
	            )
	            , "facebook" => array(
	                "class" => "facebook"
	                , "button" => array(
	                    "value" => "primary"
	                    , "params" => array(
	                        "width" => "full"
	                    )
	                )
	            )        
	            , "janrain" => array(
	                "class" => "janrain"
	            )		        
	        )        
        )
        , "logout" => array(
        	"def" => array(
	            "class" => "logout" 
	            , "col" => array( //false OR array(xs, sm, md, lg) OR 'row' OR 'row-fluid'
	                            "xs" => 12
	                            , "sm" => 12
	                            , "md" => 6
	                            , "lg" => 5
	                        ) 
				                     
	            , "util" => array(
	            	"align-center"
	            )
	        )
	        , "account" => array(
	        	"def" => array(
		            "class" => "account" 
		            , "col" => false
		            , "util" => "align-center"
		        )
		        , "avatar" => array(
					"class" => "avatar" 
		            , "util" => "corner-circle"		        
		        )
		        , "username" => array(
		        )
		        , "email" => array(
		        )
	        )
        )
 		, "actions" => array(
		    "def" => array(
			    "class" => "actions"
			    , "form" => null
		    )
			, "login" => array(
                "class" => null
                , "button" => array(
                    "value" => "primary" 
                    , "params" => array(
                        "width" => "full"
                    )
                )
            )
        	, "logout" => array(
                "class" => null
                , "button" => array(
                    "value" => "primary"
                    , "params" => array(
                        "width" => "full"
                    )
                )
            )
			, "activation" => array(
	            "class" => null
	            , "button" => array(
	                "value" => "primary"
	                , "params" => array(
	                    "width" => "full"
	                )
	            )
	        )
	        , "recover" => array(
	            "class" => null
	            , "button" => array(
	                "value" => "primary"
	                , "params" => array(
	                    "width" => "full"
	                )
	            )
	        )		    
		)        
        , "links" => array(
        	"def" => array(
            	"class" => "link-login" 
            )
	        , "register" => array(
	            "class" => "register"
	            , "util" => "left"
	        )
	        , "back" => array(
	            "class" => "back"
	            , "util" => "right" 
	        )
        )
        , "error" => array(
            "class" => "error"
            , "callout" => "danger"
        )
    );

	return $framework_css;
}
  
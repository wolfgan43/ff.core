<?php
/**
 * image canvas
 *
 * @package FormsFramework
 * @subpackage utils
 * @author Samuele Diella <samuele.diella@gmail.com>, Alessandro Stucchi <wolfgan@blueocarina.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

/**
 * image canvas
 *
 * @package FormsFramework
 * @subpackage utils
 * @author Samuele Diella <samuele.diella@gmail.com>, Alessandro Stucchi <wolfgan@blueocarina.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */
class ffCanvas extends ffClassChecks
{
	var $cvs_res 						= NULL;
	var $cvs_res_dim_x 					= NULL;
	var $cvs_res_dim_y 					= NULL;
	var $cvs_max_dim_x					= NULL;
	var $cvs_max_dim_y					= NULL;
	var $cvs_res_background_color_hex 	= NULL;
	var $cvs_res_background_color_alpha = 0; 		// 0-127
	var $cvs_res_transparent_color_hex 	= NULL;

	var $tmb_res						= array();

	var $format							= "png"; /*
	 * can be:
	 *			png
	 *			jpg
	 */

	var $format_jpg_quality				= 77;        
		
		
	
	var $optimize						= false;
	var $optiBin						= array(
											"jpg" => array(
                                            	"convert" => 77
                                                , "JpegTran" => array(
													"path" 		=> "/usr/local/bin/jpegtran"
													, "cmd" 	=> ' -optimize -progressive -copy none '
												)
												, "JpegOptim" => array(
													"path" 		=> "/usr/bin/jpegoptim"
													, "cmd" 	=> ' --strip-all --all-progressive '
												)
											)
											, "png" => array(
                                            	"convert" => true
												, "OptiPng" => array(
													"path" 		=> '/usr/bin/optipng'
													, "cmd" 	=> ' -i0 -o7 -zm1-9 '
												)
												, "PngOut" => array(
													"path" 		=> '/usr/local/bin/pngout'
													, "cmd" 	=> ' -s0 -q -y '
												)
												, "AdvPng" => array(
													"path" 		=> ''
													, "cmd" 	=> ' -z -4 -i20 -- '
												)
												, "PngCrush" => array(
													"path" 		=> ''
													, "cmd" 	=> ' -rem gAMA -rem cHRM -rem iCCP -rem sRGB -brute -l 9 -max -reduce -m 0 -q '
												)
												, "PngQuant" => array(
													"path" 		=> ''
													, "cmd" 	=> ' --speed 1 --ext=.png --force '
												)
											)
											, "gif" => array(
												"Gifsicle" => array(
													"path" 		=> ''
													, "cmd" 	=> ' -b -O2 '
												)
											)
	
										);

	function ffCanvas($dim_x = NULL, $dim_y = NULL) 
	{
		$this->cvs_res_dim_x = $dim_x;
		$this->cvs_res_dim_y = $dim_y;
	}


	function addChild($obj, $dim_x_start = 0, $dim_y_start = 0, $z = 0, $method = "none", $dim_x = NULL, $dim_y = NULL, $dim_max_x = NULL, $dim_max_y = NULL)
	{
		$this->tmb_res[$z][] = array();
		$count_image = count($this->tmb_res[$z]) - 1;

		$this->tmb_res[$z][$count_image]["obj"]			= $obj;
		$this->tmb_res[$z][$count_image]["dim_x_start"]	= $dim_x_start;
		$this->tmb_res[$z][$count_image]["dim_y_start"] = $dim_y_start;
		$this->tmb_res[$z][$count_image]["dim_x"] 		= $dim_x;
		$this->tmb_res[$z][$count_image]["dim_y"] 		= $dim_y;
		$this->tmb_res[$z][$count_image]["max_x"] 		= $dim_max_x;
		$this->tmb_res[$z][$count_image]["max_y"] 		= $dim_max_y;
		$this->tmb_res[$z][$count_image]["method"] 		= $method;
		
		$obj->parent = array(&$this);
	}
		
	function process($filename = NULL)
	{
		ksort($this->tmb_res);
		
		if ($this->cvs_res_dim_x === NULL || $this->cvs_res_dim_y === NULL)
		{
			$calc_x = ($this->cvs_res_dim_x === NULL ? true : false);
			$calc_y = ($this->cvs_res_dim_y === NULL ? true : false);
			
			foreach ($this->tmb_res as $zkey => $zvalue)
			{
				foreach ($this->tmb_res[$zkey] as $key => $value)
				{
					$ref = $this->tmb_res[$zkey][$key];
					
					$ref["obj"]->pre_process();
					
					if ($calc_x && $this->cvs_res_dim_x < $ref["obj"]->new_res_dim_real_x)
						$this->cvs_res_dim_x = $ref["obj"]->new_res_dim_real_x;
				
					if ($calc_y && $this->cvs_res_dim_y < $ref["obj"]->new_res_dim_real_y)
						$this->cvs_res_dim_y = $ref["obj"]->new_res_dim_real_y;
                        
                    if($ref["obj"]->watermark !== null) 
                    {
                    	if(is_array($ref["obj"]->watermark) && count($ref["obj"]->watermark)) {
							foreach($ref["obj"]->watermark AS $watermark_key => $watermark_value) {
								//$ref["obj"]->watermark[$watermark_key]->new_res_dim_real_x = $this->cvs_res_dim_x;
								//$ref["obj"]->watermark[$watermark_key]->new_res_dim_real_y = $this->cvs_res_dim_y;
								$ref["obj"]->watermark[$watermark_key]->new_res_max_x = $ref["obj"]->new_res_dim_real_x;
								$ref["obj"]->watermark[$watermark_key]->new_res_max_y = $ref["obj"]->new_res_dim_real_y;
		                        $ref["obj"]->watermark[$watermark_key]->pre_process();
							}
                    	} else {
							//$ref["obj"]->watermark->new_res_dim_real_x = $this->cvs_res_dim_x;
							//$ref["obj"]->watermark->new_res_dim_real_y = $this->cvs_res_dim_y;
							$ref["obj"]->watermark->new_res_max_x = $ref["obj"]->new_res_dim_real_x;
							$ref["obj"]->watermark->new_res_max_y = $ref["obj"]->new_res_dim_real_y;
	                        $ref["obj"]->watermark->pre_process();
                    	}
                        
                        //print_r($watermark_obj);

                    }
				}
				reset($this->tmb_res[$zkey]);
			}
			reset($this->tmb_res);
			
			if ($this->cvs_max_dim_x !== NULL && $this->cvs_res_dim_x > $this->cvs_max_dim_x)
				$this->cvs_res_dim_x = $this->cvs_max_dim_x;
		
			if ($this->cvs_max_dim_y !== NULL && $this->cvs_res_dim_y > $this->cvs_max_dim_y)
				$this->cvs_res_dim_y = $this->cvs_max_dim_y;
		}
		
//		ffErrorHandler::raise("DEBUG", E_USER_ERROR, $this, get_defined_vars());
	
		$this->cvs_res = @imagecreatetruecolor($this->cvs_res_dim_x, $this->cvs_res_dim_y);
		imagealphablending($this->cvs_res, true);
		imagesavealpha($this->cvs_res, true);
		
		if (is_resource($this->cvs_res) == 1) 
		{
			if(strlen($this->cvs_res_transparent_color_hex) == 6) 
			{
				$color_transparent = imagecolorallocate($this->cvs_res
														, hexdec(substr($this->cvs_res_transparent_color_hex, 0, 2))
														, hexdec(substr($this->cvs_res_transparent_color_hex, 2, 2))
														, hexdec(substr($this->cvs_res_transparent_color_hex, 4, 2))
														);

				$transparent = imagecolortransparent($this->cvs_res, $color_transparent);
			}
			
			if (strlen($this->cvs_res_background_color_hex) == 6)
			{
				if ($this->cvs_res_transparent_color_hex == $this->cvs_res_background_color_hex)
					$color_background = $color_transparent;
				else
					$color_background = imagecolorallocatealpha(
																	$this->cvs_res 
																	, hexdec(substr($this->cvs_res_background_color_hex, 0, 2))
																	, hexdec(substr($this->cvs_res_background_color_hex, 2, 2))
																	, hexdec(substr($this->cvs_res_background_color_hex, 4, 2))
																	, $this->cvs_res_background_color_alpha
																);
			}
			else
			{
				$color_background = imagecolorallocatealpha(
																$this->cvs_res 
																, 0
																, 0
																, 0
																, $this->cvs_res_background_color_alpha
															);
			}
			imagefill($this->cvs_res, 0, 0, $color_background);

			foreach ($this->tmb_res as $zkey => $zvalue)
			{
				foreach ($this->tmb_res[$zkey] as $key => $value)
				{
					$ref = $this->tmb_res[$zkey][$key];
					$src_res = $ref["obj"]->process($ref["max_x"], $ref["max_y"]);

					imagecopy($this->cvs_res
										, $src_res
										, $ref["dim_x_start"]
										, $ref["dim_y_start"]
										, 0
										, 0
										, $ref["obj"]->new_res_dim_real_x
										, $ref["obj"]->new_res_dim_real_y
										);

					@imagedestroy($src_res);

                    if($ref["obj"]->watermark !== null) 
                    {
                    	if(is_array($ref["obj"]->watermark) && count($ref["obj"]->watermark)) {
							foreach($ref["obj"]->watermark AS $watermark_key => $watermark_value) {
								$watermark_obj[$watermark_key] = $watermark_value;

		                        if(($this->cvs_res_dim_x - $watermark_obj[$watermark_key]->new_res_dim_real_x) > 0) {
                        			$watermark_x_start = ($this->cvs_res_dim_x - $watermark_obj[$watermark_key]->new_res_dim_real_x);
								} else {
                        			$watermark_x_start = 0;
								}
		                        if(($this->cvs_res_dim_y - $watermark_obj[$watermark_key]->new_res_dim_real_y) > 0) {
                        			$watermark_y_start = ($this->cvs_res_dim_y - $watermark_obj[$watermark_key]->new_res_dim_real_y);
								} else {
                        			$watermark_y_start = 0;
								}

		                        $src_res = $watermark_obj[$watermark_key]->process();

		                        imagecopy($this->cvs_res
		                                            , $src_res
		                                            , $watermark_x_start
		                                            , $watermark_y_start
		                                            , 0
		                                            , 0
		                                            , $watermark_obj[$watermark_key]->new_res_dim_real_x
		                                            , $watermark_obj[$watermark_key]->new_res_dim_real_y
		                                            );

		                        @imagedestroy($src_res);
							}
						} else {
                    		$watermark_obj = $ref["obj"]->watermark;

	                        if(($this->cvs_res_dim_x - $watermark_obj->new_res_dim_real_x) > 0) {
                        		$watermark_x_start = ($this->cvs_res_dim_x - $watermark_obj->new_res_dim_real_x);
							} else {
                        		$watermark_x_start = 0;
							}
	                        if(($this->cvs_res_dim_y - $watermark_obj->new_res_dim_real_y) > 0) {
                        		$watermark_y_start = ($this->cvs_res_dim_y - $watermark_obj->new_res_dim_real_y);
							} else {
                        		$watermark_y_start = 0;
							}

	                        $src_res = $watermark_obj->process();

	                        imagecopy($this->cvs_res
	                                            , $src_res
	                                            , $watermark_x_start
	                                            , $watermark_y_start
	                                            , 0
	                                            , 0
	                                            , $watermark_obj->new_res_dim_real_x
	                                            , $watermark_obj->new_res_dim_real_y
	                                            );

	                        @imagedestroy($src_res);
						}
                    }
                    
				}
				reset($this->tmb_res[$zkey]);
			}
			reset($this->tmb_res);
		}

		switch ($this->format)
		{
			case "jpg":
				if ($filename === NULL)
					header("Content-Type: image/jpg");
				
				if(@imagejpeg($this->cvs_res, $filename, ($this->optimize ? 100 : $this->format_jpg_quality)) === false) {
					die("Permission Denied: " . $filename);
				}
				break;

			case "png":
			default:
				if ($filename === NULL)
					header("Content-Type: image/png");
				
				if(@imagepng($this->cvs_res, $filename) === false) {
					die("Permission Denied: " . $filename);
				}
		}

		if($filename && $this->optimize && isset($this->optiBin[$this->format])) {
        	if($this->optiBin[$this->format]["convert"])
            	$shell_cmd = 'convert -strip -quality ' . $this->optiBin[$this->format]["convert"] . '% ' . $filename . " " . $filename . ";";

			foreach($this->optiBin[$this->format] AS $optim) { 
				if($optim["path"])
					$shell_cmd .= $optim["path"] . $optim["cmd"] . $filename . "; ";
			}
			//@shell_exec($shell_cmd);
			@shell_exec("(" . $shell_cmd . ") > /dev/null 2>/dev/null &"); //serve per far andare la procedura in modo asincrono
                        //die($shell_cmd);
		}
	}
}
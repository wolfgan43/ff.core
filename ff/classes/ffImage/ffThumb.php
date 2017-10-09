<?php
/**
 * thumb from file layer
 *
 * @package FormsFramework
 * @subpackage utils
 * @author Samuele Diella <samuele.diella@gmail.com>, Alessandro Stucchi <wolfgan@blueocarina.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

/**
 * thumb from file layer
 *
 * @package FormsFramework
 * @subpackage utils
 * @author Samuele Diella <samuele.diella@gmail.com>, Alessandro Stucchi <wolfgan@blueocarina.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */
class ffThumb extends ffImage
{

	var $template_dir		= NULL;
	var $disk_path			= "";
	var $theme				= NULL;
	var $parent				= NULL;
    var $watermark          = NULL;
    
	var $src_res_path		= "";
	
	var $icon_path 			= "/";
	var $icons = 
			array (
				"text/plain" 					=> "txt.png",
                "text/html"                     => "html.png",
				"application/pdf" 				=> "[CONVERT]",
                "application/x-shockwave-flash" => "swf.png",
                "application/msword"			=> "doc.png",
                "application/octet-stream"		=> "exe.png",
                "video/quicktime"				=> "mov.png",
                "audio/mpeg3" 					=> "mp3.png",
                "audio/mpeg" 					=> "mp3.png",
                "audio/x-wav" 					=> "wav.png",
                "application/mspowerpoint"		=> "ppt.png",
                "application/octet-stream"		=> "psd.png",
                "application/excel"				=> "xls.png",
                "application/x-compressed" 		=> "zip.png",
                "application/vnd.openxmlformats-officedocument.presentationml.presentation" 	=> "ppt.png",
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 			=> "xls.png",
                "application/vnd.openxmlformats-officedocument.wordprocessingml.document" 		=> "doc.png",
				"image/jpeg" 					=> "[IMAGEPATH]",
				"image/gif" 					=> "[IMAGEPATH]",
				"image/png" 					=> "[IMAGEPATH]",
				"directory" 					=> "dir.png",
				"unknown" 						=> "unknown.png",
				"noicon" 						=> "noicon.png",
				"error"  						=> "error.png"
			);


	/**
	 * Crea un oggetto thumbnail, ossia la riduzione in scala di un file presente su disco
	 * @param Int $new_res_dim_real_x Le dimensioni finali orizzontali della risorsa
	 * @param Int $new_res_dim_real_y Le dimensioni finali verticali della risorsa
	 * @param resource $src_res la risorsa in cui inserire il thumbnail
	 */
	function ffThumb($new_res_dim_real_x = NULL, $new_res_dim_real_y = NULL, $src_res = NULL)
	{
		//$this->getDefaults();

		$this->ffImage($new_res_dim_real_x, $new_res_dim_real_y, $src_res);
	}
		
	/**
	 * Carica un immagine da disco per la creazione del thumbnail, basandosi sul mime-type
	 * E' in grado di convertire i file PDF usando l'utility convert di linux
	 * E' anche possibile associare icone a mime-type predefiniti
	 * @param String $src_res_path Il percorso dell'immagine da caricare
	 * @return resource La risorsa immagine caricata
	 */
	function load_image($src_res_path)
	{
//		$mime = ffMimeType($src_res_path);
		$mime = ffMimeTypeByFilename($src_res_path);

		if (is_dir($src_res_path))
			$src_res_path_tmp = $this->icons["directory"];
		elseif (is_file($src_res_path))
			$src_res_path_tmp = (isset($this->icons[$mime]) ? $this->icons[$mime] : $this->icons["noicon"]);
		elseif (!strlen($src_res_path))
			$src_res_path_tmp = $this->icons["unknow"];

		switch ($src_res_path_tmp)
		{
			case "[IMAGEPATH]":
				$src_res_path_tmp = $src_res_path;
				break;

			case "[CONVERT]":
				$src_res_path_tmp = $src_res_path . "-conversion.png";
				exec('convert -antialias -density 300 -depth 24 "' . $src_res_path . '"[0] "' . $src_res_path_tmp . '"');
                if (!is_file($src_res_path_tmp))
				{
					if (is_file($src_res_path . "-conversion-0.png"))
					{
						exec('mv "' . $src_res_path . '-conversion-0.png" "' . $src_res_path_tmp . '"');
						exec('rm -f "' . $src_res_path . '-conversion-*.png"');
					}
				}
                if(!is_file($src_res_path_tmp)) {
                    $src_res_path_tmp = $this->get_template_dir("pdf.png");
                }

               // $mime = ffMimeType($src_res_path); 
				break;

			default:
				$src_res_path_tmp = $this->get_template_dir($src_res_path_tmp);
				if(!is_file($src_res_path_tmp))
					$src_res_path_tmp = $this->get_template_dir($this->icons["error"]);
				//$mime = ffMimeType($src_res_path_tmp);
		}

        $mime = ffMimeType($src_res_path_tmp);
		if (!function_exists(str_replace("/","", $mime))) 
            $src_res_path_tmp = $this->get_template_dir($this->icons["unknow"]);
		
		$src_res = false;
        switch ($mime)
        {
            case "image/jpeg":
                $src_res = @imagecreatefromjpeg($src_res_path_tmp);
                /*if($src_res === false)
                	$src_res = @imagecreatefrompng($src_res_path_tmp);*/
                break;
            case "image/png":
                $src_res = @imagecreatefrompng($src_res_path_tmp);
                /*if($src_res === false)
                	$src_res = @imagecreatefromjpeg($src_res_path_tmp);*/
                break;
            case "image/gif":
                $src_res = @imagecreatefromgif($src_res_path_tmp);
                break;
        }

		if($src_res === false) {
			$src_res_path_tmp = $this->get_template_dir($this->icons["error"]);
			$mime = ffMimeType($src_res_path_tmp);

	        switch ($mime) 
	        {
	            case "image/jpeg":
	                $src_res = @imagecreatefromjpeg($src_res_path_tmp);
	                break;
	            case "image/png":
	                $src_res = @imagecreatefrompng($src_res_path_tmp);
	                break;
	            case "image/gif":
	                $src_res = @imagecreatefromgif($src_res_path_tmp);
	                break;
	        }
		}
		if($src_res !== false) {
	        imagealphablending($src_res, true);
	        imagesavealpha($src_res, true);
	        
	        return $src_res;
		} else {
			return null;
		}
		
	}			
		
	/**
	 * Recupera il tema corrente
	 * @return String il tema corrente
	 */
	function get_theme()
	{
		if ($this->theme !== NULL)
			return $this->theme;
		else if ($this->theme === NULL && $this->parent !== NULL)
			return $this->parent[0]->theme;
		else
			return "default";
	}

	/**
	 * Recupera il percorso del tema corrente
	 * Utile per caricare le icone per i mime-type predefiniti
	 * @return String il percorso del tema corrente
	 */
	function get_template_dir($file)
	{
		if(is_file($this->disk_path . "/themes/" . $this->get_theme() . rtrim($this->icon_path, "/") . "/" . $file)) {
			return $this->disk_path . "/themes/" . $this->get_theme() . rtrim($this->icon_path, "/") . "/" . $file;
		} else if ($this->template_dir !== NULL && is_file($this->template_dir . "/" . $file)) {
			return $this->template_dir . "/" . $file;
		} else {
			return false;
		}			

/*
		if ($this->template_dir !== NULL && $this->icon_path == "/") {
			return $this->template_dir . "/" . $file;
		} else {
			if(file_exists($this->disk_path . "/themes/" . $this->get_theme() . rtrim($this->icon_path, "/") . "/" . $file)) {
				return $this->disk_path . "/themes/" . $this->get_theme() . rtrim($this->icon_path, "/") . "/" . $file;
			} else {
				return false;
			}			
		}
*/
	}
}

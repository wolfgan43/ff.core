<?php
/**
* @package Forms PHP Framework
* @category Common Functions Class
* @desc ffTemplate.php - Forms Framework Template Engine
* @author Samuele Diella <samuele.diella@gmail.com>
* @copyright Copyright &copy; 2004-2017, Samuele Diella
* @license https://opensource.org/licenses/LGPL-3.0
* @link http://www.formsphpframework.com
* @version v2, alpha
* @since v1, alpha 1
*/

define("FF_TEMPLATE_REGEXP", "/\{([\w\[\]\:\=\-\|\.]+)\}/U");

/**
* @desc ffTemplate è la classe preposta alla gestione dei template
* @author Samuele Diella <samuele.diella@gmail.com>
* @version v4, alpha
* @since v1, alpha 1
*/

class ffTemplate 
{
	var $root_element			= "main";
	
	var $BeginTag				= "Begin";
	var $EndTag					= "End";
	
	var $debug_msg				= false;
	var $display_unparsed_sect	= false;
	var $doublevar_to_commenthtml 			= false;
	
	var $DBlocks = array();			// initial data: files and blocks
	var $ParsedBlocks = array();		// result data and variables
	var $DVars = array();
	var $DBlockVars = array();

	var $template_root;
	var $sTemplate;

	var $minify						= false; /* can be: false, strip, strong_strip, minify
	 											 NB: minify require /library/minify (set CM_CSSCACHE_MINIFIER and CM_JSCACHE_MINIFIER too) */
	var $compress					= false;
	
	// FF enabled settings (u must have FF and use ::factory()
	var $force_mb_encoding			= "UTF-8"; // false or UTF-8 (require FF)

	public 	$events 				= null;
	static protected $_events		= null;

	// MultiLang SETTINGS
	var $MultiLang							= true; // enable support (require class ffDB_Sql)
	
	var $MultiLang_database					= null; // if null == static version (see below)
	var $MultiLang_user						= null;
	var $MultiLang_password					= null;
	var $MultiLang_host						= null;
	var $MultiLang_session_parameter		= null;
	var $MultiLang_default					= null;
    var $MultiLang_table_international		= null;
    var $MultiLang_table_languages			= null;

	static $_MultiLang_database 			= null; // if null == FF_DATABASE_NAME
	static $_MultiLang_user     			= null; // if null == FF_DATABASE_USER
	static $_MultiLang_password 			= null; // if null == FF_DATABASE_PASS
	static $_MultiLang_host     			= null; // if null == FF_DATABASE_HOST
	static $_MultiLang_session_parameter 	= "LangID";
	static $_MultiLang_default 				= null; // if null == FF_LOCALE
    static $_MultiLang_table_international  = "ff_international";
    static $_MultiLang_table_languages      = "ff_languages";
	
    static $_MultiLang_cache                = true;
    static $_MultiLang_cache_path           = "/cache/international"; // auto-prefixed with FF_DISK_PATH
	
	static $_MultiLang_db 					= null;
	static $_MultiLang_Hide_code 			= false;
    static $_MultiLang_Insert_code_empty    = false;
	
	// PRIVATES
	private $useFormsFramework		= false;
	
	// COMMON CHECKS
 	public function __set ($name, $value)
 	{
 		if ($this->useFormsFramework)
 			ffErrorHandler::raise("property \"$name\" not found on class " . __CLASS__, E_USER_ERROR, $this, get_defined_vars());
 		else
 			die("property \"$name\" not found on class " . __CLASS__);
	}

	public function __get ($name)
	{
 		if ($this->useFormsFramework)
 			ffErrorHandler::raise("property \"$name\" not found on class " . __CLASS__, E_USER_ERROR, $this, get_defined_vars());
 		else
 			die("property \"$name\" not found on class " . __CLASS__);
	}

	public function __isset ($name)
	{
 		if ($this->useFormsFramework)
 			ffErrorHandler::raise("property \"$name\" not found on class " . __CLASS__, E_USER_ERROR, $this, get_defined_vars());
 		else
 			die("property \"$name\" not found on class " . __CLASS__);
	}

	public function __unset ($name)
	{
 		if ($this->useFormsFramework)
 			ffErrorHandler::raise("property \"$name\" not found on class " . __CLASS__, E_USER_ERROR, $this, get_defined_vars());
 		else
 			die("property \"$name\" not found on class " . __CLASS__);
	}

	public function __call ($name, $arguments)
	{
 		if ($this->useFormsFramework)
 			ffErrorHandler::raise("function \"$name\" not found on class " . __CLASS__, E_USER_ERROR, $this, get_defined_vars());
 		else
 			die("function \"$name\" not found on class " . __CLASS__);
	}

	/*static public function __callStatic ($name, $arguments)
	{
 		if ($this->useFormsFramework)
 			ffErrorHandler::raise("function \"$name\" not found on class " . get_class($this), E_USER_ERROR, $this, get_defined_vars());
 		else
 			die("function \"$name\" not found on class " . get_class($this));
	}*/

	// STATIC EVENTS MANAGEMENT
	static public function addEvent($event_name, $func_name, $priority = null, $index = 0, $break_when = null, $break_value = null)
	{
		if (!class_exists("ffCommon", false))
			die(__CLASS__ . ": " . __FUNCTION__ . " method require Forms Framework");

		self::initEvents();
		self::$_events->addEvent($event_name, $func_name, $priority, $index, $break_when, $break_value);
	}

	static private function doEvent($event_name, $event_params = array())
	{
		self::initEvents();
		return self::$_events->doEvent($event_name, $event_params);
	}
	
	static private function initEvents()
	{
		if (self::$_events === null)
			self::$_events = new ffEvents();
	}

	/**
	 * This method istantiate a ffTemplate instance based on dir path. When using this
	 * function, the resulting object will deeply use Forms Framework.
	 *
	 * @param string $template_root
	 * @return ffTemplate
	 */
	public static function factory($template_root)
	{
		if (!class_exists("ffCommon", false))
			die(__CLASS__ . ": " . __FUNCTION__ . " method require Forms Framework");

		$res = self::doEvent("on_factory", array($template_root));

		$tmp = new ffTemplate($template_root);
		$tmp->useFormsFramework = true;
		$tmp->events = new ffEvents();
		if(defined("FF_TEMPLATE_ENABLE_TPL_JS"))
			$tmp->doublevar_to_commenthtml = FF_TEMPLATE_ENABLE_TPL_JS;

		$res = self::doEvent("on_factory_done", array($tmp));

		return $tmp;
	}
			
	// CONSTRUCTOR
	function __construct($template_root)
	{
		$this->template_root = $template_root;
	}
	
	function load_file($filename, $root_element = null)
	{
		if ($root_element !== null)
			$this->root_element = $root_element;
		
		$this->sTemplate = $filename;
		if (substr($filename, 0, 1) != "/")
			$filename = "/" . $filename;
		$template_path = $this->template_root . $filename;

		$ffcache_tpl_success = false;
		if ($this->useFormsFramework && FF_ENABLE_MEM_TPL_CACHING)
		{
			$cache = ffCache::getInstance(FF_CACHE_ADAPTER);
			$res = $cache->get($template_path, $ffcache_tpl_success);
		}

		if (!$ffcache_tpl_success)
		{
			$this->DBlocks[$this->root_element] = @file_get_contents($template_path);
			if ($this->DBlocks[$this->root_element] !== false)
			{
				if ($this->useFormsFramework && $this->force_mb_encoding !== false)
					$this->DBlocks[$this->root_element] = ffCommon_charset_encode($this->DBlocks[$this->root_element], $this->force_mb_encoding);

				$this->getDVars();
				$nName = $this->NextDBlockName($this->root_element);
				while($nName != "")
				{
					$this->SetBlock($this->root_element, $nName);
					$this->blockVars($nName);
					$nName = $this->NextDBlockName($this->root_element);
				}
			}
			else
			{
				if ($this->useFormsFramework)
					ffErrorHandler::raise("Unable to find the template", E_USER_ERROR, null, get_defined_vars());
				else
					die("<br><b><u><font color=\"red\">Unable to find the template</font></u></b><br>");
			}
			
			if ($this->useFormsFramework && FF_ENABLE_MEM_TPL_CACHING) $cache->set($template_path, null, array("DBlocks" => $this->DBlocks, "DVars" => $this->DVars, "DBlockVars" => $this->DBlockVars), true);
		}
		else
		{
			$this->DBlocks = $res["DBlocks"];
			$this->DVars = $res["DVars"];
			$this->DBlockVars = $res["DBlockVars"];
		}
		
		self::$_events->doEvent("on_loaded_data", array($this));
	}

	function load_content($content, $root_element = null)
	{
		if ($root_element !== null)
			$this->root_element = $root_element;
		
		$nName = "";
		
		$this->DBlocks[$this->root_element] = $content;
		$this->getDVars();
		$nName = $this->NextDBlockName($this->root_element);
		while($nName != "")
		{
			$this->SetBlock($this->root_element, $nName);
			$nName = $this->NextDBlockName($this->root_element);
		}

		self::$_events->doEvent("on_loaded_data", array($this));
	}
	
	function getDVars()
	{
		$matches = array();

		if($this->doublevar_to_commenthtml)
			$this->DBlocks[$this->root_element]	= preg_replace('/\{\{([\w\[\]\:\=\-\|\.]+)\}\}/U', "<!--{\{$1\}\}-->", $this->DBlocks[$this->root_element]);// str_replace(array("{{", "}}"), array("<!--", "-->"), $this->DBlocks[$this->root_element]);

		$rc = preg_match_all (FF_TEMPLATE_REGEXP, $this->DBlocks[$this->root_element], $matches);
		if ($rc)
			$this->DVars = array_flip($matches[1]);
		else
			$this->DVars = array();
	}

	function NextDBlockName($sTemplateName)
	{
		$sTemplate = $this->DBlocks[$sTemplateName];
		$BTag = strpos($sTemplate, "<!--" . $this->BeginTag);
		if($BTag === false)
		{
			return "";
		}
		else
		{
			$ETag = strpos($sTemplate, "-->", $BTag);
			$sName = substr($sTemplate, $BTag + 9, $ETag - ($BTag + 9));
			if(strpos($sTemplate, "<!--" . $this->EndTag . $sName . "-->") > 0)
			{
				return $sName;
			}
			else
			{
				return "";
			}
		}
	}
	
	
	function SetBlock($sTplName, $sBlockName)
	{
		if(!isset($this->DBlocks[$sBlockName]))
			$this->DBlocks[$sBlockName] = $this->getBlock($this->DBlocks[$sTplName], $sBlockName);
		
		$this->DBlocks[$sTplName] = $this->replaceBlock($this->DBlocks[$sTplName], $sBlockName);
		
		$nName = $this->NextDBlockName($sBlockName);
		while($nName != "")
		{
			$this->SetBlock($sBlockName, $nName);
			$nName = $this->NextDBlockName($sBlockName);
		}
	}
	
	function getBlock($sTemplate, $sName)
	{
		$alpha = strlen($sName) + 12;
		
		$BBlock = strpos($sTemplate, "<!--" . $this->BeginTag . $sName . "-->");
		$EBlock = strpos($sTemplate, "<!--" . $this->EndTag . $sName . "-->");

		if($BBlock === false || $EBlock === false)
			return "";
		else
			return substr($sTemplate, $BBlock + $alpha, $EBlock - $BBlock - $alpha);
	}
	
	
	function replaceBlock($sTemplate, $sName)
	{
		$BBlock = strpos($sTemplate, "<!--" . $this->BeginTag . $sName . "-->");
		$EBlock = strpos($sTemplate, "<!--" . $this->EndTag . $sName . "-->");

		if($BBlock === false || $EBlock === false)
			return $sTemplate;
		else
			return substr($sTemplate, 0, $BBlock) . "{" . $sName . "}" . substr($sTemplate, $EBlock + strlen("<!--End" . $sName . "-->"));
	}
	
	function GetVar($sName)
	{
		return $this->DBlocks[$sName];
	}
	
	function set_var($sName, $sValue)
	{
		$this->ParsedBlocks[$sName] = $sValue;
		if (isset($this->DVars[$sName]) || isset($this->DBlocks[$sName]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function isset_var($sName)
	{
		if (isset($this->DVars[$sName]) || isset($this->DBlocks[$sName]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function set_regexp_var($sPattern, $sValue)
	{
		$rc = false;
		$tmp = array_keys($this->ParsedBlocks);
		foreach ($tmp as $key => $value)
		{
			if (preg_match($sPattern, $value))
			{
				$rc = true;
				$this->ParsedBlocks[$value] = $sValue;
			}
		}
		return $rc;
	}
	
	function parse_regexp($sPattern, $sValue)
	{
		$rc = false;
		$tmp = array_keys($this->DBlocks);
		foreach ($tmp as $key => $value)
		{
			if (preg_match($sPattern, $value))
			{
				$rc = true;
				$this->parse($value, $sValue);
			}
		}
		return $rc;
	}
	
	function print_var($sName)
	{
		echo $this->ParsedBlocks[$sName];
	}
	
	function parse($sTplName, $bRepeat, $bBefore = false)
	{
		if(isset($this->DBlocks[$sTplName]))
		{
			if($bRepeat && isset($this->ParsedBlocks[$sTplName]))
			{
				if ($bBefore)
					$this->ParsedBlocks[$sTplName] = $this->ProceedTpl($sTplName) . $this->ParsedBlocks[$sTplName];
				else
					$this->ParsedBlocks[$sTplName] .= $this->ProceedTpl($sTplName);
			}
			else
				$this->ParsedBlocks[$sTplName] = $this->ProceedTpl($sTplName);
			
			return true;
		}
		else if ($this->debug_msg)
		{
			echo "<br><b>Block with name <u><font color=\"red\">$sTplName</font></u> does't exist</b><br>";
		}

		return false;
	}
	
	function pparse($block_name, $is_repeat)
	{
		$ret = $this->rpparse($block_name, $is_repeat);

		if($this->compress)
			ffTemplate::http_compress($ret);
		else 
			echo $ret;
	}
	
	function rpparse($block_name, $is_repeat)
	{
		$this->parse($block_name, $is_repeat);
		return $this->getBlockContent($block_name);
	}
	
	function getBlockContent($block_name, $minify = null)
	{
		$minify = ($minify === null ? $this->minify : $minify);
		
		if ($minify === false)
			return $this->entities_replace($this->ParsedBlocks[$block_name]);
		else if ($minify === "strip")
			return $this->entities_replace(preg_replace("/\n\s*/", "\n", $this->ParsedBlocks[$block_name], -1, $count));
		else if ($minify === "strong_strip")
			return $this->entities_replace(preg_replace(
						array (
							'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
							'/[^\S ]+\</s',  // strip whitespaces before tags, except space
							'/(\s)+/s'       // shorten multiple whitespace sequences
						)
						, array (
							'>',
							'<',
							'\\1'
						)
						, $this->ParsedBlocks[$block_name]
						, -1
						, $count
					));
		else if ($minify === "minify")
		{
			if (!class_exists("Minify_HTML"))
				require FF_DISK_PATH . '/library/minify/min/lib/Minify/HTML.php';
			if (!class_exists("CSSmin"))
				require FF_DISK_PATH . '/library/minify/min/lib/CSSmin.php';
			if (!class_exists("JSMin"))
				require FF_DISK_PATH . '/library/minify/min/lib/JSMin.php';
			
			return $this->entities_replace(str_replace(chr(10), " ", Minify_HTML::minify(
						$this->ParsedBlocks[$block_name]
						, array(
							"cssMinifier" => "CSSmin::_minify"
							, "jsMinifier" => "JSMin::minify"
							, "jsCleanComments" => true
						))
					));
		}
		else if ($minify === "yui")
		{
			if (!class_exists("Minify_HTML"))
				require FF_DISK_PATH . '/library/minify/min/lib/Minify/HTML.php';
			if (!class_exists("Minify_YUICompressor"))
				require(FF_DISK_PATH . "/library/gminify/YUICompressor.php");
			Minify_YUICompressor::$jarFile = FF_DISK_PATH . "/library/gminify/yuicompressor-2.4.8.jar";
			if (!file_exists(CM_JSCACHE_DIR))
			{
				@mkdir(CM_JSCACHE_DIR, 0777, true);
			}
			Minify_YUICompressor::$tempDir = CM_JSCACHE_DIR;
			return $this->entities_replace(str_replace(chr(10), " ", Minify_HTML::minify(
						$this->ParsedBlocks[$block_name]
						, array(
							"cssMinifier" => "Minify_YUICompressor::minifyCss"
							, "jsMinifier" => "Minify_YUICompressor::minifyJs"
						))
					));
		}
		else
		{
			if ($this->useFormsFramework)
				ffErrorHandler::raise("Unknown minify method", E_USER_ERROR, $this, get_defined_vars());
			else
				die("Unknown minify method");
		}
	}
	
	static function http_compress($data, $output_result = true, $method = null, $level = 9)
	{
		if ($method === null)
		{
			$encodings = array_flip(explode(",", $_SERVER["HTTP_ACCEPT_ENCODING"]));
			if (isset($encodings["gzip"])) // better gzip
				$method = "gzip";
			elseif (isset($encodings["deflate"]))
				$method = "deflate";
		}
		
		if ($method == "deflate")
		{
			if ($output_result)
			{
				header("Content-Encoding: deflate");
				echo gzdeflate($data, $level);
				/*gzcompress($this->tpl[0]->rpparse("main", false), 9);
				gzencode($this->tpl[0]->rpparse("main", false), 9, FORCE_DEFLATE);
				gzencode($this->tpl[0]->rpparse("main", false), 9, FORCE_GZIP);*/
			}
			else
				return array(
					"method" => "deflate"
					, "data" => gzdeflate($data, $level)
				);
		}
		elseif ($method == "gzip")
		{
			if ($output_result)
			{
				header("Content-Encoding: gzip");
				echo gzencode($data, $level);
			}
			else
				return array(
					"method" => "gzip"
					, "data" => gzencode($data, $level)
				);
		}
		else
		{
			if ($output_result)
				echo $data;
			else
				return array(
					"method" => null
					, "data" => $data
				);
		}
	}

	function blockVars($sTplName)
	{
		if (isset($this->DBlockVars[$sTplName]))
			return $this->DBlockVars[$sTplName];
		
		$sTpl = $this->DBlocks[$sTplName];
		
		$matches = array();
		$rc = preg_match_all (FF_TEMPLATE_REGEXP, $sTpl, $matches);
		if ($rc)
		{
			$vars = $matches[1];

			// --- AUTOMATIC LANGUAGE LOOKUP FOR INTERNATIONALIZATION
			if ($this->useFormsFramework)
			{
				foreach ($vars as $nName)
				{
					if (substr($nName, 0, 1) == "_")
					{
						if ($this->MultiLang)
							$this->set_var($nName, $this->get_word_by_code(substr($nName, 1)));
						else
							$this->set_var($nName, "{" . substr($nName, 1) . "}");
					}
				}
				reset($vars);
			}
			
			$this->DBlockVars[$sTplName] = $vars;
			
			return $vars;
		}
		else
			return false;
	}
	
	function ProceedTpl($sTplName)
	{
		$vars = $this->blockVars($sTplName);
		$sTpl = $this->DBlocks[$sTplName];
		
		if($vars)
		{
			$search_for = array();
			$replace_with = array();
			
			reset($vars);
			foreach($vars as $key => $value)
			{
				$tmp = $this->ParsedBlocks[$value];
				if (is_object($tmp))
				{
					if ($this->useFormsFramework)
						ffErrorHandler::raise("bad value into template", E_USER_ERROR, $this, get_defined_vars());
					else
						die("bad value into template");
				}
				
				$search_for[] = "{" . $value . "}";
				if(isset($this->ParsedBlocks[$value]))
				{
					$replace_with[] = $this->ParsedBlocks[$value];
				}
				else if(isset($this->DBlocks[$value]) && $this->display_unparsed_sect)
				{
					$replace_with[] = $this->DBlocks[$value];
				}
				else
				{
					$replace_with[] = "";
				}
			}
			$sTpl = str_replace($search_for, $replace_with, $sTpl);
		}
		return $sTpl;
	}
	
	function PrintAll()
	{
		$res = "<table border=\"1\" width=\"100%\">";
		$res .= "<tr bgcolor=\"#C0C0C0\" align=\"center\"><td>Key</td><td>Value</td></tr>";
		$res .= "<tr bgcolor=\"#FFE0E0\"><td colspan=\"2\" align=\"center\">ParsedBlocks</td></tr>";
		reset($this->ParsedBlocks);
		foreach($this->ParsedBlocks as $key => $value)
		{
			$res .= "<tr><td><pre>" . ffCommon_specialchars($key) . "</pre></td>";
			$res .= "<td><pre>" . ffCommon_specialchars($value) . "</pre></td></tr>";
		}
		$res .= "<tr bgcolor=\"#E0FFE0\"><td colspan=\"2\" align=\"center\">DBlocks</td></tr>";
		reset($this->DBlocks);

		foreach($this->DBlocks as $key => $value)
		{
			$res .= "<tr><td><pre>" . ffCommon_specialchars($key) . "</pre></td>";
			$res .= "<td><pre>" . ffCommon_specialchars($value) . "</pre></td></tr>";
		}
		$res .= "</table>";
		return $res;
	}
	
	function get_word_by_code($code, $language = null)
	{
		if ($this->useFormsFramework)
		{
			$res = $this->events->doEvent("on_get_word_by_code", array($code, $language, $this));
			$rc = end($res);
			if ($rc === null)
				return ffTemplate::_get_word_by_code($code, $language, $this);
			else
				return $rc;
		}
		else
			return "{" . $code . "}";
	}
	
	static function _get_word_by_code($code, $language = null, ffTemplate $tpl = null, $return_i18n = false)
	{
		static $i18n_data = array();
		static $i18n_data_key = array();
		
		if($return_i18n)
			return $i18n_data_key;

		self::initEvents();
		$res = self::$_events->doEvent("on_get_word_by_code", array($code, $language, $tpl));
		$rc = end($res);
		if ($rc !== null)
			return $rc;

		if (ffTemplate::$_MultiLang_db === null)
			ffTemplate::$_MultiLang_db = ffDb_Sql::factory();
	
		if ($tpl !== null && $tpl->MultiLang_database !== null)
			$database = $tpl->MultiLang_database;
		elseif (ffTemplate::$_MultiLang_database === null)
			$database = FF_DATABASE_NAME;
		else
			$database = ffTemplate::$_MultiLang_database;
			
		if ($tpl !== null && $tpl->MultiLang_host !== null)
			$host = $tpl->MultiLang_host;
		elseif (ffTemplate::$_MultiLang_host === null)
			$host = FF_DATABASE_HOST;
		else
			$host = ffTemplate::$_MultiLang_host;
			
		if ($tpl !== null && $tpl->MultiLang_user !== null)
			$user = $tpl->MultiLang_user;
		elseif (ffTemplate::$_MultiLang_user === null)
			$user = FF_DATABASE_USER;
		else
			$user = ffTemplate::$_MultiLang_user;
			
		if ($tpl !== null && $tpl->MultiLang_password !== null)
			$password = $tpl->MultiLang_password;
		elseif (ffTemplate::$_MultiLang_password === null)
			$password = FF_DATABASE_PASSWORD;
		else
			$password = ffTemplate::$_MultiLang_password;
			
		//??? da sistemare come si deve	..
        if(!$language)
        {
            if ($tpl !== null && strlen($tpl->MultiLang_session_parameter))
                $session_parameter = $tpl->MultiLang_session_parameter;
            else
                $session_parameter = ffTemplate::$_MultiLang_session_parameter;

            if (strlen($session_parameter) && isset($_SESSION[$session_parameter])) {
                $language = $_SESSION[$session_parameter];
            }
        }
        //.. fino a qui
		if (!strlen($language))
		{
			if ($tpl !== null && $tpl->MultiLang_default !== null)
				$language = $tpl->MultiLang_default;
			elseif (ffTemplate::$_MultiLang_default !== null)
				$language = ffTemplate::$_MultiLang_default;
		}
			
		if (!strlen($language))
			$language = FF_LOCALE;
	
		if (!strlen($language))
			ffErrorHandler::raise("A DEFAULT LANGUAGE MUST BE SET WITH ACTIVE INTERNATIONALIZATION (try to define FF_LOCALE)", E_USER_ERROR, null, get_defined_vars());
	
		if ($tpl !== null && strlen($tpl->MultiLang_table_international))
			$table_international = $tpl->MultiLang_table_international;
		else
			$table_international = ffTemplate::$_MultiLang_table_international;

		if ($tpl !== null && strlen($tpl->MultiLang_table_languages))
			$table_languages = $tpl->MultiLang_table_languages;
		else
			$table_languages = ffTemplate::$_MultiLang_table_languages;

		$uLanguage = strtoupper($language);
		
        if(ffTemplate::$_MultiLang_cache) {
            $i18n_file = FF_DISK_PATH . ffTemplate::$_MultiLang_cache_path . "/" . $uLanguage . "." . FF_PHP_EXT;
            if(!isset($i18n_data[$uLanguage])) {
                if(is_file($i18n_file)) {
                    $i18n_data[$uLanguage] = include($i18n_file);
					if(!is_array($i18n_data[$uLanguage]))
                    	$i18n_data[$uLanguage] = array();                    
				}
			}
        }
			
		if(!isset($i18n_data[$uLanguage])) {
			$i18n_data[$uLanguage] = array();
		}

		if(!isset($i18n_data_key[$uLanguage])) {
			$i18n_data_key[$uLanguage] = array();
		}

        if(isset($i18n_data[$uLanguage][$code])) {
        	if(array_key_exists($code, $i18n_data_key[$uLanguage]))
        		$i18n_data_key[$uLanguage][$code] = true;

            return (ffTemplate::$_MultiLang_Hide_code && $i18n_data[$uLanguage][$code] == "{" . $code . "}"
            	? stripcslashes($code)
            	: stripcslashes($i18n_data[$uLanguage][$code])
            );
		}

        ffTemplate::$_MultiLang_db->connect($database, $host, $user, $password);

        ffTemplate::$_MultiLang_db->query("SELECT
                                " . $table_international . ".*
                            FROM
                                " . $table_international . "
                                INNER JOIN " . $table_languages . " ON
                                    " . $table_international . ".`ID_lang` = " . $table_languages . ".ID
                            WHERE
                                " . $table_languages . ".`code` = '$language'
                                AND " . $table_international . ".`word_code` =" . ffTemplate::$_MultiLang_db->toSql(new ffData($code))
                        ); 
        if(ffTemplate::$_MultiLang_db->nextRecord())
		{
            if(array_search("is_new", ffTemplate::$_MultiLang_db->fields_names) !== false && ffTemplate::$_MultiLang_db->getField("is_new", "Number", true)) 
            {
                if(ffTemplate::$_MultiLang_Hide_code)
                    $i18n_data[$uLanguage][$code] = $code;
                else
                    $i18n_data[$uLanguage][$code] = "{" . $code . "}";

                $i18n_data_key[$uLanguage][$code] = false;
            } 
            else 
            {
                $MultiLang_description = ffTemplate::$_MultiLang_db->getField("description", "Text", true);
/*                if(ffTemplate::$_MultiLang_cache)
			    {
                    ffTemplate::multilang_write_cache($i18n_file, $code, $MultiLang_description);
                }*/
                
                $i18n_data[$uLanguage][$code] = $MultiLang_description;
                $i18n_data_key[$uLanguage][$code] = true;
            }
        } 
		else 
		{       
            if(ffTemplate::$_MultiLang_Insert_code_empty) 
            {
                $sSQL = "INSERT INTO " . $table_international . "
                        (
                            `ID`
                            , `ID_lang`
                            , `word_code`
                            , `is_new`
                        )
                        VALUES
                        (
                            null
                            , IFNULL((SELECT " . $table_languages . ".`ID` FROM " . $table_languages . " WHERE " . $table_languages . ".`code` = " . ffTemplate::$_MultiLang_db->toSql($language) . " LIMIT 1), 0)
                            , " . ffTemplate::$_MultiLang_db->toSql($code) . "
                            , " . ffTemplate::$_MultiLang_db->toSql("1", "Number") . "
                        )";
                ffTemplate::$_MultiLang_db->execute($sSQL);
            }
			if(ffTemplate::$_MultiLang_Hide_code)
				$i18n_data[$uLanguage][$code] = $code;
			else
            	$i18n_data[$uLanguage][$code] = "{" . $code . "}";

            $i18n_data_key[$uLanguage][$code] = false;
		}
        
        if(ffTemplate::$_MultiLang_cache)
		{
			@mkdir(ffCommon_dirname($i18n_file), 0777, true);
   			@file_put_contents($i18n_file, "<?php\n\nreturn " . var_export($i18n_data[$uLanguage], true) . ";\n\n", LOCK_EX);
        }
        
        return $i18n_data[$uLanguage][$code];            
    }
	
	function entities_replace($text)
	{
		return str_replace(array("{\\","\\}"), array("{","}"), $text);
	}
}

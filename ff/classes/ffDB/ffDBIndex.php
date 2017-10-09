<?php
/**
 * @ignore
 * @package FormsFramework
 * @subpackage Abstract DB Index Rapresentation Class
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright &copy; 2004-2007, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

/**
 *
 * @ignore
 * @package FormsFramework
 * @subpackage Abstract DB Index Rapresentation Class
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright &copy; 2004-2007, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */
class ffDBIndex extends ffCommon
{
	var $sName		= "";
	var $iOffset 	= null;

	var $bUnique	= null;
	
	var $aFields	= array();
	
	var $pParent;
	
	// SPECIFIC DATA CONNECTION OPTIONS
	
	function __construct($sName)
	{
		$this->get_defaults();
		
		if (!strlen($sName))
			ffErrorHandler::raise("u must enter a valid name for ffDBIndex objects", E_USER_ERROR, $this, get_defined_vars());
			
		$this->sName = $sName;
	}
	
	function addField($sName, $iSeq)
	{
		if (!strlen($sName))
			ffErrorHandler::raise("u must enter a valid name for field into ffDBIndex", E_USER_ERROR, $this, get_defined_vars());
		if (!intval($iSeq))
			ffErrorHandler::raise("u must enter a valid name for field into ffDBIndex", E_USER_ERROR, $this, get_defined_vars());
		
		if (isset($this->aFields[$sName]))
			ffErrorHandler::raise("field exists into ffDBIndex", E_USER_ERROR, $this, get_defined_vars());
		
		$this->aFields[$sName]["seq"] = $iSeq;
	}
}

<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Hendrik Reimers <hendrik.reimers@web.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'TypoScript Object Select' for the 'ts_select' extension.
 *
 * @author	Hendrik Reimers <hendrik.reimers@web.de>
 * @package	TYPO3
 * @subpackage	tx_tsselect
 */
class tx_tsselect_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_tsselect_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_tsselect_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ts_select';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		$this->pi_initPIflexForm(); // Einmal am Anfang der main-Funktion
		$this->conf['selObj'] = ( strlen($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'selObj', 'sDEF')) > 0 ) ? $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'selObj', 'sDEF') : $this->conf['selObj'];
		$this->conf['selObj'] = ( $this->conf['selObj'][strlen($this->conf['selObj'])-1] == '.' ) ? substr($this->conf['selObj'],0,strlen($this->conf['selObj']) - 1) : $this->conf['selObj'];
		
		$objType  = &$this->conf['objList.'][$this->conf['selObj'].'.']['cObject'];
		$objArray = &$this->conf['objList.'][$this->conf['selObj'].'.']['cObject.'];
		
		$content = $this->parseTypoScriptObj($objType,$objArray);
		
		return $this->pi_wrapInBaseClass($content);
	}
	
	// Parsed TypoScript Objekte
	private function parseTypoScriptObj($objType,$objArray) {
		if ( (!empty($objType)) && (sizeof($objArray) > 0) ) {
			return $this->cObj->cObjGetSingle($objType,$objArray);
		} else return false;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ts_select/pi1/class.tx_tsselect_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ts_select/pi1/class.tx_tsselect_pi1.php']);
}

?>
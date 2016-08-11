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

use TYPO3\CMS\Frontend\Plugin\AbstractPlugin;

/**
 * Plugin 'TypoScript Object Select' for the 'ts_select' extension.
 *
 * @author  Hendrik Reimers <hendrik.reimers@web.de>
 * @package TYPO3
 * @subpackage  tx_tsselect
 */
class tx_tsselect_pi1 extends AbstractPlugin
{
    public $prefixId      = 'tx_tsselect_pi1';                  // Same as class name
    public $scriptRelPath = 'pi1/class.tx_tsselect_pi1.php';    // Path to this script relative to the extension dir.
    public $extKey        = 'ts_select';                        // The extension key.
    public $pi_checkCHash = true;

    /**
     * The main method of the PlugIn
     *
     * @param   string      $content: The PlugIn content
     * @param   array       $conf: The PlugIn configuration
     * @return  The content that is displayed on the website
     */
    public function main($content, $conf)
    {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();

        // TypoScript Auswahl laden
        $this->pi_initPIflexForm(); // Einmal am Anfang der main-Funktion
        if (0 < strlen($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'selObj', 'sDEF'))) {
            $this->conf['selTS'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'selObj', 'sDEF');
        }
        if ('.' == ($this->conf['selTS'][strlen($this->conf['selTS'])-1])) {
            $this->conf['selTS'] =  substr($this->conf['selTS'], 0, strlen($this->conf['selTS']) - 1);
        }

        // Zuordnung (KEY / VAL)
        $objType  = &$this->conf['objList.'][$this->conf['selTS'].'.']['cObject'];
        $objArray = &$this->conf['objList.'][$this->conf['selTS'].'.']['cObject.'];

        // Extra Auswahl laden
        $this->conf['extMode']  = $this->conf['extra.']['tsMode'];
        if (0 < strlen($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'tsMode', 'sEXTRA'))) {
            $this->conf['extMode'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'tsMode', 'sEXTRA');
        }

        $this->conf['extText'] = $this->conf['extra.']['text'];
        if (0 < strlen($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'text', 'sEXTRA'))) {
            $this->conf['extText'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'text', 'sEXTRA');
        }

        $this->conf['extObj'] = $this->conf['extra.']['object'];
        if (0 < strlen($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'object', 'sEXTRA'))) {
            $this->conf['extObj'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'object', 'sEXTRA');
        }

        $this->conf['extFile'] = $this->conf['extra.']['file'];
        if (0 < strlen($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'file', 'sEXTRA'))) {
            $this->conf['extFile'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'file', 'sEXTRA');
        }

        // ExtMgr Conf laden
        $extMgrConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ts_select']);

        // Extra Auswahl pruefen
        $cObjAr = array();
        if (($this->conf['extMode'] == 'object') || ($extMgrConf['disableExtraSelect'] == 1)) {
            if (! empty($this->conf['extObj'])) {
                // Objektdaten laden
                $dbObj = t3lib_div::makeInstance('t3lib_loadDBGroup');
                $dbObj->fromTC = 0;
                $dbObj->start($this->conf['extObj'], '*');
                $dbObj->getFromDB();

                // Aktuelle Auswahl laden
                $resultTbl = $dbObj->itemArray[0]['table'];
                $resultUid = $dbObj->itemArray[0]['id'];
                $resultArr = $dbObj->results[$resultTbl][$resultUid];

                // Ergebnis dem .field im TypoScript zuordnen
                foreach ($resultArr as $key => $val) {
                    $cObjAr[$key] = $val;
                }

                // Speicher freigeben
                unset(
                    $dbObj,
                    $resultUid,
                    $resultTbl,
                    $resultArr,
                    $key,
                    $val
                );
            }
        }
        if (($this->conf['extMode'] == 'text') || ($extMgrConf['disableExtraSelect'] == 1)) {
            $cObjAr['flex_text'] = $this->conf['extText'];
        }

        if (($this->conf['extMode'] == 'file') || ($extMgrConf['disableExtraSelect'] == 1)) {
            $cObjAr['flex_file'] = $this->conf['extFile'];
        }

        // TypoScript FIELD Startpoint setzen
        $data = $this->cObj->data; // Die original Daten zwischen speichern
        $this->cObj->start($cObjAr);

        // Mod from Net Inventors GmbH to load pages multiple times
        foreach (array_keys($GLOBALS['TSFE']->recordRegister) as $key) {
            if ('pages:' === substr($key, 0, 6)) {
                $GLOBALS['TSFE']->recordRegister[$key] = 0;
            }
        }

        // Ausfuehren
        $content = $this->parseTypoScriptObj($objType, $objArray);
        // Reset des CURRENT Wert damit die Content ID wieder eingefuegt werden kann
        $this->cObj->start($data, 'tt_content');

        // Ergebnis liefern
        return $this->pi_wrapInBaseClass($content);
    }

    // Parsed TypoScript Objekte
    private function parseTypoScriptObj($objType, $objArray)
    {
        if ((! empty($objType)) && (0 < count($objArray))) {
            return $this->cObj->cObjGetSingle($objType, $objArray);
        }
        return false;
    }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ts_select/pi1/class.tx_tsselect_pi1.php']) {
    include_once $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ts_select/pi1/class.tx_tsselect_pi1.php'];
}

?>

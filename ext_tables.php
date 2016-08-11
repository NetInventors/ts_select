<?php

if (! defined('TYPO3_MODE')) {
    die('Access denied.');
}

$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY);

\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_pi1'] = 'layout,select_key,pages,recursive';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array(
   'LLL:EXT:ts_select/locallang_db.xml:tt_content.list_type_pi1',
   $_EXTKEY . '_pi1',
    t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
), 'list_type');

$tss_confAr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ts_select']);

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
if (1 === (int) $tss_confAr['disableExtraSelect']) {
    $tss_flexForm = 'flexform_nosel.xml';
} else {
    $tss_flexForm = 'flexform.xml';
}
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $_EXTKEY . '_pi1',
    'FILE:EXT:' . $_EXTKEY . '/pi1/' . $tss_flexForm
);

include_once $extensionPath . 'lib/class.tx_tsselect_flexFormFields.php';
unset(
    $extensionPath,
    $tss_confAr,
    $tss_flexForm
);

if (TYPO3_MODE == 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_tsselect_pi1_wizicon'] = $extensionPath . 'pi1/class.tx_tsselect_pi1_wizicon.php';
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'static/ts_obj_sample/',
    'TS Select (Sample)'
);

?>

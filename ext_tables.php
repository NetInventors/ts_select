<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:ts_select/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

// ExtMgr Config laden
$tss_confAr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ts_select']);

// Flexforms einbinden
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] ='pi_flexform';
if ( $tss_confAr['disableExtraSelect'] == 1 ) {
    $tss_flexForm = 'flexform_nosel.xml';
} else $tss_flexForm = 'flexform.xml';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY . '/pi1/'.$tss_flexForm);
include_once(t3lib_extMgm::extPath($_EXTKEY).'lib/class.tx_tsselect_flexFormFields.php');
unset($tss_confAr,$tss_flexForm);

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_tsselect_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_tsselect_pi1_wizicon.php';
}

t3lib_extMgm::addStaticFile($_EXTKEY,'static/ts_obj_sample/', 'TS Select (Sample)');

?>
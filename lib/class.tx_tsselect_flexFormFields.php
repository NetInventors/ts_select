<?php

class tx_tsselect_flexFormFields {
	var $obj_TypoScript = null;
	var $pid  = null;
	var $obj_page = null;
	
	function addFields($config) {
		$bool_success = $this->init($config);
		if(!$bool_success) {
			return $config;
		}
		
		// TypoScript configuration for extension templates
		$arr_list = $this->obj_TypoScript->setup['plugin.']['tx_tsselect_pi1.']['objList.'];
		
		// Sortieren
		uksort($arr_list,create_function('$a,$b','return strcmp($a["title"], $b["title"]);'));
		
		// Ausgabe
		$optionList = array();
		$usergroup  = explode(",",$GLOBALS['BE_USER']->user['usergroup']);
		
		if ( sizeof($arr_list) > 0 ) {
			foreach ( $arr_list as $key => $data ) {
				
				$hidegroup = explode(",",$data['hideBeGroups']);
				
				if ( !$this->array_search_all($usergroup,$hidegroup) ) { # hide in backend
					$optionList[] = array(
						0 => $data['title'],
						1 => $key,
					);
				}
			}
		}
		
		$config['items'] = array_merge($config['items'],$optionList);
		
		return $config;
	}
	
	function array_search_all($needleAr,$haystackAr) {
		foreach ( $needleAr as $nkey => $nval ) {
			foreach ( $haystackAr as $hkey => $hval ) {
				if ( ($nval > 0) && ($hval > 0) ) {
					if ( $nval == $hval ) return true;
				} else continue;
			}
		}
		
		return false;
	}
	
	private function init($arr_pluginConf) {
		// Require classes
		require_once(PATH_t3lib.'class.t3lib_page.php');
		require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
		require_once(PATH_t3lib.'class.t3lib_tsparser_ext.php');
		  
		// Init page id and the page object
		$this->init_pageUid($arr_pluginConf);
		$this->init_pageObj($arr_pluginConf);
		
		// Init agregrated TypoScript
		$arr_rows_of_all_pages_inRootLine = $this->obj_page->getRootLine($this->pid);
		if (empty($arr_rows_of_all_pages_inRootLine)) {
			return false;
		}
		
		$this->init_tsObj($arr_rows_of_all_pages_inRootLine);
		
		return true;
	}
	
	private function init_pageObj($arr_pluginConf) {
		if(!empty($this->obj_page)) {
			return false;
		}
		
		// Set current page object
		$this->obj_page = t3lib_div::makeInstance('t3lib_pageSelect');
		
		return false;
	}
	
	private function init_pageUid($arr_pluginConf) {
		if(!empty($this->pid)) {
			return false;
		}
		
		// Update: Get current page id from the plugin
		$int_pid = false;
		if( $arr_pluginConf['row']['pid'] > 0 ) {
			$int_pid = $arr_pluginConf['row']['pid'];
		}
		// Update: Get current page id from the plugin
		
		// New: Get current page id from the current URL
		if(!$int_pid) {
			// Get backend URL - something like .../alt_doc.php?returnUrl=db_list.php&id%3D2926%26table%3D%26imagemode%3D1&edit[tt_content][1734]=edit
			$str_url    = $_GET['returnUrl'];
			
			// Get curent page id
			$int_pid = intval(substr($str_url, strpos($str_url, 'id=')+3));
		}
		// New: Get current page id from the current URL
		
		// Set current page id
		$this->pid      = $int_pid;
		
		return false;
	}
	
	private function init_tsObj($arr_rows_of_all_pages_inRootLine) {
		if(!empty($this->obj_TypoScript)) {
			return false;
		}
		
		$this->obj_TypoScript = t3lib_div::makeInstance('t3lib_tsparser_ext');
		$this->obj_TypoScript->tt_track = 0;
		$this->obj_TypoScript->init();
		$this->obj_TypoScript->runThroughTemplates($arr_rows_of_all_pages_inRootLine);
		$this->obj_TypoScript->generateConfig();
		
		return false;
	}
}

?>
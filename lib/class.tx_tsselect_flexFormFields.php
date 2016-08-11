<?php

class tx_tsselect_flexFormFields {
    var $obj_TypoScript = null;
    var $pid  = null;
    var $obj_page = null;

    function addFields($config) {
        $arr_list = $this->loadTS($config);
        if( empty($arr_list) ) {
            return $config;
        }

        // TypoScript configuration for extension templates
        $arr_list = $arr_list['plugin.']['tx_tsselect_pi1.']['objList.'];

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

    // Load the TypoScript Conf Array in the Backend
    private function loadTS(&$conf,$pageUid = 0) {
        if ( intval($pageUid) > 0 ) {
            $pid = intval($pageUid);
        } elseif ( intval($conf['row']['pid']) > 0 ) {
            $pid = intval($conf['row']['pid']);
        } else {
            $url = $_GET['returnUrl'];
            $pid = intval(substr($str_url, strpos($str_url, 'id=') + 3));
        }

        $ps  = t3lib_div::makeInstance('t3lib_pageSelect');

        $rootline = $ps->getRootLine($pid);
        if ( empty($rootline) ) return false;

        $tsObj = t3lib_div::makeInstance('t3lib_tsparser_ext');
        $tsObj->tt_track = 0;
        $tsObj->init();
        $tsObj->runThroughTemplates($rootline);
        $tsObj->generateConfig();

        return $tsObj->setup;
    }
}

?>

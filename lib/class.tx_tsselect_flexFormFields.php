<?php

class tx_tsselect_flexFormFields
{
    public $obj_TypoScript = null;
    public $pid            = null;
    public $obj_page       = null;

    public function addFields($config)
    {
        $arr_list = $this->loadTS($config);

        if (empty($arr_list)) {
            return $config;
        }

        // TypoScript configuration for extension templates
        $arr_list = $arr_list['plugin.']['tx_tsselect_pi1.']['objList.'];

        // Sortieren
        uasort($arr_list, create_function(
            '$a, $b',
            'return strcmp($a["title"], $b["title"]);'
        ));

        // Ausgabe
        $optionList = array();
        $usergroup  = explode(",",$GLOBALS['BE_USER']->user['usergroup']);

        if (sizeof($arr_list) > 0) {
            foreach ($arr_list as $key => $data) {
                $hidegroup = explode(",", $data['hideBeGroups']);

                if (! $this->array_search_all($usergroup,$hidegroup)) { # hide in backend
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

    public function array_search_all ($needleAr, $haystackAr)
    {
        foreach ($needleAr as $nkey => $nval) {
            foreach ($haystackAr as $hkey => $hval) {
                if (($nval > 0) && ($hval > 0)) {
                    if ($nval == $hval) {
                        return true;
                    }
                } else {
                    continue;
                }
            }
        }

        return false;
    }

    // Load the TypoScript Conf Array in the Backend
    private function loadTS(&$conf, $pageUid = 0) {
        if (intval($pageUid) > 0) {
            $pid = intval($pageUid);
        } elseif (intval($conf['row']['pid']) > 0) {
            $pid = intval($conf['row']['pid']);
        } else {
            $url = $_GET['returnUrl'];
            $pid = intval(substr($url, strpos($url, 'id=') + 3));
        }

        /** @var \TYPO3\CMS\Frontend\Page\PageRepository $ps */
        $ps       = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\CMS\Frontend\Page\PageRepository'
        );

        $rootline = $ps->getRootLine($pid);

        if (empty($rootline)) {
            return false;
        }

        /** @var \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService $tsObj */
        $tsObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\CMS\Core\TypoScript\ExtendedTemplateService'
        );

        $tsObj->tt_track = 0;
        $tsObj->init();
        $tsObj->runThroughTemplates($rootline);
        $tsObj->generateConfig();

        return $tsObj->setup;
    }
}

?>

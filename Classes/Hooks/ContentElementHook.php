<?php
namespace VK\CustomFluidStyledContent\Hooks;

use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class ContentElementHook implements ClearCacheActionsHookInterface
{
    private $startToken = "### CFSC TOKEN START ###";
    private $endToken = "### CFSC TOKEN END ###";

    /**
     * Add an entry to the CacheMenuItems array
     *
     * @param array $cacheActions Array of CacheMenuItems
     * @param array $optionValues Array of AccessConfigurations-identifiers (typically  used by userTS with options.clearCache.identifier)
     * @return
     */
    public function manipulateCacheActions(&$cacheActions, &$optionValues) {
        $cacheActions[] = array(
            'id'    => 'addContentElements',
            'title' => 'Refresh Custom Fluid Styled Content Elements',
            'href' => BackendUtility::getAjaxUrl('CustomFluidStyledContent::addContentElements'),
        );

        $optionValues[] = 'addContentElements';
    }

    /**
     * adds / copy the content to the places where TYPO3 can handle them
     */
    public function addContentElements() {
        $this->addTypoScripts('PageTS');
        $this->addTypoScripts('TypoScript');
        $this->addTca();
        $this->addSql();
    }

    /**
     * merges all content element typoscript files into one single typoscript file
     * and places it in the configuration folder. this file will be rewritten with
     * every time you renewed the ce cache
     *
     * it handles both, the pagets and the typoscript files
     *
     * @param string $folder
     */
    private function addTypoScripts($folder = 'TypoScript') {
        $ts = '';
        foreach (glob(ExtensionManagementUtility::extPath('CustomFluidStyledContent') . 'Resources/Private/ContentElements/*/'. $folder .'/*.tsc') as $file) {
            if ($file) {
                $ts .= file_get_contents($file);
            }
        }

        if ($ts != '') {
            $script = fopen(ExtensionManagementUtility::extPath('CustomFluidStyledContent') . 'Configuration/'. $folder .'/custom_fluid_styled_content.tsc', 'w');
            fwrite($script, $ts);
            fclose($script);
        }
    }

    /**
     * adds for every tca file an include to the tt_content override file
     *
     * if no tt_content.php file exists, it will create a new one based on
     * the file in the preset folder.
     *
     * it uses tokens to add the includes there. if you want to make
     * custom changes in that file, just do it right before the tokens.
     *
     */
    private function addTca() {
        if (file_exists(ExtensionManagementUtility::extPath('CustomFluidStyledContent') .'Configuration/TCA/Overrides/tt_content.php')) {
            $override = file_get_contents(ExtensionManagementUtility::extPath('CustomFluidStyledContent') .'Configuration/TCA/Overrides/tt_content.php');

            if (strpos($override, $this->startToken) !== false) {
                $start = strpos($override, $this->startToken) + strlen($this->startToken);
                $end = strpos($override, $this->endToken, $start);
                $override = trim(substr($override, 0, $start - strlen($this->startToken)));
            }

            $add = "\r\n\n";
            $add .= $this->startToken;
            $add .= "\r\n";

            foreach (glob(ExtensionManagementUtility::extPath('CustomFluidStyledContent') . 'Resources/Private/ContentElements/*/TCA/*.php') as $tca) {
                $ce = pathinfo($tca)['filename'];
                $add .= 'include_once("'. $tca .'"); # '. $ce;
                $add .= "\r\n";
            }

            $add .= $this->endToken;

            $file = fopen(ExtensionManagementUtility::extPath('CustomFluidStyledContent') .'Configuration/TCA/Overrides/tt_content.php', 'w');
            fwrite($file, $override . $add);
            fclose($file);
        }
            else {
                $newTtContentFile = file_get_contents(ExtensionManagementUtility::extPath('CustomFluidStyledContent') .'Resources/Private/Presets/tt_content.php');

                $add = "\r\n\n";
                $add .= $this->startToken;
                $add .= "\r\n";

                foreach (glob(ExtensionManagementUtility::extPath('CustomFluidStyledContent') . 'Resources/Private/ContentElements/*/TCA/*.php') as $tca) {
                    $ce = pathinfo($tca)['filename'];
                    $add .= 'include_once("'. $tca .'"); # '. $ce;
                    $add .= "\r\n";
                }

                $add .= $this->endToken;

                $file = fopen(ExtensionManagementUtility::extPath('CustomFluidStyledContent') .'Configuration/TCA/Overrides/tt_content.php', 'w');
                fwrite($file, $newTtContentFile . $add);
                fclose($file);
            }
    }

    /**
     * merges ext_tables.sql file for every sql file found in ce folder
     *
     * if no ext_tables.sql file exists, it will create a new one based on
     * the file in the preset folder.
     *
     * it uses tokens to add the includes there. if you want to make
     * custom changes in that file, just do it right before the tokens.
     *
     */
    private function addSql() {
        if (file_exists(ExtensionManagementUtility::extPath('CustomFluidStyledContent') .'ext_tables.sql')) {
            $override = file_get_contents(ExtensionManagementUtility::extPath('CustomFluidStyledContent') .'ext_tables.sql');

            if (strpos($override, $this->startToken) !== false) {
                $start = strpos($override, $this->startToken) + strlen($this->startToken);
                $end = strpos($override, $this->endToken, $start);
                $override = trim(substr($override, 0, $start - strlen($this->startToken)));
            }

            $add = "\r\n\n";
            $add .= $this->startToken;
            $add .= "\r\n";

            foreach (glob(ExtensionManagementUtility::extPath('CustomFluidStyledContent') . 'Resources/Private/ContentElements/*/SQL/*.sql') as $sql) {
                $add .= file_get_contents($sql);
            }

            $add .= $this->endToken;


            $file = fopen(ExtensionManagementUtility::extPath('CustomFluidStyledContent') .'ext_tables.sql', 'w');
            fwrite($file, $override . $add);
            fclose($file);
        }
            else {
                $newSqlFile = file_get_contents(ExtensionManagementUtility::extPath('CustomFluidStyledContent') .'Resources/Private/Presets/ext_tables.sql');

                $add = "\r\n\n";
                $add .= $this->startToken;
                $add .= "\r\n";

                foreach (glob(ExtensionManagementUtility::extPath('CustomFluidStyledContent') . 'Resources/Private/ContentElements/*/SQL/*.sql') as $sql) {
                    $add .= file_get_contents($sql);
                }

                $add .= $this->endToken;

                $file = fopen(ExtensionManagementUtility::extPath('CustomFluidStyledContent') .'ext_tables.sql', 'w');
                fwrite($file, $newSqlFile . $add);
                fclose($file);
            }
    }
}

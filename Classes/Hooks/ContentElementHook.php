<?php
namespace VK\CustomFluidStyledContent\Hooks;

use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class ContentElementHook implements ClearCacheActionsHookInterface
{
    /**
     * start token for conctenated new ts files
     * @var string
     */
    private $startToken = '### CFSC TOKEN START ###';

    /**
     * end token for conctenated new ts files
     * @var string
     */
    private $endToken = '### CFSC TOKEN END ###';

    /**
     * all public assets
     *
     * @var array
     */
    private static $assets = array();

    /**
     * Add an entry to the CacheMenuItems array
     *
     * @param array $cacheActions Array of CacheMenuItems
     * @param array $optionValues Array of AccessConfigurations-identifiers (typically  used by userTS with options.clearCache.identifier)
     * @return void
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
        $this->addAssets();
    }

    /**
     * merges all content element typoscript files into one single typoscript file
     * and places it in the configuration folder. this file will be rewritten with
     * every time you renewed the ce cache.
     *
     * in case there are no PageTS or TypoScript Folders/Files available, a Preset File
     * will be taken for generating a new PageTS or TypoScript File based on the Folder
     * Name of the Element.
     *
     * it handles both, the pagets and the typoscript files
     *
     * @param string $tsType
     */
    private function addTypoScripts($tsType = 'TypoScript') {
        $ts = '';
        foreach (glob(ExtensionManagementUtility::extPath('CustomFluidStyledContent') . 'Resources/Private/ContentElements/*') as $element) {
            $tsFolder = glob($element .'/'. $tsType .'/*');

            // A File is available.
            if (count($tsFolder) > 0) {
                foreach($tsFolder as $file) {
                    if ($file) {
                        $ts .= file_get_contents($file);
                    }
                }
            }
                // no file available, take the preset file located in presets folder
                // see preset file for possible variables, if you want to modify the
                // preset file
                //
                // it is also possible to hand over a php function for string manipulation
                // like strtolower
                else {
                    $presetTsFile = file_get_contents(ExtensionManagementUtility::extPath('CustomFluidStyledContent') .'Resources/Private/Presets/'. $tsType .'.tsc');
                    preg_match_all("{{(.*)}}", $presetTsFile, $variables);

                    foreach($variables[0] as $key => $var) {
                        if (stripos($var, '|') !== false) {
                            $modify = explode('|', str_replace(array('{{', '}}'), '', $var));
                            if (isset($modify[1]) && function_exists($modify[1])) {
                                $variables[1][$key] = call_user_func($modify[1], pathinfo($element)['basename']);
                            }
                        }
                            else {
                                $variables[1][$key] = pathinfo($element)['basename'];
                            }
                    }

                    $ts .= "\n";
                    $ts .= str_replace($variables[0], $variables[1], $presetTsFile);

                    // if type is pagets and a backend template is available
                    // set the preview file
                    if ($tsType == 'PageTS') {
                        if (file_exists($element . '/Templates/Backend/')) {
                            $backendTemplate = glob($element . '/Templates/Backend/*.html');
                            if (file_exists($backendTemplate[0])) {
                                $ts .= 'mod.web_layout.tt_content.preview.' . strtolower(pathinfo($element)['basename']) . ' = ' . str_replace($_SERVER['DOCUMENT_ROOT'] . 'typo3conf/ext/', 'EXT:', $backendTemplate[0]);
                            }
                        }
                    }

                    // add the paths variables for the typoscript file based on available
                    // templates, partials and layouts.
                    if ($tsType == 'TypoScript') {
                        $pathVariables = '';
                        if (file_exists($element . '/Templates/')) {
                            if (file_exists($element . '/Templates/Template/')) {
                                $templateFile = glob($element . '/Templates/Template/*.html');
                                if (file_exists($templateFile[0])) {
                                    $pathVariables .= 'templateName = '. pathinfo($templateFile[0])['filename'] ."\n";
                                }
                            }

                            foreach(glob($element . '/Templates/*') as $templateFolder) {
                                if (stripos($templateFolder, 'Backend') === false) {
                                    $pathVariables .= "\t\t". strtolower(pathinfo($templateFolder)['basename']) .'RootPaths.10 = '. str_replace($_SERVER['DOCUMENT_ROOT'] . 'typo3conf/ext/', 'EXT:', $templateFolder) ."/\n";
                                }
                            }
                        }

                        if ($pathVariables != '') {
                            $ts = str_replace('$pathVariables', trim($pathVariables), $ts);
                        }
                    }

                    $ts .= "\n\n";
                }
        }

        // write the new file
        // the file will be overwritten everytime you renew the content elements
        // so all individual modifyings in that file are getting lost.
        if ($ts != '') {
            $script = fopen(ExtensionManagementUtility::extPath('CustomFluidStyledContent') . 'Configuration/'. $tsType .'/custom_fluid_styled_content.tsc', 'w');
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
                $add .= "include_once(TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('CustomFluidStyledContent') .'". substr($tca, strlen(ExtensionManagementUtility::extPath('CustomFluidStyledContent'))) ."');";
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

    /**
     * copies all possible assets to Resources/Public Folder due to access restrictions
     */
    private function addAssets()
    {
        $dest = ExtensionManagementUtility::extPath('CustomFluidStyledContent') . 'Resources/Public/';

        if (file_exists($dest)) {
            foreach (glob(ExtensionManagementUtility::extPath('CustomFluidStyledContent') . 'Resources/Private/ContentElements/*/Assets/*') as $assetFolder) {
                if (is_dir($assetFolder)) {
                    if (!file_exists($dest . pathinfo($assetFolder)['basename'])) {
                        mkdir($dest . pathinfo($assetFolder)['basename']);
                    }

                    foreach(glob($assetFolder .'/*') as $file) {
                        if (is_file($file)) {
                            copy($file, $dest . pathinfo($assetFolder)['basename'] . '/' . pathinfo($file)['basename']);
                        }
                    }
                }
            }
        }
    }

    public static function getAssets() {
        $path = ExtensionManagementUtility::extPath('CustomFluidStyledContent') . 'Resources/Public/';

        if (file_exists($path)) {
            foreach (glob(ExtensionManagementUtility::extPath('CustomFluidStyledContent') . 'Resources/Private/ContentElements/*/Assets/*') as $assetFolder) {
                if (is_dir($assetFolder)) {
                    foreach(glob($assetFolder .'/*') as $file) {
                        if (is_file($file)) {
                            self::$assets[pathinfo($assetFolder)['basename']][] = $file;
                        }
                    }
                }
            }
        }

        return self::$assets;
    }
}

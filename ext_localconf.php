<?php

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function ($extKey) {

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="DIR:EXT:'. $extKey .'/Configuration/PageTS/" extensions="tsc">');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('<INCLUDE_TYPOSCRIPT: source="DIR:EXT:'. $extKey .'/Configuration/TypoScript/" extensions="tsc">');

        /**
         * show cache wizard only in dev mode
         */
        if (\TYPO3\CMS\Core\Utility\GeneralUtility::getApplicationContext()->isDevelopment() === true) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['additionalBackendItems']['cacheActions'][$extKey] = \VK\CustomFluidStyledContent\Hooks\ContentElementHook::class;

            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
                'CustomFluidStyledContent::addContentElements',
                'VK\\CustomFluidStyledContent\\Hooks\\ContentElementHook->addContentElements'
            );
        }

        // load / register assets
        if (TYPO3_MODE === 'BE') {
            $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

            // register preset icon
            $iconRegistry->registerIcon(
                'custom-fluid-styled-content-preset',
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => 'EXT:' . $extKey . '/Resources/Public/Icons/custom-fluid-styled-content-preset.svg']
            );

            // add all other icons
            foreach(\VK\CustomFluidStyledContent\Hooks\ContentElementHook::getAssets() as $asset => $files) {
                switch (strtolower($asset)) {
                    case 'icons':
                        foreach($files as $icon) {
                            switch (\GuzzleHttp\Psr7\mimetype_from_filename($icon)) {
                                case 'image/png':
                                    $iconRegistry->registerIcon(
                                        pathinfo($icon)['filename'],
                                        \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
                                        ['source' => 'EXT:' . $extKey . '/Resources/Public/Icons/' . pathinfo($icon)['basename']]
                                    );
                                    break;
                            }
                        }
                    break;
                }
            }
        }
    },
    $_EXTKEY
);

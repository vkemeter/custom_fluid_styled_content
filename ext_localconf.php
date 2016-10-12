<?php

defined('TYPO3_MODE') || die('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="DIR:EXT:CustomFluidStyledContent/Configuration/PageTS/" extensions="tsc">');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('<INCLUDE_TYPOSCRIPT: source="DIR:EXT:CustomFluidStyledContent/Configuration/TypoScript/" extensions="tsc">');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['additionalBackendItems']['cacheActions'][$_EXTKEY] = \VK\CustomFluidStyledContent\Hooks\ContentElementHook::class;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerAjaxHandler(
    'CustomFluidStyledContent::addContentElements',
    'VK\\CustomFluidStyledContent\\Hooks\\ContentElementHook->addContentElements'
);

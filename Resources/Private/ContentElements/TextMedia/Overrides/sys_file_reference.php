<?php
call_user_func(
    function() {
        $tempColumns = Array (
            "tx_cfsc_responsive" => Array (
                'exclude' => 0,
                'label' => 'Responsive Variants',
                'config' => array(
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => array(
                        array('', ''),
                        array('xs', '320px'),
                        array('sm', '544px'),
                        array('md', '768px'),
                        array('lg', '992px'),
                        array('xl', '1260px'),
                    )
                ),
            ),
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("sys_file_metadata", $tempColumns);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_file_metadata', '--div--;Responsive,tx_cfsc_responsive');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("sys_file_reference", $tempColumns);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_file_reference', 'tx_cfsc_responsive');

        $GLOBALS['TCA']['sys_file_reference']['palettes']['imageoverlayPalette']['showitem'] .= ',--linebreak--,tx_cfsc_responsive';
    }
);

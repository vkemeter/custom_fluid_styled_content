<?php
call_user_func(
    function() {
        $newColumn = [
            'test' => [
                'label' => 'Responsive Image Sizes',
                'config' => [
                    'type' => 'text',
                    'rows' => 5
                ]
            ],
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file_metadata', $newColumn);

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'sys_file_metadata',
            'test',
            'test',
            'after:title'
        );

        $GLOBALS['TCA']['sys_file_metadata']['palettes']['imageoverlayPalette']['showitem'] .= 'test';
        $GLOBALS['TCA']['sys_file_metadata']['types']['1']['showitem'] .= 'test';
        #\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS);
        #die();

    }
);

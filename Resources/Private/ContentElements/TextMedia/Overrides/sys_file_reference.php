<?php
call_user_func(
    function() {
        $tempColumns = [
            "tx_cfsc_responsive" => [
                'exclude' => 0,
                'label' => 'Responsive Variants',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        ['', ''],
                        ['xs', '320px'],
                        ['sm', '544px'],
                        ['md', '768px'],
                        ['lg', '992px'],
                        ['xl', '1260px'],
                    ]
                ],
            ],
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("sys_file_metadata", $tempColumns);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_file_metadata', '--div--;Responsive,tx_cfsc_responsive');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("sys_file_reference", $tempColumns);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_file_reference', 'tx_cfsc_responsive');

        $GLOBALS['TCA']['sys_file_reference']['palettes']['imageoverlayPalette']['showitem'] .= ',--linebreak--,tx_cfsc_responsive';

        $allowedAspectRatios = [
            '4:3' => [
                'title' => '4:3',
                'value' => 4 / 3
            ],
            '16:9' => [
                'title' => '16:9',
                'value' => 16 / 9
            ],
            'NaN' => [
                'title' => 'Free',
                'value' => 0.0
            ],
        ];

        $tca = [
            'columns' => [
                'crop' => [
                    'config' => [
                        'type' => 'imageManipulation',
                        'cropVariants' => [
                            '1260px' => [
                                'title' => 'XL',
                                'allowedAspectRatios' => $allowedAspectRatios,
                            ],
                            '992px' => [
                                'title' => 'LG',
                                'allowedAspectRatios' => $allowedAspectRatios,
                            ],
                            '768px' => [
                                'title' => 'MD',
                                'allowedAspectRatios' => $allowedAspectRatios,
                            ],
                            '544px' => [
                                'title' => 'SM',
                                'allowedAspectRatios' => $allowedAspectRatios,
                            ],
                            '320px' => [
                                'title' => 'XS',
                                'allowedAspectRatios' => $allowedAspectRatios,
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $GLOBALS['TCA']['sys_file_reference'] = array_replace_recursive($GLOBALS['TCA']['sys_file_reference'], $tca);
    }
);

<?php
call_user_func(
    function () {
        $name = 'TextMedia';

        $tca = [
            'columns' => [
                'CType' => [
                    'config' => [
                        'items' => [
                            strtolower($name) => [
                                $name,
                                $name,
                                'content-textmedia'
                            ]
                        ]
                    ]
                ]
            ],
            'types' => [
                $name => [
                    'showitem' => '
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.header;header,
                        bodytext,
                        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.media,
                        assets,
                        imageorient,
                        image_zoom,
                        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
                        hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility;visibility,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
                        --div--;Gridelements,tx_gridelements_container,tx_gridelements_columns,
                    ',
                    'columnsOverrides' => [
                        'bodytext' => [
                            'defaultExtras' => 'richtext:rte_transform'
                        ]
                    ],
                ],
            ],
        ];

        $GLOBALS['TCA']['tt_content'] = array_replace_recursive($GLOBALS['TCA']['tt_content'], $tca);

        $newColumn = [
            'tx_cfsc_responsive' => [
                'label' => 'Responsive Image Sizes',
                'config' => [
                    'type' => 'input',
                    'renderType' => 'json',
                ]
            ],
            'tx_cfsc_isgallery' => [
                'label' => 'Is Gallery',
                'config' => [
                    'type' => 'check',
                ]
            ],
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $newColumn);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'tt_content',
            '--div--;Global Image Responsive,tx_cfsc_responsive',
            'TextMedia',
            'after:image_zoom'
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            'tt_content',
            'tx_cfsc_isgallery',
            'TextMedia',
            'after:image_zoom'
        );

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][14331977591] = array(
            'nodeName' => 'json',
            'priority' => 40,
            'class' => \VK\CustomFluidStyledContent\Hooks\Backend\Element\JsonElement::class,
        );



    },
    strtolower($name)
);

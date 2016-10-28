<?php
call_user_func(
    function () {
        $name = 'Example2';

        $tca = [
            'columns' => [
                'CType' => [
                    'config' => [
                        'items' => [
                            $name => [
                                $name,
                                $name,
                                'custom-fluid-styled-content-preset'
                            ]
                        ]
                    ]
                ]
            ],
            'types' => [
                $name => [
                    'showitem' => '
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
                        header;Headline,
                        subheader;Subheadline,
                        bodytext,
                        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility;visibility,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
                        --div--;Gridelements,tx_gridelements_container,tx_gridelements_columns,
                    ',
                    'columnsOverrides' => [
                        'header' => [
                            'config' => [
                                'type' => 'text',
                                'rows' => 2,
                                'eval' => 'required'
                            ]
                        ],
                        'subheader' => [
                            'config' => [
                                'type' => 'text',
                                'rows' => 3,
                            ]
                        ],
                        'bodytext' => [
                            'defaultExtras' => 'richtext:rte_transform'
                        ]
                    ],
                ],
            ],
        ];

        $GLOBALS['TCA']['tt_content'] = array_replace_recursive($GLOBALS['TCA']['tt_content'], $tca);
    },
    $name
);

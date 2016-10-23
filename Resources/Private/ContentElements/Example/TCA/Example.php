<?php
call_user_func(
    function () {
        $tca = [
            'columns' => [
              'CType' => [
                  'config' => [
                      'items' => [
                          'example' => [
                              'Example',
                              'Example',
                              '/typo3conf/ext/CustomFluidStyledContent/ext_icon.gif'
                          ]
                      ]
                  ]
              ]
            ],
            'types' => [
                'Example' => [
                    'showitem' => '
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
                        header;Headline,
                        subheader;Sup7Header,
                        header_layout;Headline type,
                        rowDescription,
                        image,
                        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility;visibility,
                        --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
                        --div--;Gridelements,tx_gridelements_container,tx_gridelements_columns,
                    ',
                    'columnsOverrides' => [
                        'header' => [
                            'config' => [
                                'eval' => 'required'
                            ]
                        ],
                        'subheader' => [
                            'config' => [
                                'eval' => 'required'
                            ]
                        ],
                        'image' => [
                            'config' => [
                                'maxitems' => '4',
                                'minitems' => '1'
                            ]
                        ],
                    ],
                ],
            ],
        ];

        $GLOBALS['TCA']['tt_content'] = array_replace_recursive($GLOBALS['TCA']['tt_content'], $tca);

    },
    'example'
);
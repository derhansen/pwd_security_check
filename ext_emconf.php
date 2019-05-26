<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Password Security Check',
    'description' => 'Symfony Command and System Report to check BE and FE user passwords against a list of popular passwords',
    'category' => 'module',
    'author' => 'Torben Hansen',
    'author_email' => 'derhansen@gmail.com',
    'author_company' => '',
    'state' => 'stable',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'scheduler' => ''
        ],
    ],
];

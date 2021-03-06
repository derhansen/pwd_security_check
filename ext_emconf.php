<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Password Security Check',
    'description' => 'Symfony Command and System Report to check BE and FE user passwords against a list of popular passwords',
    'category' => 'be',
    'author' => 'Torben Hansen',
    'author_email' => 'derhansen@gmail.com',
    'author_company' => '',
    'state' => 'stable',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '3.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '11.2.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'scheduler' => ''
        ],
    ],
];

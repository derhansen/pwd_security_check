<?php

use Derhansen\PwdSecurityCheck\Controller\PasswordSecurityCheckController;

return [
    'system_reports_pwdsecuritycheck' => [
        'parent' => 'system_reports',
        'access' => 'admin',
        'path' => '/module/system/reports/pwdsecuritycheck',
        'iconIdentifier' => 'ext-pwdsecuritycheck-report',
        'labels' => [
            'title' => 'pwd_security_check.messages:report.title',
            'description' => 'pwd_security_check.messages:report.description',
        ],
        'routes' => [
            '_default' => [
                'target' => PasswordSecurityCheckController::class . '::handleRequest',
            ],
        ],
    ],
];
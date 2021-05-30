<?php

defined('TYPO3') or die();

call_user_func(
    function () {
        if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_pwdsecuritycheck'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_pwdsecuritycheck'] = [];
        }

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['reports']['tx_pwdsecuritycheck']['password_security_check'] =
            [
                'title' => 'LLL:EXT:pwd_security_check/Resources/Private/Language/locallang.xlf:report.title',
                'description' => 'LLL:EXT:pwd_security_check/Resources/Private/Language/locallang.xlf:report.description',
                'icon' => 'EXT:pwd_security_check/Resources/Public/Icons/report.svg',
                'report' => \Derhansen\PwdSecurityCheck\Reports\PasswordSecurityCheckReport::class
            ];
    }
);

<?php

declare(strict_types = 1);

namespace Derhansen\PwdSecurityCheck\Reports;

/*
 * This file is part of the Extension "pwd_security_check" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PwdSecurityCheck\Service\ReportDataService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Reports\ReportInterface;

/**
 * Password Security Check Report
 */
class PasswordSecurityCheckReport implements ReportInterface
{
    public function __construct(protected readonly ReportDataService $reportDataService)
    {
    }

    public function getIdentifier(): string
    {
        return 'tx_pwdsecuritycheck';
    }

    public function getTitle(): string
    {
        return 'LLL:EXT:pwd_security_check/Resources/Private/Language/locallang.xlf:report.title';
    }

    public function getDescription(): string
    {
        return 'LLL:EXT:pwd_security_check/Resources/Private/Language/locallang.xlf:report.description';
    }

    public function getIconIdentifier(): string
    {
        return 'EXT:pwd_security_check/Resources/Public/Icons/report.svg';
    }

    /**
     * This method renders the report
     */
    public function getReport(): string
    {
        $view = $this->getStandaloneView();
        $view->assignMultiple([
            'data' => $this->reportDataService->getData()
        ]);
        return $view->render();
    }

    /**
     * Returns the view used to render the report
     */
    protected function getStandaloneView(): StandaloneView
    {
        // Rendering of the output via fluid
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setPartialRootPaths(['EXT:pwd_security_check/Resources/Private/Partials/']);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
            'EXT:pwd_security_check/Resources/Private/Templates/Report.html'
        ));
        return $view;
    }
}

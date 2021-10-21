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
use TYPO3\CMS\Reports\Controller\ReportController;
use TYPO3\CMS\Reports\ReportInterface;

/**
 * Password Security Check Report
 */
class PasswordSecurityCheckReport implements ReportInterface
{
    protected ?ReportController $reportsModule;
    protected ?ReportDataService $reportDataService = null;

    /**
     * Constructor
     *
     * @param ReportController $reportsModule Back-reference to the calling reports module
     */
    public function __construct(ReportController $reportsModule)
    {
        $this->reportsModule = $reportsModule;
        $this->reportDataService = GeneralUtility::makeInstance(ReportDataService::class);
    }

    /**
     * This method renders the report
     *
     * @return string The status report as HTML
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
     *
     * @return object|StandaloneView
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException
     */
    protected function getStandaloneView(): StandaloneView
    {
        // Rendering of the output via fluid
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->getRequest()->setControllerExtensionName('PwdSecurityCheck');
        $view->setPartialRootPaths(['EXT:pwd_security_check/Resources/Private/Partials/']);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName(
            'EXT:pwd_security_check/Resources/Private/Templates/Report.html'
        ));
        return $view;
    }
}

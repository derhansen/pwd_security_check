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
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Reports\RequestAwareReportInterface;

class PasswordSecurityCheckReport implements RequestAwareReportInterface
{
    public function __construct(
        protected readonly ReportDataService $reportDataService,
        private readonly ViewFactoryInterface $viewFactory
    ) {
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

    public function getReport(?ServerRequestInterface $request = null): string
    {
        if (!$request) {
            throw new \RuntimeException('No request object provided');
        }

        $viewFactoryData = new ViewFactoryData(
            partialRootPaths: ['EXT:pwd_security_check/Resources/Private/Partials/'],
            templatePathAndFilename: 'EXT:pwd_security_check/Resources/Private/Templates/Report.html',
            request: $request,
        );
        $view = $this->viewFactory->create($viewFactoryData);
        $view->assignMultiple([
            'data' => $this->reportDataService->getData()
        ]);
        return $view->render();
    }
}

<?php

declare(strict_types = 1);

namespace Derhansen\PwdSecurityCheck\Controller;

/*
 * This file is part of the Extension "pwd_security_check" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PwdSecurityCheck\Service\ReportDataService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ComponentFactory;
use TYPO3\CMS\Backend\Template\Enum\ModuleLayout;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Localization\LanguageService;

#[AsController]
class PasswordSecurityCheckController
{
    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly ReportDataService $reportDataService,
        private readonly UriBuilder $uriBuilder,
        private readonly ComponentFactory $componentFactory,
    ) {
    }

    public function handleRequest(ServerRequestInterface $request): ResponseInterface
    {
        $languageService = $this->getLanguageService();
        $view = $this->moduleTemplateFactory->create($request);
        $view->setLayout(ModuleLayout::NORMAL);
        $view->makeDocHeaderModuleMenu();
        $view->getDocHeaderComponent()->setShortcutContext(
            'system_reports_pwdsecuritycheck',
            $languageService->translate('report.title', 'pwd_security_check.messages')
        );

        $buttonBar = $view->getDocHeaderComponent()->getButtonBar();
        $backButton = $this->componentFactory->createBackButton((string)$this->uriBuilder->buildUriFromRoute('system_reports'));
        $buttonBar->addButton($backButton);

        return $view->assignMultiple([
            'data' => $this->reportDataService->getData()
        ])->renderResponse('Report');
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}

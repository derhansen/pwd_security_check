<?php
namespace Derhansen\PwdSecurityCheck\ViewHelpers\Be;

/*
 * This file is part of the Extension "pwd_security_check" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper for a backend link that should edit the given user UID
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class EditRecordViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('uid', 'integer', 'User UID', true);
        $this->registerArgument('table', 'string', 'The table name', true);
    }

    /**
     * Renders a edit link for the given Event UID
     *
     * @return string
     */
    public function render()
    {
        $uid = $this->arguments['uid'];
        $table = $this->arguments['table'];
        $parameters = [
            'edit[' . $table . '][' . (int)$uid . ']' => 'edit',
            'returnUrl' => $this->getReturnUrl(),
        ];

        return $this->getModuleUrl($parameters);
    }

    public function getReturnUrl() : string
    {
        return GeneralUtility::getIndpEnv('REQUEST_URI');
    }

    /**
     * Returns the full module URL for the given parameters depending on the current TYPO3 version
     *
     * @param array $parameters
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     * @return string
     */
    public function getModuleUrl($parameters)
    {
        if ($this->isV9up()) {
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $uri = (string)$uriBuilder->buildUriFromRoute('record_edit', $parameters);
        } else {
            $uri = BackendUtility::getModuleUrl('record_edit', $parameters);
        }

        return $uri;
    }

    /**
     * @return bool
     */
    private function isV9up(): bool
    {
        return VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 9000000;
    }
}

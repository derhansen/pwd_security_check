<?php
declare(strict_types = 1);
namespace Derhansen\PwdSecurityCheck\Service;

/*
 * This file is part of the Extension "pwd_security_check" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service class to set and retrieve data from the TYPO3 registry
 */
class ReportDataService
{
    const REGISTRY_NAMESPACE = 'tx_pwdsecuritycheck';
    const REGISTRY_KEY = 'reportData';

    /**
     * @var Registry
     */
    protected $registry = null;

    /**
     * @var array
     */
    protected $defaultData = [
        'mode-0' => [
            'checked' => false,
            'lastCheck' => 0,
            'users' => [],
            'amountPasswords' => 0,
        ],
        'mode-1' => [
            'checked' => false,
            'lastCheck' => 0,
            'users' => [],
            'amountPasswords' => 0,
        ],
        'mode-2' => [
            'checked' => false,
            'lastCheck' => 0,
            'users' => [],
            'amountPasswords' => 0,
        ],
    ];

    /**
     * ReportDataService constructor.
     */
    public function __construct()
    {
        $this->registry = GeneralUtility::makeInstance(Registry::class);
    }

    /**
     * Returns data from the registry
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->registry->get(self::REGISTRY_NAMESPACE, self::REGISTRY_KEY, $this->defaultData);
    }

    /**
     * Sets data in the registry
     *
     * @param array $data
     * @param int $mode
     * @param int $amountPasswords
     * @throws \Exception
     */
    public function setData(array $data, int $mode, int $amountPasswords): void
    {
        $currentData = $this->getData();
        $currentData['mode-' . $mode]['checked'] = true;
        $currentData['mode-' . $mode]['users'] = $data;
        $currentData['mode-' . $mode]['lastCheck'] = new \DateTime();
        $currentData['mode-' . $mode]['amountPasswords'] = $amountPasswords;
        $this->registry->set(self::REGISTRY_NAMESPACE, self::REGISTRY_KEY, $currentData);
    }
}
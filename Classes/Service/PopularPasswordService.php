<?php

declare(strict_types=1);

namespace Derhansen\PwdSecurityCheck\Service;

/*
 * This file is part of the Extension "pwd_security_check" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class with helpers to check TYPO3 user passwords
 */
class PopularPasswordService
{
    private const MODE_BE_ADMIN = 0;
    private const MODE_BE_USERS = 1;
    private const MODE_FE_USERS = 2;
    private const TABLE_FE_USERS = 'fe_users';
    private const TABLE_BE_USERS = 'be_users';

    protected int $mode = 0;
    protected string $checkMode = 'BE';

    /**
     * PopularPasswordService constructor.
     */
    public function __construct(int $mode)
    {
        $this->mode = $mode;
        if ($mode === 2) {
            $this->checkMode = 'FE';
        }
    }

    /**
     * Returns an array with users depending on the given mode
     */
    public function getUsers(int $mode): array
    {
        switch ($mode) {
            case self::MODE_BE_ADMIN:
                $users = $this->getBackendUsers(true);
                break;
            case self::MODE_BE_USERS:
                $users = $this->getBackendUsers(false);
                break;
            case self::MODE_FE_USERS:
                $users = $this->getFrontendUsers();
                break;
            default:
                $users = [];
        }
        return $users;
    }

    /**
     * Returns an array with $amountPasswords passwords from the given password file
     */
    public function getCheckPasswords(string $filename, int $amountPasswords): array
    {
        $passwordsFile = GeneralUtility::getFileAbsFileName($filename);

        if (!file_exists($passwordsFile)) {
            throw new FileDoesNotExistException('Given Password file does not exist.', 1558846456);
        }

        $weakPasswords = file($passwordsFile, FILE_IGNORE_NEW_LINES);

        if ($amountPasswords > 0) {
            $weakPasswords = array_slice($weakPasswords, 0, $amountPasswords);
        }

        return $weakPasswords;
    }

    /**
     * Returns the hashing instance
     */
    public function getHashInstance(): ?PasswordHashInterface
    {
        $hashInstance = null;

        if (class_exists(PasswordHashFactory::class)) {
            $hashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)
                ->getDefaultHashInstance($this->checkMode);
        }

        return $hashInstance;
    }

    /**
     * Returns an array of TYPO3 backend users
     */
    protected function getBackendUsers(bool $isAdmin): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE_BE_USERS);
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder
            ->select('uid', 'username', 'password', 'disable')
            ->from(self::TABLE_BE_USERS)
            ->where(
                $queryBuilder->expr()->eq(
                    'admin',
                    $queryBuilder->createNamedParameter((int)$isAdmin, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * Returns an aray of TYPO3 frontend users
     */
    protected function getFrontendUsers(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE_FE_USERS);
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder
            ->select('uid', 'username', 'password', 'disable')
            ->from(self::TABLE_FE_USERS)
            ->executeQuery()
            ->fetchAllAssociative();
    }
}

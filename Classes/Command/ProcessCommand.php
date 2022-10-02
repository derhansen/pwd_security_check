<?php

declare(strict_types = 1);

namespace Derhansen\PwdSecurityCheck\Command;

/*
 * This file is part of the Extension "pwd_security_check" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Derhansen\PwdSecurityCheck\Service\PopularPasswordService;
use Derhansen\PwdSecurityCheck\Service\ReportDataService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Command to check the password of TYPO3 backend or frontend users against a given list of popular passwords
 */
class ProcessCommand extends Command
{
    protected array $allowedModes = [0, 1, 2];

    /**
     * Configuring the command options
     */
    public function configure()
    {
        $this
            ->addArgument(
                'mode',
                InputArgument::REQUIRED,
                'Mode (0 = Backend Admin Users, 1 = Backend Users, 2 = Frontend Users)'
            )
            ->addArgument(
                'recipients',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'E-Mail addresses to receive notification separated by space.'
            )
            ->addOption(
                'amountPasswords',
                'a',
                InputOption::VALUE_OPTIONAL,
                'Amount of passwords to check from passwords file. Warning: Higher value = longer check.',
                100
            )
            ->addOption(
                'passwordFile',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Absolute path to password file (EXT: notation allowed)',
                'EXT:pwd_security_check/Resources/Private/Text/popular_passwords.txt'
            );
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ProgressBar::setFormatDefinition('custom', ' %current%/%max% -- %message%');
        $usersWithMatchingPasswords = [];

        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $mode = (int)$input->getArgument('mode');
        if (!in_array($mode, $this->allowedModes, true)) {
            $mode = 0;
        }

        $amountPasswords = (int)$input->getOption('amountPasswords');
        $passwordFile = $input->getOption('passwordFile');
        $recipients = $input->getArgument('recipients');

        $popularPasswordService = GeneralUtility::makeInstance(PopularPasswordService::class, $mode);
        $reportDataService = GeneralUtility::makeInstance(ReportDataService::class);
        $checkPasswords = $popularPasswordService->getCheckPasswords($passwordFile, $amountPasswords);

        $users = $popularPasswordService->getUsers($mode);
        $usersCount = count($users);

        $this->printCommandHeader($io, $mode, $amountPasswords);

        if ($usersCount > 0) {
            foreach ($users as $user) {
                $io->newLine();
                $passwordsProgressbar = new ProgressBar($io, count($checkPasswords));
                $passwordsProgressbar->setFormat('custom');
                $passwordsProgressbar->setMessage('Checking user: ' . $user['username']);
                $passwordsProgressbar->start();

                $hashInstance = $popularPasswordService->getHashInstance();

                if ($hashInstance) {
                    $passwordMatch = false;

                    foreach ($checkPasswords as $checkPassword) {
                        if ($hashInstance->checkPassword($checkPassword, $user['password'])) {
                            unset($user['password']);
                            $usersWithMatchingPasswords[] = $user;
                            $passwordMatch = true;
                            break;
                        }
                        $passwordsProgressbar->advance();
                    }

                    if ($passwordMatch) {
                        $passwordsProgressbar->setMessage('<error>Match for user: ' . $user['username'] . '</error>');
                    } else {
                        $passwordsProgressbar->setMessage('<info>No match for user: ' . $user['username'] . '</info>');
                    }

                    $passwordsProgressbar->finish();
                } else {
                    $passwordsProgressbar->setMessage(
                        '<warning>Unknown Hash Instance: ' . $user['username'] . '</warning>'
                    );
                }
            }
        }

        // Save result to registry
        $reportDataService->setData($usersWithMatchingPasswords, $mode, $amountPasswords);

        $usersFound = count($usersWithMatchingPasswords) > 0;
        if ($usersFound) {
            $io->newLine();
            $io->newLine();
            $io->writeln(
                '<info>The following users have a password that is found in the configured passwords file:</info>'
            );
            $this->printResultTable($io, $usersWithMatchingPasswords);
        }

        if ($usersFound && count($recipients) > 0) {
            $this->sendResultMail($recipients, $usersWithMatchingPasswords, $mode);
        }

        if ($usersFound) {
            $io->error('Users with matching password found!');
        } else {
            $io->success('All done!');
        }
        
        return 0;
    }

    /**
     * Prints the result as table
     *
     * @param SymfonyStyle $io
     * @param array $users
     */
    protected function printResultTable(SymfonyStyle $io, array $users): void
    {
        $io->newLine();
        $table = new Table($io);
        $table
            ->setHeaders(['UID', 'Username', 'Disabled'])
            ->setRows($users)
        ;
        $table->render();
    }

    /**
     * Prints the command header
     *
     * @param SymfonyStyle $io
     * @param int $mode
     * @param int $amountPasswords
     */
    protected function printCommandHeader(SymfonyStyle $io, int $mode, int $amountPasswords): void
    {
        $title = 'Mode: Checking TYPO3 Backend Admin Users';
        switch ($mode) {
            case 1:
                $title = 'Mode: Checking TYPO3 Backend Users';
                break;
            case 2:
                $title = 'Mode: Checking TYPO3 Frontend Users';
                break;
        }
        $io->writeln('<comment>' . $title . '</comment>');
        $io->newLine();
        $io->writeln('<info>Checking Top ' . $amountPasswords . ' Popular Passwords from password file</info>');
    }

    /**
     * Sents a notification email to the given list of recipients
     *
     * @param array $recipients
     * @param array $users
     * @param int $mode
     */
    protected function sendResultMail(array $recipients, array $users, int $mode): void
    {
        switch ($mode) {
            case 1:
                $userType = 'Backend';
                break;
            case 2:
                $userType = 'Frontend';
                break;
            default:
                $userType = 'Backend Admins';
        }
        $message = 'The following ' . $userType . ' users have a password found in the popular password file:';
        $message .= CRLF . CRLF;
        $subject = 'Password Check results for ' . ($GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] ?? '');

        foreach ($users as $user) {
            $message .= $user['username'] . ' (uid: ' . $user['uid'] . ')';
            $message .= CRLF . CRLF;
        }

        /** @var MailMessage $mail */
        $mail = GeneralUtility::makeInstance(MailMessage::class);
        $mail->setTo($recipients);
        $mail->setSubject($subject);
        $mail->text($message);
        $mail->send();
    }
}

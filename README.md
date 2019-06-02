Password Security Check for TYPO3
=================================

## What is it?

Since it is not possible out of the box in TYPO3 to define password complexity rules of Backend and Frontend users, 
some users may choose very easy passwords for their user accounts. Especially for TYPO3 Backend Admin accounts, this 
can be dangerous, since it will be more easy for attackers using brute force techniques to get access to the TYPO3 
backend in this case.

This extension can help to get an overview or to get notified about TYPO3 Backend or Frontend users, who use a password 
that is found in a given list of popular passwords. The extension ships with a list of 10.000 most popular passwords 
used, but you can also use your own list (e.g. list with top passwords in local language).  

## Screenshots

### Console Command

![Console Command](/Documentation/Images/command.gif)

### TYPO3 Report

![Report](/Documentation/Images/report.png)

## Installation

### Installation using Composer

The recommended way to install the extension is by using [Composer](https://getcomposer.org/). 
In your Composer based TYPO3 project root, just do `composer require derhansen/pwd_security_check`. 

### Installation as extension from TYPO3 Extension Repository (TER)

Download and install the extension with the TYPO3 extension manager module.

## Usage

Please note, that the check can take a lot of time to finish. This depends primary on the amount of users and the 
amount of passwords to check.  

### CLI Arguments and Options

Command: `bin/typo3 pwd_security_check:process`

Get Help: `bin/typo3 help pwd_security_check:process`

Command arguments:

* `mode` Mode (0 = Backend Admin Users, 1 = Backend Users, 2 = Frontend Users)
* `recipients` E-Mail addresses to receive notification separated by space.

Command options:

* `-a` Amount of passwords to check from passwords file. Warning: Higher value = longer check. [default: 100]
* `-f` Absolute path to password file (EXT: notation allowed) [default: "EXT:pwd_security_check/Resources/Private/Text/popular_passwords.txt"]

### TYPO3 Scheduler Support

The Symfony Command can also be executed using the TYPO3 scheduler. Note, that arguments can only be configured in 
TYPO3 9.5 and that options are currently not configurable using the TYPO3 scheduler.

## FAQ

**Is this a hacker tool?**

No, at least it is not meant to be one. Therefore matched passwords are not displayed. Also, it is not very 
worthwhile to use this tool and try to bruteforce crack a TYPO3 account password, as it does not support parallel
checks and the task may take hours/days/weeks/years. 

## Feedback and updates

The extension is hosted on GitHub. Please report feedback, bugs and changerequests directly at 
https://github.com/derhansen/pwd_security_check

## Credits

### Password file

The included file with top 10.000 popular passwords has been downloaded from https://github.com/danielmiessler/SecLists
    
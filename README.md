# Neovim php 7 plugin host

[![Build Status](https://travis-ci.org/Xerkus/neovim_php7_host.svg?branch=master)](https://travis-ci.org/Xerkus/neovim_php7_host)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Xerkus/neovim_php7_host/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Xerkus/neovim_php7_host/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Xerkus/neovim_php7_host/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Xerkus/neovim_php7_host/?branch=master)


Prototype for php 7 pthreads based plugin host for neovim. Experimental, do not use.

# Dependencies

This project uses pthreads and requires thread safe php version to run. That
means for composer and phpunit too.

Use composer to install dependencies `zts-php path/to/composer.phar install`.

Run tests with `zts-php vendor/bin/phpunit`

# Coding styles

Project conforms to PSR-2 coding style guide with few stricter rules:

- Each php file must declare `strict_types=1`
- Scalar typehints must be used where applicable.
- Return types must be declared where applicable.

Coding styles are enforced with PHP_CodeSniffer. Checks can be run with
`vendor/bin/phpcs` and some violations automatically fixed with
`vendor/bin/phpcbf`


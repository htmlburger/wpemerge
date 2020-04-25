# WP Emerge Unit Tests

## Initial Setup

Install WordPress and the WP Unit Test lib using the `install.sh` script. Change to the framework root directory and type:

    $ tests/bin/install.sh <db-name> <db-user> <db-password> [db-host] [wp-version]

Sample usage:

    $ tests/bin/install.sh wpemerge_tests root root localhost 4.8

**Important**: Make sure that the `<db-name>` database exists and it contains no information. All data inside it will be removed during testing!

## Running Tests

1. Install PHPUnit globally using `composer require phpunit/phpunit`.
2. Run `composer run test:unit` in the root directory of the framework.

Refer to the [phpunit command line test runner reference](https://phpunit.com/manual/current/en/phpunit-book.html#textui) for more information and command line options.

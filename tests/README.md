# Obsidian Unit Tests

## Initial Setup

Install WordPress and the WP Unit Test lib using the `install.sh` script. Change to the framework root directory and type:

    $ tests/bin/install.sh <db-name> <db-user> <db-password> [db-host] [wp-version]

Sample usage:

    $ tests/bin/install.sh obsidian_tests root root localhost 4.8

**Important**: Make sure that the `<db-name>` database has been created. Note that all data will be removed during testing.

## Running Tests

Simply change to the framework root directory and type:

    $ ./vendor/bin/phpunit

Refer to the [phpunit command line test runner reference](https://phpunit.de/manual/current/en/phpunit-book.html#textui) for more information and command line options.

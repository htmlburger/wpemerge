# How to contribute

WP Emerge is completely open source and we encourage everybody to participate by:

- ⭐ the project on GitHub (https://github.com/htmlburger/wpemerge)
- Posting bug reports (https://github.com/htmlburger/wpemerge/issues)
- (Emailing security issues to [hi@atanas.dev](mailto:hi@atanas.dev) instead)
- Posting feature suggestions (https://github.com/htmlburger/wpemerge/issues)
- Posting and/or answering questions (https://github.com/htmlburger/wpemerge/issues)
- Submitting pull requests (https://github.com/htmlburger/wpemerge/pulls)
- Sharing your excitement about WP Emerge with your community

## Development setup

1. Fork this repository.
1. Open up your theme directory in your terminal of choice.
1. Clone your fork e.g. `git clone git@github.com:your-username/wpemerge.git wpemerge`.
1. Run `cd wpemerge/ && composer install`.
1. Run `mkdir ../wpemerge-dev && cd ../wpemerge-dev`.
1. Run `printf '<?php\n' > web.php && printf '<?php\n' > admin.php && printf '<?php\n' > ajax.php`.
1. Open up your theme's `functions.php` file in your editor and add the following lines at the top:
    ```php
    use WPEmerge\Facades\WPEmerge;

    require_once( 'wpemerge/vendor/autoload.php' );

    add_action( 'init', function() {
        session_start(); // required only if you use Flash and OldInput
    } );

    add_action( 'after_setup_theme', function() {
        WPEmerge::bootstrap( [
            'routes'              => [
                'web'   => __DIR__ . '/wpemerge-dev/web.php',
                'admin' => __DIR__ . '/wpemerge-dev/admin.php',
                'ajax'  => __DIR__ . '/wpemerge-dev/ajax.php',
            ],
        ] );
    } );
    ```
1. To make sure everything is running correctly, open up the new `wpemerge-dev/web.php` file and add this:
    ```php
    <?php
    use WPEmerge\Facades\Route;

    Route::get()
        ->url( '/' )
        ->handle( function () {
            return WPEmerge\output( 'Hello World!' );
        } );
    ```
1. Now open up your site's homepage and if everything is setup correctly it should read `Hello World!`.

## Running tests

To setup and run tests for WP Emerge, follow the steps outlined in `tests/README.md`.

## Pull Requests

- Pull request branches MUST follow this format: `{issue-number}-{short-description}`.
  Example: `12345-fix-route-condition`
- Pull requests MUST target the `master` branch
- Pull requests MUST NOT break unit tests
- Pull requests MUST follow the current code style
- Pull requests SHOULD include unit tests for new code/features

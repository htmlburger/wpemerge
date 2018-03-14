# Quickstart

1. Run `composer require htmlburger/wpemerge` in your theme directory
1. Make sure you've included the generated `autoload.php` file inside your `functions.php` file
    ```php
    require_once( 'vendor/autoload.php' );
    ```
1. Add the following to your `functions.php`:
    ```php
    add_action( 'init', function() {
        session_start(); // required only if you use Flash and OldInput
    } );

    add_action( 'after_setup_theme', function() {
        WPEmerge::boot();

        Router::get( '/', function() {
            return app_output( 'Hello World!' );
        } );
    } );
    ```

## Optional: Setting up autoloading for your own classes

1. Add the following to your `composer.json`:
    ```json
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    }
    ```
    - `App` represents the base namespace for your classes
    - `app/` represents the base path for your classes relative to your theme (i.e. `twentyseventeen/app`)

    With this change any class in the `App\` namespace will be autoloaded from the `app/` directory relative to your `composer.json`.
1. Run `composer dumpautoload` so your changes take effect

Here are a few example classes (and their filepaths) that will be autoloaded:

| Class                    | File                       |
|--------------------------|----------------------------|
| `App\MyClass`            | `app/MyClass.php`          |
| `App\Foo\Bar\Baz`        | `app/Foo/Bar/Baz.php`      |
| `App\Controllers\Home`   | `app/Controllers/Home.php` |


You can find more information about PSR-4 autoloading on http://www.php-fig.org/psr/psr-4/

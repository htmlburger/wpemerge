# Оbsidian [![Build Status](https://scrutinizer-ci.com/g/htmlburger/obsidian/badges/build.png?b=master)](https://scrutinizer-ci.com/g/htmlburger/obsidian/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/htmlburger/obsidian/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/htmlburger/obsidian/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/htmlburger/obsidian/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/htmlburger/obsidian/?branch=master)

A micro framework which modernizes WordPress as a CMS development by providing tools to implement MVC and more.

Integration is incremental - you can use it only on the pages you want or on all pages.

If you've used Laravel, Slim or Symfony and miss the control over the request&response flow in WordPress - Obsidian is definitely for you.

## Documentation

https://htmlburger.gitbooks.io/obsidian/content/

## Features

- Routes with optional rewrite rule integration
    ```php
    Router::get( '/', 'HomeController@index' );
    Router::get( '/custom', 'HomeController@custom' )
        ->rewrite( 'index.php?...' );
    ```
- __Real__ Controllers (not ViewModels)
    ```php
    class HomeController {
        public function index( $request ) {
            $name = $request->get( 'name' );
            return obs_template( 'templates/home.php', [
                'welcome' => 'Welcome, ' . $name . '!',
            ] );
        }
    }
    ```
- [PSR-7](http://www.php-fig.org/psr/psr-7/) Responses (using [Guzzle/Psr7](https://github.com/guzzle/psr7))
    ```php
    class HomeController {
        public function index( $request ) {
            return obs_response()
                ->withHeader( 'X-Custom-Header', 'foo' );
        }
    }
    ```
- Middleware
    ```php
    Router::get( '/', 'HomeController@index' )
        ->add( function( $request, $next ) {
            // perform action before
            $response = $next( $request );
            // perform action after
            return $response;
        } );
    ```
- Service container (using [Pimple](https://pimple.symfony.com/))
    ```php
    $container = \Obsidian\Framework::getContainer();
    $container['my_service'] = function() {
        return new MyService();
    };
    ```
- Service providers
    ```php
    class MyServiceProvider implements ServiceProviderInterface {
        public function register( $container ) {
            $container['my_service'] = function() {
                return new MyService();
            };
        }

        public function boot( $container ) {
            // bootstrap code
            // e.g. add hooks, actions etc.
        }
    }
    ```
- Custom template engine support (Twig and Blade available as add-on packages)
    ```php
    $container = \Obsidian\Framework::getContainer();
    $container['framework.templating.engine'] = function( $container ) {
        return new MyTemplateEngine();
    };
    ```

## Quickstart

1. Run `composer require htmlburger/obsidian` in your theme directory
1. Make sure you've included the generated `autoload.php` file inside your `functions.php` file
    ```php
    require_once( 'vendor/autoload.php' );
    ```
1. Add the following to your `functions.php`:
    ```php
    add_action( 'init', function() {
        session_start(); // required for Flash and OldInput
    } );

    add_action( 'after_setup_theme', function() {
        \Obsidian\Framework::boot();

        Router::get( '/', function() {
            return obs_output( 'Hello World!' );
        } );
    } );
    ```

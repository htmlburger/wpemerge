# <a href="http://wpemerge.com"><img src="https://raw.githubusercontent.com/htmlburger/wpemerge/master/docs/assets/logo-bar.png" height="60" alt="WP Emerge Logo" aria-label='WPEmerge.com' /></a>

[![Packagist](https://img.shields.io/packagist/vpre/htmlburger/wpemerge.svg?style=flat-square&colorB=0366d6)](https://packagist.org/packages/htmlburger/wpemerge) [![Travis branch](https://img.shields.io/travis/htmlburger/wpemerge/master.svg?style=flat-square)](https://travis-ci.org/htmlburger/wpemerge/builds) [![Scrutinizer](https://img.shields.io/scrutinizer/g/htmlburger/wpemerge.svg?style=flat-square)](https://scrutinizer-ci.com/g/htmlburger/wpemerge/) [![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/htmlburger/wpemerge.svg?style=flat-square)](https://scrutinizer-ci.com/g/htmlburger/wpemerge/code-structure/master/code-coverage) [![Gitter](https://img.shields.io/gitter/room/nwjs/nw.js.svg?style=flat-square&colorB=7d07d1)](https://gitter.im/wpemerge/Lobby)

📦 A micro framework which modernizes WordPress as a CMS development by providing tools to implement MVC and more.

🔥 Integration is incremental - you can use it only on the pages you want or on all pages.

❤ If you've used frameworks such as Laravel, Slim or Symfony you will love WP Emerge.

🚀 Also, make sure you check out the [WP Emerge Starter Theme](https://docs.theme.wpemerge.com/) project.

## Requirements

- PHP >= 5.5
- WordPress >= 4.7
- Composer

## Features & Documentation

[https://docs.wpemerge.com/](https://docs.wpemerge.com/)

[https://docs.wpemerge.com/quickstart.html](https://docs.wpemerge.com/quickstart.html)

## API Reference

[https://api.wpemerge.com/](https://api.wpemerge.com/)

## Development Team

Brought to you by [Atanas Angelov](https://github.com/atanas-angelov-dev) and the lovely folks at [htmlBurger](http://htmlburger.com).

## Comparison Table ¹ ²

|  | WPEmerge | Sage | Timber |
| --- | --- | --- | --- |
| View Engine | PHP, Blade, Twig, Custom | PHP, Blade | Twig |
| Routing | ✔ | ✖ | ✖ |
| MVC | ✖✔✔ | ✖✔✖³ | ✖✔✖ |
| Middleware | ✔ | ✖ | ✖ |
| View Composers | ✔ | ✔/✖⁴ | ✖ |
| Service Container | ✔ | ✖ | ✖ |

_¹ We are comparing frameworks and themes - style, build tools etc. are not mentioned. For a full comparison check out the [WP Emerge Starter Theme](https://docs.theme.wpemerge.com/)._

_² WP Emerge is theme agnostic - you can use it in any theme._

_³ Sage's Controller is more of a View Composer than a Controller._

_⁴ Sage's Controller provides similar functionality but is limited to 1 composer (controller) per view and vice versa._

_Email any factual inaccuracies to [atanas.angelov.dev@gmail.com](mailto:atanas.angelov.dev@gmail.com) so they can be corrected._ 

## Features

{% method -%}
#### Routes with optional rewrite rule integration

- Enables the use of controllers to compartmentalize your business logic away from your presentation.
- Use existing routes or add new ones with a rewrite.
- Use built-in dynamic route conditions or define your own custom ones.
- Use anonymous functions for quick one-off conditions.

{% sample lang="php" -%}
```php
Router::get( '/', 'HomeController@index' );

Router::get( '/custom', 'CustomController@custom' )
    ->rewrite( 'index.php?...' );
    
Router::get( ['post_id', get_option('page_on_front')], 'HomeController@index' );

Router::get( function() {
    return is_front_page();
}, 'HomeController@index' );
```
{% endmethod %}

{% method -%}
#### Controllers

- Separate unrelated business logic into controllers, and related business logic into controller methods.
- Receive an object representing the current request and respond with a PSR-7 response.
- Use different methods for different routes.
- Respond with a view, json, a redirect etc.
- Easy to test.

{% sample lang="php" -%}
```php
class HomeController {
    public function index( $request ) {
        $name = $request->get( 'name' );
        return app_view( 'templates/home.php' )
            ->with( [
                'welcome' => 'Welcome, ' . $name . '!',
            ] );
    }
}
```
{% endmethod %}

{% method -%}
#### Middleware

- Hook before and/or after route handlers (e.g. controller methods).
- Add globally or to specific routes or route groups.
- Powers features such as Flash and OldInput.

{% sample lang="php" -%}
```php
Router::get( '/', 'HomeController@index' )
    ->add( function( $request, $next ) {
        // perform action before
        $response = $next( $request );
        // perform action after
        return $response;
    } );
```
{% endmethod %}

{% method -%}
#### [PSR-7](http://www.php-fig.org/psr/psr-7/) Responses

- Use PSR-7 objects for your responses.
- Easy to stream and modify before outputting.
- Uses Guzzle's implementation - [read more](https://github.com/guzzle/psr7).

{% sample lang="php" -%}
```php
class HomeController {
    public function index( $request ) {
        return app_response()
            ->withHeader( 'X-Custom-Header', 'foo' );
    }
}
```
{% endmethod %}

{% method -%}
#### View Composers

- Pass generic context to partials regardless of which controller or parent view uses them.
- Work with any View engine (Php, Blade, Twig).

{% sample lang="php" -%}
```php
View::addComposer( 'templates/about-us', function( $view ) {
    $view->with( ['hello' => 'world'] );
} );
```
{% endmethod %}

{% method -%}
#### Service container

- Define your dependencies in a service container.
- Override any and all WP Emerge dependencies when needed.
- Enables dependency injection.
- Uses Pimple - [read more](https://pimple.symfony.com/).

{% sample lang="php" -%}
```php
// getContainer() used for brevity's sake - use a Service Provider instead.
$container = WPEmerge::getContainer();
$container['my_service'] = function( $container ) {
    return new MyService( $container['my_dependency'] );
};
```
{% endmethod %}

{% method -%}
#### Service providers

- Register dependencies into the service container and boot them, if needed.
- Enables to split your dependencies logically into separate providers.
- WP Emerge's own dependencies are provided via Service providers.

{% sample lang="php" -%}
```php
class MyServiceProvider implements ServiceProviderInterface {
    public function register( $container ) {
        $container['my_service'] = function( $container ) {
            return new MyService( $container['my_dependency'] );
        };
    }

    public function boot( $container ) {
        // bootstrap code if needed
    }
}
```
{% endmethod %}

{% method -%}
#### Custom view engine support

- Replace the view engine used in the service container.
- Blade and Twig available as add-on packages.
- You can even write your own view engine and use it seamlessly.

{% sample lang="php" -%}
```php
// getContainer() used for brevity's sake - use a Service Provider instead.
$container = WPEmerge::getContainer();
$container[ WPEMERGE_VIEW_ENGINE_KEY ] = function( $container ) {
    return new MyViewEngine();
};
```
{% endmethod %}

## Contributing

WP Emerge is completely open source and we encourage everybody to participate by:

- ⭐ the project on GitHub \([https://github.com/htmlburger/wpemerge](https://github.com/htmlburger/wpemerge)\)
- Posting bug reports \([https://github.com/htmlburger/wpemerge/issues](https://github.com/htmlburger/wpemerge/issues)\)
- (Emailing security issues to [atanas.angelov.dev@gmail.com](mailto:atanas.angelov.dev@gmail.com) instead)
- Posting feature suggestions \([https://github.com/htmlburger/wpemerge/issues](https://github.com/htmlburger/wpemerge/issues)\)
- Posting and/or answering questions \([https://github.com/htmlburger/wpemerge/issues](https://github.com/htmlburger/wpemerge/issues)\)
- Submitting pull requests \([https://github.com/htmlburger/wpemerge/pulls](https://github.com/htmlburger/wpemerge/pulls)\)
- Sharing your excitement about WP Emerge with your community

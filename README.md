# Оbsidian [![Build Status](https://scrutinizer-ci.com/g/htmlburger/obsidian/badges/build.png?b=master)](https://scrutinizer-ci.com/g/htmlburger/obsidian/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/htmlburger/obsidian/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/htmlburger/obsidian/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/htmlburger/obsidian/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/htmlburger/obsidian/?branch=master)

Obsidian is a micro framework for WordPress which provides tools for *VC and routing.

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

## Optional: Setting up autoloading for your own classes

1. Add the following to your `composer.json`:
    ```json
    "autoload": {
        "psr-4": {
            "Theme\\": "theme/"
        }
    }
    ```
    - `Theme` represents the base namespace for your classes
    - `theme/` represents the base path for your classes
    With this change any class in the `Theme\` namespace will be autoloaded from the `theme/` directory relative to your `composer.json`.
1. Run `composer dumpautoload` so your changes take effect

Here are a few example classes (and their filepaths) that will be autoloaded:
- `Theme\MyClass` - `theme/MyClass.php`
- `Theme\Foo\Bar\Baz` - `theme/Foo/Bar/Baz.php`
- `Theme\Controllers\Home` - `theme/Controllers/Home.php`

You can find more information about PSR-4 autoloading on http://www.php-fig.org/psr/psr-4/

## Routing

### Route method

The method you call on the router when you start a route definitions defines which requestmethod the route will match

```php
Router::[get|post|put|patch|delete|options|any]( $target, $handler );
```

If you wish to match a specific set of methods you can also use the generic `Router::route()` method:

```php
Router::route( ['GET', 'HEAD', 'POST'], $target, $handler );
```

### Route conditions

#### URL

If you wish to match against a specific path:

```php
Route::get( '/foo/bar/', $handler );
```

If you wish to have parameters in the path:

```php
Route::get( '/foo/{param1}/bar/{param2?}/baz/{param3:\d+}/{param4?:\d+}', function( $request, $template, $param1, $param2, $param3, $param4 ) {
    // ...
} );
```

- `param1` - required, matches everything
- `param2` - optional, matches everything
- `param3` - required, matches a custom regex
- `param4` - optional, matches a custom regex

_Parameter values are passed as arguments to the handler method._

If you wish to add a rewrite rule for your route (if it does not match any predefined rewrite rule):

```php
Route::get( '/foo/bar/', $handler )
    ->rewrite( 'index.php' ); // see https://codex.wordpress.org/Rewrite_API/add_rewrite_rule
```

If you wish to match __any__ url:

```php
Route::get( '*', $handler );
```

#### Post ID

Matches against the current post id:

```php
Route::get( ['post_id', 10], $handler );
```

#### Post slug

Matches against the current post slug:

```php
Route::get( ['post_slug', 'about-us'], $handler );
```

#### Post template

Matches against the current post template:

```php
Route::get( ['post_template', 'templates/contact-us.php'], $handler );
```

#### Post type

Matches against the current post type:

```php
Route::get( ['post_type', 'crb_product'], $handler );
```

#### Has query var

Matches when a specified query var is present (any value is accepted):

```php
Route::get( ['has_query_var', 's'], $handler );
```

This is especially useful when dealing with custom endpoints ([add_rewrite_endpoint()](https://codex.wordpress.org/Rewrite_API/add_rewrite_endpoint)):

```php
add_action( 'init', function() {
    add_rewrite_endpoint( 'my_custom_endpoint', EP_PAGES ); // remember to refresh your rewrite rules!
} );

...

Route::get( ['has_query_var', 'my_custom_endpoint'], $handler );
```

When combined with the post template condition, you can create pages that optionally receive additional parameters in the url without using query arguments:

```php
add_action( 'init', function() {
    add_rewrite_endpoint( 'secret', EP_PAGES ); // remember to refresh your rewrite rules!
} );

...

Route::get( [
    ['post_template', 'templates/page-with-secret.php'],
    ['has_query_var', 'secret'],
], $handler );
```

#### Query var

Similar to the previous one, but this time match the query var to a specific value:

```php
Route::get( ['query_var', 'some_query_var_name', 'some_query_var_value'], $handler );
```

#### Custom

The custom condition allows you to add a callable which must return a boolean (whether the route has matched the current request or not):

_Note: when using the array syntax, adding `'custom'` literally is optional and all examples will not use it for simplicity._

```php
Route::get( function() {
    $my_condition = true; // your custom code here
    return $my_condition;
}, $handler );
```

You can also pass parameters to use built-in callables, for example:

```php
Route::get( ['is_tax', 'crb_custom_taxonomy'], $handler );
```

Any parameters you pass will be provided to both the callable AND the $handler:

```php
Route::get( ['is_tax', 'crb_custom_taxonomy'], function( $request, $template, $taxonomy ) {
    // $taxonomy is passed after $request and $tempalte which are always passed to handlers
} );
```

This works with closures as well, which can be used to reduce duplication:

```php
Route::get( [function( $foo, $bar ) {
    // $foo and $bar are available here
    return true;
}, 'foo', 'bar'], function( $request, $template, $foo, $bar ) {
    // ... and here!
} );
// you may notice this use-case is a bit hard to read - exact same usage is not advisable
```

#### Multiple

The multiple condition allows you to specify an array of conditions which must ALL match:

```php
Route::get( ['multiple', [
    ['is_tax', 'crb_custom_taxonomy'],
    [function() {
        return true;
    }],
]], $handler );
```

The syntax can also be simplified by directly passing an array of conditions:

```php
Route::get( [
    ['is_tax', 'crb_custom_taxonomy'],
    [function() {
        return true;
    }],
], $handler );
```

### Route groups

You can group URL-based routes into nested groups which will share the group url as a prefix:

```php
Route::group( '/foo/', function( $group ) {
    $group->get( '/bar/', $handler ); // will match '/foo/bar/'
    $group->get( '/baz/', $handler ); // will match '/foo/baz/'
} );
```

### Route handlers

A route handler can be any callable or a reference in the `CONTROLLER_CLASS@CONTROLLER_METHOD` format. For example:

```php
Router::get( '/', 'HomeController@index' );
```

... will create a new instance of the `HomeController` class and call its `index` method.

If your controller class is registered in the service container with its class name as the key, then the class will be resolved
from the service container instead of directly being instantiated:

```php
$container = \Obsidian\Framework::getContainer();
$container[ HomeController::class ] = function() {
    // your custom instantiation code here, e.g.:
    return new HomeController();
}
```

Refer to the Controllers section for more info on route handlers.

### Route middleware

Middleware allow you to modify the request and/or response before and/or after it reaches the route handler. Middleware can be any callable or the class name of a class that implement `MiddlewareInterface` (see `src/Middleware/MiddlewareInterface`).

A common example for middleware usage is protecting certain routes to be accessible by logged in users only:

```php
class AuthenticationMiddleware implements \Obsidian\Middleware\MiddlewareInterface {
    public function handle( $request, Closure $next ) {
        if ( ! is_user_logged_in() ) {
            return obs_redirect( wp_login_url() );
        }
        return $next( $request );
    }
}

Router::get( '/protected-url/')
    ->add( AuthenticationMiddleware::class );
```

You can also define global middleware which is applied to all defined routes when booting the framework:

```php
\Obsidian\Framework::boot( [
    'global_middleware' => [
        AuthenticationMiddleware::class
    ]
] );
```

_Note: global middleware is only applied on defined routes - normal WordPress requests that do not match any route will NOT have middleware applied. To apply global middleware to all requests add this route definition after all your route definitions:_

```php
Router::get( '*' );
```

This route defintion will match any url (i.e. any request) and not specifying a handler means that it will be handled as any normal WordPress request. Since all requests are matched this will also apply global middleware to all requests.

## Controllers

A controller can be any class and any method of that class can be used as a route handler.

Route handlers have a couple of requirements:

1. Must receive at least 2 arguments
    1. `$request` - an object representing the current request to the server
    1. `$template` - the template filepath WordPress is currently attempting to load
    1. You may have additional arguments depending on the route condition(s) you are using (e.g. URL parameters, custom condition arguments etc.)
1. Must return one the following:
    1. Any `string` which will be output literally
    1. Any `array` which will be output as a JSON response
    1. an object implementing the `Psr\Http\Message\ResponseInterface` interface.

To return a suitable response object you can use one of the built-in utility functions:

```php
class MyController {
    public function someHandlerMethod( $request, $template ) {
        return obs_template( 'templates/about-us.php' );
        return obs_redirect( home_url( '/' ) );
        return obs_reload();
        return obs_error( 404 );
        return obs_response(); // a blank response object
        return obs_output( 'Hello World!' ); // same as returning a string
        return obs_json( ['foo' => 'bar'] ); // same as returning an array
    }
}
```

Since all of the above functions return an object implementing the `ResponseInterface` interface, you can use immutable chain calls to modify the response, e.g. changing the status:

```php
class MyController {
    public function someHandlerMethod( $request, $template ) {
        return obs_template( 'templates/about-us.php' )->withStatus( 201 );
    }
}
```

### obs_output( $output );

Returns a new response object with the supplied string as the body.

### obs_template( $templates, $context = [] );

Uses `locate_template( $templates )` to resolve a template and applies the template output as the response body.
Optionally, a context array can be supplied to be used from inside the template.

### obs_json( $data );

Returns a new response object json encoding the passed data as the body.

### obs_redirect( $url, $status = 302 );

Returns a new response object with location and status headers to redirect the user.

### obs_reload( $request, $status = 302 );

Returns a new response object with location and status headers to force the user to reload the current URL.
Useful when responding to POST requests and when you want to force the user to request the same URL using the GET request method (e.g. to show the same form the user has submitted but with error messages).

### obs_error( $status );

Returns a new response object with the supplied status code. Additionally, attempts to render a suitable `{$status}.php` template file.

### obs_response();

Returns a blank response object.

## Flash

TODO

## OldInput

TODO

## Service Providers

TODO

## Templating

Obsidian comes with a default template engine built-in - `\Obsidian\Templating\Php`.
This template engine uses `extract()` for the template context and then includes the template file.
The resulting output is then passed as the rendered template string.

Implementing your own or a third-party engine is simple and straightforward - there are only a couple requirements:
1. Your class must implement the `\Obsidian\Templating\EngineInterface` interface
    ```php
    class MyCustomTemplateEngine implements \Obsidian\Templating\EngineInterface {
        ...
    }
    ```
1. You must replace the built-in engine in the service container:
    ```php
    // ... somewhere after \Obsidian\Framework::boot()
    $container = \Obsidian\Framework::getContainer();
    $container['framework.templating.engine'] = function() {
        return new MyCustomTemplateEngine();
    };
    ```

### External template engines

#### ObsidianTwig

Renders your templates using Twig: https://github.com/htmlburger/obsidian-twig

#### ObsidianBlade

Renders your templates using Blade: https://github.com/htmlburger/obsidian-blade

### Other built-in template engines

#### FilenameProxy

Obsidian also comes with a small utility template engine which delegates template rendering to other engines depending on the template's filename suffix.
The main use-case for it is to allow you to use multiple template engines so you can migrate to a new one on a template-by-template basis instead of forcing you to rewrite all of your templates.

Replacing the default template engine:
```php
$container = \Obsidian\Framework::getContainer();
$container['framework.templating.engine'] = function( $container ) {
    return new \Obsidian\Templating\FilenameProxy( [
        // filename suffix => service container key for alternative engine
        '.twig.php' => 'obsidian_twig.templating.engine',
        '.blade.php' => 'obsidian_blade.templating.engine',
    ] );
};
```
_Note: the example above assumes you have included both ObsidianTwig and ObsidianBlade. `obsidian_twig.templating.engine` and `obsidian_blade.templating.engine` are not provided by default._

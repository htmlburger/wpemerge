# Controllers

A controller can be any class and any method of that class can be used as a route handler.

## Requirements

Route handlers have a couple of requirements:

1. Must receive at least 2 arguments
    1. `$request` - an object representing the current request to the server
    1. `$view` - the view filepath WordPress is currently attempting to load
    1. You may have additional arguments depending on the route condition(s) you are using (e.g. URL parameters, custom condition arguments etc.)
1. Must return one the following:
    1. Any `string` which will be output literally
    1. Any `array` which will be output as a JSON response
    1. an object implementing the `Psr\Http\Message\ResponseInterface` interface.
1. Can optionally throw exceptions. Make sure you catch these exceptions in your `ErrorHandler` and translate them to responses so that visitors are not greeted with blank pages or stack traces. An example exception that is handled for you by default is the `\WPEmerge\Exceptions\NotFoundException` exception which is translated to a 404 response.

## Instantiation

{% method -%}
By default, WP Emerge will instantiate your controller class and call the specified method.
However, if your controller class is registered in the service container with its class name as the key, then the class will be resolved from the service container instead of being directly instantiated:

{% sample lang="php" -%}
```php
// getContainer() used for brevity's sake - use a Service Provider instead.
$container = WPEmerge::getContainer();
$container[ HomeController::class ] = function( $container ) {
    // your custom instantiation code here, e.g.:
    return new HomeController();
}
```
{% endmethod %}

## Response Objects

{% method -%}
To return a suitable response object you can use one of the built-in utility functions:

{% sample lang="php" -%}
```php
class MyController {
    public function someHandlerMethod( $request, $view ) {
        return app_view( 'templates/about-us.php' );
        return app_redirect()->to( home_url( '/' ) );
        return app_error( 404 );
        return app_output( 'Hello World!' ); // same as returning a string
        return app_json( ['foo' => 'bar'] ); // same as returning an array
        return app_response(); // a blank response object
    }
}
```
{% endmethod %}

{% method -%}
Since all of the above functions return an object implementing the `ResponseInterface` interface, you can use immutable chain calls to customize the response, e.g. changing the status:

{% sample lang="php" -%}
```php
class MyController {
    public function someHandlerMethod( $request, $view ) {
        return app_view( 'templates/about-us.php' )->withStatus( 201 );
    }
}
```
{% endmethod %}

### `app_output( $output );`

Returns a new response object with the supplied string as the body.

### `app_view( $views );`

By default, uses `locate_template( $views )` to resolve a view and applies the view output as the response body.

Optionally, you can pass context values to be used from inside the view by chaining `->with( ['foo' => 'bar'] )`.

### `app_json( $data );`

Returns a new response object json encoding the passed data as the body.

### `app_redirect()->to( $url, $status = 302 );`

Returns a new response object with location and status headers to redirect the user.

### `app_redirect()->back( $fallback, $status = 302 );`

Returns a new response object with location and status headers to redirect the user. By default it will use the `Referer` request header. If one is not specified, it will use the supplied `$fallback` as the url. If neither is specified it will use the current request url instead.

### `app_error( $status );`

Returns a new response object with the supplied status code. Additionally, attempts to render a suitable `{$status}.php` view file.

### `app_response();`

Returns a blank response object.

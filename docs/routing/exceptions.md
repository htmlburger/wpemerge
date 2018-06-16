# Error Handling

WP Emerge comes with a basic `ErrorHandler` class which is used to translate errors and exceptions to response objects.
In addition, the [filp/whoops](https://github.com/filp/whoops) package is included which provides you with a beautiful and interactive stack trace interface whenever an error occurs in debug mode:
![filp/whoops](https://camo.githubusercontent.com/31a4e1410e740fd0ccda128cbcab8723f45e7e73/687474703a2f2f692e696d6775722e636f6d2f305651706539362e706e67)

## Customization

Often it is a good idea is to extend the base `ErrorHandler` class and use it to translate common exceptions to responses - this way you will have handy shortcuts in controller methods. For example, you can throw a custom `ValidationException` and then translate it to a redirect responses with flashed error messages to handle invalid form requests.

{% method -%}
The default `ErrorHandler` class comes with automatic handling for the built-in `\WPEmerge\Exceptions\NotFoundException` exception. When thrown it will be translated to a 404 response by setting the status header to 404 and using the `404.php` view:

{% sample lang="php" -%}
```php
use WPEmerge\Exceptions\NotFoundException;

// ...

public function index( $request, $view ) {
    // ...
    if ( ! $some_entity ) {
        throw new NotFoundException();
    }
    // ...
}
```
{% endmethod %}

{% method -%}
To use a custom `ErrorHandler` class, replace the `WPEMERGE_EXCEPTIONS_ERROR_HANDLER_KEY` key in the service container with a class that implements the `\WPEmerge\Exceptions\ErrorHandlerInterface` interface:
{% sample lang="php" -%}
```php
// getContainer() used for brevity's sake - use a Service Provider instead.
$container = WPEmerge::getContainer();
$container[ WPEMERGE_EXCEPTIONS_ERROR_HANDLER_KEY ] = function() {
    return new MyCustomErrorHandler();
};
```
{% endmethod %}

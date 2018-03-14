# Exceptions

WP Emerge comes with a simple `ExceptionHandler` class which is used to translate exceptions which are thrown during the execution of a route or its middleware to response objects.
This functionality simplifies controller logic as you can throw certain exceptions as a short-cut to common responses.

{% method -%}
For example, the default `ExceptionHandler` class comes with automatic handling for the built-in `\WPEmerge\Exceptions\NotFoundException` exception. Such exceptions will be translated to 404 responses by setting the status header to 404 and using the `404.php` view:

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
To use a custom `ExceptionHandler` class, replace the `WPEMERGE_EXCEPTIONS_EXCEPTION_HANDLER_KEY` key in the service container with a class that implements the `\WPEmerge\Exceptions\ExceptionHandlerInterface` interface:
{% sample lang="php" -%}
```php
$container = WPEmerge::getContainer();
$container[ WPEMERGE_EXCEPTIONS_EXCEPTION_HANDLER_KEY ] = function() {
    return new MyCustomExceptionHandler();
};
```
{% endmethod %}

# Route Handlers

{% method -%}
A route handler can be an anonymous function or a reference in the `CONTROLLER_CLASS@CONTROLLER_METHOD` format.

The shown example will create a new instance of the `HomeController` class and call its `index` method.

Refer to the [Controllers](../controllers/overview.md) section for more information on route handlers.

{% sample lang="php" -%}
```php
Router::get( '/', 'HomeController@index' );

Router::get( '/', function( $request, $view ) {
    return app_view( $view );
} );
```
{% endmethod %}
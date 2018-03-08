# Route Groups

{% method -%}
You can group URL-based routes into nested groups which will share the group url as a prefix as well as any middleware assigned to the group:

{% sample lang="php" -%}
```php
Route::group( '/foo/', function( $group ) {
    $group->get( '/bar/', $handler ); // will match '/foo/bar/'
    $group->get( '/baz/', $handler ); // will match '/foo/baz/'
} );

Route::group( '/foo/', function( $group ) {
    $group->get( '/bar/', $handler );
} )->add( MyMiddleware::class );

```
{% endmethod %}

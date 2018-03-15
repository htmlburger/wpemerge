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

{% method -%}
You can also group routes with dynamic conditions which will share the group condition(s) as well as any middleware assigned to the group:

{% sample lang="php" -%}
```php
Route::group( ['post_type', 'product'], function( $group ) {
    $group->get( ['query_var', 'quickview'], $handler ); // will match any post of type 'product' when there is a query var 'quickview' used to access it
} );
```
{% endmethod %}

Mixing URL-based and dynamic conditions is not supported at this time and can lead to unexpected results.
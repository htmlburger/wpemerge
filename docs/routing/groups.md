# Route Groups

{% method -%}
You can group routes which will share the group condition as well as any middleware assigned to the group:

{% sample lang="php" -%}
```php
Route::group( '/foo/', function( $group ) {
    $group->group( '/bar/', function( $group ) {
        // Match if '/foo/bar/' is the full path:
        $group->get( '/', $handler );

        // Match if '/foo/bar/baz/' is the full path:
        $group->get( '/baz/', $handler );
    } );
    
    // Match if '/foo/' is the full path
    // AND the query var 'my_query_var' is present:
    $group->get( ['query_var', 'my_query_var'], $handler );
} )->add( MyMiddleware::class );
```
{% endmethod %}

Note that URL conditions will be concatenated as long as they are directly one after the other and are in a chain which starts from a root group. The following examples will NOT concatenate URL conditions:

{% sample lang="php" -%}
```php
// Root condition is not a URL:
Route::group( ['post_id', 1], function( $group ) {
    // Match if the current query loads the single post with id of 1
    // AND '/foo/' is the full path
    // -> this usage is fine so far
    $group->group( '/foo/', function( $group ) {
        // Match if the current query loads the single post with id of 1
        // AND '/foo/' is the full path
        // AND '/bar/' is the full path
        // -> The above will always fail since both '/foo/' and '/bar/' are required
        // -> to be the full path as we've started with a non-URL root condition
        $group->get( '/bar/', $handler );
    } );
} );

// Root condition is a URL:
Route::group( '/foo/', function( $group ) {
    // Match if '/foo/' is the full path
    // AND the current query loads the single post with id of 1
    // -> this usage is fine, but note that we are breaking the URL chain
    $group->group( ['post_id', 1], function( $group ) {
        // Match if '/foo/' is the full path
        // AND the current query loads the single post with id of 1
        // AND '/bar/' is the full path
        // -> the above will always fail since both '/foo/' and '/bar/' are required
        // -> to be the full path as we've broken the concatenation chain
        $group->get( '/bar/', $handler );
    } );
} );
```
{% endmethod %}
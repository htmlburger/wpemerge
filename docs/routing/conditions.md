# Route Conditions

## URL

{% method -%}
Match against a specific path:

_Note: Paths in URL conditions are relative to the site's home url._

{% sample lang="php" -%}
```php
Route::get( '/foo/bar/', $handler );
```
{% endmethod %}

{% method -%}
Use parameters in the path:

- `param1` - required, matches everything
- `param2` - optional, matches everything
- `param3` - required, matches a custom regex
- `param4` - optional, matches a custom regex

_Parameter values are passed as arguments to the handler method._

{% sample lang="php" -%}
```php
Route::get(
    '/foo/{param1}/bar/{param2?}/baz/{param3:\d+}/{param4?:\d+}',
    function( $request, $view, $param1, $param2, $param3, $param4 ) {
        // ...
    }
);
```
{% endmethod %}

{% method -%}
Add a rewrite rule for your route (if it does not match any predefined rewrite rule):

_Note: Remember to refresh your rewrite rules after this change._

_Note: See https://codex.wordpress.org/Rewrite_API/add_rewrite_rule for more details._

{% sample lang="php" -%}
```php
Route::get( '/foo/bar/', $handler )
    ->rewrite( 'index.php' );
```
{% endmethod %}

{% method -%}
Match __any__ url:

_Note: `Router::handleAll()` uses this internally._

{% sample lang="php" -%}
```php
Route::get( '*', $handler );
```
{% endmethod %}

## Post ID

{% method -%}
Match against the current post id:

{% sample lang="php" -%}
```php
Route::get( ['post_id', 10], $handler );
```
{% endmethod %}

## Post slug

{% method -%}
Match against the current post slug:

{% sample lang="php" -%}
```php
Route::get( ['post_slug', 'about-us'], $handler );
```
{% endmethod %}

## Post template

{% method -%}
Match against the current post template:

{% sample lang="php" -%}
```php
Route::get( ['post_template', 'templates/contact-us.php'], $handler );
```
{% endmethod %}

## Post status

{% method -%}
Match against the current post status:

{% sample lang="php" -%}
```php
Route::get( ['post_status', 'publish'], $handler );
```
{% endmethod %}

## Post type

{% method -%}
Match against the current post type:

{% sample lang="php" -%}
```php
Route::get( ['post_type', 'product'], $handler );
```
{% endmethod %}

## Query var

{% method -%}
Match when a specified query var is present (any value is accepted):

{% sample lang="php" -%}
```php
Route::get( ['query_var', 's'], $handler );
```
{% endmethod %}

{% method -%}
This is especially useful when dealing with custom endpoints ([add_rewrite_endpoint()](https://codex.wordpress.org/Rewrite_API/add_rewrite_endpoint)):

{% sample lang="php" -%}
```php
add_action( 'init', function() {
    // remember to refresh your rewrite rules!
    add_rewrite_endpoint( 'my_custom_endpoint', EP_PAGES );
} );

// ...

Route::get( ['query_var', 'my_custom_endpoint'], $handler );
```
{% endmethod %}

{% method -%}
When combined with the post template condition, you can create pages that optionally receive additional parameters in the url using clean url "/sections/" instead of query arguments:

{% sample lang="php" -%}
```php
add_action( 'init', function() {
    // remember to refresh your rewrite rules!
    add_rewrite_endpoint( 'secret', EP_PAGES );
} );

...

Route::get( [
    ['post_template', 'templates/page-with-secret.php'],
    ['query_var', 'secret'],
], $handler );
```
{% endmethod %}

{% method -%}
You can match with a specific value of the query var as well:

{% sample lang="php" -%}
```php
Route::get( ['query_var', 'some_query_var_name', 'some_query_var_value'], $handler );
```
{% endmethod %}

## Custom

{% method -%}
The custom condition allows you to add a callable which must return a boolean (whether the route has matched the current request or not):

_Note: when using the array syntax, adding `'custom'` literally is optional and all examples will not use it for simplicity._

{% sample lang="php" -%}
```php
Route::get( function() {
    $my_condition = true; // your custom code here
    return $my_condition;
}, $handler );
```

{% endmethod %}

{% method -%}
You can also pass parameters to use built-in callables:

{% sample lang="php" -%}
```php
Route::get( ['is_tax', 'app_custom_taxonomy'], $handler );
```
{% endmethod %}

{% method -%}
Any parameters you pass will be provided to both the callable AND the $handler:

{% sample lang="php" -%}
```php
Route::get( ['is_tax', 'app_custom_taxonomy'], function( $request, $view, $taxonomy ) {
    // $taxonomy is passed after $request and $view which are always passed to handlers
} );
```
{% endmethod %}

{% method -%}
This works with closures as well, which can be used to reduce duplication:

{% sample lang="php" -%}
```php
Route::get( [function( $foo, $bar ) {
    // $foo and $bar are available here
    return true;
}, 'foo', 'bar'], function( $request, $view, $foo, $bar ) {
    // ... and here!
} );
// you may notice this use-case is a bit hard to read - exact same usage is not advisable
```
{% endmethod %}

## Multiple

{% method -%}
The multiple condition allows you to specify an array of conditions which must ALL match:

{% sample lang="php" -%}
```php
Route::get( ['multiple', [
    ['is_tax', 'app_custom_taxonomy'],
    [function() {
        return true;
    }],
]], $handler );
```
{% endmethod %}

The multiple condition will also pass ALL arguments that its child conditions provide to the handler following the child conditions definition order.

{% method -%}
The syntax can also be simplified by directly passing an array of conditions:

{% sample lang="php" -%}
```php
Route::get( [
    ['is_tax', 'app_custom_taxonomy'],
    [function() {
        return true;
    }],
], $handler );
```
{% endmethod %}

## Negate

{% method -%}
The negate condition allows you to negate another condition's result. The following example will match any request as long as it is not for the singular view of the post with id of 3:

_Note: The negate condition will also pass whatever arguments its child condition passes to the handler._

{% sample lang="php" -%}
```php
Router::get( ['!post_id', 3], $handler ); // notice the exclamation mark
```
{% endmethod %}

{% method -%}
Since not all conditions are defined using strings, here's the full syntax which you can use for any condition:

{% sample lang="php" -%}
```php
Router::get( ['negate', 'post_id', 3], $handler );
```
{% endmethod %}

{% method -%}
You can also use it with any of the simplified syntaxes of other conditions:

{% sample lang="php" -%}
```php
Router::get( ['negate', function() {
    return false;
], $handler );

Router::get( ['negate', [
    ['is_tax', 'app_custom_taxonomy'],
    [function() {
        return true;
    }],
], $handler );
```
{% endmethod %}

## Extending Conditions

You can define your own custom condition classes that you can reuse throughout your project. For more information on how to do this please refer to the [Extending](../extending/overview.md) section.

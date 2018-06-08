# View Composers

View composers are closures, class names or class methods (`'CLASS_NAME@METHOD_NAME'` format) that prepare a context for a view whenever it is rendered.

__Default View Engine WARNING:__ Due to the nature of how the default view engine works, you __MUST__ render partials using `app_partial()` instead of `get_template_part()` in order to support composition.
If you wish to compose core partials (e.g. `header.php`, `footer.php`) that are rendered using a `get_*()` function call (e.g. `get_header()`) you will have to use `app_partial( 'name' )` (e.g. `app_partial( 'header' )`) instead.
More information on how to use `app_partial()` is available at the end of this article.

## Example

In this example we want to pass the latest posts to the `latest-news.php` partial:
```php
// immediately after WPEmerge::boot()
View::addComposer( 'templates/partials/latest-news', function( $view ) {
	return [
		'news' => new WP_Query( [
			'posts_per_page' => 3,
		] ),
	];
} );
```

With this, whenever the `latest-news.php` partial is loaded, we will have the `$news` variable automatically available:
```php
// latest-news.php
<?php while ( $news->have_posts() ) : $news->the_post() ?>
	...
<?php endwhile; ?>
<?php wp_reset_postdata(); ?>
```

Here's the same example, but using a class:

```php
class LatestNewsViewComposer {
    public function compose( $view ) {
        return [
            'news' => new WP_Query( [
                'posts_per_page' => 3,
            ] ),
        ];
    }
} );
```

```php
// immediately after WPEmerge::boot()
View::addComposer( 'templates/partials/latest-news', LatestNewsViewComposer::class );
```

The expected method name by default is `compose` but you can use a custom one as well:
```php
View::addComposer( 'templates/partials/latest-news', 'LatestNewsViewComposer@customMethodName' );
```

By default, WP Emerge will instantiate your class directly. However, if your class is registered in the service container with its class name as the key, then the class will be resolved from the service container instead of being directly instantiated:

```php
$container = WPEmerge::getContainer();
$container[ LatestNewsViewComposer::class ] = function( $container ) {
    // your custom instantiation code here, e.g.:
    return new LatestNewsViewComposer();
}
```

## app_partial( $views, $context = [] )

Why you would use `app_partial()`:

1. You are using the default default view engine. You do not need it when using Blade or Twig, for example, as they have composition built-in.
1. Partials rendered using `include`, `require`, `get_template_part()` etc. __DO NOT__ support composition, `app_partial()` does.
1. `app_partial()` optionally provides context to the partial through the `$context` parameter.

For example, instead of using
```php
<?php get_template_part( 'latest-news' ); ?>
```
you would use
```php
<?php app_partial( 'latest-news' ); ?>
```
or instead of using
```php
<?php get_template_part( 'post', $post_type ); ?>
```
you would use
```php
<?php app_partial( ['post-' . $post_type, 'post'] ); ?>
```

If you do not need composition or context for a given partial, feel free to use `get_template_part()` instead.

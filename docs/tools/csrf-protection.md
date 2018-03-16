# CSRF Protection

WP Emerge comes with an experimental CSRF protection middleware (disabled by default) which employs WordPress nonces.

The middleware will check for a CSRF token in the following places:
- `__wpemergeCsrfToken` in `$_GET`.
- `__wpemergeCsrfToken` in `$_POST`.
- The `X-CSRF-TOKEN` header.

If the middleware cannot find a valid token in a non-read request (e.g. `POST`, `PUT`, `PATCH`, `DELETE`) it will show the default WordPress ["Are you sure?"](https://codex.wordpress.org/Function_Reference/wp_nonce_ays) screen.

## Adding the middleware

Here's how to add the middleware to a route:
```php
Route::get( ... )
    ->add( \WPEmerge\Csrf\CsrfMiddleware::class );
```

... or to add it globally:
```php
WPEmerge::boot( [
    ...
    'global_middleware' => [
        ...
        \WPEmerge\Csrf\CsrfMiddleware::class,
        ...
    ],
    ...
] );
```

## Using the token

To output a hidden CSRF token field to your forms:
```html
<form>
    <?php Csrf::field(); ?>
</form>
```

To add the token to a url:
```php
$url = Csrf::url( $url );
```

To get the token directly:
```php
$token = Csrf::getToken();
```
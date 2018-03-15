# Route Method

The method you call on the router when you start a route definition defines which request method the route will match

```php
Router::[get|post|put|patch|delete|options|any]( $condition, $handler );
```

If you wish to match a specific set of methods you can also use the generic `Router::route()` method:

```php
Router::route( ['GET', 'HEAD', 'POST'], $condition, $handler );
```

## Handling all requests

By default, WP Emerge will only handle the requests which match its routes so you can implement it only where you need to (even on legacy projects). However, you can also handle all requests if you wish to do so:
```php
// Add this AFTER all of your route definitions
Router::handleAll();
```

Adding this will initially seem like it makes no difference because WP Emerge will make sure requests produce the same results as normal WordPress requests would, however, there are a couple notable differences:

1. Global middleware will be applied to ALL requests (since this declaration will match all requests)
1. All views will be rendered using WP Emerge's current view engine. By default there will be no difference, however, if you use a different view engine all WordPress templates will be rendered through it.

    _Check out [NameProxy](../view/overview.md#nameproxy) if you wish to have mixed views._

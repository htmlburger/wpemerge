# Global Context

You can pass variables to all views and partials by adding them as globals:

```php
// immediately after WPEmerge::boot()

// add one variable
View::addGlobal( 'foo', 'bar' );

// add many variables
View::addGlobals( [
    'name' => get_bloginfo( 'name' ),
    'url' => home_url( '/' ),
] );
```

Then, to use them in a view:
```php
<?php echo $global['foo']; ?>
<?php echo $global['name']; ?>
<?php echo $global['url']; ?>
```


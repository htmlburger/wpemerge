# Extending

WP Emerge uses a service container for all of it's dependencies which means that you can easily replace or add dependencies.

For a real-world example, we will be adding our own custom routing condition:

1. First, we will define our custom condition class:
    ```php
    class MyCondition implements \WPEmerge\Routing\Conditions\ConditionInterface {
        public function isSatisfied( \WPEmerge\Requests\Request $request ) {
            // your custom logic whether this route condition is satisfied
            return true;
        }

        public function getArguments( \WPEmerge\Requests\Request $request ) {
            // return an array of arguments you wish to pass on to the route handler
            // for convenience
            return [];
        }
    }
    ```

1. Next, we will define a service provider class which will register our new condition:
    ```php
    class MyConditionServiceProvider implements \WPEmerge\ServiceProviders\ServiceProviderInterface {
        public function register( $container ) {
            // conditions are registered by appending them to the
            // array of condition types which is registered with
            // the WPEMERGE_ROUTING_CONDITION_TYPES_KEY key
            // in the container
            $condition_name = 'my_condition';
            $condition_class = MyCondition::class;
            
            $container[ WPEMERGE_ROUTING_CONDITION_TYPES_KEY ] = array_merge(
                $container[ WPEMERGE_ROUTING_CONDITION_TYPES_KEY ],
                [
                    $condition_name => $condition_class,
                ]
            );
        }

        public function boot( $container ) {
            // nothing to boot
        }
    }
    ```

1. Finally, we pass our brand new service provider while booting WP Emerge:
    ```php
    WPEmerge::boot( [
        'providers' => [
            MyConditionServiceProvider::class,
            // ... other providers go here
        ],
        // ... other options go here
    ] );
    ```

We can now use our custom route condition like so:
```php
Router::get( ['my_condition'], function( $request ) {
    return 'Hello World!';
} );
```

Since our condition always returns true, open up any page of your site and you will be greeted with `Hello World!`.

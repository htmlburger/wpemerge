# Configuration

When booting WPEmerge you have to option to specify a number of configuration options:
```php
WPEmerge::boot( [
    // Array of classes that implement \WPEmerge\ServiceProviders\ServiceProviderInterface
    'providers' => [
        // Examples:
        MyServiceProviders::class,
        WPEmergeBlade\View\ServiceProvider::class, // see htmlburger/wpemerge-blade
    ],

    // Array of middleware to apply to all routes    
    'global_middleware' => [
        // Examples:
        MyMiddleware::class,
        function( $request, $next ) {
            $response = $next( $request );
            // minify response here, for example
            return $response;
        },
    ],
    
    // Array of global middleware priority to change execution order
    'global_middleware_priority' => [
        // Examples:
        MyMiddleware::class => 90,
        MySecondMiddleware::class => 110,
    ],
    
    // Default global middleware priority
    'global_middleware_default_priority' => 100,
] )
```

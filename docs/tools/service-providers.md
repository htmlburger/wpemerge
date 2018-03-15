# Service Providers

A service provider is a class that registers and bootstraps services into the WP Emerge service container.
We __highly suggest__ you write simple service provider classes to define your dependencies instead of using `WPEmerge::getContainer()` (the documentation uses it for examples as it's simpler).

WP Emerge itself uses service providers to register and bootstrap most of its functionality, for example:
- `\WPEmerge\Flash\FlashServiceProvider`
- `\WPEmerge\Routing\RoutingServiceProvider`

The external Twig and Blade view implementations also use service providers to add their respective view engines:
- https://github.com/htmlburger/wpemerge-twig - `\WPEmergeTwig\View\ServiceProvider`
- https://github.com/htmlburger/wpemerge-blade - `\WPEmergeBlade\View\ServiceProvider`

Here's how to register a service provider with WP Emerge:
```php
WPEmerge::boot( [
    'providers' => [
        SomeServiceProvider::class,
        SomeOtherServiceProvider::class,
        // ...
    ],
    // ... other boot options here
] );
```

Take a look at the [Extending](../extending/overview.md) section for a real-world usage example.

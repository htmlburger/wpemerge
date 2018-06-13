# View

WP Emerge comes with a default view engine built-in - `\WPEmerge\View\PhpViewEngine`.
This view engine uses `extract()` for the view context and then includes the view file. The resulting output is then passed as the rendered view string.
Essentially, this engine loads views in the same way WordPress does, but with the added feature of context variable passing.

Implementing your own or a third-party engine is simple and straightforward - there are only a couple requirements:

1. Your class must implement the `\WPEmerge\View\ViewEngineInterface` interface
    ```php
    class MyCustomViewEngine implements \WPEmerge\View\ViewEngineInterface {
        ...
    }
    ```
1. You must replace the built-in engine in the service container:
    ```php
    // ... somewhere after WPEmerge::boot()
    // getContainer() used for brevity's sake - use a Service Provider instead.
    $container = WPEmerge::getContainer();
    $container[ WPEMERGE_VIEW_ENGINE_KEY ] = function() {
        return new MyCustomViewEngine();
    };
    ```
    _Note: We're assigning to the container directly for the sake of brevity - a Service Provider is a much better solution._

## External view engines

### WP Emerge Blade

Renders your views using Blade: https://github.com/htmlburger/wpemerge-blade

### WP Emerge Twig

Renders your views using Twig: https://github.com/htmlburger/wpemerge-twig

## Other built-in view engines

### NameProxyViewEngine

WP Emerge also comes with a small utility view engine which delegates view rendering to other engines depending on the view's name suffix.
The main use-case for it is to allow you to use multiple view engines so you can migrate to a new one on a view-by-view basis instead of forcing you to rewrite all of your views.

Replacing the default view engine:
```php
// getContainer() used for brevity's sake - use a Service Provider instead.
$container = WPEmerge::getContainer();
$container[ WPEMERGE_VIEW_ENGINE_KEY ] = function( $container ) {
    return new \WPEmerge\View\NameProxyViewEngine( [
        // view name suffix => service container key for alternative engine
        '.twig.php' => WPEMERGETWIG_VIEW_TWIG_VIEW_ENGINE_KEY, // use Twig for twig.php views
        '.blade.php' => WPEMERGEBLADE_VIEW_BLADE_VIEW_ENGINE_KEY, // use Blade for .blade.php
        '.php' => WPEMERGE_VIEW_PHP_VIEW_ENGINE_KEY, // use default Php engine for .php views
    ], WPEMERGEBLADE_VIEW_BLADE_VIEW_ENGINE_KEY ); // use Blade for all other cases as blade views can be referenced in blade.format.as.well without an extension
};
```
_Note: The example above assumes you have included both WP Emerge Twig and WP Emerge Blade composer packages. `WPEMERGETWIG_VIEW_TWIG_VIEW_ENGINE_KEY` and `WPEMERGEBLADE_VIEW_BLADE_VIEW_ENGINE_KEY` are not provided by default._
_Note: The same note regarding the usage of a Service Container applies here as well._

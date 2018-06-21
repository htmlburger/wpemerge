# Request Lifecycle

In order to enable WP Emerge you first have to boot it. This is usually done in the `after_setup_theme` action as it is early enough to allow WP Emerge to hook itself into `init` (which is required for functionality such as custom rewrite rules). Once WP Emerge is booted, it will hook into the`template_include` action to process the request. This means that there are 2 major parts to the request lifecycle, each named after the action they are executed in:

## `after_setup_theme`

1. Configuration options passed to `WPEmerge::boot( $config )` are loaded.
1. All service providers listed in the configuration are registered.
1. All service providers listed in the configuration are booted.

__Step 2 and 3 are a critical part of bootstrapping which is why you should make sure your own service container registrations or overrides are done in your own Service Provider instead of after WP Emerge has been booted.__


## `template_include`

1. All defined routes are evaluated in the order they are defined until a satisified route is found.
    - If no route is satisfied, normal WordPress execution takes place and no further action is taken by WP Emerge.
1. If a route is satisfied, normal WordPress template output will be halted and WP Emerge will take over.
1. All suitable arguments depending on the route condition are prepared and passed to the route handler.
1. All global and route-specific middleware are sorted according to the global middleware priority array and executed in ascending order. At the end of the middleware chain, the route handler (a controller method, for example) is executed. The middleware and route handler chain will be referred to as the `pipeline`.
1. If an exception is thrown from the pipeline the `ErrorHandler` defined in the service container will be invoked with that exception as its argument and the pipeline will be halted. The exception handler must return a corresponding response object.
1. The returned response object from the pipeline or exception handler will be used to set the headers and output the body, ending the response.

## WP Emerge and The Loop

Since WP Emerge hooks right before the first template is loaded, the main WordPress query is not interrupted and you can use The Loop as you normally would.
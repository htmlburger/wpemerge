# OldInput

OldInput is a built-in global middleware which allows you to recall POST request input from the previous request ( this will be familiar if you've used [Laravel's old()](https://laravel.com/docs/5.4/requests#old-input) ).

Since OldInput internally uses Flash, you have to ensure a session is available for it:
```php
add_action( 'init', function() {
    session_start();
} );
```

## Usage

A typical use case is to fill in field values after an error has occurred with the user's form submission:
```php
// inside your form view
<input name="my_email" value="<php esc_attr( OldInput::get( 'my_email', 'default' ) ) ?>" />
```

_Note: to reduce verbosity you can define your own simple `old()` function like this:_
```php
function old() {
    return call_user_func_array( [OldInput::class, 'get'], func_get_args() );
}
```

# Layouts

It is a common pattern in WordPress themes to see `get_header()` and `get_footer()` called in every template. In addition, these calls are often followed or preceded by related markup.
In WP Emerge, all of this repetition can be avoided by utilizing layouts.

## Example

Here's the main `theme/index.php` view of the WP Emerge Theme:

```php
<?php
/**
 * App Layout: layouts/app.php
 */

if ( is_single() ) {
	Theme::partial( 'loop', 'single' );
} else {
	Theme::partial( 'loop' );
}
```

The above is not a snippet - this is the entire file!

### Where are get_header() and get_footer()?

You've probably already noticed the `App Layout` declaration in the comment at the top of the file - this declaration uses the WordPress [File Header](https://codex.wordpress.org/File_Header) API to set a layout for the current file.
Whenever the PHP View Engine encounters an `App Layout` declaration, it will automatically load that layout and wrap the current view with it.

The `layouts/app.php` layout looks like this:
```php
<?php
get_header();

if ( ! is_singular() ) {
	app_the_title( '<h2 class="pagetitle">', '</h2>' );
}

app_layout_content();

get_sidebar();

get_footer();
```

Nothing unusual about this file except the call to `app_layout_content()` - this is what defines where the actual view content should be rendered. Essentially, any view can be used as a layout as long as it calls `app_layout_content()` to include the "child" view content.
Also like any other view, layouts can declare their own `App Layout` to achieve layout nesting and you can even use View Composers on layouts to pass context values.

You no longer need to have the header/footer boilerplate in every view while retaining the flexibility of having different header/footer partials for different templates by having loading different layouts when needed.
# 0 to 100 Setup

Sometimes it can be hard to see the full picture which is why this guide will show you how to add WP Emerge to the standard Twenty Seventeen WordPress theme, step-by-step.

## What this guide will cover

- Installation
- Routing
- Controllers
- View Composers

## Assumptions & Definitions

- You have PHP >= 5.5 installed.
- You have Composer installed.
- You have a blank WordPress >= 4.7 installation with the Twenty Seventeen theme installed and activated.
- We will refer to your WordPress installation home url as `HOME_URL` (e.g. `http://localhost/my-website/`).
- We will refer to your Twenty Seventeen theme directory as `THEME_DIR` (e.g. `/var/www/html/my-website/wp-content/themes/twentyseventeen/`).

## Installing WP Emerge

We begin by installing WP Emerge through Composer:

1. Open your terminal of choice.
1. `$ cd THEME_DIR`
1. `$ composer require htmlburger/wpemerge`

Once that's done, we have to make sure the Composer `autoload.php` file is required into our theme, otherwise no composer packages will be loaded at all:

1. Open `THEME_DIR/functions.php` in your favorite editor.
1. Add the following to the **end** of the file:
    ```php
    /**
     * Load Composer's autoloader.
     */
    require 'vendor/autoload.php';
    ```
1. Now that we have the autoloader ready, let's boot WP Emerge itself:
    ```php
    /**
     * Load Composer's autoloader.
     */
    require 'vendor/autoload.php';

    /**
     * Bootstrap WP Emerge.
     */
    add_action( 'after_setup_theme', function() {
        WPEmerge::boot();
    } );
    ```

And we're done - we have composer and WP Emerge loaded and bootstrapped! But ... we're not really doing anything with WP Emerge - let's change that:

1. In order to avoid bloating up the main theme `functions.php` we will separate our framework code into its own file.
1. Create a new directory inside `THEME_DIR` called `app`.
1. Create a new file inside the new `app` directory called `framework.php` and open it in your editor.
1. Add the following code to your new file:
    ```php
    <?php
    /**
     * Routes
     */
    // Note: '/' is the URL we are matching with, and it is always relative to the WordPress homepage url
    Router::get( '/', function() {
        return app_output( 'Hello World!' );
    } );
    ```
1. The above code defines a new route which matches the Homepage url. This way we will override what WordPress displays on the homepage as a quick test.

Let's open up `HOME_URL` in our browser and we should be greeted with an almost blank page with the `Hello World!` sentence ... but we are not. Did we miss anything? Ah yes - we forgot to load our `framework.php` file! Let's fix this so our `functions.php` code looks like this:
```php
/**
 * Load Composer's autoloader.
 */
require 'vendor/autoload.php';

/**
 * Bootstrap WP Emerge.
 */
add_action( 'after_setup_theme', function() {
    WPEmerge::boot();
    require 'app/framework.php';
} );
```

If we open up our browser again we will now see the `Hello World!` sentence as we originally expected!

## Making something useful

Let's go over what we achieved in the previous section:

- We installed WP Emerge.
- We defined a route which will override the homepage.
- We replaced the homepage content with a simple sentence.

That's great and all but we didn't really do anything useful that can help us build a better website which is why in this section we will do the following:

1. Create a landing Call-to-Action template with a "Skip" button.
1. Show the CTA only on the homepage.
1. Show the real homepage if the user clicks the "Skip" button.

Let's get started!

### Creating a CTA template

Let's create a new `THEME_DIR/template-cta.php` file with the following content:
```php
<?php
/**
 * Template Name: Call To Action
 */
?>
<?php get_header(); ?>

<div class="wrap">
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>

            <a href="#">Skip &raquo;</a>

        </main><!-- #main -->
    </div><!-- #primary -->
</div><!-- .wrap -->

<?php get_footer();
```

This is a pretty basic template with nothing but a little hard-coded Lorem Ipsum and a placeholder "Skip" link. Let's update the link so it adds `?cta=0` to our url, for example:
```php
<a href="<?php echo esc_url( add_query_arg( 'cta', '0' ) ); ?>">Skip &raquo;</a>
```

This works OK but it is certainly not perfect and if we ever have other CTAs or other buttons that needs to skip the CTA we will end up with copy-pasted url logic. We'll keep this in mind for now and come back to it later when we have a better understanding of the control WP Emerge provides us with.

### Using our new template

We have our pretty basic template ready so let's put it to use by editing `THEME_DIR/app/framework.php` to look like this (deleting our previous test route):
```php
<?php
/**
 * Routes
 */
Router::get( '/', function() {
    return app_view( 'template-cta.php' );
} );
```

If we open up the homepage we will now be presented with our template.

### Creating our first controller

WP Emerge allows us to use anonymous functions to define as our route handlers, however, it's best if we define controller classes instead so our logic is neatly compartmentalized and separated from other handlers' logic:

1. Create a new `THEME_DIR/app/src` directory.
1. Create a new `THEME_DIR/app/src/Controllers/HomeController.php` file with the following content:
    ```php
    <?php

    namespace App\Controllers;
    
    class HomeController {
        public function index( $request, $view ) {
            return app_view( 'template-cta.php' );
        }
    }
    ```
    As you probably already guessed, this controller will do exactly the same as our anonymous function.
1. Edit `THEME_DIR/app/framework.php` and assign our controller and its `index` method to the route:
    ```php
    <?php
    /**
     * Routes
     */
    Router::get( '/', '\App\Controllers\HomeController@index' );
    ```

If we open up the homepage we will now be presented with an error:
```
Fatal error: Uncaught Error: Class '\App\Controllers\HomeController' not found
```

This is because we have not loaded our class file. We can add a simple `require` statement but we will have to do this every time we add a new controller, view composer or other type of class. Instead let's add class autoloading to our theme:

1. Open `THEME_DIR/composer.json` - it should currently look something like this:
    ```json
    {
        "require": {
            "htmlburger/wpemerge": "^0.8.5"
        }
    }
    ```
1. Edit it so it looks like this:
    ```json
    {
        "require": {
            "htmlburger/wpemerge": "^0.8.5"
        },
        "autoload": {
           "psr-4": {
                "App\\": "app/src/"
           }
       }
    }
    ```
    The above will tell Composer's autoloader package to autoload any class that is in the `App\` namespace by searching for a file in the `app/src/` directory according to [PSR-4](https://www.php-fig.org/psr/psr-4/) based on the class's namespace and class name.
1. Execute `composer dump-autoload` in your terminal so the changes take effect.

If we refresh the homepage we will be greeted with our error-free CTA template again.

### Implementing the Skip button

Now that we have our controller's `index` method ready let's add some logic to it:
```php
public function index( $request, $view ) {
    // $request is a WP Emerge class which represents the current request made to the server.
    // $view is the view file that WordPress is trying to load for the current request.
    
    // If the request includes "cta" a GET parameter with a value of "0" ...
    if ( $request->get( 'cta' ) === '0' ) {
        // ... return the view WordPress was trying to load:
        return app_view( $view );
    }
    // otherwise, return our custom CTA view:
    return app_view( 'template-cta.php' );
}
```

Now if we visit `HOME_URL` we will see our CTA template but when we click on our "Skip" link we will be presented with our usual homepage!

## Improvements

Let's say that we want to have 2 "Skip" buttons - one above and one below our Lorem Ipsum content. The link url code is short but if we want to stay [DRY](https://en.wikipedia.org/wiki/Don%27t_repeat_yourself) we can't just copy-paste it.
We could, of course, define a new variable at the top of our template file but this means that the variable will only be available in this particular template file (what if we wanted that variable to also appear in a child template partial?) and we will be mixing php logic with presentation (which we should always strive to avoid).

Instead we can modify our controller method to supply our template (which we will be referring to as a `view` from now on) with a variable which will hold the skip url (we will refer to variables passed to views as `context`):
```php
public function index( $request, $view ) {
    if ( $request->get( 'cta' ) === '0' ) {
        return app_view( $view );
    }

    $skip_url = add_query_arg( 'cta', '0', $request->getUrl() );

    return app_view( 'template-cta.php' )
        ->with( 'skip_url', $skip_url );
}
```

We also have to modify the template to use the newly available variable:
```php
<a href="<?php echo esc_url( $skip_url ); ?>">Skip &raquo;</a>

<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>

<a href="<?php echo esc_url( $skip_url ); ?>">Skip &raquo;</a>
```
    
We reduced PHP logic duplication, but this doesn't solve the use case where we want to have that variable available in a view partial, for example. For this we can use the `app_partial( $view, $context = [] )` function which works very similarly to `get_template_part()` but as you can see it has an optional argument which allows you to pass context variables as well:
```php
<?php app_partial( 'foo-partial.php', ['skip_url' => $skip_url] ); ?>
```

## FAQ

##### What if we wish to have a partial that is reused throughout the site but it needs a certain variable? Do we have to add that logic to every controller which loads a view which includes that partial?

Thankfully, no! This is where view composers come into play - check out the [View Composers](view/view-composers.md) article to see how they can solve this problem and more.

##### What if we wanted to show the CTA on a page other than the homepage - do we have to hardcode that page's url in the route definition?

We can but we don't have to. We can take advantage of WP Emerge's dynamic [Route Conditions](routing/conditions.md). As an example, this is what our route definition will look like if we wish to show the CTA on any page that uses a custom template called `template-cta-enabled-page.php`:
```php
Router::get( ['post_template', 'template-cta-enabled-page.php'], '\App\Controllers\HomeController@index' );
```
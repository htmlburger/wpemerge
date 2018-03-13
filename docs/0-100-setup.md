# 0 to 100 Setup (Work In Progress)

Sometimes it can be hard to see the full picture which is why this guide will show you how to add WP Emerge to the standard Twenty Seventeen WordPress theme, step-by-step.

## Assumptions & Definitions

- You have PHP >= 5.5 installed.
- You have Composer installed.
- You have a blank WordPress >= 4.7 installation with the Twenty Seventeen theme installed and activated.
- We will refer to your WordPress installation home url as `HOME_URL` (e.g. `http://localhost/wpemerge/`).
- We will refer to your Twenty Seventeen theme directory as `THEME_DIR` (e.g. `/var/www/html/wpemerge/wp-content/themes/twentyseventeen/`).

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

TODO

## Partials

TODO

## Bonus

TODO
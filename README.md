# WP Nonce Function
WP Nonce Function if object oriented (OOP) way to handle with WordPress wp_nonce_*() functions. 


Table of contents:
 * [Requirements](#requirements)
 * [Installation](#installation)
 * [Usage](#usage)

## Requirements

* PHP >= 5.6.4
* WordPress >= 4.6

## Installation

You can install this class both on command-line or by pasting it into the root of your plugin directory.

### via Command-line

Using [Composer](https://getcomposer.org/), add Custom Nonce Class to your plugin's dependencies.

```sh
composer require creativform/wp_nonce_function:dev-master
```

### Other way

1. Download the [latest zip](https://github.com/CreativForm/wp-nonce-function/archive/master.zip) of this repo.
2. Unzip the master.zip file.
3. Copy and paste it into the root of your plugin directory.
4. Continue with your project.

## Usage

Setup the minimum required thigs:

```php
<?php 
// Autoload files using Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// use class
use Nonce\WP_Nonce;


// Instantiate the class
$nonce = new WP_Nonce();
```
### Options and Global Settup

Basic global setup for all object is `nonce_name` and `nonce_action`. You can define it once and don't use in other rendering for this session.
```php
$nonce->option([
    'nonce_name' 	=> '_wpnonce',
    'nonce_action' 	=> 'edit-post_'.$post->ID,
]);
```

Also you have session time expire if you need it:

```php
$nonce->option( 'nonce_life',(4 * HOUR_IN_SECONDS) );
```
Like you see, you can pass `array` of settings or use single setup like above.
### Examples

Adding a nonce to a URL:

```php
$url="/../wp-admin/post.php?post=48";
$complete_url = $nonce->url( $url );
```

Adding a nonce to a form:

```php
$nonce->field();
```

creating a nonce:

```php
$newnonce = $nonce->create();
```
NOTE: `$nonce->create();` passing values inside other objects and you don't need to use string to pass values but optional function return value.

Verifying a nonce:

```php
$nonce->wp_verify();
```

Verifying a nonce passed in an AJAX request:

```php
$nonce->ajax_verify( 'post-comment' );
```

Verifying a nonce passed in some other context:

```php
$nonce->admin_referer( $_REQUEST['my_nonce'] );
```

destructing and finish

When you finish, just close this session and destruct all setups
```php
$nonce->clean();
```


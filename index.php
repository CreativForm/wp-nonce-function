<?php
// Autoload files using Composer autoload
require_once __DIR__ . '/vendor/autoload.php';

use Nonce\WP_Nonce;

$nonce = new WP_Nonce();
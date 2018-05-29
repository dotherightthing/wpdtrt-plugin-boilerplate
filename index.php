<?php
/**
 * PSR-4 Autoloader
 *
 * @package WPDTRT_Plugin_Boilerplate
 * @version 1.4.23
 * @see     http://phpenthusiast.com/blog/how-to-autoload-with-composer
 */

/**
 * Autoload PHP classes.
 *  autoload.php is generated by Composer and autoloads classes for all vendors.

 *  WPDTRT_PLUGIN_CHILD allows for child plugins, where this PHP file is nested within vendor/, see #51
 *
 * @see https://www.php-fig.org/psr/psr-4/
 * @see https://stackoverflow.com/a/37952183/6850747
 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/51
 * @todo https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/124
 */
if ( defined( 'WPDTRT_PLUGIN_CHILD' ) ) {
	$project_root_path = realpath( __DIR__ . '/../../..' ) . '/';
} else {
	$project_root_path = '';
}

require_once $project_root_path . 'vendor/autoload.php';

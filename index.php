<?php
/**
 * File: index.php
 *
 * Topic: PSR-4 Autoloader.
 *
 * Note:
 * - Autoloads PHP classes.
 * - autoload.php is generated by Composer and autoloads classes for all vendors.
 * - WPDTRT_PLUGIN_CHILD allows for child plugins, where this PHP file is nested within vendor/, see #51
 *
 * TODO:
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/124>
 *
 * See:
 * - <http://phpenthusiast.com/blog/how-to-autoload-with-composer>
 * - <https://www.php-fig.org/psr/psr-4/>
 * - <https://stackoverflow.com/a/37952183/6850747>
 * - <https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/51>
 *
 * @version 1.7.7
 */

if ( defined( 'WPDTRT_PLUGIN_CHILD' ) ) {
	$project_root_path = realpath( __DIR__ . '/../../..' ) . '/';
} else {
	$project_root_path = '';
}

require_once $project_root_path . 'vendor/autoload.php';

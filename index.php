<?php
/**
 * PSR-4 Autoloader
 * @see http://phpenthusiast.com/blog/how-to-autoload-with-composer
 *
 * @package     WPPlugin
 * @since 		0.6.0
 * @version 	1.0.0
 */

	// vendor/autoload.php
	// composer autoload file used by all vendors
	require_once dirname( dirname(__DIR__) ) . "/autoload.php";
	//require __DIR__.'/../vendor/autoload.php';

	use DoTheRightThing\WPPlugin\Plugin;
	use DoTheRightThing\WPPlugin\TemplateLoader;
	use DoTheRightThing\WPPlugin\Shortcode;
	use DoTheRightThing\WPPlugin\Widget;

?>

/**
 * Scripts for the plugin settings page
 *
 * This file contains JavaScript.
 *    PHP variables are provided in wpdtrt_plugin_config.
 *
 * @package   	WPPlugin
 * @since       1.0.0
 */

jQuery(document).ready(function($) {

	// wpdtrt_plugin_config is generic
	// but we can only view one settings page at a time
	var config = wpdtrt_plugin_config;
	var loading_message = config.messages.loading;
	var ajaxurl = config.ajaxurl;
	var ajax_data = {
		'action': 'refresh_api_data'
	};
	var $container = $('.wpdtrt-plugin-ajax-response');

	$container
		.empty()
		.append('<div class="spinner is-active">' + loading_message + '</div>');

	$.post( ajaxurl, ajax_data, function( response ) {
		$container
			.empty()
			.html( response );
	});
});

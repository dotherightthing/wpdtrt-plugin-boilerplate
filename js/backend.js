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

	var ajax_data_ui = {
		'action': 'refresh_api_data',
		'format': 'ui'
	};

	var ajax_data_data = {
		'action': 'refresh_api_data',
		'format': 'data'
	};

	var $ajax_containers = $('.wpdtrt-plugin-ajax-response');

	$ajax_containers
		.empty()
		.append('<div class="spinner is-active">' + loading_message + '</div>');

	$.each( $ajax_containers, function(i, item) {

		var $container = $(item);

		if ( $container.data('format') === 'ui' ) {

			$.post( ajaxurl, ajax_data_ui, function( response ) {
				$container
					.empty()
					.html( response );
			});
		}
		else if ( $container.data('format') === 'data' ) {

			$.post( ajaxurl, ajax_data_data, function( response ) {
				$container
					.empty()
					.html( response );
			});
		}
	});
});

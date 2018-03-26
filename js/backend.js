/**
 * Scripts for the plugin settings page
 *
 * This file contains JavaScript.
 *    PHP variables are provided in wpdtrt_plugin_config.
 *
 * @since       1.0.0
 * @version 	1.0.1
 */

/* global jQuery, wpdtrt_plugin_config */

jQuery(document).ready(function($) {

	// wpdtrt_plugin_config is generic
	// but we can only view one settings page at a time
	var config = wpdtrt_plugin_config;
	var loading_message = config.messages.loading;
	var ajaxurl = config.ajaxurl;

	if ( config.refresh_api_data === 'true' ) {
		var $ajax_container_data = $('.wpdtrt-plugin-ajax-response[data-format="data"]');
		var $ajax_container_ui = $('.wpdtrt-plugin-ajax-response[data-format="ui"]');

		$ajax_container_data
			.empty()
			.append('<div class="spinner is-active">' + loading_message + '</div>');

		$ajax_container_ui
			.empty()
			.append('<div class="spinner is-active">' + loading_message + '</div>');

		var data = $.post( ajaxurl, {
				'action': 'refresh_api_data',
				'format': 'ui'
			},
			function(response) {
				$ajax_container_ui
					.empty()
					.html( response );
			}
		);
				
		data.done( function() {
			$.post(
				ajaxurl,
				{
					'action': 'refresh_api_data',
					'format': 'data'
				},
				function(response) {
					$ajax_container_data
						.empty()
						.html( response );
				}
			);
		});
	}
});

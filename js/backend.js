/**
 * @file DTRT WordPress Plugin Boilerplate backend.js
 * @summary
 *     Front-end scripting for WP Admin's plugin settings page
 *     PHP variables are provided in `wpdtrt_plugin_boilerplate_config`.
 * @version 1.0.1
 * @since   1.0.0
 */

/* eslint-env browser */
/* global jQuery, wpdtrt_plugin_boilerplate_config */

/**
 * @namespace wpdtrt_plugin_boilerplate_admin_ui
 */
var wpdtrt_plugin_boilerplate_admin_ui = {

    /**
     * Load front-end content via Ajax
     * @param {object} $ jQuery
     */
    ajax_init: function($) {
        "use strict";

        // wpdtrt_plugin_boilerplate_config is generic
        // but we can only view one settings page at a time
        var config = wpdtrt_plugin_boilerplate_config;
        var loading_message = config.messages.loading;
        var prefix = config.prefix;
        var ajaxurl = config.ajaxurl;
        var $ajax_container_data;
        var $ajax_container_ui;
        var abbr = "wpdtrt-plugin-boilerplate";
        var data;

        if (config.refresh_api_data === "true") {
            $ajax_container_data =
                    $("." + abbr + "-ajax-response[data-format=\"data\"]");
            $ajax_container_ui =
                    $("." + abbr + "-ajax-response[data-format=\"ui\"]");

            $ajax_container_data
                .empty()
                .append(
                    "<div class=\"spinner is-active\">" +
                    loading_message +
                    "</div>"
                );

            $ajax_container_ui
                .empty()
                .append(
                    "<div class=\"spinner is-active\">" +
                    loading_message +
                    "</div>"
                );

            data = $.post(ajaxurl, {
                "action": prefix + "_refresh_api_data",
                "format": "ui"
            }, function (response) {
                $ajax_container_ui
                    .empty()
                    .html(response);
            });

            data.done(function () {
                $.post(
                    ajaxurl,
                    {
                        "action": prefix + "_refresh_api_data",
                        "format": "data"
                    },
                    function (response) {
                        $ajax_container_data
                            .empty()
                            .html(response);
                    }
                );
            });
        }
    }
}

jQuery(document).ready(function ($) {
    wpdtrt_plugin_boilerplate_admin_ui.ajax_init($);
});

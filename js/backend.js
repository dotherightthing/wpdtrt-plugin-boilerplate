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
 * Namespace: wpdtrt_plugin_boilerplate_admin_ui
 */
const wpdtrt_plugin_boilerplate_admin_ui = {

    /**
     * Method: ajax_init
     * 
     * Load front-end content via Ajax
     *
     * Parameters:
     *   $ - jQuery object
     */
    ajax_init: ($) => {
        // wpdtrt_plugin_boilerplate_config is generic
        // but we can only view one settings page at a time
        const config = wpdtrt_plugin_boilerplate_config;
        const loading_message = config.messages.loading;
        const prefix = config.prefix;
        const ajaxurl = config.ajaxurl;
        const abbr = "wpdtrt-plugin-boilerplate";
        let $ajax_container_data = $();
        let $ajax_container_ui = $();
        let data = {};

        if (config.refresh_api_data === "true") {
            $ajax_container_data =
                    $(`.${abbr}-ajax-response[data-format="data"]`);
            $ajax_container_ui =
                    $(`.${abbr}-ajax-response[data-format="ui"]`);

            $ajax_container_data
                .empty()
                .append(
                    `<div class="spinner is-active">${loading_message}</div>`
                );

            $ajax_container_ui
                .empty()
                .append(
                    `<div class="spinner is-active">${loading_message}</div>`
                );

            data = $.post(ajaxurl, {
                "action": `${prefix}_refresh_api_data`,
                "format": "ui"
            }, (response) => {
                $ajax_container_ui
                    .empty()
                    .html(response);
            });

            data.done( () => {
                $.post(
                    ajaxurl,
                    {
                        "action": `${prefix}_refresh_api_data`,
                        "format": "data"
                    },
                    (response) => {
                        $ajax_container_data
                            .empty()
                            .html(response);
                    }
                );
            });
        }
    }
}

jQuery(document).ready( ($) => {
    wpdtrt_plugin_boilerplate_admin_ui.ajax_init($);
});

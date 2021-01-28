/**
 * @file js/backend.js
 * @summary Front-end scripting for WP Admin's plugin settings page.
 * @description PHP variables are provided in `wpdtrtPluginBoilerplateConfig`.
 */

/* global jQuery, wpdtrtPluginBoilerplateConfig */
/* eslint-disable func-names, camelcase */

/**
 * jQuery object
 *
 * @external jQuery
 * @see {@link http://api.jquery.com/jQuery/}
 */

/**
 * @namespace wpdtrtPluginBoilerplateAdminUi
 */
const wpdtrtPluginBoilerplateAdminUi = {

    /**
     * @function ajax_init
     * @summary Load front-end content via Ajax
     * @memberof wpdtrtPluginBoilerplateAdminUi
     *
     * @param {external: jQuery} $ - jQuery
     */
    ajax_init: ($) => {
        // wpdtrtPluginBoilerplateConfig is generic
        // but we can only view one settings page at a time
        // see Plugin.php - render_js_backend()
        const config = wpdtrtPluginBoilerplateConfig;

        // config.refresh_api_data comes from wp_localize_script in Plugin.php
        if (config.refresh_api_data === 'true') {
            $.each([ 'ui', 'data' ], (index, value) => {
                let $ajaxContainer = $(`.wpdtrt-plugin-boilerplate-ajax-response[data-format="${value}"]`);

                let settings = {
                    type: 'POST',
                    url: config.ajaxurl,
                    data: {
                        action: `${config.prefix}_refresh_api_data`,
                        format: value,
                    },
                    cache: false
                };

                $ajaxContainer
                    .empty()
                    .append(
                        `<div class="spinner is-active">${config.messages.loading}</div>`
                    );

                $.ajax(settings)
                    .done((response) => {
                        $ajaxContainer
                            .empty()
                            .html(response);
                    })
                    .fail(() => {
                        $ajaxContainer
                            .empty()
                            .html('Sorry, the demo couldn\'t be loaded.');
                    });
            });
        }
    }
};

jQuery(document).ready(($) => {
    wpdtrtPluginBoilerplateAdminUi.ajax_init($);
});

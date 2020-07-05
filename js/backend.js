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
        const loadingMessage = config.messages.loading;
        const prefix = config.prefix;
        const ajaxurl = config.ajaxurl;
        const abbr = 'wpdtrt-plugin-boilerplate';
        let $ajaxContainerData = $();
        let $ajaxContainerUi = $();
        let data = {};

        if (config.refresh_api_data === 'true') {
            $ajaxContainerData = $(`.${abbr}-ajax-response[data-format="data"]`);
            $ajaxContainerUi = $(`.${abbr}-ajax-response[data-format="ui"]`);

            $ajaxContainerData
                .empty()
                .append(
                    `<div class="spinner is-active">${loadingMessage}</div>`
                );

            $ajaxContainerUi
                .empty()
                .append(
                    `<div class="spinner is-active">${loadingMessage}</div>`
                );

            data = $.post(ajaxurl, {
                action: `${prefix}_refresh_api_data`,
                format: 'ui'
            }, (response) => {
                $ajaxContainerUi
                    .empty()
                    .html(response);
            });

            data.done(() => {
                $.post(
                    ajaxurl, {
                        action: `${prefix}_refresh_api_data`,
                        format: 'data'
                    }, (response) => {
                        $ajaxContainerData
                            .empty()
                            .html(response);
                    }
                );
            });
        }
    }
};

jQuery(document).ready(($) => {
    wpdtrtPluginBoilerplateAdminUi.ajax_init($);
});

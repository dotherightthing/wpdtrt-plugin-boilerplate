/**
 * File: backend.js
 * Topic: DTRT WordPress Plugin Boilerplate
 *
 * Front-end scripting for WP Admin's plugin settings page
 *
 * PHP variables are provided in `wpdtrtPluginBoilerplateConfig`.
 *
 * See package.json for scripts, which can be run with:
 * --- Text
 * yarn run scriptname
 * ---
 *
 * @version 1.0.1
 * @since   1.0.0
 */

/* eslint-env browser */
/* global jQuery, wpdtrtPluginBoilerplateConfig */

/**
 * Namespace: wpdtrtPluginBoilerplateAdminUi
 */
const wpdtrtPluginBoilerplateAdminUi = {

  /**
     * Method: ajax_init
     *
     * Load front-end content via Ajax
     *
     * Parameters:
     *   $ - jQuery object
     */
  ajax_init: ( $ ) => {
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

    if ( config.refresh_api_data === 'true' ) {
      $ajaxContainerData = $( `.${abbr}-ajax-response[data-format="data"]` );
      $ajaxContainerUi = $( `.${abbr}-ajax-response[data-format="ui"]` );

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

      data = $.post( ajaxurl, {
        action: `${prefix}_refresh_api_data`,
        format: 'ui'
      }, ( response ) => {
        $ajaxContainerUi
          .empty()
          .html( response );
      } );

      data.done( () => {
        $.post(
          ajaxurl,
          {
            action: `${prefix}_refresh_api_data`,
            format: 'data'
          },
          ( response ) => {
            $ajaxContainerData
              .empty()
              .html( response );
          }
        );
      } );
    }
  }
};

jQuery( document ).ready( ( $ ) => {
  wpdtrtPluginBoilerplateAdminUi.ajax_init( $ );
} );

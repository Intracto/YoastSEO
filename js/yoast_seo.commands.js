(function ($, Drupal) {

    'use strict';

    Drupal.AjaxCommands.prototype.renderEntity = function (ajax, response, status) {
        $(response.selector).data(response.key, response.content);
        $(document).trigger('seo-content-refreshed');
    }

})(jQuery, Drupal);
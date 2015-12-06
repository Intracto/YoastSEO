# ITR Yoast SEO
---------------

## Features

Using the Yoast SEO plugin, this module will guide you in writing content which is fully SEO-optimized! It will check the length of your text,
the page description, whether your focus keyword is present enough... Just to name a few.

Thanks to our real-time calculations you get feedback instantaneously!

## History

This module is an upgrade of the Drupal 7 version, based on the work of [GoalGorilla](http://www.goalgorilla.com) but improved for Drupal 8.

## Installation & configuration

1. Download the module from http://drupal.org/project/itr_yoast_seo or download with drush (drush dl itr_yoast_seo).
2. Make sure to install and enable the module, as wel as the Metatag module.
3. Enable the plugin on your content type settings page
4. Set a focus keyword, page title and description when editing a piece of content.
5. Start writing your content and get real-time feedback on it's search engine performance with page analysis.

## Developer information / architecture

A new field type 'yoast_seo' has been created that holds the focus keyword and the status of the node. Using the default widget, all the
necessary form input fields are rendered and the Javascript gets attached.

When typing, a hidden input field is triggered that will spawn an Ajax request. Use the Drupal Ajax framework, the command will render
a preview of that node and return the HTML. After that, the new status will be calculated by the plugin and shown to the user.
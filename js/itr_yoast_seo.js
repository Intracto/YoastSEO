(function ($, Drupal, drupalSettings) {

    'use strict';

    Drupal.behaviors.itr_yoast_seo = {
        attach: function (context) {
            if (typeof drupalSettings.itr_yoast_seo != 'undefined') {
                YoastSEO = ( "undefined" === typeof YoastSEO ) ? {} : YoastSEO;

                // Create analyzer arguments
                YoastSEO.analyzerArgs = {
                    analyzer: true,
                    snippetPreview: true,
                    typeDelay: 300,
                    typeDelayStep: 100,
                    maxTypeDelay: 1500,
                    dynamicDelay: true,
                    multiKeyword: false,
                    targets: {
                        output: drupalSettings.itr_yoast_seo.targets.output,
                        overall: drupalSettings.itr_yoast_seo.targets.overall,
                        snippet: drupalSettings.itr_yoast_seo.targets.snippet
                    },
                    snippetFields: {
                        title: "snippet_title",
                        url: "snippet_cite",
                        meta: "snippet_meta"
                    },
                    sampleText: {
                        url: drupalSettings.itr_yoast_seo.default_text.url,
                        title: drupalSettings.itr_yoast_seo.default_text.page_title,
                        keyword: drupalSettings.itr_yoast_seo.default_text.keyword,
                        meta: drupalSettings.itr_yoast_seo.default_text.page_description
                    },
                    placeholderText: {
                        title: drupalSettings.itr_yoast_seo.placeholder_text.title,
                        description: drupalSettings.itr_yoast_seo.placeholder_text.description,
                        url: drupalSettings.itr_yoast_seo.placeholder_text.url
                    },
                    fields: {
                        keyword: drupalSettings.itr_yoast_seo.field_ids.focus_keyword,
                        title: drupalSettings.itr_yoast_seo.field_ids.page_title,
                        nodeTitle: drupalSettings.itr_yoast_seo.field_ids.node_title,
                        meta: drupalSettings.itr_yoast_seo.field_ids.description,
                        url: drupalSettings.itr_yoast_seo.field_ids.url
                    },
                    scoreElement: drupalSettings.itr_yoast_seo.field_ids.seo_status,
                    baseRoot: drupalSettings.itr_yoast_seo.base_root,
                    copyCallback: drupalSettings.itr_yoast_seo.copy_callback
                };

                // Create a new scraper object and map the callbacks
                var DrupalScraper = new YoastSEO.ITRScraper(YoastSEO.analyzerArgs);
                YoastSEO.analyzerArgs.callbacks = {
                    getData: DrupalScraper.getData.bind(DrupalScraper),
                    getAnalyzerInput: DrupalScraper.getAnalyzerInput.bind(DrupalScraper),
                    bindElementEvents: DrupalScraper.bindElementEvents.bind(DrupalScraper),
                    saveScores: DrupalScraper.saveScores.bind(DrupalScraper)
                };

                // Instantiate a new YoastSEO app and make it global accessible
                window.YoastSEO.app = new YoastSEO.App(YoastSEO.analyzerArgs);

                // Parse the input from snippet preview fields to their corresponding metatag and path fields
                DrupalScraper.parseSnippetData(YoastSEO.analyzerArgs.snippetFields.title, YoastSEO.analyzerArgs.fields.title);
                DrupalScraper.parseSnippetData(YoastSEO.analyzerArgs.snippetFields.url, YoastSEO.analyzerArgs.fields.url);
                DrupalScraper.parseSnippetData(YoastSEO.analyzerArgs.snippetFields.meta, YoastSEO.analyzerArgs.fields.meta);
            }
        }
    }

}(jQuery, Drupal, drupalSettings));
DrupalScraper = function (args) {
    this.config = args;

    /**
     * Get the value from a input field
     *
     * @param fieldId
     * @returns {*}
     */
    this.getInputData = function (fieldId) {
        return jQuery('#' + this.config.fields[fieldId]).val();
    };

    /**
     * When an input element has been altered, trigger the YoastSEO app
     *
     * @param e
     */
    this.renewData = function () {
        jQuery('#' + YoastSEO.analyzerArgs.contentElement).trigger('change');
        YoastSEO.app.analyzeTimer();
    };

    /**
     * Add a listener to every input element
     */
    this.bindInputElements = function () {
        var self = this;
        jQuery.each(this.config.fields, function (fieldKey) {
            if (typeof self.config.fields[fieldKey] != 'undefined') {
                var $field = jQuery('#' + self.config.fields[fieldKey]);
                $field.__refObj = this;
                $field.on('input', self.renewData);
            }
        });
    };

    /**
     * Get a human-readable version of the score
     *
     * @param score
     * @returns {string}
     */
    this.rateScore = function (score, getClass) {
        if (getClass === undefined) {
            getClass = true;
        }
        var scoreRate;
        switch (true) {
            case score <= 4:
                scoreRate = "bad";
                break;
            case score > 4 && score <= 7:
                scoreRate = "ok";
                break;
            case score > 7:
                scoreRate = "good";
                break;
            default:
            case score === "na":
                scoreRate = "na";
                break;
        }

        var output = (getClass) ? scoreRate : "SEO: <strong>" + scoreRate + "</strong>";

        return output;
    };

    /**
     * Dispatch an event to fire analysis magic ;)
     *
     * @param field
     */
    this.triggerEvent = function (field) {
        var $field = jQuery('#' + field);
        if ('createevent' in document) {
            var e = document.createEvent('HTMLEvents');
            e.initEvent('input', false, true);
            $field.dispatchEvent(e);
        }
        else {
            $field.trigger('input');
        }
    };
};

/**
 * Collect and return input data
 *
 * @returns {}
 */
DrupalScraper.prototype.getData = function () {
    var seoContent = jQuery('#' + YoastSEO.analyzerArgs.contentElement).data('seo-content');
    jQuery('#preview--wrapper').html(seoContent);

    var data = {
        keyword: this.getInputData('keyword'),
        meta: this.getInputData('meta'),
        snippetMeta: this.getInputData('meta'),
        text: seoContent,
        snippetTitle: this.getInputData('title'),
        pageTitle: this.getInputData('title'),
        baseUrl: this.config.baseRoot + '/',
        url: this.config.baseRoot + '/' + this.getInputData('url'),
        snippetCite: this.getInputData('url')
    }

    // Placeholder text in snippet if nothing was found.
    if (data.meta == '') {
        data.snippetMeta = this.config.placeholderText.description;
    }
    if (data.pageTitle == '') {
        data.snippetTitle = this.config.placeholderText.title;
    }
    if (data.snippetCite == '') {
        data.snippetCite = this.config.placeholderText.url;
    }

    return data;
}

/**
 * Bind events to input elements
 */
DrupalScraper.prototype.bindElementEvents = function () {
    this.bindInputElements();
}

/**
 * Display the score in a proper way
 *
 * @param score
 */
DrupalScraper.prototype.saveScores = function (score) {
    var overallScore = score / 10;
    jQuery('.score_title').html(this.rateScore(overallScore, false));
    jQuery('#' + this.config.scoreElement).val(score);

    var $scoreCircle = jQuery('#score_circle')[0];
    var classLength = $scoreCircle.classList.length;
    while (classLength > 3) {
        $scoreCircle.classList.remove($scoreCircle.classList.item(classLength - 1));
        classLength = $scoreCircle.classList.length;
    }
    $scoreCircle.classList.add(this.rateScore(overallScore));

}

/**
 * Trigger an update when the data in the snippet is altered
 *
 * @param source
 * @param target
 */
DrupalScraper.prototype.parseSnippetData = function (source, target) {
    var self = this;
    var listener = function (e) {
        jQuery('#' + target).val(e.target.innerText);
        self.triggerEvent(target);
    }.bind(this);
    jQuery('#' + source).on('blur', listener);
}
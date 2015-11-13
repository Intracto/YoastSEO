YoastSEO = ( "undefined" === typeof YoastSEO ) ? {} : YoastSEO;

YoastSEO.ITRScraper = function (args) {
    this.config = args;

    /**
     * Grab the id of the field and return the value of that element
     *
     * @param field_id
     * @returns {*}
     */
    this.getInputData = function (field_id) {
        return document.getElementById(this.config.fields[field_id]).value;
    }

    /**
     * When an input element has been altered, trigger the Yoast SEO app timer
     * @param e
     */
    this.renewData = function (e) {
        YoastSEO.app.analyzeTimer(e);
    }

    /**
     * Add a listener to every input element
     */
    this.bindInputElementEvents = function () {
        for (var field_key in this.config.fields) {
            if (typeof this.config.fields[field_key] != 'undefined') {
                var $field = document.getElementById(this.config.fields[field_key]);
                $field.__refObj = this;
                $field.addEventListener('input', this.renewData.bind(this));
            }
        }
    }

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
    }

    /**
     * Dispatch an event to fire analysis magic ;)
     *
     * @param field
     */
    this.triggerEvent = function (field) {
        if ('createEvent' in document) {
            var e = document.createEvent('HTMLEvents');
            e.initEvent('input', false, true);
            document.getElementById(field).dispatchEvent(e);
        }
        else {
            document.getElementById(field).fireEvent('input');
        }
    }
}

/**
 * Collect and return input data
 *
 * @returns {};
 */
YoastSEO.ITRScraper.prototype.getData = function () {
    var request = new XMLHttpRequest();
    request.open('GET', this.config.baseRoot + this.config.copyCallback, false);
    request.send();

    var requestData = '';
    if (request.status === 200) {
        requestData = JSON.parse(request.responseText);
    }

    var data = {
        keyword: this.getInputData('keyword'),
        meta: this.getInputData('meta'),
        snippetMeta: this.getInputData('meta'),
        text: requestData,
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
 * Called when the score has been determined by the analyzer
 *
 * @param score
 */
YoastSEO.ITRScraper.prototype.saveScores = function (score) {
    var overallScore = score / 10;
    document.getElementById(this.config.targets.overall).getElementsByClassName('score_title')[0].innerHTML = this.rateScore(overallScore, false);
    document.getElementById(this.config.scoreElement).value = score;

    var score_circle = document.getElementById('score_circle');
    var class_length = score_circle.classList.length;
    while (class_length > 3) {
        score_circle.classList.remove(score_circle.classList.item(class_length - 1));
        class_length = score_circle.classList.length;
    }
    score_circle.classList.add(this.rateScore(overallScore));

}

/**
 * Binds events to input elements
 *
 * @param app
 */
YoastSEO.ITRScraper.prototype.bindElementEvents = function (app) {
    this.bindInputElementEvents(app);
}

/**
 * Trigger an update when the data in the snippet is altered
 *
 * @param source
 * @param target
 */
YoastSEO.ITRScraper.prototype.parseSnippetData = function (source, target) {
    var listener = function (e) {
        document.getElementById(target).value = e.target.innerText;
        this.triggerEvent(target);
    }.bind(this);
    document.getElementById(source).addEventListener('blur', listener);
}
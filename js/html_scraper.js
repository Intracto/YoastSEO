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
     * Replace the data of the app with updated values
     */
    this.updateRawData = function () {
        var data = {
            keyword: this.getInputData('keyword'),
            meta: this.getInputData('meta'),
            snippetMeta: this.getInputData('meta'),
            text: 'this is a description page title this is a description this is a description this is a description',
            nodeTitle: this.getInputData('nodeTitle'),
            snippetTitle: this.getInputData('title'),
            pageTitle: this.getInputData('title'),
            baseUrl: this.config.baseRoot,
            url: this.config.baseRoot + this.getInputData('url'),
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

        YoastSEO.app.rawData = data;
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
    this.rateScore = function (score) {
        var scoreRate;
        switch ( score ) {
            case 0:
                scoreRate = "na";
                break;
            case 4:
            case 5:
                scoreRate = "poor";
                break;
            case 6:
            case 7:
                scoreRate = "ok";
                break;
            case 8:
            case 9:
            case 10:
                scoreRate = "good";
                break;
            default:
                scoreRate = "bad";
                break;
        }

        var output = "SEO: <strong>" + scoreRate + "</strong>";

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
    var data = {
        keyword: this.getInputData('keyword'),
        meta: this.getInputData('meta'),
        snippetMeta: this.getInputData('meta'),
        text: '<p>this is a <h2>description page</h2>  title this is a description this is a description this is a description</p>',
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
 * Initializes the snippetPreview if it isn't there
 * Otherwise, new data from the inputs is gathered and the preview re-rendered
 */
YoastSEO.ITRScraper.prototype.getAnalyzerInput = function () {
    if (typeof  YoastSEO.app.snippetPreview === 'undefined') {
        YoastSEO.app.init();
    }
    else {
        this.updateRawData();
        YoastSEO.app.reloadSnippetText();
    }
}

/**
 * Called when the score has been determined by the analyzer
 *
 * @param score
 */
YoastSEO.ITRScraper.prototype.saveScores = function (score) {
    document.getElementById(this.config.targets.overall).getElementsByClassName('score_title')[0].innerHTML = this.rateScore(score);
    document.getElementById(this.config.scoreElement).value = score;
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
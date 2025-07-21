$.fn.dataTable.pipeline = function (opts) {
    var conf = $.extend(
        {
            pages: 5,
            url: "",
            data: null,
            method: "GET",
        },
        opts
    );

    var cacheLower = -1,
        cacheUpper = null,
        cacheLastRequest = null,
        cacheLastJson = null;

    return function (request, drawCallback, settings) {
        var ajax = false,
            requestStart = request.start,
            requestLength = request.length,
            requestEnd = requestStart + requestLength;

        if (settings.clearCache) {
            ajax = true;
            settings.clearCache = false;
        } else if (
            cacheLower < 0 ||
            requestStart < cacheLower ||
            requestEnd > cacheUpper
        ) {
            ajax = true;
        } else if (
            JSON.stringify(request.order) !==
                JSON.stringify(cacheLastRequest.order) ||
            JSON.stringify(request.columns) !==
                JSON.stringify(cacheLastRequest.columns) ||
            JSON.stringify(request.search) !==
                JSON.stringify(cacheLastRequest.search)
        ) {
            ajax = true;
        }

        cacheLastRequest = $.extend(true, {}, request);

        if (ajax) {
            if (requestStart < cacheLower) {
                requestStart = requestStart - requestLength * (conf.pages - 1);
                if (requestStart < 0) {
                    requestStart = 0;
                }
            }

            cacheLower = requestStart;
            cacheUpper = requestStart + requestLength * conf.pages;

            request.start = requestStart;
            request.length = requestLength * conf.pages;

            if ($.isFunction(conf.data)) {
                var d = conf.data(request);
                if (d) {
                    $.extend(request, d);
                }
            } else if ($.isPlainObject(conf.data)) {
                $.extend(request, conf.data);
            }

            return $.ajax({
                type: conf.method,
                url: conf.url,
                data: request,
                dataType: "json",
                cache: false,
                success: function (json) {
                    cacheLastJson = $.extend(true, {}, json);

                    if (cacheLower != request.start) {
                        json.data.splice(0, request.start - cacheLower);
                    }
                    json.data.splice(requestLength, json.data.length);

                    drawCallback(json);
                },
            });
        } else {
            var json = $.extend(true, {}, cacheLastJson);
            json.draw = request.draw;
            json.data.splice(0, requestStart - cacheLower);
            json.data.splice(requestLength, json.data.length);

            drawCallback(json);
        }
    };
};

$.fn.dataTable.Api.register("clearPipeline()", function () {
    return this.iterator("table", function (settings) {
        settings.clearCache = true;
    });
});

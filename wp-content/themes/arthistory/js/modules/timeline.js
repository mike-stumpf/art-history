(function() {

    var that = artHistory.timeline,
        timelineClass = '.map-timeline',
        timeFormat = 'YYYY-MM-DD';

    this.mapTimelines = [];

    this.hasTimeline = function(){
        return $(timelineClass).length > 0;
    };

    function convertDate(rawDate){
        return moment(rawDate, timeFormat);
    }

    function compareDates(firstDate, secondDate, operatorGreaterThan){
        firstDate = convertDate(firstDate);
        if (operatorGreaterThan) {
            return firstDate.isAfter(secondDate) ? firstDate : secondDate;
        } else {
            return firstDate.isBefore(secondDate) ? firstDate : secondDate;
        }
    }

    function initializeTimeline(element){
        var container = element,
            data = window[$(container).attr('data-items-list')],
            options = {
                zoomable: false,
                minHeight: 350,
                stack: true,
                throttleRedraw: 100,
                template: function (item) {
                    var template;
                    if (item.hasOwnProperty('end')){
                        template = Handlebars.templates.timeline_duration;
                    } else {
                        template = Handlebars.templates.timeline_moment;
                    }
                    return template(item);
                }
            },
            minDate,
            maxDate,
            items;
        data.forEach(function(entry){
            if (!minDate || !maxDate){
                minDate = maxDate = convertDate(entry.start);//everything is stored in momentjs format
            }
            minDate = compareDates(entry.start, minDate, false);
            if (entry.hasOwnProperty('end')){
                maxDate = compareDates(entry.end, maxDate, true);
            } else {
                maxDate = compareDates(entry.start, maxDate, true);
                // entry.type = 'point';//todo, determine if point is the correct type
            }
        });
        if (minDate){
            options.min = minDate.subtract(1,'month').format(timeFormat);
        }
        if (maxDate){
            options.max = maxDate.add(1,'month').format(timeFormat);//todo, update to years with real data
        }
        items = new vis.DataSet(data);
        that.mapTimelines.push(new vis.Timeline(container, items, options));
    }

    this.init = function(){

        artHistory.handlebars.applyHelpers();

        $(timelineClass).each(function(element){
            initializeTimeline(element);
        });
    };

}).apply(artHistory.timeline);
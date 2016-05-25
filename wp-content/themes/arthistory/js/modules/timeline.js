(function() {

    var that = artHistory.timeline,
        animations = artHistory.animations,
        timelineClass = '.map-timeline',
        timeFormat = 'YYYY-MM-DD',
        bodyElement = $('body');

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

    this.selectTimeline = function(selectedTimelineIndex){
        var bodyClasses = bodyElement.attr('class').split(' '),
            timelineLogicClass = '.l--show-for-map-',
            selectedTimePrefix = 'map-timeline-',
            selectedTimeline = selectedTimePrefix+selectedTimelineIndex,
            previousTimelineSelector,
            previousTimelineIndex;
        bodyClasses.forEach(function(entry){
            if(entry.indexOf(selectedTimePrefix) !== -1){
                previousTimelineSelector = entry;
                var pieces = previousTimelineSelector.split('-');
                previousTimelineIndex = pieces[pieces.length-1];
            }
        });
        if (previousTimelineSelector && previousTimelineIndex){
            bodyElement.removeClass(previousTimelineSelector);
            animations.fadeOut($(timelineLogicClass+previousTimelineIndex));
        }
        bodyElement.addClass(selectedTimeline);
        animations.fadeIn($(timelineLogicClass+selectedTimelineIndex));
    };

    this.init = function(){

        artHistory.handlebars.applyHelpers();

        $(timelineClass).each(function(element){
            initializeTimeline(element);
        });

        that.selectTimeline(1);
    };

}).apply(artHistory.timeline);
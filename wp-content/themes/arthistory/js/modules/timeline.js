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
            items = new vis.DataSet(data),
            options = {
                zoomable: false
            },
            minDate,
            maxDate;

        data.forEach(function(entry){
            //todo, add hbs template based on moment or duration
            if (!minDate || !maxDate){
                minDate = maxDate = convertDate(entry.start);//everything is stored in momentjs format
            }
            minDate = compareDates(entry.start, minDate, false);
            if (entry.hasOwnProperty('end')){
                maxDate = compareDates(entry.end, maxDate, true);
            } else {
                maxDate = compareDates(entry.start, maxDate, true);
            }
        });
        if (minDate){
            options.min = minDate.subtract(1,'month').format(timeFormat);
        }
        if (maxDate){
            options.max = maxDate.add(1,'month').format(timeFormat);//todo, update to years with real data
        }
        that.mapTimelines.push(new vis.Timeline(container, items, options));
    }

    this.init = function(){

        $(timelineClass).each(function(element){
            initializeTimeline(element);
        });
    };

}).apply(artHistory.timeline);
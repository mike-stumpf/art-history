(function() {

    //todo, documentation

//variables
//-----------------------------

    var that = artHistory.timeline,
        animations = artHistory.animations,
        helpers = artHistory.helpers,
        sidebarContainer = $('#maps-sidebar-container'),
        timelineSelectors = $('.maps-timeline-selector'),
        transitionOverlay = $('#maps-transition-overlay'),
        bodyElement = $('body'),
        timelineClass = '.map-timeline',
        timeFormat = 'YYYY-MM-DD';

    this.mapTimelines = [];


//helpers
//-----------------------------

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

    function isDateDuration(item){
        return item.hasOwnProperty('end') && item.end.length > 0;
    }

    function getDataItemsList(container){
        return $(container).attr('data-items-list');
    }


//timeline
//-----------------------------

    function initializeTimeline(element){
        var container = element,
            data = mapData[getDataItemsList(container)],
            options = {
                zoomable: false,
                minHeight: 350,
                stack: true,
                throttleRedraw: 100,
                template: function (item) {
                    var template;
                    if (isDateDuration(item)){
                        template = Handlebars.templates.timeline_duration;
                    } else {
                        template = Handlebars.templates.timeline_moment;
                    }
                    return template(item);
                }
            },
            minDate,
            maxDate,
            items = [],
            compiledDataObject;
        data.events.forEach(function(entry){
            compiledDataObject = {
                id: entry.id,
                image: entry.image,
                title: entry.timelineTitle,
                start: entry.start
            };
            entry.start = convertDate(entry.start);
            if (!minDate || !maxDate){
                minDate = maxDate = entry.start;//everything is stored in momentjs format
            }
            minDate = compareDates(entry.start, minDate, false);
            if (isDateDuration(entry)){
                compiledDataObject.end = entry.end;
                entry.end = convertDate(entry.end);
                maxDate = compareDates(entry.end, maxDate, true);
            } else {
                maxDate = compareDates(entry.start, maxDate, true);
            }
            items.push(compiledDataObject);
        });
        if (minDate){
            options.min = moment(minDate).subtract(4,'year').format(timeFormat);
        }
        if (maxDate){
            options.max = moment(maxDate).add(4,'year').format(timeFormat);
        }
        items = new vis.DataSet(items);
        that.mapTimelines.push(new vis.Timeline(container, items, options));
    }

    this.openEvent = function(eventId){
        console.log('open event',eventId);
    };


//sidebar
//----------------------------- 

    function populateSidebar(selectedTimelineIndex){
        var data = mapData[getDataItemsList('#timeline-map-'+selectedTimelineIndex)],
            template = Handlebars.templates.sidebar,
            html = template(data);
        animations.fadeOut(sidebarContainer)
            .then(function(){
                sidebarContainer.html(html);
                animations.fadeIn(sidebarContainer);
            });
    }

//navigation
//-----------------------------
    function handleSelectorClick(element){
        if (!element.hasClass(helpers.activeClass)) {
            timelineSelectors.each(function () {
                $(this).removeClass(helpers.activeClass);
            });
            selectTimeline(element.attr('data-timeline-selector'));
            element.addClass(helpers.activeClass);
        }
    }

   function selectTimeline(selectedTimelineIndex){
        var bodyClasses = bodyElement.attr('class').split(' '),
            timelineLogicClass = '.l--show-for-map-',
            selectedTimePrefix = 'map-timeline-',
            selectedTimeline = selectedTimePrefix+selectedTimelineIndex,
            previousTimelineSelector,
            previousTimelineIndex;
        animations.fadeIn(transitionOverlay)
            .then(function(){
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
                populateSidebar(selectedTimelineIndex);
                bodyElement.addClass(selectedTimeline);
                animations.fadeIn($(timelineLogicClass+selectedTimelineIndex))
                    .then(function(){
                        animations.fadeOut(transitionOverlay);
                    });
            });
    }

//main
//-----------------------------

    this.init = function(){

        artHistory.handlebars.applyHelpers();

        $(timelineClass).each(function(element){
            initializeTimeline(element);
        });

        selectTimeline(1);

        bodyElement
            .on('click', '.timeline-artwork', function(){
                that.openEvent($(this).attr('data-event-id'));
            });

        bodyElement
            .on('click', '.timeline-duration', function(){
                that.openEvent($(this).attr('data-event-id'));
            });

        timelineSelectors
            .on('click', function(event){
                event.preventDefault();
                handleSelectorClick($(this));
            });
    };

}).apply(artHistory.timeline);
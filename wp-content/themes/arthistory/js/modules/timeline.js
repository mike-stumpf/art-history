(function() {

//variables
//-----------------------------

    var that = artHistory.timeline,
        animations = artHistory.animations,
        helpers = artHistory.helpers,
        mediaQueries = artHistory.mediaQueries,
        sidebarContainer = $('#maps-sidebar-container'),
        timelineSelectors = $('.maps-timeline-selector'),
        transitionOverlay = $('#maps-transition-overlay'),
        headerMapTitle = $('#maps-header-title'),
        mobileModalTrigger = $('#maps-timeline-mobile-modal-trigger'),
        mobileModalContent = $('#maps-timeline-mobile-modal-content'),
        sidebarDataContainer = $('#maps-sidebar-data-container'),
        imageZoom = $('#image-zoom-container'),
        imageZoomOverlay = $('#image-zoom-overlay'),
        bodyElement = $('body'),
        activeClass = 'active',
        timelineClass = '.map-timeline',
        timelineArtworkClass = '.timeline-artwork',
        timelineDurationClass = '.timeline-duration',
        sidebarEntryClass = '.sidebar-entry',
        closeSidebarClass = '.f--close-sidebar',
        imageZoomClass = '.f--image-zoom',
        timeFormat = 'YYYY-MM-DD',
        currentTimeline,
        currentEvent;

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
                zoomMax: 173448000000,//5.5 years to ms
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
                title: entry.title,
                start: entry.date
            };
            entry.date = convertDate(entry.date);
            if (!minDate || !maxDate){
                minDate = maxDate = entry.date;//everything is stored in momentjs format
            }
            minDate = compareDates(entry.date, minDate, false);
            if (isDateDuration(entry)){
                compiledDataObject.end = entry.end;
                entry.end = convertDate(entry.end);
                maxDate = compareDates(entry.end, maxDate, true);
            } else {
                maxDate = compareDates(entry.date, maxDate, true);
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
        if(mediaQueries.isDesktop()){
            //if desktop
            highlightSidebarEvent(eventId);
        } else {
            populateModal(eventId);
        }
    };


//sidebar
//----------------------------- 

    function populateSidebar(){
        var data = mapData[getDataItemsList('#timeline-map-'+that.currentTimelineIndex)],
            template = Handlebars.templates.sidebar,
            html = template(data);
        sidebarContainer.html(html);
    }

    function populateModal(eventId){
        var currentTimelineData = mapData[getDataItemsList('#timeline-map-'+that.currentTimelineIndex)],
            data = _.find(currentTimelineData.events, {id: parseInt(eventId)}),
            template = Handlebars.templates.sidebar_modal,
            html = template(data);
        mobileModalContent.html(html);
        mobileModalTrigger.trigger('click');
    }

    function openSidebar(){
        return animations.animateElement(sidebarDataContainer, {
            properties: {
                left: '25.5%'
            }
        });
    }

    function closeSidebar(){
        that.currentEvent = null;
        $(sidebarEntryClass+'.'+activeClass).removeClass(activeClass);
        return animations.animateElement(sidebarDataContainer, {
            properties: {
                left: '-300px'
            }
        });
    }

    function highlightSidebarEvent(eventId){
        if(eventId !== that.currentEvent) {
            var currentTimelineData = mapData[getDataItemsList('#timeline-map-' + that.currentTimelineIndex)],
                data = _.find(currentTimelineData.events, {id: parseInt(eventId)}),
                template = Handlebars.templates.sidebar_event,
                html = template(data);
            //scroll timeline to center selected event
            that.mapTimelines[that.currentTimelineIndex-1].moveTo(data.date,{
                animation: true
            });
            closeSidebar()
                .then(function () {
                    that.currentEvent = eventId;
                    $(sidebarEntryClass+'[data-event-id="'+that.currentEvent+'"]').addClass(activeClass);
                    sidebarDataContainer.html(html);
                    openSidebar();
                });
        } else {
            closeSidebar();
        }
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

    function updateMapTitle(){
        var data = mapData[getDataItemsList('#timeline-map-'+that.currentTimelineIndex)];
        headerMapTitle.html(data.title.replace('-',' \u2013 '));
    }

    function selectTimeline(selectedTimelineIndex){
        that.currentTimelineIndex = selectedTimelineIndex;
        var bodyClasses = bodyElement.attr('class').split(' '),
            timelineLogicClass = '.l--show-for-map-',
            selectedTimePrefix = 'map-timeline-',
            selectedTimeline = selectedTimePrefix+that.currentTimelineIndex,
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
                closeSidebar();
                updateMapTitle();
                populateSidebar();
                bodyElement.addClass(selectedTimeline);
                animations.fadeIn($(timelineLogicClass+that.currentTimelineIndex))
                    .then(function(){
                        animations.fadeOut(transitionOverlay);
                    });
            });
    }

//zoom
//-----------------------------

    function openImageZoom(element){
        imageZoom.css('background-image','url(\''+element.attr('src')+'\')');
        animations.fadeIn(imageZoomOverlay)
            .then(function() {
                animations.fadeIn(imageZoom);
            });
    }

    function closeImageZoom(){
        animations.fadeOut(imageZoom)
            .then(function() {
                animations.fadeOut(imageZoomOverlay);
                imageZoom.attr('background-image','');
            });
    }

//main
//-----------------------------

    this.init = function(){

        if(window.location.href.indexOf('#maps-timeline-mobile-modal') !== -1){
            //don't show empty modal on page load
            window.location.href = window.location.href.split('#')[0];
        }

        artHistory.handlebars.applyHelpers();

        $(timelineClass).each(function(element){
            initializeTimeline(element);
        });

        selectTimeline(1);

        bodyElement
            .on('click', timelineArtworkClass, function(){
                that.openEvent($(this).attr('data-event-id'));
            });

        bodyElement
            .on('click', timelineDurationClass, function(){
                that.openEvent($(this).attr('data-event-id'));
            });

        bodyElement
            .on('click', sidebarEntryClass, function(){
                that.openEvent($(this).attr('data-event-id'));
            });

        timelineSelectors
            .on('click', function(event){
                event.preventDefault();
                handleSelectorClick($(this));
            });

        bodyElement
            .on('click', closeSidebarClass, function(){
                closeSidebar();
            });

        bodyElement
            .on('click', imageZoomClass, function(){
                openImageZoom($(this));
            });

        imageZoomOverlay
            .on('click', function(){
                closeImageZoom();
            });
    };

}).apply(artHistory.timeline);
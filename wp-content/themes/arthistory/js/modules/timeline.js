(function() {
    
    var that = artHistory.timeline,
        timelineClass = '.map-timeline';

    this.mapTimelines = [];
    
    this.hasTimeline = function(){
        return $(timelineClass).length > 0;
    };
    
    function initializeTimeline(element){
        // Configuration for the Timeline
        var container = element,
            items = new vis.DataSet(window[$(container).attr('data-items-list')]),
            options = {
                zoomable: false
            };
        // Create a Timeline
        that.mapTimelines.push(new vis.Timeline(container, items, options));
    }
    
    this.init = function(){

        $(timelineClass).each(function(element){
            initializeTimeline(element);
        });
    };
    
}).apply(artHistory.timeline);
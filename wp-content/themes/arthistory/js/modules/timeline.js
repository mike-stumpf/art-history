(function() {
    
    var that = artHistory.timeline,
        timelineClass = '.map-timeline',
        mapTimelines = [];
    
    this.hasTimeline = function(){
        return $(timelineClass).length > 0;
    };
    
    function initializeTimeline(element){
        // Configuration for the Timeline
        var options = {},
            container = element[0],
            items = new vis.DataSet(window[element.data('items-list')]);

        // Create a Timeline
        mapTimelines.push(new vis.Timeline(container, items, options));
    }
    
    this.init = function(){

        $(timelineClass).each(function(){
            initializeTimeline($(this));
        });
    };
    
}).apply(artHistory.timeline);
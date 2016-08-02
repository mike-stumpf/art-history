var artHistory = {

    //lib
    animations: {},
    handlebars: {},
    helpers: {},
    mediaQueries: {},

    //modules
    timeline: {},

    //functions
    init: function(){
        var timeline = artHistory.timeline;
        if (timeline.hasTimeline()){
            timeline.init();
        }
    }

};
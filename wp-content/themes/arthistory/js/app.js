var artHistory = {

    //lib
    animations: {},
    handlebars: {},

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
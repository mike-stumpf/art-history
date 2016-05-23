var artHistory = {

    //lib
    animations: {},

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
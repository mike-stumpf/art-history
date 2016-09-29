<?php namespace artHistory;

//reference external functions outside of current namespace
use artHistory\Lib\Helpers;
use artHistory\Lib\Dictionary;

// base class
class artHistory {

    private $timelineData;

    public function __construct(){
        //variables
        $timelines = array();

        //get timeline categories
        $timelineOptions = get_terms(
            array(
                'taxonomy' => Dictionary::$typeEventTimeline,
                'hide_empty' => false
            )
        );
        //sort timeline options by starting year
        usort($timelineOptions, array('artHistory\Lib\Helpers','sortByName'));

        //get timeline objects and events based on timeline category
        foreach($timelineOptions as $option){
            $timeline = new Data\Timeline($option);
            array_push($timelines, $timeline->getTimeline());
        }

        $this->timelineData = $timelines;
    }

    public function getTimelineData(){
        return $this->timelineData;
    }

}
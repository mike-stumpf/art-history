<?php namespace artHistory;

//todo, documentation

//reference external functions outside of current namespace
use WP_Query;
use artHistory\Lib\Helpers;
use artHistory\Lib\Dictionary;

// base class
class Maps {

    private $mapData;

    public function __construct(){
        //variables
        $mapGroups = array();

        //get timeline categories
        $mapOptions = get_terms(
            array(
                'taxonomy' => Dictionary::$typeEventTimeline,
                'hide_empty' => false
            )
        );
        //sort timeline options by starting year
        usort($mapOptions, array('artHistory\Lib\Helpers','sortByName'));

        //get timeline objects and events based on timeline category
        foreach($mapOptions as $option){

            //variables
            $timeline = null;
            $events = array();

            //get timeline object
            $timelineArguments = array(
                'post_status' => 'publish',
                'post_type' => Dictionary::$typeTimeline,
                'tax_query' => array(
                    array(
                        'taxonomy' => Dictionary::$typeEventTimeline,
                        'field'    => 'slug',
                        'terms'    => $option->slug
                    )
                ),
                'posts_per_page' => 1
            );
            $timelineQuery = new WP_Query($timelineArguments);
            if ($timelineQuery->have_posts()) {
                while ($timelineQuery->have_posts()) {
                    $timelineQuery->the_post();
                    $timelineId = get_the_ID();
                    $timelineObject = new Data\Timeline($timelineId);
                    $timeline = $timelineObject->getTimeline();
                }
            }

            //get event objects
            $eventArguments = array(
                'post_status' => 'publish',
                'post_type' => Dictionary::$typeEvent,
                'tax_query' => array(
                    array(
                        'taxonomy' => Dictionary::$typeEventTimeline,
                        'field'    => 'slug',
                        'terms'    => $option->slug
                    )
                ),
                'posts_per_page' => -1
            );
            $eventQuery = new WP_Query($eventArguments);
            if ($eventQuery->have_posts()) {
                while ($eventQuery->have_posts()) {
                    $eventQuery->the_post();
                    $eventId = get_the_ID();
                    $eventObject = new Data\Event($eventId);
                    array_push($events, $eventObject->getEvent());
                }
            }
            $optionNiceSlug = '';
            $optionNiceSlugPieces = explode('-',$option->slug);
            $i = 0;
            //make the JS compatible nice slug
            foreach($optionNiceSlugPieces as $piece){
                if ($i !== 0){
                    $piece = ucfirst($piece);
                }
                $optionNiceSlug .= $piece;
                $i++;
            }

            array_push($mapGroups, (object)array(
                'name'=>$option->name,
                'slug'=>$option->slug,
                'niceSlug'=>$optionNiceSlug,
                'timeline'=>$timeline,
                'events'=>$events
            ));
        }

        $this->mapData = $mapGroups;
    }

    public function getMapData(){
        return $this->mapData;
    }

}
<?php namespace artHistory\Data;

//reference external functions outside of current namespace
use WP_Query;
use artHistory\Lib\Helpers;
use artHistory\Lib\Dictionary;

// base class
class Timeline {

    private $id;
    private $navigationName;
    private $slug;
    private $niceSlug;
    private $timeline;
    private $timelineEvents;
    private $image;
    private $movements;
    private $title;

    public function __construct($option){
        //variables
        $this->title = $option->name;
        $this->slug = $option->slug;
        $this->timeline = null;
        $this->timelineEvents = array();
        $this->movements = array();

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
                $this->id = get_the_ID();
                $this->navigationName = get_the_title();
                $this->image = Helpers::getMetaValue($this->id,'timeline-header-image');
            }
        }

        //get movements
        $movementArguments = array(
            'post_status' => 'publish',
            'post_type' => Dictionary::$typeMovement,
            'tax_query' => array(
                array(
                    'taxonomy' => Dictionary::$typeEventTimeline,
                    'field'    => 'slug',
                    'terms'    => $option->slug
                )
            ),
            'posts_per_page' => -1
        );
        $movementQuery = new WP_Query($movementArguments);
        if ($movementQuery->have_posts()) {
            while ($movementQuery->have_posts()) {
                $movementQuery->the_post();
                $movementId = get_the_ID();
                $movementObject = new Movement($movementId);
                $movement = $movementObject->getMovement();
                array_push($this->movements, $movement);

                //add artworks to timeline from movement
                foreach($movement['artworks'] as $artwork){
                    array_push($this->timelineEvents, $artwork);
                }
            }
        }
        // sort movements by start and class
        usort($this->movements, array($this, "sortMovements"));

        //get events
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
                $eventObject = new Event($eventId);
                array_push($this->timelineEvents, $eventObject->getEvent());
            }
        }

        //get slug
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
        $this->niceSlug = $optionNiceSlug;
    }

    public function getTimeline() {
        return array(
            'id' => $this->id,
            'navigationName' => $this->navigationName,
            'title' => $this->title,
            'slug' => $this->slug,
            'niceSlug' => $this->niceSlug,
            'timeline' => $this->timeline,
            'timelineEvents' => $this->timelineEvents,
            'image' => $this->image,
            'movements' => $this->movements
        );
    }

    private function sortMovements($a, $b) {
        $c = strcmp($a['start'], $b['start']);
        if($c != 0) {
            return $c;
        }
        return strcmp($a['taughtClass'], $b['taughtClass']);
    }

}
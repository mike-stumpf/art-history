<?php namespace artHistory\Data;

//reference external functions outside of current namespace
use WP_Query;
use artHistory\Lib\Helpers;
use artHistory\Lib\Dictionary;

// base class
class Timeline {

    private $id;
    private $name;
    private $slug;
    private $niceSlug;
    private $timeline;
    private $events;
    private $image;

    public function __construct($option){
//var_dump($option);
//        die();
        //variables
        $this->name = $option->name;
        $this->slug = $option->slug;
        $this->timeline = null;
        $this->events = array();

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
                $this->image = Helpers::getMetaValue($this->id,'timeline-header-image');
            }
        }

        //get event objects
        $eventArguments = array(
            'post_status' => 'publish',
            'post_type' => Dictionary::$typeArtwork,
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
                $eventObject = new Artwork($eventId);
                array_push($this->events, $eventObject->getArtwork());
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
        $this->niceSlug = $optionNiceSlug;
    }

    public function getTimeline(){
        return array(
            'id'=>$this->id,
            'title'=>$this->name,
            'slug'=>$this->slug,
            'niceSlug'=>$this->niceSlug,
            'timeline'=>$this->timeline,
            'events'=>$this->events,
            'image'=>$this->image
        );
    }

}
<?php
/**
 * variables
 */

// base class
class artHistoryTimeline {
    /**
     * variables
     */

    public $typeTimeline;
    public $typeEvent;
    public $typeBook;
    public $typeArticle;
    public $typePowerpoint;
    public $typeVideo;


    /**
     * main
     */
    public function __construct(){
        $this->typeTimeline = 'timeline';
        $this->typeEvent = 'event';
        $this->typeBook = 'book';
        $this->typeArticle = 'article';
        $this->typePowerpoint = 'powerpoint';
        $this->typeVideo = 'video';
    }

    private static function sortByName($a, $b) {
        return $a->name > $b->name;
    }

    public function getTimelineData(){

        //variables
        $timelineGroups = array();

        //get timeline categories
        $timelineOptions = get_terms(
            array(
                'taxonomy' => 'event-timeline',
                'hide_empty' => false
            )
        );
        //sort timeline options by starting year
        usort($timelineOptions, array('artHistoryTimeline','sortByName'));

        //get timeline objects and events based on timeline category
        foreach($timelineOptions as $option){

            //variables
            $timeline = null;
            $events = array();

            //get timeline object
            $timelineArguments = array(
                'post_status' => 'publish',
                'post_type' => $this->typeTimeline,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'event-timeline',
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
                    $timeline = $this->constructTimelineObject($timelineId);
                }
            }

            //get event objects
            //todo

            array_push($timelineGroups, (object)array(
                'name'=>$option->name,
                'slug'=>$option->slug,
                'timeline'=>$timeline,
                'events'=>$events
            ));
        }

        return $timelineGroups;
    }

    /**
     * helper functions
     */

    private function getChildren($parentId, $parentType, $postType){
        //variables
        $children = array();
        $childArguments = array(
            'meta_query' => array(
                array(
                    'key' => '_wpcf_belongs_'.$parentType.'_id',
                    'value' => $parentId
                )
            ),
            'post_type' => $postType,
            'posts_per_page'=>-1,
            'post_status' => 'publish'
        );
        $childQuery = new WP_Query($childArguments);
//        var_dump($childArguments);
//        echo '<br/><br/>';
//        var_dump($childQuery->posts);
//        die();
        if ($childQuery->have_posts()) {
            while ($childQuery->have_posts()) {
                $childQuery->the_post();
                $childId = get_the_ID();
                switch($postType){
                    case 'book':
                        $child = $this->constructBookObject($childId);
                        break;
                    case 'article':
                        $child = $this->constructArticleObject($childId);
                        break;
                    case 'powerpoint':
                        $child = $this->constructPowerpointObject($childId);
                        break;
                    case 'video':
                        $child = $this->constructVideoObject($childId);
                        break;
                }
                array_push($children, $child);
            }
        }
        return $children;
    }

    /**
     * object constructors
     */

    public function constructTimelineObject($timelineId){
        //variables
        $parentType = $this->typeTimeline;

        //fields
        $headerImage = getMetaValue($timelineId,'timeline-header-image');
        $description = wpautop(getMetaValue($timelineId,'timeline-description'));

        //get children
        $books = $this->getChildren($timelineId,$parentType,$this->typeBook);
        $powerpoints = $this->getChildren($timelineId,$parentType,$this->typePowerpoint);
        $articles = $this->getChildren($timelineId,$parentType,$this->typeArticle);
        $videos = $this->getChildren($timelineId,$parentType,$this->typeVideo);

        //response object
        return (object)array(
            'id'=>$timelineId,
            'image'=>$headerImage,
            'description'=>$description,
            'books'=>$books,
            'powerpoints'=>$powerpoints,
            'articles'=>$articles,
            'videos'=>$videos
        );
    }

    public function constructEventObject($eventId){
        //fields
        $title = getMetaValue($eventId,'event-title');
        $timelineTitle = getMetaValue($eventId,'event-timeline-title');
        $eventStart = getMetaValue($eventId,'event-start');
        $eventEnd = getMetaValue($eventId,'event-end');
        $eventImage = getMetaValue($eventId,'event-image');
        $powerpoints = array();
        $books = array();
        $articles = array();
        $videos = array();

        //response object
        return (object)array(
            'id'=>$eventId,
            'title'=>$title,
            'timelineTitle'=>$timelineTitle,
            'start'=>$eventStart,
            'end'=>$eventEnd,
            'image'=>$eventImage,
            'powerpoints'=>$powerpoints,
            'books'=>$books,
            'articles'=>$articles,
            'videos'=>$videos
        );
    }

    public function constructBookObject($bookId){
        //fields
//todo
        //response object
        return (object)array(
            'id'=>$bookId
        );
    }

    public function constructPowerpointObject($powerpointId){
        //fields
//todo
        //response object
        return (object)array(
            'id'=>$powerpointId
        );
    }


    public function constructArticleObject($articleId){
        //fields
//todo
        //response object
        return (object)array(
            'id'=>$articleId
        );
    }


    public function constructVideoObject($videoId){
        //fields
//todo
        //response object
        return (object)array(
            'id'=>$videoId
        );
    }

}
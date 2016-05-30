<?php

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
    public $typeEventTimeline;
    private $helpers;

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
        $this->typeEventTimeline = 'event-timeline';
        $this->helpers = new artHistoryHelpers();
    }

    public function getTimelineData(){

        //variables
        $timelineGroups = array();

        //get timeline categories
        $timelineOptions = get_terms(
            array(
                'taxonomy' => $this->typeEventTimeline,
                'hide_empty' => false
            )
        );
        //sort timeline options by starting year
        usort($timelineOptions, array('artHistoryHelpers','sortByName'));

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
                        'taxonomy' => $this->typeEventTimeline,
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
        $headerImage = $this->helpers->getMetaValue($timelineId,'timeline-header-image');
        $description = wpautop($this->helpers->getMetaValue($timelineId,'timeline-description'));

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
        //variables
        $parentType = $this->typeEvent;

        //fields
        $title = $this->helpers->getMetaValue($eventId,'event-title');
        $timelineTitle = $this->helpers->getMetaValue($eventId,'event-timeline-title');
        $eventStart = $this->helpers->getMetaValue($eventId,'event-start');
        $eventEnd = $this->helpers->getMetaValue($eventId,'event-end');
        $eventImage = $this->helpers->getMetaValue($eventId,'event-image');

        //get children
        $books = $this->getChildren($eventId,$parentType,$this->typeBook);
        $powerpoints = $this->getChildren($eventId,$parentType,$this->typePowerpoint);
        $articles = $this->getChildren($eventId,$parentType,$this->typeArticle);
        $videos = $this->getChildren($eventId,$parentType,$this->typeVideo);

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
        $title = $this->helpers->getMetaValue($bookId,'book-title');
        $author = $this->helpers->getMetaValue($bookId,'book-author');
        $callNumber = $this->helpers->getMetaValue($bookId,'book-call-number');
        $year = $this->helpers->getMetaValue($bookId,'book-year');

        //response object
        return (object)array(
            'id'=>$bookId,
            'title'=>$title,
            'author'=>$author,
            'callNumber'=>$callNumber,
            'year'=>$year
        );
    }

    public function constructPowerpointObject($powerpointId){
        //fields
        $title = $this->helpers->getMetaValue($powerpointId,'powerpoint-title');
        $url = $this->helpers->getMetaValue($powerpointId,'powerpoint-url');

        //response object
        return (object)array(
            'id'=>$powerpointId,
            'title'=>$title,
            'url'=>$url
        );
    }


    public function constructArticleObject($articleId){
        //fields
        $title = $this->helpers->getMetaValue($articleId,'article-title');
        $author = $this->helpers->getMetaValue($articleId,'article-author');

        //response object
        return (object)array(
            'id'=>$articleId,
            'title'=>$title,
            'author'=>$author
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
<?php

function sortByName($a, $b) {
    return $a->name > $b->name;
}

function getTimelineData(){

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
    usort($timelineOptions, 'sortByName');

    //get timeline objects and events based on timeline category
    foreach($timelineOptions as $option){

        //variables
        $timeline = null;
        $events = array();

        //get timeline object
        $timelineArguments = array(
            'post_status' => 'publish',
            'post_type' => 'timeline',
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
                $timeline = constructTimelineObject($timelineId);
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

function constructTimelineObject($timelineId){
    //fields
    $headerImage = getMetaValue($timelineId,'timeline-header-image');
    $description = wpautop(getMetaValue($timelineId,'timeline-description'));

    //response object
    return (object)array(
        'id'=>$timelineId,
        'image'=>$headerImage,
        'description'=>$description
    );
}

function constructEventObject($eventId){
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
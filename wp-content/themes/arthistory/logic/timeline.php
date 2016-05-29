<?php

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

    //get timeline objects and events based on timeline category
    foreach($timelineOptions as $option){

        //variables
        $timeline = null;
        $events = array();

        //get timeline object
        $timelineArguments = array(
            'post_type' => 'post',
            'tax_query' => array(
                array(
                    'taxonomy' => 'event-timeline',
                    'field'    => 'slug',
                    'terms'    => $option->slug,
                )
            ),
            'posts_per_page' => 1
        );
        $timelineQuery = new WP_Query($timelineArguments);
        if ($timelineQuery->have_posts() ) {
            while ($timelineQuery->have_posts() ) {
                $timelineQuery->the_post();
                $timeline = constructTimelineObject(get_the_ID());
            }
        }

        //get event objects
        //todo

        array_push($timelineGroups, array(
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
    $headerImage = get_post_meta($timelineId,'timeline-header-image');
    $description = get_post_meta($timelineId,'timeline-description');

    //response object
    return (object)array(
        'image'=>$headerImage,
        'description'=>$description
    );
}
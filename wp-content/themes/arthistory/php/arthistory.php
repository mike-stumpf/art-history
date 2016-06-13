<?php namespace artHistory;

//todo, documentation

//reference external functions outside of current namespace
use artHistory\Lib\Helpers;
use artHistory\Lib\Dictionary;

// base class
class artHistory {

    private $mapData;

    public function __construct(){
        //variables
        $maps = array();

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
            $map = new Data\Map($option);
            array_push($maps, $map->getMap());
        }

        $this->mapData = $maps;
    }

    public function getMapData(){
        return $this->mapData;
    }

}
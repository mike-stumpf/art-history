<?php namespace artHistory\Data;

use artHistory\Lib\Helpers;
use artHistory\Lib\Dictionary;

//todo, documentation

class Timeline {

    private $timelineId;
    private $headerImage;
    private $description;
    private $books;
    private $powerpoints;
    private $articles;
    private $videos;

    public function __construct($timelineId){
        //variables
        $parentType = Dictionary::$typeTimeline;

        //fields
        $this->headerImage = Helpers::getMetaValue($timelineId,'timeline-header-image');
        $this->description = wpautop(Helpers::getMetaValue($timelineId,'timeline-description'));

        //get children
        $this->books = Helpers::getChildren($timelineId,$parentType,Dictionary::$typeBook);
        $this->powerpoints = Helpers::getChildren($timelineId,$parentType,Dictionary::$typePowerpoint);
        $this->articles = Helpers::getChildren($timelineId,$parentType,Dictionary::$typeArticle);
        $this->videos = Helpers::getChildren($timelineId,$parentType,Dictionary::$typeVideo);

        //response object
        return $this->getTimeline();
    }

    public function __toString(){
        return 'timeline here';
    }

    public function getTimeline(){
        return array(
            'id'=>$this->timelineId,
            'image'=>$this->headerImage,
            'description'=>$this->description,
            'books'=>$this->books,
            'powerpoints'=>$this->powerpoints,
            'articles'=>$this->articles,
            'videos'=>$this->videos
        );
    }
}
<?php namespace artHistory\Data;

use artHistory\Lib\Helpers;
use artHistory\Lib\Dictionary;

//todo, documentation

class Timeline {

    public $id;
    public $image;
    public $description;
    public $books;
    public $powerpoints;
    public $articles;
    public $videos;

    public function __construct($timelineId){
        //variables
        $parentType = Dictionary::$typeTimeline;

        //fields
        $this->id = $timelineId;
        $this->image = Helpers::getMetaValue($this->id,'timeline-header-image');
        $this->description = '';//htmlentities(wpautop(Helpers::getMetaValue($this->id,'timeline-description')));

        //get children
        $this->books = Helpers::getChildren($this->id,$parentType,Dictionary::$typeBook);
        $this->powerpoints = Helpers::getChildren($this->id,$parentType,Dictionary::$typePowerpoint);
        $this->articles = Helpers::getChildren($this->id,$parentType,Dictionary::$typeArticle);
        $this->videos = Helpers::getChildren($this->id,$parentType,Dictionary::$typeVideo);
        
    }

    public function __toString(){
        return json_encode($this->getTimeline());
    }

    public function getTimeline(){
        return array(
            'id'=>$this->id,
            'image'=>$this->image,
            'description'=>$this->description,
            'books'=>$this->books,
            'powerpoints'=>$this->powerpoints,
            'articles'=>$this->articles,
            'videos'=>$this->videos
        );
    }
}
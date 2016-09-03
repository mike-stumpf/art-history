<?php namespace artHistory\Data;

use DateTime;
use artHistory\Lib\Helpers;
use artHistory\Lib\Dictionary;

class Event {

    private $id;
    private $title;
    private $timelineTitle;
    private $image;
    private $powerpoints;
    private $books;
    private $articles;
    private $videos;

    public function __construct($eventId) {
        //variables
        $parentType = Dictionary::$typeEvent;

        //fields
        $this->id = $eventId;
        $this->title = Helpers::getMetaValue($eventId,'event-title');
        $timelineTitle = Helpers::getMetaValue($eventId,'event-timeline-title');
        $this->timelineTitle = !empty($timelineTitle)?$timelineTitle:$this->title;
        $this->image = Helpers::resizeImage(Helpers::getMetaValue($eventId,'event-image'),100,100);
        $this->eventStart = Helpers::getMetaValue($eventId,'event-start');
        if(strlen($this->eventStart) > 1) {
            //only convert to date if not null
            $eventStartTime = new DateTime();
            $eventStartTime->setTimestamp((int)$this->eventStart);
            $this->eventStart = $eventStartTime->format('Y-m-d');
        }
        $this->eventEnd = Helpers::getMetaValue($eventId,'event-end');
        if(strlen($this->eventEnd) > 1){
            $eventEndTime = new DateTime();
            $eventEndTime->setTimestamp((int)$this->eventEnd);
            $this->eventEnd = $eventEndTime->format('Y-m-d');
        }

        //get children
        $this->books = Helpers::getChildren($eventId,$parentType,Dictionary::$typeBook);
        $this->powerpoints = Helpers::getChildren($eventId,$parentType,Dictionary::$typePowerpoint);
        $this->articles = Helpers::getChildren($eventId,$parentType,Dictionary::$typeArticle);
        $this->videos = Helpers::getChildren($eventId,$parentType,Dictionary::$typeVideo);
        
    }

    public function __toString() {
        return json_encode($this->getEvent());
    }
    
    public function getEvent(){
        return array(
            'id'=>$this->id,
            'title'=>$this->title,
            'timelineTitle'=>$this->timelineTitle,
            'start'=>$this->eventStart,
            'end'=>$this->eventEnd,
            'image'=>$this->image,
            'powerpoints'=>$this->powerpoints,
            'books'=>$this->books,
            'articles'=>$this->articles,
            'videos'=>$this->videos
        );
    }
}
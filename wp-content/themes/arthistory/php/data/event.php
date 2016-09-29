<?php namespace artHistory\Data;
use DateTime;
use artHistory\Lib\Helpers;

class Event {
    private $id;
    private $title;
    private $image;
    private $largeImage;
    public function __construct($eventId) {
        //fields
        $this->id = $eventId;
        $this->title = Helpers::getMetaValue($eventId,'event-title');
        $this->image = Helpers::resizeImage(Helpers::getMetaValue($eventId,'event-image'),100,100);
        $this->largeImage = Helpers::getMetaValue($eventId,'event-image');
        $this->eventStart = Helpers::getMetaValue($eventId,'event-start');
        if(strlen($this->eventStart) > 1) {
            //only convert to date if not null
            $eventStartTime = new DateTime('@'.(float)$this->eventStart);
            $this->eventStart = $eventStartTime->format('Y-m-d');
        }
        $this->eventEnd = Helpers::getMetaValue($eventId,'event-end');
        if(strlen($this->eventEnd) > 1){
            $eventEndTime = new DateTime('@'.(float)$this->eventEnd);
            $this->eventEnd = $eventEndTime->format('Y-m-d');
        }
    }
    public function __toString() {
        return json_encode($this->getEvent());
    }

    public function getEvent(){
        return array(
            'id'=>$this->id,
            'title'=>$this->title,
            'start'=>$this->eventStart,
            'end'=>$this->eventEnd,
            'image'=>$this->image,
            'largeImage'=>$this->largeImage
        );
    }
}
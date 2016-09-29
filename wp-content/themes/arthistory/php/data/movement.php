<?php namespace artHistory\Data;

use DateTime;
use artHistory\Lib\Helpers;
use artHistory\Lib\Dictionary;

class Movement {

    private $id;
    private $title;
    private $image;
    private $largeImage;
    private $movementStart;
    private $movementEnd;

    private $powerpoints;
    private $books;
    private $articles;
    private $videos;
    private $artworks;
    private $websites;

    public function __construct($movementId) {
        //variables
        $parentType = Dictionary::$typeMovement;

        //fields
        $this->id = $movementId;
        $this->title = Helpers::getMetaValue($movementId,'movement-title');
        $this->image = Helpers::resizeImage(Helpers::getMetaValue($movementId,'movement-image'),100,100);
        $this->largeImage = Helpers::getMetaValue($movementId,'movement-image');
        $this->movementStart = Helpers::getMetaValue($movementId,'movement-start');
        if(strlen($this->movementStart) > 1) {
            //only convert to date if not null
            $movementStartTime = new DateTime('@'.(float)$this->movementStart);
            $this->movementStart = $movementStartTime->format('Y-m-d');
        }
        $this->movementEnd = Helpers::getMetaValue($movementId,'movement-end');
        if(strlen($this->movementEnd) > 1){
            $movementEndTime = new DateTime('@'.(float)$this->movementEnd);
            $this->movementEnd = $movementEndTime->format('Y-m-d');
        }

        //get children
        $this->books = Helpers::getChildren($movementId,$parentType,Dictionary::$typeBook);
        $this->powerpoints = Helpers::getChildren($movementId,$parentType,Dictionary::$typePowerpoint);
        $this->articles = Helpers::getChildren($movementId,$parentType,Dictionary::$typeArticle);
        $this->videos = Helpers::getChildren($movementId,$parentType,Dictionary::$typeVideo);
        $this->artworks = Helpers::getChildren($movementId,$parentType,Dictionary::$typeArtwork);
        $this->websites = Helpers::getChildren($movementId,$parentType,Dictionary::$typeWebsite);
    }

    public function __toString() {
        return json_encode($this->getMovement());
    }

    public function getMovement(){
        return array(
            'id'=>$this->id,
            'title'=>$this->title,
            'image'=>$this->image,
            'largeImage'=>$this->largeImage,
            'start'=>$this->movementStart,
            'end'=>$this->movementEnd,
            'powerpoints'=>$this->powerpoints,
            'books'=>$this->books,
            'articles'=>$this->articles,
            'videos'=>$this->videos,
            'artworks'=>$this->artworks,
            'websites'=>$this->websites
        );
    }
}
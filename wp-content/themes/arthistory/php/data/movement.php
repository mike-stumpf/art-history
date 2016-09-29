<?php namespace artHistory\Data;

use artHistory\Lib\Helpers;
use artHistory\Lib\Dictionary;

class Movement {

    private $id;
    private $title;
    private $image;
    private $largeImage;
    private $powerpoints;
    private $books;
    private $articles;
    private $videos;
    private $artworks;

    public function __construct($movementId) {
        //variables
        $parentType = Dictionary::$typeMovement;

        //fields
        $this->id = $movementId;
        $this->title = Helpers::getMetaValue($movementId,'movement-title');
        $this->image = Helpers::resizeImage(Helpers::getMetaValue($movementId,'movement-image'),100,100);
        $this->largeImage = Helpers::getMetaValue($movementId,'movement-image');

        //get children
        $this->books = Helpers::getChildren($movementId,$parentType,Dictionary::$typeBook);
        $this->powerpoints = Helpers::getChildren($movementId,$parentType,Dictionary::$typePowerpoint);
        $this->articles = Helpers::getChildren($movementId,$parentType,Dictionary::$typeArticle);
        $this->videos = Helpers::getChildren($movementId,$parentType,Dictionary::$typeVideo);
        $this->artworks = Helpers::getChildren($movementId,$parentType,Dictionary::$typeArtwork);
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
            'powerpoints'=>$this->powerpoints,
            'books'=>$this->books,
            'articles'=>$this->articles,
            'videos'=>$this->videos,
            'artworks'=>$this->artworks
        );
    }
}
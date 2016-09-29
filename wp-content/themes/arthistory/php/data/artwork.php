<?php namespace artHistory\Data;

use DateTime;
use artHistory\Lib\Helpers;
use artHistory\Lib\Dictionary;

class Artwork {

    private $id;
    private $title;
    private $image;
    private $largeImage;
    private $powerpoints;
    private $books;
    private $articles;
    private $videos;
    private $artworkDate;

    public function __construct($artworkId) {
        //variables
        $parentType = Dictionary::$typeArtwork;

        //fields
        $this->id = $artworkId;
        $this->title = Helpers::getMetaValue($artworkId,'artwork-title');
        $this->image = Helpers::resizeImage(Helpers::getMetaValue($artworkId,'artwork-image'),100,100);
        $this->largeImage = Helpers::getMetaValue($artworkId,'artwork-image');
        $this->artworkDate = Helpers::getMetaValue($artworkId,'artwork-date');
        if(strlen($this->artworkDate) > 1) {
            //only convert to date if not null
            $artworkDateTime = new DateTime('@'.(float)$this->artworkDate);
            $this->artworkDate = $artworkDateTime->format('Y-m-d');
        }

        //get children
        $this->books = Helpers::getChildren($artworkId,$parentType,Dictionary::$typeBook);
        $this->powerpoints = Helpers::getChildren($artworkId,$parentType,Dictionary::$typePowerpoint);
        $this->articles = Helpers::getChildren($artworkId,$parentType,Dictionary::$typeArticle);
        $this->videos = Helpers::getChildren($artworkId,$parentType,Dictionary::$typeVideo);
        
    }

    public function __toString() {
        return json_encode($this->getArtwork());
    }
    
    public function getArtwork(){
        return array(
            'id'=>$this->id,
            'title'=>$this->title,
            'date'=>$this->artworkDate,
            'image'=>$this->image,
            'largeImage'=>$this->largeImage,
            'powerpoints'=>$this->powerpoints,
            'books'=>$this->books,
            'articles'=>$this->articles,
            'videos'=>$this->videos
        );
    }
}
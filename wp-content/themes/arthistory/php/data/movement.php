<?php namespace artHistory\Data;

use artHistory\Lib\Helpers;
use artHistory\Lib\Dictionary;

class Movement {

    private $id;
    private $title;
    private $image;
    private $largeImage;
    private $movementStart;

    private $powerpoints;
    private $books;
    private $articles;
    private $videos;
    private $artworks;
    private $websites;
    private $taughtClass;

    public function __construct($movementId) {
        //variables
        $parentType = Dictionary::$typeMovement;

        //fields
        $this->id = $movementId;
        $this->title = Helpers::getMetaValue($movementId,'movement-title');
        $imagePath = Helpers::getMetaValue($movementId,'movement-image');
        if (strlen($imagePath) > 0) {
            $this->image = get_home_url().Helpers::resizeImage($imagePath,100,100);
            $this->largeImage = $imagePath;
        }
        $taughtClassObjectArray = wp_get_post_terms($movementId, Dictionary::$typeTaughtClass);
        if (sizeof($taughtClassObjectArray) > 0) {
            $this->taughtClass = $taughtClassObjectArray[0]->slug;
        } else {
            $this->taughtClass = '';
        }

        //get children
        $this->books = Helpers::getChildren($movementId,$parentType,Dictionary::$typeBook);
        $this->powerpoints = Helpers::getChildren($movementId,$parentType,Dictionary::$typePowerpoint);
        $this->articles = Helpers::getChildren($movementId,$parentType,Dictionary::$typeArticle);
        $this->videos = Helpers::getChildren($movementId,$parentType,Dictionary::$typeVideo);
        $this->artworks = Helpers::getChildren($movementId,$parentType,Dictionary::$typeArtwork);
        $this->websites = Helpers::getChildren($movementId,$parentType,Dictionary::$typeWebsite);

        foreach($this->artworks as $index=>$artwork){
            // set taught class for ui distinction
            $artwork['taughtClass'] = $this->taughtClass;
            $artwork['movementId'] = $this->id;
            $this->artworks[$index] = $artwork;
            //get movement start
            if (!$this->movementStart || $this->movementStart > $artwork['start']){
                $this->movementStart = $artwork['start'];
            }
        }
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
            'powerpoints'=>$this->powerpoints,
            'books'=>$this->books,
            'articles'=>$this->articles,
            'videos'=>$this->videos,
            'artworks'=>$this->artworks,
            'websites'=>$this->websites,
            'taughtClass'=>$this->taughtClass
        );
    }
}
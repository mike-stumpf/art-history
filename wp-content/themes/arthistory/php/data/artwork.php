<?php namespace artHistory\Data;

use DateTime;
use artHistory\Lib\Helpers;

class Artwork {

    private $id;
    private $title;
    private $image;
    private $largeImage;
    private $artworkDate;

    public function __construct($artworkId) {

        //fields
        $this->id = $artworkId;
        $this->title = Helpers::getMetaValue($artworkId,'artwork-title');
        $this->image = get_home_url().Helpers::resizeImage(Helpers::getMetaValue($artworkId,'artwork-image'),100,100);
        $this->largeImage = Helpers::getMetaValue($artworkId,'artwork-image');
        $this->artworkDate = Helpers::getMetaValue($artworkId,'artwork-date');
        if(strlen($this->artworkDate) > 1) {
            //only convert to date if not null
            $artworkDateTime = new DateTime('@'.(float)$this->artworkDate);
            $this->artworkDate = $artworkDateTime->format('Y-m-d');
        }
        
    }

    public function __toString() {
        return json_encode($this->getArtwork());
    }
    
    public function getArtwork(){
        return array(
            'id'=>$this->id,
            'title'=>$this->title,
            'start'=>$this->artworkDate,
            'image'=>$this->image,
            'largeImage'=>$this->largeImage
        );
    }
}
<?php namespace artHistory\Data;

use artHistory\Lib\Helpers;

class Video {

    private $id;
    private $title;
    private $url;

    public function __construct($powerpointId){

        //fields
        $this->id = $powerpointId;
        $this->title = Helpers::getMetaValue($powerpointId,'video-title');
        $this->url = Helpers::getMetaValue($powerpointId,'video-url');
        
    }

    public function __toString(){
        return json_encode($this->getVideo());
    }

    public function getVideo(){
        return array(
            'id'=>$this->id,
            'title'=>$this->title,
            'url'=>$this->url
        );
    }
}
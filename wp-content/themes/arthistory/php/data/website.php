<?php namespace artHistory\Data;

use artHistory\Lib\Helpers;

class Website {

    private $id;
    private $title;
    private $url;

    public function __construct($websiteId){

        //fields
        $this->id = $websiteId;
        $this->title = Helpers::getMetaValue($websiteId,'website-title');
        $this->url = Helpers::getMetaValue($websiteId,'website-url');

    }

    public function __toString(){
        return json_encode($this->getWebsite());
    }

    public function getWebsite(){
        return array(
            'id'=>$this->id,
            'title'=>$this->title,
            'url'=>$this->url
        );
    }
}
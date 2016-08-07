<?php namespace artHistory\Data;

use artHistory\Lib\Helpers;

class PowerPoint {

    private $id;
    private $title;
    private $url;

    public function __construct($powerpointId){

        //fields
        $this->id = $powerpointId;
        $this->title = Helpers::getMetaValue($powerpointId,'powerpoint-title');
        $this->url = Helpers::getMetaValue($powerpointId,'powerpoint-url');
        
    }

    public function __toString(){
        return json_encode($this->getPowerPoint());
    }

    public function getPowerPoint(){
        return array(
            'id'=>$this->id,
            'title'=>$this->title,
            'url'=>$this->url
        );
    }
}
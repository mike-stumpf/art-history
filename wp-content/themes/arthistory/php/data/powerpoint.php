<?php namespace artHistory\Data;

use artHistory\Lib\Helpers;

//todo, documentation

class PowerPoint {

    private $id;
    private $title;
    private $url;

    public function __construct($powerpointId){

        //fields
        $this->id = $powerpointId;
        $this->title = Helpers::getMetaValue($powerpointId,'powerpoint-title');
        $this->url = Helpers::getMetaValue($powerpointId,'powerpoint-url');

        //response object
        return $this->getPowerPoint();
    }

    public function __toString(){
        return 'powerpoint here';
    }

    public function getPowerPoint(){
        return (object)array(
            'id'=>$this->id,
            'title'=>$this->title,
            'url'=>$this->url
        );
    }
}
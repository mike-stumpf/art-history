<?php namespace artHistory\Data;

use artHistory\Lib\Helpers;

class PowerPoint {

    private $id;
    private $title;
    private $url;
    private $file;

    public function __construct($powerpointId){

        //fields
        $this->id = $powerpointId;
        $this->title = Helpers::getMetaValue($powerpointId,'powerpoint-title');
        $this->url = Helpers::getMetaValue($powerpointId,'powerpoint-url');
        $this->file = Helpers::getMetaValue($powerpointId,'powerpoint-file');

    }

    public function __toString(){
        return json_encode($this->getPowerPoint());
    }

    public function getPowerPoint(){
        return array(
            'id'=>$this->id,
            'title'=>$this->title,
            'url'=>$this->url,
            'file'=>$this->file
        );
    }
}
<?php namespace artHistory\Data;

use artHistory\Lib\Helpers;

class Book {

    private $id;
    private $title;
    private $author;
    private $callNumber;
    private $year;
    private $url;
    private $publisher;

    public function __construct($bookId){

        ///fields
        $this->id = $bookId;
        $this->title = Helpers::getMetaValue($bookId,'book-title');
        $this->author = Helpers::getMetaValue($bookId,'book-author');
        $this->callNumber = Helpers::getMetaValue($bookId,'book-call-number');
        $this->year = Helpers::getMetaValue($bookId,'book-year');
        $this->url = Helpers::getMetaValue($bookId,'book-url');
        $this->publisher = Helpers::getMetaValue($bookId,'book-publisher');
    }

    public function __toString(){
        return json_encode($this->getBook());
    }

    public function getBook(){
        return array(
            'id'=>$this->id,
            'title'=>$this->title,
            'author'=>$this->author,
            'callNumber'=>$this->callNumber,
            'year'=>$this->year,
            'url'=>$this->url,
            'publisher'=>$this->publisher
        );
    }
}
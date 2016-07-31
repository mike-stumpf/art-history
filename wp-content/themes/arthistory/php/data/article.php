<?php namespace artHistory\Data;

use artHistory\Lib\Helpers;

//todo, documentation

class Article {

    private $id;
    private $title;
    private $author;
    private $year;

    public function __construct($articleId){

        //fields
        $this->id = $articleId;
        $this->title = Helpers::getMetaValue($articleId,'article-title');
        $this->author = Helpers::getMetaValue($articleId,'article-author');
        $this->year = Helpers::getMetaValue($articleId,'article-year');
    }

    public function __toString(){
        return json_encode($this->getArticle());
    }

    public function getArticle(){
        return array(
            'id'=>$this->id,
            'title'=>$this->title,
            'author'=>$this->author,
            'year'=>$this->year
        );
    }
}
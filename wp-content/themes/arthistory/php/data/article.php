<?php namespace artHistory\Data;

use artHistory\Lib\Helpers;

//todo, documentation

class Article {

    private $id;
    private $title;
    private $author;

    public function __construct($articleId){

        //fields
        $this->id = $articleId;
        $this->title = Helpers::getMetaValue($articleId,'article-title');
        $this->author = Helpers::getMetaValue($articleId,'article-author');

        //response object
        return $this->getArticle();
    }

    public function __toString(){
        return 'article here';
    }

    public function getArticle(){
        return (object)array(
            'id'=>$this->id,
            'title'=>$this->title,
            'author'=>$this->author
        );
    }
}
<?php namespace artHistory\Lib;

use WP_Query;
use artHistory\Data;

//todo, documentation

/**
 * helper functions
 */

// base class
class Helpers {
    
    public static function getAssetDirectory() {
        return get_template_directory_uri() . '/build/assets/';
    }

    public static function getMetaValue($postId, $metaKey, $isSingleValue = true) {
        //get keys one at a time instead of not specifying a meta key for convenience
        return get_post_meta($postId, 'wpcf-' . $metaKey, $isSingleValue);
    }

    public static function sortByName($a, $b) {
        return $a->name > $b->name;
    }

    public static function getChildren($parentId, $parentType, $postType){
        //variables
        $children = array();
        $childArguments = array(
            'meta_query' => array(
                array(
                    'key' => '_wpcf_belongs_'.$parentType.'_id',
                    'value' => $parentId
                )
            ),
            'post_type' => $postType,
            'posts_per_page'=>-1,
            'post_status' => 'publish'
        );
        $childQuery = new WP_Query($childArguments);
        if ($childQuery->have_posts()) {
            while ($childQuery->have_posts()) {
                $childQuery->the_post();
                $childId = get_the_ID();
                switch($postType){
                    case Dictionary::$typeBook:
                        $child = new Data\Book($childId);
                        array_push($children, $child->getBook());
                        break;
                    case Dictionary::$typeArticle:
                        $child = new Data\Article($childId);
                        array_push($children, $child->getArticle());
                        break;
                    case Dictionary::$typePowerpoint:
                        $child = new Data\Powerpoint($childId);
                        array_push($children, $child->getPowerPoint());
                        break;
                    case Dictionary::$typeVideo:
                        $child = new Data\Video($childId);
                        array_push($children, $child->getVideo());
                        break;
                }
            }
        }
        return $children;
    }
}
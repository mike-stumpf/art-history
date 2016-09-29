<?php namespace artHistory\Lib;

use WP_Query;
use artHistory\Data;

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
                    case Dictionary::$typeArtwork:
                        $child = new Data\Artwork($childId);
                        array_push($children, $child->getArtwork());
                        break;
                    case Dictionary::$typeWebsite:
                        $child = new Data\Website($childId);
                        array_push($children, $child->getWebsite());
                        break;
                }
            }
        }
        return $children;
    }

    public static function resizeImage($imageUrl, $sizeX = 300, $sizeY = 300) {
        if (!empty($imageUrl)) {
            $imageFile = parse_url($imageUrl);
            $imageFullPath = $_SERVER['DOCUMENT_ROOT'].$imageFile['path'];
            $imagePathPieces = explode('/', $imageFullPath);
            //remove old file name from path
            array_pop($imagePathPieces);
            $imageShortPath = implode('/', $imagePathPieces);
            //get file info and construct new path
            $fileInfo = pathinfo($imageFullPath);
            $newImagePath = $imageShortPath.'/'.$fileInfo['filename'].'-'.$sizeX.'x'.$sizeY.'.'.$fileInfo['extension'];
            $newImageUrl = substr($newImagePath,strripos($newImagePath,'/wp-content'));
            if (file_exists($newImagePath)){
                return $newImageUrl;
            } else {
                $imageEditor = wp_get_image_editor($imageFullPath);
                if (!is_wp_error($imageEditor)) {
                    try {
                        $imageEditor->resize($sizeX, $sizeY, true);
                        $imageEditor->save($newImagePath);
                        return $newImageUrl;
                    } catch (Exception $e) {
                        return $imageUrl;
                    }
                } else {
                    return $imageUrl;
                }
            }
        } else {
            return $imageUrl;
        }
    }

}
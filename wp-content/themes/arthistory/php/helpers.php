<?php namespace artHistory;

//todo, documentation

/**
 * helper functions
 */

// base class
class Helpers {

    public static function getAssetDirectory() {
        return get_template_directory_uri() . '/build/assets/';
    }

    public function getMetaValue($postId, $metaKey, $isSingleValue = true) {
        //get keys one at a time instead of not specifying a meta key for convenience
        return get_post_meta($postId, 'wpcf-' . $metaKey, $isSingleValue);
    }

    public static function sortByName($a, $b) {
        return $a->name > $b->name;
    }
}
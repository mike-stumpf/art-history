<?php

/**
 * helper functions
 */

function getAssetDirectory(){
    return get_template_directory_uri().'/build/assets/';
}

function getMetaValue($postId,$metaKey,$isSingleValue = true){
    //get keys one at a time instead of not specifying a meta key for convenience
    return get_post_meta($postId,'wpcf-'.$metaKey,$isSingleValue);
}
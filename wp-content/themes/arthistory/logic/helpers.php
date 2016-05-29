<?php

/**
 * helper functions
 */

function getAssetDirectory(){
    return get_template_directory_uri().'/build/assets/';
}

function getSingleMetaValue($postId,$metaKey){
    return get_post_meta($postId,'wpcf-'.$metaKey,true);
}
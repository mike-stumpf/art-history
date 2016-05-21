<?php
/*
Template Name: Homepage

*/
get_header(); ?>
    <section id="map-header-image-container">
        <img class="map-header-image l--show-for-map-1" src="<?php echo getAssetDirectory();?>1845-1915.jpg" alt="1845-1915"/>
        <img class="map-header-image faded-out l--show-for-map-2" src="<?php echo getAssetDirectory();?>1916-1945.jpg" alt="1916-1945"/>
        <img class="map-header-image faded-out l--show-for-map-3" src="<?php echo getAssetDirectory();?>1946-1994.jpg" alt="1946-1994"/>
    </section>
    <section id="map-timeline-container">
        timeline here
    </section>
<?php
get_footer();
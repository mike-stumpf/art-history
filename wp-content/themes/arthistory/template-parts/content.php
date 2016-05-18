<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package arthistory
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if (is_single()) {
            the_title( '<h1 class="entry-title">', '</h1>' );
        } else {
            the_title('<h2 class="entry-title"><a href="' . esc_url( get_permalink()) . '" rel="bookmark">', '</a></h2>' );
        } ?>
    </header><!-- .entry-header -->
    <div class="entry-content">
        <?php the_content(); ?>
    </div><!-- .entry-content -->
</article><!-- #post-## -->

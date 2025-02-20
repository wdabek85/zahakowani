<?php
/**
 * The loop template file.
 *
 * Included on pages like index.php, archive.php and search.php to display a loop of posts
 * Learn more: https://codex.wordpress.org/The_Loop
 *
 * @package autozpro
 */

do_action('autozpro_loop_before');

$blog_style  = autozpro_get_theme_option('blog_style');
$columns     = autozpro_get_theme_option('blog_columns');
$check_style = $blog_style && $blog_style !== 'standard';
if ($blog_style && $blog_style == 'grid') {
    echo '<div class="blog-style-grid" data-elementor-columns="' . esc_attr($columns) . '">';
} else {
    if ($blog_style && $blog_style == 'list') {
        echo '<div class="blog-style-list">';
    }
}

while (have_posts()) :
    the_post();

    /**
     * Include the Post-Format-specific template for the content.
     * If you want to override this in a child theme, then include a file
     * called content-___.php (where ___ is the Post Format name) and that will be used instead.
     */
    if ($blog_style && $blog_style == 'grid') {
        get_template_part('template-parts/posts-grid/item-post-style-1');
    } else {
        get_template_part('content', get_post_format());
    }

endwhile;

if ($check_style) {
    echo '</div>';
}

/**
 * Functions hooked in to autozpro_loop_after action
 *
 * @see autozpro_paging_nav - 10
 */
do_action('autozpro_loop_after');

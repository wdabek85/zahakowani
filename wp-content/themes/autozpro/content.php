<article id="post-<?php the_ID(); ?>" <?php post_class('article-default'); ?>>
    <div class="post-inner">
        <?php autozpro_post_thumbnail('post-thumbnail',true); ?>
        <div class="post-content">
            <?php
            /**
             * Functions hooked in to autozpro_loop_post action.
             *
             * @see autozpro_post_header          - 15
             * @see autozpro_post_content         - 30
             */
            do_action('autozpro_loop_post');
            ?>
        </div>
    </div>
</article><!-- #post-## -->
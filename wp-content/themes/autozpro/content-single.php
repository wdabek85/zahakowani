<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="single-content">
        <?php
        /**
         * Functions hooked in to autozpro_single_post_top action
         *
         */
        do_action('autozpro_single_post_top');

        /**
         * Functions hooked in to autozpro_single_post action
         * @see autozpro_post_header         - 10
         * @see autozpro_post_thumbnail - 20
         * @see autozpro_post_content         - 30
         */
        do_action('autozpro_single_post');

        /**
         * Functions hooked in to autozpro_single_post_bottom action
         *
         * @see autozpro_post_taxonomy      - 5
         * @see autozpro_post_nav            - 10
         * @see autozpro_display_comments    - 20
         */
        do_action('autozpro_single_post_bottom');
        ?>

    </div>

</article><!-- #post-## -->

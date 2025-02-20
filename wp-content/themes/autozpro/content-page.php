<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	/**
	 * Functions hooked in to autozpro_page action
	 *
	 * @see autozpro_page_header          - 10
	 * @see autozpro_page_content         - 20
	 *
	 */
	do_action( 'autozpro_page' );
	?>
</article><!-- #post-## -->

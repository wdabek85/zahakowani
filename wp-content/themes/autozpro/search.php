<?php
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
					<?php
						/* translators: %s: search term */
						printf( esc_attr__( 'Search Results for: %s', 'autozpro' ), '<span>' . get_search_query() . '</span>' );
					?>
				</h1>
			</header><!-- .page-header -->

			<?php
			get_template_part( 'loop' );

		else :

			get_template_part( 'content', 'none' );

		endif;
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'autozpro_sidebar' );
get_footer();

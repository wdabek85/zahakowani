<?php
get_header(); ?>

    <div id="primary" class="content">
        <main id="main" class="site-main">
            <div class="error-404 not-found">
                <div class="page-content">
                   <!-- .page-header -->
                    <div class="error-img404">
                        <img src="<?php echo get_theme_file_uri('assets/images/404/404_image.png') ?>" alt="<?php echo esc_attr__('404 Page', 'autozpro'); ?>">
                    </div>
                    <header class="page-header">
                        <h3 class="sub-title"><?php esc_html_e('Oops! that links is broken.', 'autozpro'); ?></h3>
                    </header>
                    <div class="error-text">
                        <span><?php esc_html_e('Page doesn’t exist or some other error occured.', 'autozpro') ?>
                           <br>  <span><?php esc_html_e(' Go to our ', 'autozpro') ?>
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="return-home"><?php esc_html_e('Home page', 'autozpro'); ?></a></span>
                    </div>

                </div><!-- .page-content -->
            </div><!-- .error-404 -->
        </main><!-- #main -->
    </div><!-- #primary -->
<?php

get_footer();

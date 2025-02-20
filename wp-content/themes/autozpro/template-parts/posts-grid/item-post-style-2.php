<div class="column-item post-style-2">
    <div class="post-inner">
        <?php
        autozpro_post_thumbnail('autozpro-post-grid', true);
        ?>
        <div class="post-content">
            <header class="entry-header">
                <?php if(!has_post_thumbnail() && 'post' == get_post_type()){ ?>
                    <div class="entry-meta">
                        <?php autozpro_post_meta(['show_cat' => true, 'show_date' => false, 'show_author'  => false]); ?>
                    </div>
                <?php } ?>
                <?php the_title('<h3 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h3>'); ?>
                <div class="entry-meta">
                    <?php autozpro_post_meta(); ?>
                </div>
            </header>
            <div class="entry-content">
                <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                <div class="more-link-wrap">
                    <a class="more-link" href="<?php the_permalink() ?>"><?php echo esc_html__('Read More', 'autozpro'); ?><i class="autozpro-icon-angle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

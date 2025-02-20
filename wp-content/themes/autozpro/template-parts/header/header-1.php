<header id="masthead" class="site-header header-1" role="banner">
    <div class="header-container">
        <div class="container header-main">
            <div class="header-left">
                <?php
                autozpro_site_branding();
                if (autozpro_is_woocommerce_activated()) {
                    ?>
                    <div class="site-header-cart header-cart-mobile">
                        <?php autozpro_cart_link(); ?>
                    </div>
                    <?php
                }
                ?>
                <?php autozpro_mobile_nav_button(); ?>
            </div>
            <div class="header-center">
                <?php autozpro_primary_navigation(); ?>
            </div>
            <div class="header-right desktop-hide-down">
                <div class="header-group-action">
                    <?php
                    autozpro_header_account();
                    if (autozpro_is_woocommerce_activated()) {
                        autozpro_header_wishlist();
                        autozpro_header_cart();
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</header><!-- #masthead -->

<?php

namespace Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Event_Chain\Event;

use Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Event_Chain\Abstracts\Abstract_Event;
use Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Event_Chain\Interfaces\Wp_Post_Id_Aware_Interface;
class Wc_Product_Options_Pricing extends Abstract_Event implements Wp_Post_Id_Aware_Interface
{
    /**
     * @var int
     */
    private $post_id;
    public function get_post_id() : int
    {
        return $this->post_id;
    }
    public function create()
    {
        add_action('woocommerce_product_options_pricing', function () {
            global $post;
            $this->post_id = $post->ID;
            $this->callback();
        }, 3, 1000);
    }
}

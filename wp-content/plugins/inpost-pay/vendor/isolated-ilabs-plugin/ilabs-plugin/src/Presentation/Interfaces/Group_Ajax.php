<?php

declare (strict_types=1);
namespace Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Presentation\Interfaces;

interface Group_Ajax
{
    public function get_action() : ?string;
    public function set_action(string $action = null);
    public function set_button_text(string $button_text = null);
    public function get_button_text() : ?string;
    public function set_show_button(bool $show_button);
    public function get_show_button() : bool;
}

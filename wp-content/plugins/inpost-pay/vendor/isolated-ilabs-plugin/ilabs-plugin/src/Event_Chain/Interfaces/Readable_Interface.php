<?php

declare (strict_types=1);
namespace Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Event_Chain\Interfaces;

interface Readable_Interface
{
    public function read(string $key = null);
    public function get_key() : string;
}

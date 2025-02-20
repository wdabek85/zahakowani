<?php

namespace Ilabs\Inpost_Pay\Lib\item;

use Ilabs\Inpost_Pay\Lib\Item;

class Consent extends Item
{
    protected $consent_id;
    protected $consent_link;
    protected $consent_description;
    protected $consent_version;
    protected $requirement_type;
}

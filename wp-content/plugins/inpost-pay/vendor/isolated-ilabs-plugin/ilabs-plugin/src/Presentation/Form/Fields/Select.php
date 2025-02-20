<?php

declare (strict_types=1);
namespace Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Presentation\Form\Fields;

use Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Presentation\Interfaces\Field_Interface;
use Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Presentation\Interfaces\Field_Select_Interface;
use Isolated\Inpost_Pay\Ilabs\Ilabs_Plugin\Presentation\Interfaces\Group_Item_Interface;
class Select implements Group_Item_Interface, Field_Interface, Field_Select_Interface
{
    /**
     * @var string
     */
    private $value;
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $label;
    /**
     * @var string
     */
    private $desc;
    /**
     * @var array
     */
    private $options;
    /**
     * @var array
     */
    private $values;
    /**
     * @var string
     */
    private $default;
    public function to_array() : array
    {
        return ['value' => $this->value, 'id' => $this->id, 'label' => $this->label, 'desc' => $this->desc, 'options' => $this->options];
    }
    /**
     * @return array
     */
    public function get_options() : array
    {
        return $this->options;
    }
    /**
     * @param array $options
     */
    public function set_options(array $options) : void
    {
        $this->options = $options;
    }
    /**
     * @return string
     */
    public function get_value() : string
    {
        return $this->value;
    }
    /**
     * @param string $value
     */
    public function set_value(string $value) : void
    {
        $this->value = $value;
    }
    /**
     * @return string
     */
    public function get_id() : string
    {
        return $this->id;
    }
    /**
     * @param string $id
     */
    public function set_id(string $id) : void
    {
        $this->id = $id;
    }
    /**
     * @return string
     */
    public function get_label() : string
    {
        return $this->label;
    }
    /**
     * @param string $label
     */
    public function set_label(string $label) : void
    {
        $this->label = $label;
    }
    /**
     * @return string
     */
    public function get_desc() : string
    {
        return $this->desc;
    }
    /**
     * @param string $desc
     */
    public function set_desc(string $desc) : void
    {
        $this->desc = $desc;
    }
    /**
     * @return string
     */
    public function get_default() : string
    {
        return $this->default;
    }
    /**
     * @param string $default
     */
    public function set_default(?string $default) : void
    {
        $this->default = $default;
    }
}

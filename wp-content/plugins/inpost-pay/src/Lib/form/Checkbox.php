<?php

namespace Ilabs\Inpost_Pay\Lib\form;

use Ilabs\Inpost_Pay\Lib\form\exception\NotAllowedConfigOptionException;
use Ilabs\Inpost_Pay\Lib\form\exception\NotFoundConfigOptionException;
use Ilabs\Inpost_Pay\Lib\form\exception\RequiredConfigOptionException;

class Checkbox extends AbstractFormField {

	private array $configOptions = [
		'label'       => [
			'required' => true
		],
		'label_class' => [
			'default'  => 'label',
			'required' => false
		],
		'name'        => [
			'required' => true
		],
		'class'       => [
			'required' => false,
			'default'  => 'checkbox'
		],
	];
	private string $value;

	/**
	 * @param string $value
	 * @param array $config
	 *
	 * @throws NotAllowedConfigOptionException
	 * @throws RequiredConfigOptionException
	 */
	public function __construct(
		string $value,
		array $config
	) {
		parent::__construct( $this->configOptions, $config );

		$this->value = $value;
	}

	/**
	 * @throws NotFoundConfigOptionException
	 */
	public function print_field(): void {
		$this->print_checkbox();
	}

	public function print_checked(): string {
		if (is_numeric($this->value) ) {
			return $this->value == 1 ? 'checked' : '';
		}
		return $this->value === 'yes' ? 'checked' : '';
	}

	/**
	 * @throws NotFoundConfigOptionException
	 */
	public function print_checkbox(): void {

		echo sprintf(
			'<input type="checkbox" name="%s" id="%s" class="%s" %s>',
			$this->get_field_name(),
			$this->get_config_option( 'name' )->get_value(),
			$this->get_config_option( 'class' )->get_value(),
			$this->print_checked()
		);

	}

	/**
	 * @throws NotFoundConfigOptionException
	 */
	public function get_field_name() {
		return $this->get_config_option( 'name' )->get_value();
	}


}

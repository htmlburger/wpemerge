<?php

namespace WPEmerge\View;

trait HasNameTrait {
	/**
	 * Name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set name.
	 *
	 * @param  string $name
	 * @return self   $this
	 */
	public function setName( $name ) {
		$this->name = $name;
		return $this;
	}
}

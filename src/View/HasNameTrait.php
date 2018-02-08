<?php

namespace WPEmerge\View;

trait HasNameTrait {
	/**
	 * View name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setName( $name ) {
		$this->name = $name;
		return $this;
	}
}

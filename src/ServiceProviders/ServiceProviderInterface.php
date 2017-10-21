<?php

namespace CarbonFramework\ServiceProviders;

use ArrayAccess;

interface ServiceProviderInterface {
	public function register( ArrayAccess $container );

	public function boot();
}
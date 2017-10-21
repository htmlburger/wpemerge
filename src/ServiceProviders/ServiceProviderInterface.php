<?php

namespace CarbonFramework\ServiceProviders;

interface ServiceProviderInterface {
	public function register( $container );

	public function boot();
}
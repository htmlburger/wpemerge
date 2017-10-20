<?php

namespace CarbonFramework\Routing;

interface RouteInterface {
	public function satisfied();

	public function getHandler();
}

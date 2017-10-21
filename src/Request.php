<?php

namespace CarbonFramework;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request {
	public static function fromGlobals() {
		return SymfonyRequest::createFromGlobals();
	}
}

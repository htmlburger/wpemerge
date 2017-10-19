<?php

namespace CarbonFramework;

class Url {
	public static function getCurrentPath() {
		global $wp;
		return '/' . $wp->request;
	}

	public static function getCurrentUrl() {
		return home_url( add_query_arg( array() ) );
	}
}

<?php

use Obsidian\Framework\Framework;
use Obsidian\Framework\FrameworkFacade;
use Obsidian\Support\Facade;
use Obsidian\Support\AliasLoader;
use Pimple\Container;

$container = new Container();

$container['framework'] = function( $container ) {
	return new Framework( $container );
};

Facade::setFacadeApplication( $container );
AliasLoader::getInstance()->register();
AliasLoader::getInstance()->alias( 'Obsidian', FrameworkFacade::class );

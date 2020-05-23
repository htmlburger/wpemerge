<?php
/**
 * @package   WPEmerge
 * @author    Atanas Angelov <hi@atanas.dev>
 * @copyright 2017-2019 Atanas Angelov
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0
 * @link      https://wpemerge.com/
 */

use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Version\GitVersionCollection;
use Sami\Parser\Filter\PublicFilter;

define( 'DS', DIRECTORY_SEPARATOR );

$repository = 'htmlburger/wpemerge';
$dir = __DIR__ . DS . 'wpemerge' . DS . 'src';
$documentation_dir = dirname( __DIR__ );

$versions = GitVersionCollection::create($dir)
	->addFromTags('*.*.*')
	->add('master', 'master branch');

return new \Sami\Sami( $dir, [
	'title' => 'WP Emerge',
	'versions' => $versions,
	'remote_repository' => new GitHubRemoteRepository( $repository, dirname( $dir ) ),
	'build_dir' => $documentation_dir . DS . '%version%',
	'cache_dir' => $documentation_dir . DS . 'cache' . DS . '%version%',
	'filter' => new PublicFilter(),
] );

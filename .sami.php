<?php
use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Version\GitVersionCollection;
use Sami\Parser\Filter\PublicFilter;

define( 'DS', DIRECTORY_SEPARATOR );

$repository = 'htmlburger/wpemerge';
$relative_dir = 'src';
$dir = __DIR__ . DS . $relative_dir;
$documentation_dir = __DIR__ . DS . 'docs';

$versions = GitVersionCollection::create($dir)
    ->addFromTags('*.*.*')
    ->add('master', 'master branch');

return new \Sami\Sami( $dir, [
	'title' => 'WP Emerge',
	'versions' => $versions,
	'remote_repository' => new GitHubRemoteRepository( $repository, dirname( $dir ) ),
    'build_dir' => $documentation_dir . DS . 'build' . DS . '%version%',
    'cache_dir' => $documentation_dir . DS . 'cache' . DS . '%version%',
    'filter' => new PublicFilter(),
] );

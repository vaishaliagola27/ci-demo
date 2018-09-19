<?php
namespace Deployer;
require 'recipe/common.php';        //adds common necessities for the deployment
set('ssh_type', 'native');
set('ssh_multiplexing', true);
if (file_exists('vendor/deployer/recipes/rsync.php')) {
	require 'vendor/deployer/recipes/rsync.php';
} else {
	require getenv('COMPOSER_HOME') . '/vendor/deployer/recipes/rsync.php';
}
set('writable_dirs', [
	'wp-content'
]);

// Add tests and other directory uncessecary for
// production to exclude block.
set('rsync', [
	'exclude'      => [
		'.git',
		'deploy.php',
		'composer.json',
		'composer.lock',
		'.env',
		'.env.example',
		'.gitignore',
		'.gitlab-ci.yml',
		'Gruntfile.js',
		'package.json',
		'README.md',
		'gulpfile.js',
		'.circleci',
		'package-lock.json',
		'package.json',
		'screenshot.png',
		'vendor/deployer',
		'phpcs.xml'
	],
	'exclude-file' => true,
	'include'      => [],
	'include-file' => false,
	'filter'       => [],
	'filter-file'  => false,
	'filter-perdir'=> false,
	'flags'        => 'rz', // Recursive, with compress
	'options'      => ['delete', 'delete-excluded', 'links'],
	'timeout'      => 300,
]);
set('rsync_src', getenv('build_root'));
set('rsync_dest', '{{release_path}}');

$server_name = getenv( 'SERVER_NAME' );
$user        = getenv( 'SERVER_USER' );
$dep_path    = getenv( 'DEP_PATH' );

/* list the servers and deployment path with other details*/
server('develop', $server_name)   //server name for the deployment process to choose from  and dns name or ip address to the server, must be pointable from the internet
->user($user)          //the user with which files are to be copied, as EE uses www-data it wont change
->identityFile('~/.ssh/id_rsa.pub', '~/.ssh/id_rsa')    // identification files, wont change
->set('deploy_path', $dep_path);        // deployment path

/*  custom task defination    */
desc('Download cachetool');
task('cachetool:download', function () {
	run('wget https://raw.githubusercontent.com/gordalina/cachetool/gh-pages/downloads/cachetool-3.0.0.phar -O {{release_path}}/cachetool.phar');
});
/*  custom task defination    */
desc('Reset opcache');
task('opcache:reset', function () {
	$output = run('php {{release_path}}/cachetool.phar opcache:reset --fcgi=127.0.0.1:9070');
	writeln('<info>' . $output . '</info>');
});

/*   deployment task   */
desc('Deploy the project');
task('deploy', [
	'deploy:prepare',
	'deploy:unlock',
	'deploy:lock',
	'deploy:release',
	'rsync',
	//   'uploads:sync',
	'cachetool:download',
	'deploy:symlink',
	'opcache:reset',
	'deploy:unlock',
	'cleanup'
]);
after('deploy', 'success');

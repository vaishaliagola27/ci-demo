<?php
namespace Deployer;
require 'recipe/common.php';        //adds common necessities for the deployment
set('ssh_type', 'native');
set('ssh_multiplexing', true);
if (file_exists('vendor/deployer/recipes/rsync.php')) {
	require 'vendor/deployer/recipes/rsync.php';
} else {
	require getenv('COMPOSER_HOME') . '/vendor/deployer/recipes/recipe/rsync.php';
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
host('develop')   //server name for the deployment process to choose from  and dns name or ip address to the server, must be pointable from the internet
->hostname($server_name)
->user($user)          //the user with which files are to be copied, as EE uses www-data it wont change
->identityFile('~/.ssh/id_rsa.pub', '~/.ssh/id_rsa')    // identification files, wont change
->set('deploy_path', $dep_path);        // deployment path

host('master')   //server name for the deployment process to choose from  and dns name or ip address to the server, must be pointable from the internet
->hostname($server_name)
->user($user)          //the user with which files are to be copied, as EE uses www-data it wont change
->identityFile('~/.ssh/id_rsa.pub', '~/.ssh/id_rsa')    // identification files, wont change
->set('deploy_path', $dep_path);        // deployment path

host('citest')   //server name for the deployment process to choose from  and dns name or ip address to the server, must be pointable from the internet
->hostname($server_name)
->user($user)          //the user with which files are to be copied, as EE uses www-data it wont change
->identityFile('~/.ssh/id_rsa.pub', '~/.ssh/id_rsa')    // identification files, wont change
->set('deploy_path', $dep_path)        // deployment path
->stage('ci-test');

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

desc( 'Test stages' );
task( 'stage-test', function () {

	$output = run( 'ls' );
	writeln( '<info>' . $output . '</info>' );
} )->onStage( [ 'ci-test' ] );

desc( 'Test stages 2' );
task( 'stage-test2', function () {

	$output = run( 'ls -al' );
	writeln( '<info>' . $output . '</info>' );
} )->onStage( [ 'citest' ] );

desc( 'master test' );
task( 'master-test2', function () {

	$output = run( 'ls -al' );
	writeln( '<info>' . $output . '</info>' );
} )->onStage( [ 'master' ] );

/*   deployment task   */
desc('Deploy the project');
task('deploy', [
	'stage-test2',
	'stage-test',
	'master-test2',
	'deploy:prepare',
	'deploy:unlock',
	'deploy:lock',
	'deploy:release',
	'rsync',
	'cachetool:download',
	'deploy:symlink',
	'opcache:reset',
	'deploy:unlock',
	'cleanup'
]);
after('deploy', 'success');

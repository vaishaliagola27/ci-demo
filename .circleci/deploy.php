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

$file_content ='';

if ( file_exists( './.circleci/server.json' ) ) {

	$file_content = json_decode( file_get_contents ("./.circleci/server.json"), true );

}

if ( ! empty( $file_content ) && is_array( $file_content ) && json_last_error() === JSON_ERROR_NONE ) {

	$server_details = $file_content;

} else {

	$server_details = json_decode( getenv( 'SERVER_DETAILS' ), true );

}

if ( json_last_error() === JSON_ERROR_NONE && ! empty( $server_details ) && is_array( $server_details ) ) {

	foreach ( $server_details as $branch => $detail ) {

		/* list the servers and deployment path with other details*/
		host( $branch )   //server name for the deployment process to choose from  and dns name or ip address to the server, must be pointable from the internet
		->hostname($detail['server'])
		->user($detail['user'])          //the user with which files are to be copied, as EE uses www-data it wont change
		->identityFile('~/.ssh/id_rsa')    // identification files, wont change
		->set('deploy_path', $detail['path']);        // deployment path

	}

} else {

	echo "Server details are not configured properly. Please check server.json or set SERVER_DETAILS environment variable!";
	exit(1);

}

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
	'cachetool:download',
	'deploy:symlink',
	'opcache:reset',
	'deploy:unlock',
	'cleanup'
]);
after('deploy', 'success');

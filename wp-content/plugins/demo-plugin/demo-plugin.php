<?php
/**
 * Plugin Name: Circle CI Test
 * Description:  Circle CI Test
 * Author: rtCamp, vaishaliagola
 * Author URI: https://rtcamp.com/
 * Version: 0.1
 * License: GPLv2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: cicleci-test
 *
 * @package circleci-test
 */

/**
 * Test.
 */
function test() {

	echo esc_html( 'dump here' );

}

add_action( 'admin_init', 'test' );

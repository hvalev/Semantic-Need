<?php
/**
 * Creates/drops database tables for Woogle. This is equivalent to clicking the respective
 * button on the page Special:WoogleConfig.
 *
 * Note: if Woogle is not installed in its standard path under ./extensions
 *       then the MW_INSTALL_PATH environment variable must be set.
 *       See README in the maintenance directory.
 *
 * Usage:
 * php WoogleNativeDb_setup.php [options...]
 *
 * --user     <dbuser>      Database user account to use for changing DB layout. If not set,
 * 							the credentials in AdminSettings.php are used.
 * --password <dbpassword>  Password for user account to use. (Instead of custom password.)
 * --delete					Delete Woogle database tables. If not selected, this script tries
 * 							to create them.
 *
 * NOTE: specifying user credentials in a command line call will usually store them
 * within the shell history file. For security, provide credentials in AdminSettings.php
 * instead and ensure that your text editor does not create world-readable backup copies
 * when modifying this file.
 *
 * @author Hans-Joerg Happel
 */

/*
 * no guarantees, but look in the usual place for commandLine.inc, so this
 * so it will work most of the time
 */

require_once ( getenv('MW_INSTALL_PATH') !== false
? getenv('MW_INSTALL_PATH')."/maintenance/commandLine.inc"
: dirname( __FILE__ ) . '/../../../../../maintenance/commandLine.inc' );
require_once("$IP/maintenance/counter.php");

/* user/password in LocalSettings probably don't have the rights we need,
 * so allow override
 * Note: the preferred method is to use AdminSettings.php to provide such credentials
 */

if( isset( $options['user'] ) ) {
	global $wgDBuser;
	$wgDBuser = $options['user'];
}
if( isset( $options['password'] ) ) {
	global $wgDBuser;
	$wgDBpassword = $options['password'];
}

require_once('../WoogleQueryServiceNative.php');


if (  array_key_exists( 'delete', $options ) ) {
	print "\n  WARNING: Deleting all Woogle tables!\n\n";
	$delay=10;
	print "Abort with CTRL-C in the next $delay seconds ...   ";

	for ($i = $delay+1; $i >= 1;) {
		echo str_repeat( chr(8), strlen( $i ) ) . str_repeat( chr(255), strlen( $i ) ) .
		str_repeat( chr(8), strlen( $i ) ) . --$i;
		sleep(1);
	}

	$count = 0;
	$count = WoogleQueryServiceNative::dropDb();

	while (ob_get_level() > 0) { // be sure to have some buffer, otherwise some PHPs complain
		ob_end_flush();
	}

	echo "\n\n  $count tables have been deleted. You can recreate them with this script.";
} else {
	$count = 0;
	try {
		$count = WoogleQueryServiceNative::initDb();
		echo "\n  Created " . $count . ' tables in the database.';
	}
	catch (Exception $e) {
		echo 'Database error - check your access rights: ' . $e;
	}
}

print "\n\nDone.\n";

?>
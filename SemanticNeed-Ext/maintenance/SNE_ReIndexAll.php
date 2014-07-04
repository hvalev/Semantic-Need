<?php
/*******************************************************************************
*	This file is part of Woogle4MediaWiki
*   (http://www.mediawiki.org/wiki/Extension:Woogle4MediaWiki)
*
*	Copyright (c) 2007 - 2010 Hans-Jörg Happel and
*	FZI Forschungszentrum Informatik an der Universität Karlsruhe (TH)
*
*   Woogle4MediaWiki is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   Woogle4MediaWiki is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with Woogle4MediaWiki. If not, see <http://www.gnu.org/licenses/>.
*******************************************************************************/ 

/**
 * Rebuilds the Woogle search index. This is equivalent to clicking the respective
 * button on the page Special:WoogleConfig.
 *
 * Note: if Woogle is not installed in its standard path under ./extensions
 *       then the MW_INSTALL_PATH environment variable must be set.
 *       See README in the maintenance directory.
 *
 * Usage: see printUsage() below
 *
 */

echo ini_get('max_execution_time');
ini_set('max_execution_time', '15000');
set_time_limit(15000);

/*
 * no guarantees, but look in the usual place for commandLine.inc, so this
 * so it will work most of the time
 */
$optionsWithArgs = array( 'server' );
require_once ( getenv('MW_INSTALL_PATH') !== false
    ? getenv('MW_INSTALL_PATH')."/maintenance/commandLine.inc"
    : dirname( __FILE__ ) . '/../../../maintenance/commandLine.inc' );

// check options
if( isset( $options['server'] ) ) {
	global $wgServer;
	$wgServer = $options['server'];
}

$index = 0;
if( isset( $options['index'] ) ) {
	global $index;
	$index = $options['index'];
}

$start = -1;
if( isset( $options['start'] ) ) {
	global $start;
	$start = $options['start'];
}

reIndex();

function reIndex(){
	
	global $wgServer, $index, $start;
	
	//
	// do error checks
	//
	if (stripos($wgServer, 'http://localhost') === 0) echo("\n  WARNING: \$wgServer begins with 'http://localhost'. URLs written to the index might not be accessible for users!\n");
	
	/*
	if (!WoogleUtil::getIndexService()->checkIndexService()) 	echo("\n  PROBLEM: Directory not writable or connection problem!\n");
	if (WoogleUtil::getQueryService()->isNative()){
		if (!WoogleLucene::getMbstringSupport()) 					echo("\n  PROBLEM: mb_string extension missing!\n");
		if (!WoogleLucene::getPcreSupport()) 						echo("\n  PROBLEM: PCRE extension missing!\n");
	}*/
	
	//wait10seconds("\n  WARNING: Deleting existing index and creating new index!\n");
	
	// Create tables if not yet done
	
	if ($start == 0){
		SNE_DbManager::dropDb();
		$count = SNE_DbManager::initDb();
		echo 'Created ' . $count . ' tables in the database.' . "\n\n";
	}
	
	echo "Variables: index set to " . $index . " start set to " . $start;
	SNEAdmin::findAllWikiPages(true, $index, $start);
	
	while (ob_get_level() > 0) { // be sure to have some buffer, otherwise some PHPs complain
		ob_end_flush();
	}
	
	echo "Queries found " . count(SemanticQueryManager::getAllSemanticQueries()) . "\n";
	echo "\n  Done.\n";
}

function wait10seconds($msg){
	
	global $options;
	if( isset( $options['f'] ) ) {
		// force execution, no waiting
		return;
	}
	
	echo $msg;
	$delay=10;
	echo "  Abort with CTRL-C in the next $delay seconds ...   ";
	
	for ($i = $delay+1; $i >= 1;) {
		echo str_repeat( chr(8), strlen( $i ) ) . str_repeat( chr(255), strlen( $i ) ) .
	    	 str_repeat( chr(8), strlen( $i ) ) . --$i;
		sleep(1);
	}
	
}

function printUsage(){
	echo '
 * USAGE INSTRUCTIONS
 * =======================================================
 * php SNE_ReIndexAll.php [options...]
 *
 * --f			Force (Skip 10 second delay)
 * --server=...	Server name (e.g. http://mywiki.mydomain.de) - without trailing slashes
 *				Alternatively set $wgServer in LocalSettings.php
 *				(http://www.mediawiki.org/wiki/Manual:LocalSettings.php#Server_name). 
 * --conf=...	Path to LocalSettings file (e.g. /../../LocalSettings2.php) - this is
 *              only necessary, but particularly useful, if you would like to create an
 *              index without touching the productive Wiki system (which is configured
 *              in LocalSettings.php). You may just make a copy of LocalSettings.php
 *              and only include Woogle in this configuration.
 *
 ';
}

?>
<?php
/**
 * 	This is the main entry point for the Semantic Need extension
 * 
 *  Write
 *  	require_once( "$IP/extensions/SemanticNeed/SNE.php" );
 *  into your LocalSettings.php to activate Semantic Need for your MediaWiki
 *
 *  Thus, this file as such contains mainly code to intialize/setup the 
 *  Semantic Need Extension within its surrounding MediaWiki instance
 */

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/SemanticNeed/SNE.php" );
EOT;
        exit( 1 );	// exit with error code
}

// include base files
require_once(dirname(__FILE__) . '/includes/SNEFunctions.php');		// automated function
require_once(dirname(__FILE__) . '/includes/SNEHooks.php');			// contains callback functions for MediaWiki hooks


// register additional code for PHP autoloading (c.f. http://php.net/autoload)
sneAutoloadClasses(array(
	'SNEConfig'					=> '/includes',
	'SNELang'					=> '/includes',
	'SNEQueryResolver'			=> '/includes',
	'SNEUtil'					=> '/includes',

	
	'SNEVariableDisplay'		=> '/includes/gateways',
	'SNESMWQConstraintFinder'	=> '/includes/gateways',
	'SNESMWQConstraintGateway'	=> '/includes/gateways',
	'SNESMWQPrintoutFinder'		=> '/includes/gateways',
	'SNESMWQPrintoutGateway'	=> '/includes/gateways',
	'SNESMWQQueryFinder'		=> '/includes/gateways',
	'SNESMWQQueryGateway'		=> '/includes/gateways',

	'SNEAskLog'					=> '/includes/specialpages',
	'SNESemanticMatches'		=> '/includes/specialpages',
	'SNESemanticQueryInfo'		=> '/includes/specialpages',
	'SNEMockMissingAnnotations'	=> '/includes/specialpages',
	'SNEMockSemanticMatches'	=> '/includes/specialpages',
));

// maps MediaWiki Job API jobs to its handling classes (http://www.mediawiki.org/wiki/Manual:$wgJobClasses)
//$wgJobClasses['woogleAddClick']		= 'WoogleJobAddClick';

// define custom namespaces for Woogle (http://www.mediawiki.org/wiki/Manual:$wgExtraNamespaces)
/*
$wgExtraNamespaces[100] = "Woogle";
$wgExtraNamespaces[101] = "Woogle_talk";
*/

// Woogle credits shown on page Special:Version
$wgExtensionCredits['other'][] = array(
	'name'			=> 'Semantic Need',
	'description'	=> 'Semantics from the people',
	'version'		=> '0.01 ($Rev$)',
	'author'		=> 'Hans-J&ouml;rg Happel, FZI Karlsruhe, Germany',
	'url'			=> 'http://www.teamweaver.org/wiki/index.php/Semantic_Need',
);

$wgAjaxExportList[] = 'testFunction';

//load MediaWiki Special:SpecialPages defined by SNE
sneLoadSpecialPages();

?>
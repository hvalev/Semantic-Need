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
require_once(dirname(__FILE__) . '/includes/SNECoreFunctions.php');		// automated function
require_once(dirname(__FILE__) . '/includes/QueryUpdateManager.php');// contains callback functions for MediaWiki hooks
require_once(dirname(__FILE__) . '/includes/QueryMonitor.php');		// contains callback functions for MediaWiki hooks

// register additional code for PHP autoloading (c.f. http://php.net/autoload)
sneCoreAutoloadClasses(array(
	'SNEAdmin'					=> '/includes/specialpages',

	'SMWQGatewayInterface'		=> '/includes/structure',

	'SMWQQuery'					=> '/includes/structure',
	'SMWQConstraint'			=> '/includes/structure',
	'SMWQPrintout'				=> '/includes/structure',

	'SMWQQueryMapper'			=> '/includes/datamapper',
	'SMWQConstraintMapper'		=> '/includes/datamapper',
	'SMWQPrintoutMapper'		=> '/includes/datamapper',

	'QueryAnalyzer'				=> '/includes',
	'QueryResolver'				=> '/includes',
	'QueryStorage'				=> '/includes',
	
	'SNECoreConfig'				=> '/includes',
	'SNECoreLang'				=> '/includes',
	'SNECoreUtil'				=> '/includes',
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
sneCoreLoadSpecialPages();

?>

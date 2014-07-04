<?php


/**
 * SNE uses autoloading instead of explicitly 'including' other .php files
 * http://de3.php.net/__autoload
 * 
 * This method writes the SNE-specific classes in the global MediaWiki
 * autoload array
 * 
 * @param 	$classes	Array of class names as defined in SNE.php
 * @return 	void
 */


function sneCoreAutoloadClasses($classes) {
	global $wgAutoloadClasses;
	foreach ($classes as $className => $path) {
		$wgAutoloadClasses[$className] = dirname(__FILE__) . '/..' . $path . '/' . $className . '.php';
	}
}

/**
 * Registers special pages defined by Woogle with the core MediaWiki special pages array
 * @return	void
 */


function sneCoreLoadSpecialPages() {
	global $wgSpecialPages;
	//foreach (SNEUtil::getSpecialPages() as $specialPage) {
	//	$wgSpecialPages[SNEUtil::getSpecialPageClass($specialPage)] = SNEUtil::getSpecialPageClass($specialPage);
	//}
	foreach (SNECoreUtil::getSpecialPages() as $specialPage) {
		$wgSpecialPages[SNECoreUtil::getSpecialPageClass($specialPage)] = SNECoreUtil::getSpecialPageClass($specialPage);
	}
}

?>

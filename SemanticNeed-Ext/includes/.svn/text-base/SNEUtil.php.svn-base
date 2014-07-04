<?php
class SNEUtil{	
	private static $specialPages = array('MockMissingAnnotations', 'MockSemanticMatches', 'Admin', 'AskLog', 'SemanticMatches', 'SemanticQueryInfo');
	private static $messagesLoaded = false; //variable that sais if translations messages are loaded or not
		

	
	 /**
     * function that can log separate events into separate files
	 * @param		$msg			the log message
	 * @param		$file			file name
	 * @return		void
     */
	
	
	private static function writeLog($msg, $file){
		// write logs to file
		$prefix 	= '[' . date('Y-m-d H:i:s') . '] ';
		$logline 	= $prefix . $msg . "\n";
		$dfile = fopen(self::getLogPath() . $file, 'a');
		fwrite($dfile, $logline);
		fclose($dfile);
	}
	
	
	 /**
     * a side function used only for debugging
	 * @param		$msg			the message that is about to be written to the log file
	 * @return		void			
     */
	
	
	public static function debug($msg) {
		self::writeLog($msg, 'debug.log');
	}
	
	 /**
     * retrieves the log folder
	 * @return		the log folder
     */
	
	private static function getLogPath() {
		return dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
	}
	
	
	/*
	 * @return 		String, Example: "http://localhost/wiki/extensions/Woogle/"
	 */
	public static function getSNEAbsolutePath() {
		$epath = dirname( __FILE__ );
		$epath = substr( $epath , strpos($epath, 'extensions'), strpos($epath, 'SNEUtil.php')-8);
		$epath = str_replace('\\', '/', $epath);
		return self::getWikiAbsolutePath() . $epath;
	}
	
	
	/*
	 * @return 		String, Example: "http://localhost"
	 */
	public static function getAbsolutePath() {
		global $wgServer;
		return $wgServer;
	}

	/*
	 * @return 		String, Example: "http://localhost/wiki/"
	 */
	public static function getWikiAbsolutePath() {
		return self::getAbsolutePath() . self::getWikiPath();
	}

	/*
	 * Example $wgScriptPath = "/wiki"
	 * 
	 * @return 		String, Example "/wiki/"
	 */
	public static function getWikiPath() {
		global $wgScriptPath;
		return $wgScriptPath . '/';
	}	
	
	
	
	public static function getImagePath() {
		return self::getSNEAbsolutePath() . 'resources/img/';
	}
	
	
	
	public static function getMsg($key) {
		$args = func_get_args();	// get additional arguments
		array_shift($args);			// omit first argument
		SNEUtil::loadMessages();
		return wfMsgReal('sne' . $key, $args);
	}
	
	
	public static function getSpecialPages() {
		return self::$specialPages;
	}
	
	
	public static function getSpecialPageClass($specialPage) {
		return 'SNE' . $specialPage;
	}
	
	/**
	 * Looks up translation from WoogleLang::getSpecialPages()
	 * 
	 * @param 		String, key for special page
	 * @return 		String, localized value
	 */
	
	public static function getSpecialPageLocal($specialPage) {
		self::loadMessages();
		return wfMsgForContent('sne' . $specialPage);
	}
	
	
	//
	// Localization
	//


	/**
	 * Initialize localized strings / loading into MediaWiki message cache 
	 * @return 	true
	 */
	
	
	public static function loadMessages() {
		global $wgMessageCache;
		if (self::$messagesLoaded) {
			return true;
		}
		self::$messagesLoaded = true;

		// TODO not necessary to load all messages?
		$sneMessages = array();
		if (file_exists(dirname(__FILE__) . '/../languages') && is_dir(dirname(__FILE__) . '/../languages')) {
			$sneLanguages = opendir(dirname(__FILE__) . '/../languages');
			while (false !== ($file = readdir($sneLanguages))) {
				if (is_file(dirname(__FILE__) . '/../languages/' . $file)) {
					require_once(dirname(__FILE__) . '/../languages/' . $file);
					$class = substr($file, 0, strlen($file) - 4);
					if (class_exists($class)) {
						$lang = new $class();
						foreach ($lang->getMessages() as $msgKey => $msgValue) {
							$sneMessages[$lang->getIsoCode()]['sne' . $msgKey] = $msgValue;
						}
						foreach ($lang->getSpecialPages() as $specialKey => $specialValue) {
							$sneMessages[$lang->getIsoCode()]['sne' . $specialKey] = $specialValue[0];
							$sneMessages[$lang->getIsoCode()][strtolower($specialValue[0])] = $specialValue[1];
						}
						foreach ($lang->getMWMessages() as $mwKey => $mwValue){
							$sneMessages[$lang->getIsoCode()][$mwKey] = $mwValue;
						}
					}
				}
			}
		}
		foreach ($sneMessages as $lang => $langMessages) {
			$wgMessageCache->addMessages($langMessages, $lang);
		}
		return true;
	}
}
?>
<?php
/**
 *	This is the main configuration file of the Semantic Need extension.
 *
 *	It contains a number of configuration settings in the config array and several utility
 * 	methods to access these settings and more.
 */


class SNEConfig {

	/* configure Woogle here
		all these parameters can be read using WoogleConfig::get('key')
	*/
	private static $config = array(
								         // edit values here	// comment

		'test'			=> array('Test',				'test'),
		'useProfiling'			=> array(true,					'if profiling is used'),

	
	);
	
	private static $db; 	// MediaWiki main database object (master9
	private static $db_slv; // MediaWiki main database object (slave/read only)
	
	
	// *** end of configuration ***
	
	
	//
	// Configuration access methods
	//
	
	
	/**
	 * Retrieve configuration values from $config array
	 * 
	 * @param 		$key name of configuration parameter
	 * @return 		value of configuration parameter
	 */
	
	public static function get($key) {
		WoogleConfig::profileIn(__METHOD__);
		self::loadConfig();
		WoogleConfig::profileOut(__METHOD__);
		return self::$config[$key][0];
	}
	
	/**
	 * Retrieve configuration value descriptions from $config array
	 * 
	 * @param 		$key name of configuration parameter
	 * @return 		description/comment of configuration parameter
	 */
	
	public static function getComment($key) {
		WoogleConfig::profileIn(__METHOD__);
		self::loadConfig();
		WoogleConfig::profileOut(__METHOD__);
		return self::$config[$key][1];
	}
	
	/**
	 * Retrieve all configuration parameters used in the $config array
	 * 
	 * @return 		array containing configuration parameters
	 */
	
	public static function getKeys() {
		WoogleConfig::profileIn(__METHOD__);
		self::loadConfig();
		$keys = array_keys(self::$config);
		WoogleConfig::profileOut(__METHOD__);
		return $keys;
	}
	
	/**
	 * Pulls configuration values from addons by running the WoogleConfig hook
	 * 
	 * @return 	void
	 */
	
	private static function loadConfig() {
		WoogleConfig::profileIn(__METHOD__);
		if (!self::$configLoaded) {
			// set $configLoaded before running hooks, in case the hook wants to access get() resulting in an infinite recursion otherwise
			self::$configLoaded = true;
			wfRunHooks('SNEConfig', array(&self::$config));
		}
		WoogleConfig::profileOut(__METHOD__);
	}
	
	/**
	 * Wrapper method for profiling - to be called at the start of a method/function
	 * @param		$name	name of the profiled method
	 * @return		void
	 */
	
	public static function profileIn($name) {
		if (self::$config['useProfiling']) wfProfileIn('Woogle ' . $name);
	}
	
	/**
	 * Wrapper method for profiling - to be called at the end of a method/function
	 * @param		$name		name of the profiled method
	 * @param		$return		return value (optional)
	 * @return		void
	 */
	
	public static function profileOut($name, $return = true) {
		if (self::$config['useProfiling']) wfProfileOut('Woogle ' . $name);
		return $return;
	}

	/**
	 * smw method to get database and connect to it
	 * @param		$master				boolean
	 * @return		$db or $db_slv		database
	 */
	
	public static function getDB($master = true) {
		if (!isset(self::$db)) self::$db =& wfGetDB(DB_MASTER);
		if (!isset(self::$db_slv)) self::$db_slv =& wfGetDB(DB_SLAVE);
		if ($master){
			return self::$db;			
		} else {
			return self::$db_slv;
		}
	}
	
	public static function setDB($extdb, $master = true){
		if ($master){
			self::$db = $extdb;
		} else {
			self::$db_slv = $extdb;
		}
	}
	
	/**
	 * checks if a table exists inside a database
	 * @param		$tablename			string with the database's tablename
	 * @return		$res				boolean
	 */
	
	public static function tableExists($tablename){
		$db = self::getDB(false);
		$res = $db->tableExists($tablename);
		return $res;
	}
}
?>

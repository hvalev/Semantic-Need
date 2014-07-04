<?php
global $wgAutoloadClasses, $argv, $IP;
global $smwgIP;
global $wgVersion; // is needed for MediaWiki version checks and version depending test setup
global $wgLanguageCode, $wgNamespaceAliases, $wgNamespacesWithSubpages, $sfgContLang;
# Error reporting (only useful for debugging)
//$wgShowExceptionDetails = true; 
//$wgShowSQLErrors = true;
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);
//require_once('includes/WebStart.php');
//require_once(dirname( __FILE__ ) . '/../../../includes/WebStart.php' );
// get the MediaWiki commandline options from file in MW's maintenance folder
// option 1: using the "local" php_inlcude path (i dont know if this is the right word) 
// require_once(dirname( __FILE__ ) . '/../../../maintenance/commandLine.inc' );
// option 2: using the "global" php_inlcude path 
require_once('maintenance/commandLine.inc');
require_once('PHPUnit/Framework/TestCase.php');
// XXX: the MediaWiki test case is still there in MW's SVN cf.:
// http://svn.wikimedia.org/svnroot/mediawiki/trunk/phase3/tests/phpunit/MediaWikiTestCase.php
// since MW 1.16 use copy of file provided with SNE 
//require_once(dirname(__FILE__) . '/MediaWiki_TestCase.php' );	
// database setup params
global $wgDBprefix, $wgDBadminuser, $wgDBadminpassword;
//$wgDBprefix = 'sne_';
// use the db user and respective password from LocalSettings.php of the wiki
//$wgDBadminuser = $wgDBuser;
//$wgDBadminpassword = $wgDBpassword;
echo "Encoding set to: " . mb_internal_encoding() . "\n";
if (!mb_internal_encoding("UTF-8")) echo "ERROR setting encoding to UTF-8!" . "\n";
echo "Encoding set to: " . mb_internal_encoding() . "\n";
// in MW1.17 ProxyTools.php:115 throw "MWException: Unable to determine IP" otherwise
global $wgIP;
//$wgIP = '127.0.0.1'; 
// This is not a valid entry point, perform no further processing unless MEDIAWIKI is defined
if( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is part of Woogle4MediaWiki and is not a valid entry point\n";
	die( 1 );
}

class SNECore_TestCase extends PHPUnit_Framework_TestCase{
	/**/
	protected static $db;
	
	public static function getDB(){
		//TODO reimplement with mediawiki globals $wgServer etc..
		self::$db = new Database($server='localhost', $user='root', $password='moses', $dbName='snetest', $flags=0, $tablePrefix= 'get from global');
		return self::$db;
	}
	
    protected function setUp(){
    	$con = mysql_connect("localhost","root","moses");
		if (!$con){
			die('Could not connect: ' . mysql_error());
		}
		if (mysql_query("CREATE DATABASE snetest",$con)){
			echo "Database created";
		}
		else{
			echo "Error creating database: " . mysql_error();
		}
		mysql_close($con);
    }
	/*
	protected function tearDown(){
		$con = mysql_connect("localhost","root","moses");
		if (!$con){
			die('Could not connect: ' . mysql_error());
		}
		if (mysql_query("DROP DATABASE snetest",$con)){
			echo "Database dropped";
		}
		else{
			echo "Error creating database: " . mysql_error();
		}
		mysql_close($con);
	}
*/
	protected function prepareTestDb(){
		//echo 'im testing ze db';
		self::setUp();
		$db = self::getDb();
		print_r($db);
		//self::rrr();
		QueryStorage::createDbTables($db);
		//echo 'le creating tables';
		//return self::$db;
		
		/*
		
		// we need media wiki version vor version compare and test DB setup
		global $wgVersion;
		// prepare test db
		// $define which tabels are created in the test DB 
		$tbl = array('smwq_query', 'smwq_constraint', 'smwq_select');
		// if mw version is 1.16 or higher the 'l10n_cache' tabel is needed 
		if ( 1 == version_compare( $wgVersion, '1.16', '>=' ))
		{
			//do something i suppose?
		}
		$this->db = $this->buildTestDatabase( $tbl );
		$this->insertTestData($this->db);
		SNEConfig::setDB($this->db);		// set master
		SNEConfig::setDB($this->db, false);	// set slave
*/
	}

	protected function removeTestDb(){
		//smwfGetStore()->drop(true);
	}
	
	protected function insertTestData($db){
		/*
			$db->safeQuery( <<<END
		INSERT INTO ! (page_id,page_namespace,page_title,page_latest,page_touched,page_len,page_is_redirect)
		VALUES (1, 0, 'Main_Page', 1, '20100316183231', 9, 0),
			   (2, 0, 'Bird', 2, '20100316183231', 9, 0),
			   (3, 0, 'Tiger', 3, '20100316183231', 9, 0),
			   (4, 0, 'Bear', 4, '20100316183231', 9, 0),
			   (5, 0, 'PageA', 5, '20100316183231', 9, 0),
			   (6, 0, 'Africa', 6, '20100316183231', 9, 0),
			   (7, 0, 'PageC', 7, '20100316183231', 9, 0),
			   (8, 0, 'PageD', 8, '20100316183231', 9, 0),
			   (9, 0, 'Swan', 9, '20100316183231', 9, 0),
			   (10, 0, 'PageF', 10, '20100316183231', 9, 0),
			   (11, 0, 'Animals', 11, '20100316183231', 9, 0),
			   (12, 10, 'Animal', 12, '20100316183231', 9, 0)
END
			, $db->tableName( 'page' ) );
			// 10 = NS_TEMPLATE
		
		$db->safeQuery( <<<END
		INSERT INTO ! (rev_id,rev_page,rev_text_id)
		VALUES (1, 1, 1),
		       (2, 2, 2),
		       (3, 3, 3),
		       (4, 4, 4),
		       (5, 5, 5),
		       (6, 6, 6),
		       (7, 7, 7),
		       (8, 8, 8),
		       (9, 9, 9),
		       (10, 10, 10),
		       (11, 11, 11),
			   (12, 12, 12)
END
			, $db->tableName( 'revision' ) );
		
		$db->safeQuery( <<<END
		INSERT INTO ! (old_id,old_text)
		VALUES (1, 'This is a main page - note please'),
			   (2, 'Bird page - [[Category:Animal]] {{Animal}}'),
			   (3, 'Tiger page - [[Category:Animal]]'),
			   (4, 'Bear page - [[Category:Animal]]'),
			   (5, 'Nothing in this page is about the S word - talk loudly.'),
			   (6, 'This page also is unrelated. [[Category:Country]] [[hasAnimal::Swan]]'),
			   (7, 'Help me! - Ã¼berhaupt nicht!'),
			   (8, 'Blah blah erlebt ein Ãœberraschung und [[VerÃ¤nderung]]!'),
			   (9, 'yum {{Animal}}'),
			   (10,'Some arbitrary ask query {{#ask: [[Category:Boo]]}}'),
			   (11,'Show all aninmals: {{#ask: [[Category:Animal]] | ?hasOwner }}'),
			   (12,'This is a template! {{#ask: [[Category:Country]] [[hasAnimal::{{PAGENAME}}]] }}')
END
			, $db->tableName( 'text' ) );
			
			$db->safeQuery( <<<END
		INSERT INTO ! (cl_from,cl_to,cl_sortkey)
		VALUES (2, 'Animal', 'Bird'),
		       (3, 'Animal', 'Tiger'),
		       (4, 'Animal', 'Bear'),
		       (6, 'Country', 'Africa')
END
			, $db->tableName( 'categorylinks' ) );
*/
	}

	
	protected function prepareQueryTestData(){
	
		
	}
	

	
	// TODO: understand & document 
	protected function buildTestDatabase2( $tables ) {
		/*
		global $testOptions, $wgDBprefix, $wgDBserver, $wgDBadminuser, $wgDBadminpassword, $wgDBname;
		$wgDBprefix = 'parsertest_';
		$db =& wfGetDB(DB_SLAVE);
		
		/*
		$db = new Database(
			$wgDBserver,
			$wgDBadminuser,
			$wgDBadminpassword,
			$wgDBname );
		
		if( $db->isOpen() ) {
			if (!(strcmp($db->getServerVersion(), '4.1') < 0 and stristr($db->getSoftwareLink(), 'MySQL'))) {
				# Database that supports CREATE TABLE ... LIKE
				foreach ($tables as $tbl) {
					$newTableName = $db->tableName( $tbl );
					#$tableName = $this->oldTableNames[$tbl];
					$tableName = $tbl;
					$db->query("CREATE TEMPORARY TABLE $newTableName (LIKE $tableName)");
				}
			} else {
				# Hack for MySQL versions < 4.1, which don't support
				# "CREATE TABLE ... LIKE". Note that
				# "CREATE TEMPORARY TABLE ... SELECT * FROM ... LIMIT 0"
				# would not create the indexes we need....
				foreach ($tables as $tbl) {
					$res = $db->query("SHOW CREATE TABLE $tbl");
					$row = $db->fetchRow($res);
					$create = $row[1];
					$create_tmp = preg_replace('/CREATE TABLE `(.*?)`/', 'CREATE TEMPORARY TABLE `'
						. $wgDBprefix . '\\1`', $create);
					if ($create === $create_tmp) {
						# Couldn't do replacement
						wfDie( "could not create temporary table $tbl" );
					}
					$db->query($create_tmp);
				}

			}
			return $db;
		} else {
			// Something amiss
			return null;
		}
		*/
	}
}


?>

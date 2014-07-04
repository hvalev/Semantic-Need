<?php
// why are all these global definitions needed here ?
// if you know please explaing why ! --> start rigt here ...
// 
global $wgAutoloadClasses, $argv, $IP;
global $smwgIP;
global $wgVersion; // is needed for MediaWiki version checks and version depending test setup
global $wgLanguageCode, $wgNamespaceAliases, $wgNamespacesWithSubpages, $sfgContLang;
global $sfgIP; // SF extention
global $wgParser; // CSS extention
global $wgContLang;
global $GLOBALS;
global $wgExtensionMessagesFiles, $wgMessageCache, $wgLanguageNames, $wgExtraLanguageNames;
global $wgLang;
global $wgContLanguageCode, $wgRequest, $wgUser, $wgContLang;
global $wgMemc;
global $optionsWithArgs;
global $wgCommandLineMode;
global $wgLocalisationCacheConf;
global $wgLBFactoryConf;
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
require_once(dirname(__FILE__) . '/MediaWiki_TestCase.php' );	
// database setup params
global $wgDBprefix, $wgDBadminuser, $wgDBadminpassword;
$wgDBprefix = 'parsertest_';
// use the db user and respective password from LocalSettings.php of the wiki
$wgDBadminuser = $wgDBuser;
$wgDBadminpassword = $wgDBpassword;
echo "Encoding set to: " . mb_internal_encoding() . "\n";
if (!mb_internal_encoding("UTF-8")) echo "ERROR setting encoding to UTF-8!" . "\n";
echo "Encoding set to: " . mb_internal_encoding() . "\n";
// in MW1.17 ProxyTools.php:115 throw "MWException: Unable to determine IP" otherwise
global $wgIP;
$wgIP = '127.0.0.1'; 
// This is not a valid entry point, perform no further processing unless MEDIAWIKI is defined
if( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is part of Woogle4MediaWiki and is not a valid entry point\n";
	die( 1 );
}

class SNE_TestCase extends MediaWiki_TestCase{
	
	protected $db;
		
	/*
	protected $qs;
	protected $qm;
	protected $is;
 	protected $db;

	// test data summary (c.f. insertTestData() and prepareQueryTestData())
 	protected $num_test_docs = 11;
	protected $num_test_queries = 5;
	protected $num_testde_clicks = 3;
	protected $num_diff_queries = 5;
	protected $num_diff_results = 3;
	protected $num_test_clicks = 5;
	*/

    protected function setUp(){
    }
	
	protected function tearDown(){
	    // remove is called in the subclassed test classe tearDown() method
		//smwfGetStore()->drop(true);	// TODO check
	}

	protected function prepareTestDb(){
		// we need media wiki version vor version compare and test DB setup
		global $wgVersion;
		// prepare test db
		// $define which tabels are created in the test DB 
		$tbl = array('user', 'page', 'revision', 'text', 'categorylinks', 'page_restrictions', 'interwiki', 'smwq_query');
		// if mw version is 1.16 or higher the 'l10n_cache' tabel is needed 
		if ( 1 == version_compare( $wgVersion, '1.16', '>=' ))
		{
			$tbl[] = 'l10n_cache';
			$tbl[] = 'msg_resource';
			$tbl[] = 'msg_resource_links';
		}
		$this->db = $this->buildTestDatabase( $tbl );
		$this->insertTestData($this->db);
		SNEConfig::setDB($this->db);		// set master
		SNEConfig::setDB($this->db, false);	// set slave
		// without this, SMW:refresh won't find parsertest_page
		$db_zwo = wfGetDB(DB_SLAVE);
		// without this check, any second prepareTestDb call will yield an error
		// parsertest_page already exists
		if (! $db_zwo->tableExists('page')){
			$db2 = $this->buildTestDatabase2( $tbl );
			$this->insertTestData($db2);
		}
		// SMW refresh (from SMW_refreshData.php)
		
		smwfGetStore()->drop(true);
		wfRunHooks('smwDropTables');
		smwfGetStore()->setup(true);
		wfRunHooks('smwInitializeTables');

		// FIMXE: refresh data call to smwfGetStore() which returns a 'SMWSQLStore2 extends SMWStore' 
		//        Object caues an exception following the stacktrace : 
		// 
		// 1) SNE_SemanticQueryManagerTest::testScanAllWikiPages
		// Undefined property: ParserOutput::$mSMWData
		// 
		// E:\dev\Programme\xampp\htdocs\mediawiki-1.16.1\extensions\SemanticMediaWiki\includes\SMW_ParseData.php:154
		// E:\dev\Programme\xampp\htdocs\mediawiki-1.16.1\extensions\SemanticMediaWiki\includes\jobs\SMW_UpdateJob.php:65
		// E:\dev\Programme\xampp\htdocs\mediawiki-1.16.1\extensions\SemanticMediaWiki\includes\storage\SMW_SQLStore2.php:1540
		// E:\dev\Programme\xampp\htdocs\mediawiki-1.16.1\extensions\SemanticNeed\test\SNE_TestCase.php:114
		// E:\dev\Programme\xampp\htdocs\mediawiki-1.16.1\extensions\SemanticNeed\test\SNE_SemanticQueryManagerTest.php:9
		//
		// &$index, $count, $namespaces = false, $usejobs = true
		// if second param is not set to 1 there is an exception -> why ? 
		// TODO: somehow here we have to tell Semantic MediaWiki to create all DB entries for the temp 
		//       tables --> how !? 
		// $id = start index for refreshing
		// second param = how many pages to refresh = 12 since there are 12 test page objects
		// 		
		
		
		
		// Documentation hints for smwfGetStore()->refreshData() function
		// http://svn.wikimedia.org/doc/
		// official documentation of the method at SMW webpage: 
		// http://semantic-mediawiki.org/doc/classSMWStore.html#1aedb219dcd728d6c1e8ac10a659837d
		// http://semantic-mediawiki.org/doc/
		// afik, the refresh has to be done correct to make the SNESemanticQueryManager calls work!
		// to avoid the test gettingt stucked the refresh-call is quoted !
//		$id = 1;
//		smwfGetStore()->refreshData($id, 12, false, false); // TODO
	}

	protected function removeTestDb(){
		smwfGetStore()->drop(true);
	}
	
	protected function insertTestData($db){
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

	}

	/*
	protected function prepareTestDb(){
		// prepare test db
		$this->db = $this->buildTestDatabase( array('page', 'revision', 'text', 'watchlist', 'pagelinks', 'image') );
		//$this->db = $this->buildTestDatabase( array('page', 'revision', 'text', 'watchlist', 'pagelinks', 'image', 'user') );
		$this->insertTestData();
		WoogleMWUtil::setDB($this->db);			// set master
		WoogleMWUtil::setDB($this->db, false);	// set slave
		
	}
	
	protected function insertTestData() {
		
		// NOTE: keep $this->num_test_docs in sync!
		
		$this->db->safeQuery( <<<END
		INSERT INTO ! (page_id,page_namespace,page_title,page_latest,page_touched)
		VALUES (1, 0, 'Main_Page', 1, '20100316183231'),
			   (2, 1, 'Talk:Main_Page', 2, '20100316183231'),
			   (3, 0, 'Smithee', 3, '20100316183231'),
			   (4, 1, 'Talk:Smithee', 4, '20100316183231'),
			   (5, 0, 'Unrelated_page', 5, '20100316183231'),
			   (6, 0, 'VerÃ¤nderung', 6, '20100316183231'),
			   (7, 4, 'Help', 7, '20100316183231'),
			   (8, 0, 'Thppt', 8, '20100316183231'),
			   (9, 0, 'Alan_Smithee', 9, '20100316183231'),
			   (10, 0, 'Pages', 10, '20100316183231'),
			   (11, 0, 'Heiko Haller/Chairing', 11, '20100316183231')
END
			, $this->db->tableName( 'page' ) );
		
		$this->db->safeQuery( <<<END
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
		       (11, 11, 11)
END
			, $this->db->tableName( 'revision' ) );
		
		$this->db->safeQuery( <<<END
		INSERT INTO ! (old_id,old_text)
		VALUES (1, 'This is a main page - note please'),
			   (2, 'This is a talk page to the main page, see [[smithee]]'),
			   (3, 'A smithee is one who smiths. See also [[Alan Smithee]]'),
			   (4, 'This article sucks.'),
			   (5, 'Nothing in this page is about the S word - talk loudly.'),
			   (6, 'This page also is unrelated.'),
			   (7, 'Help me! - Ã¼berhaupt nicht!'),
			   (8, 'Blah blah erlebt ein Ãœberraschung und [[VerÃ¤nderung]]!'),
			   (9, 'yum'),
			   (10,'are food'),
			   (11,'A Meeting')
END
			, $this->db->tableName( 'text' ) );
			
			// NS 110 = NS_WOOGLE
		$this->db->safeQuery( <<<END
		INSERT INTO ! (wl_user,wl_namespace,wl_title)
		VALUES (1, 110, 'Test'),
		       (1, 0, 'Main_Page'),
		       (2, 110, 'Test')
END
			, $this->db->tableName( 'watchlist' ) );
			
		$this->db->safeQuery( <<<END
		INSERT INTO ! (pl_from,pl_namespace,pl_title)
		VALUES (2, 0, 'Smithee'),
		       (3, 0, 'Alan_Smithee'),
		       (8, 0, 'VerÃ¤nderung')
END
			, $this->db->tableName( 'pagelinks' ) );
			
		/*
		$this->db->safeQuery( <<<END
		INSERT INTO ! (user_id,user_name)
		VALUES (1, 'WikiSysop'),
		       (2, 'KarlHeinz')
END
			, $this->db->tableName( 'user' ) );
		*/
			
	//}
	
	protected function prepareQueryTestData(){
	
		
	}
	
	/*
	protected function prepareQueryTestData(){
		$rx = $this->qm->initDb();
//		echo "\n calling prepareQueryTestData() $rx tables created by initDb";
		$this->qm->handleQuery('Test', 20);
		$this->qm->handleQuery('Test', 20);
		$this->qm->handleQuery('test', 20);
		$this->qm->handleQuery('TesT', 20);
		$this->qm->handleQuery('TEsT', 20);
		$this->qm->handleQuery('Test Query', 20);
		$this->qm->handleQuery('Test Query', 20);
		$this->qm->handleQuery('Test Magazine', 20);
		$this->qm->handleQuery('Test ÃœmlÃ¤ut', 20);
		$this->qm->handleQuery('Test Umlaut', 20);
		$this->qm->handleClick('Test', 'http://www.test.de', 1, '');
		$this->qm->handleClick('Test', 'http://www.test.de', 1, '');
		$this->qm->handleClick('Test', 'http://www.test.de', 1, '');
		$this->qm->handleClick('Test', 'http://www.test.com', 2, '');
		$this->qm->handleClick('Test', 'http://www.test.net', 3, '');
	}
	*/
	
	/*	
	protected function removeQueryTestData(){
//		echo "\n calling dropDb";
		$this->qm->dropDb();
		
		$this->assertFalse(WoogleMWUtil::tableExists('woogle_aggquery'));
	}
	*/
	
	// TODO: understand & document 
	protected function buildTestDatabase2( $tables ) {
		global $testOptions, $wgDBprefix, $wgDBserver, $wgDBadminuser, $wgDBadminpassword, $wgDBname;
		$wgDBprefix = 'parsertest_';
		$db =& wfGetDB(DB_SLAVE);
		
		/*
		$db = new Database(
			$wgDBserver,
			$wgDBadminuser,
			$wgDBadminpassword,
			$wgDBname );
		*/
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
	}
}


?>

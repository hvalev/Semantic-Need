<?php

require_once('SNE_TestCase.php');
require_once('MockUser.php');
 
class SNE_SemanticQueryManagerTest extends SNE_TestCase{

	protected function setUp(){
		$this->prepareTestDb();
    	$count = QueryStorage::createDbTables();
		echo 'Created ' . $count . ' tables in the database.'.PHP_EOL;
	}
    
    protected function tearDown(){
		$this->removeTestDb();
		QueryStorage::dropDbTables();
    }
	
	
	public function xtestSMW(){
		//$ar = array();
		//$this->assertTrue(smwfOnParserTestTables($ar));
		echo "SMW Test";
	}
	
	public function testScanAllWikiPages(){
		echo "Starting PHPUnit Test '".__METHOD__."' ".PHP_EOL;
		//global $wgHooks;
		//print_r($wgHooks['ParserFirstCallInit']);
		
		//global $wgParser;
		//wfRunHooks( 'ParserClearState', array( $wgParser ) );
		
		/*
		echo "PARSER HOOKS \n";
		print_r($wgParser->getFunctionHooks());
		echo "PARSER HOOKS \n";
		*/
		
		//
		//	TODO: is allow purge check still necessary after changing to parse?
		//
		/*
		global $wgUser;//, $wgRequest;
		//$sysop = User::newFromName('WikiSysop');
		$sysop = new MockUser('WikiSysop');
		$sysop->setIsAllowed(true);
		$wgUser = $sysop;
		
		$this->assertTrue( $wgUser->isAllowed( 'purge' ));
		//$this->assertTrue($wgRequest->wasPosted());
		*/
		
		/*
		$req = new FauxRequest($_POST + $_GET, true);
		$wgRequest = $req;
		
		$this->assertTrue( $wgUser->isAllowed( 'purge' ));
		$this->assertTrue($wgRequest->wasPosted());
*/
		/*
		$db = SNEConfig::getDB();
		extract($db->tableNames('page'));
		
		echo "Checking for table " . $page; 
		if ($db->tableExists($page)) {
			echo "TABLE EXISTS";
		} else {
			echo "TABLE DOES NOT EXIST";
		}
		if ($db->tableExists('page')) {
			echo "TABLEp EXISTS";
		} else {
			echo "TABLEp DOES NOT EXIST";
		}
		
		$q = "SELECT page_id,page_len,page_is_redirect FROM `parsertest_page` WHERE page_namespace = '0' AND page_title = 'Main_Page'  LIMIT 1";
		$rx = $db->query($q);
		print_r($rx);
		*/
		

		//echo "calling purge \n";
		//SNEAdmin::purgeAllWikiPages();
		//SNEAdmin::purgeWikiPages();
		//echo "exit purge \n";
		
		//echo "1.) Query count = ".count(SNESemanticQueryManager::getAllSemanticQueries())."".PHP_EOL;
		//$this->assertEquals(0, count(SNESemanticQueryManager::getAllSemanticQueries()));
		//SNEAdmin::findAllWikiPages();
		// use the follwoing to activate some SNE verbose output : 
		// SNEAdmin::findAllWikiPages(true);
		//echo "Query count " . count(SNESemanticQueryManager::getAllSemanticQueries()) . "".PHP_EOL;
		//echo "Queries " . print_r(SNESemanticQueryManager::getAllSemanticQueries(), true) . "".PHP_EOL;
		//echo "2.) Query count = ".count(SNESemanticQueryManager::getAllSemanticQueries())."".PHP_EOL;
		//$this->assertEquals(4, count(SNESemanticQueryManager::getAllSemanticQueries()));
	}
	
	/* TODO: right now these methods do nothing so they are quoted for test performance reasons
	public function testGetWantedPrintouts(){
		echo "Starting PHPUnit Test '".__METHOD__."' ".PHP_EOL;
		// getWantedPrintouts($wikiPageName)
		
		// one
		//print_r(SNESemanticQueryManager::getWantedPrintouts('Bird'));
		// TODO - method missing?
	}
	
	public function testGetSemanticQueriesNearlyMatchingPage(){
		echo "Starting PHPUnit Test '".__METHOD__."' ".PHP_EOL;
		// getSemanticQueriesNearlyMatchingPage($wikiPageName)
		
		// one
		//print_r(SNESemanticQueryManager::getSemanticQueriesNearlyMatchingPage('Bird'));
		// TODO - method missing?
	}
	
	public function testgGetSemanticQueriesMatchingPage(){
		echo "Starting PHPUnit Test '".__METHOD__."' ".PHP_EOL;
		// getSemanticQueriesMatchingPage($wikiPageName)
		// one
		//print_r(SNESemanticQueryManager::getSemanticQueriesMatchingPage('Bird'));
		// TODO - method missing?
		//$this->assertEquals(1, count(SNESemanticQueryManager::getSemanticQueriesMatchingPage('Bird')));
	}*/
	

    public function xtestLogSemanticQuery(){
    	// write code calling logSemanticQuery
    }
    
    public function xtestGetSemanticQueriesByPage(){
    	// write test code    	
    }
    
    public function xtestGetSemanticQueriesBySelect(){
    	// write test code
    }
    
    public function xtestGetAllSemanticQueries(){
    	// write test code
    }
    
}

?>
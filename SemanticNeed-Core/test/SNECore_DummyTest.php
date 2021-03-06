<?php

require_once('SNECore_TestCase.php');
/******************* How 2 Run WoogleDummyTest PHPUnit Test ********************
*
* 1. Read INFO.TXT for detailed information about SNE PHPUnit Test execution
*
* 2. To execute this test open a console and go to the 
*
*     <MediaWiki SNE Test Install>/extensions/SemanticNeed-Ext/test/
*
*   directory. 
*
* 3. Run the PHPUnit Test by using the following command: 
*    
*     phpunit --include-path <Path to Test MediaWiki Running SNE> SNE_DummyTest.php 
*
*    Replace <Path to Test MediaWiki Running SNE> by the absolute path to the MediaWiki instance
*    SNE is installed with. A complete PHPUnit call of 'SNE_DummyTest.php' on a 
*    Windows OS running MediaWikig together with SNE at 'E:/dev/Programme/xampp/htdocs/ 
*    mediawiki-1.17.0' would look like : 
*   
*     phpunit --include-path E:/dev/Programme/xampp/htdocs/mediawiki-1.17.0 SNE_DummyTest.php
*
*******************************************************************************/ 
class SNECore_DummyTest extends SNECore_TestCase{

	public function setUp(){
		$this->prepareTestDb();
	}

	public function tearDown(){
		$this->removeTestDb();
	}
	
	 public function testCheckDBSetup(){
		$db1 = SNECoreConfig::getDB();
		$db2 =& wfGetDB(DB_SLAVE);
		$tab = 'user';
		$this->assertTrue($db1->tableExists($tab), "table $tab does not exist in db1");
		$this->assertTrue($db2->tableExists($tab), "table $tab does not exist in db2");
		$tab = 'page';
		$this->assertTrue($db1->tableExists($tab), "table $tab does not exist in db1");
		$this->assertTrue($db2->tableExists($tab), "table $tab does not exist in db2");
		$tab = 'smwq_query';
		$this->assertTrue($db1->tableExists($tab), "table $tab does not exist in db1");
		$this->assertTrue($db2->tableExists($tab), "table $tab does not exist in db2");
	 }
	 
}
?>

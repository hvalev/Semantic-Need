<?php
class SMWQQueryMapperTest{
		/**
	 * @test
	 */
	
	public function instantiateFromDbTest(){
		QueryStorage::createDbTables(SNECore_TestCase::getDb());
	}
	
	public function insertTest(){
		SNECoreConfig::setDB(SNECore_TestCase::getDb());
		$dummy = new SMWQQueryGateway();
		$dummy->setQid('test');
		$dummy->setType('1');
		$dummy->setFormat('test');
		$dummy->setLimit('1');
		$dummy->setResults('1');
		$dummy->setPage('test');
		$dummy->setQueryString('test');
		$dummy->setLink('test');
		$dummy->setAlias('test');
		$dummy->setIsConcept('0');
		$dummy->setIsSubquery('0');
		//$dummy->setCreatedOn('0');
		//$dummy->setRemovedOn('0');
		$dummy->setUserId('1');
		$dummy->setActive('1');
		//print_r($dummy);
		//self::rrr();
		$dummy->insert();
		
		//$db = SNECore_QueryStorageTest::getDb();
		//$db->select();
	}
}
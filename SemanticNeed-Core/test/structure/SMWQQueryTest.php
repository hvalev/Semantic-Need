<?php
require_once('/../SNECore_TestCase.php');
class SMWQQuery extends SNECore_TestCase{
	
	/*
	 * STRUCTURE
	 * 1 test get/set functions
	 * 2 test instantiate from database
	 * 3 test misc functions
	 */
	

	/**
	 * 1
	 * @test
	 * @dataProvider setterGetterFunctionsProvider
	 */
	
	public function testSetterGetterFunctions($set,$get){
		$dummy = new SMWQQueryGateway();
		$dummy->$set('test');
		$this->assertEquals('test',$dummy->$get());
	}
	
	/**
	 * 3
	 * @test 
	 * @dataProvider setterGetterFunctionsProvider
	 */
	
	public function testGetVarName($set,$get){
		$variables = array(
		'qid',
		'type',
		'format',
		'limit',
		'results',
		'page',
		'queryString',
		'link',
		'alias',
		'isConcept',
		'isSubquery',
		'createdOn',
		'removedOn',
		'userId',
		'active'
		);
		$dummy = new SMWQQueryGateway();
		$dummy->$set('test');
		$this->assertContains($dummy->getVarName($dummy->$get()),$variables);
	}
	
	/**
	 * provider function
	 */
	
	public static function setterGetterFunctionsProvider(){
		return array(
		array('setQid','getQid'),
		array('setType','getType'),
		array('setFormat','getFormat'),
		array('setLimit','getLimit'),
		array('setResults','getResults'),
		array('setPage','getPage'),
		array('setQueryString','getQueryString'),
		array('setLink','getLink'),
		array('setAlias','getAlias'),
		array('setIsConcept','getIsConcept'),
		array('setIsSubquery','getIsSubquery'),
		array('setCreatedOn','getCreatedOn'),
		array('setRemovedOn','getRemovedOn'),
		array('setUserId','getUserId'),
		array('setActive','getActive')
		);
	}
}
?>
<?php
require_once('SNECore_TestCase.php');
class SNECore_QueryStorageTest extends SNECore_TestCase{	
	/*
	 * @test
	 */
	public function testDB(){
		/*
		$array = array('smwq_constraint_qid' => 'sfsdafsdaf', 
		'smwq_constraint_order' => 0, 'smwq_constraint_andor' => 'AND',
		'smwq_constraint_isCategory' => 0, 'smwq_constraint_isNamespace' => 0,
		'smwq_constraint_isSinglePage' => 0, 'smwq_constraint_isConcept' => 0,
		'smwq_constraint_isSubquery' => 0, 'smwq_constraint_property' => 'netsky',
		'smwq_constraint_expression' => '>', 'smwq_constraint_value' => 'isawesome');
		*/
		//echo 'im testing ze db';
		self::prepareTestDb();
		//echo 'le creating tables';
	}	
}
?>
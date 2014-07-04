<?php
require_once('SNE_TestCase.php');

global $wgAPIModules;
global $wgArticle;
global $wgTitle;

class SNE_QueryAnalyzerTest extends SNE_TestCase {
	
	protected $qid;
	protected $type = null;
	protected $format = null;
	protected $limit = null;
	protected $results = null;
	protected $page = null;
	protected $queryString = null;
	protected $queryLink = null;
	protected $alias = null;
	protected $isConcept = 0;
	protected $isSubquery = 0;
	protected $createdOn = null;
	protected $removedOn = null;
	protected $nearMatches = null;
	protected $missingValues = null;
	protected $userId = null;
	protected $active = null;
	
	public function setUp(){
		$this->prepareTestDb();
	}
	
	/**
	 * @test
	 *
	 * NOTE: test Methods do NOT have arguments and should (/have to?) be named like: 
	 *       test<NameOfTestFunction>() {...}
	 */
	public function testAnalyzeSemanticQuery(){
	
		//TODO figure out how to use API-s
		$wgAPIModules = 'ApiSMWQuery';
		//$params = new FauxRequest(
		// array(
        //	'action' => 'smwinfo',
		// 	)					
		//);
		// ApiSMWQuery Requires Semantic MediaWiki version 1.6.2 or higher 
		// $api = new ApiSMWQuery();
		
		//$params = new FauxRequest(array('action' => 'SMWQuery'));//, 
										//'querystring' => '[[something::pretty]] |?limit=20|?format=list',
										//'printouts' => array()));
		
		//$api = new ApiMain('');
		//$params = new FauxRequest(array('[[something::pretty]] |?limit=20|?format=list',array()));
		//$api = new ApiMain( $params );
		
		// create a MW API Object by 1st creating a api request
		$request = new FauxRequest(array('[[something::pretty]] |?limit=20|?format=list',array()));
		// 2nd creat the Main MW Api Object
		$api = new ApiMain($request, false);
		// 3rd passing the Main MW Api Object to the API Module needed
		// should work similar with the new ApiSMWQuery Class in Semantic MW since version 1.6.2 
		$smwApi = new ApiSMWInfo($api, 'ApiSMWInfo');
		//$api->getQuery('[[something::pretty]] |?limit=20|?format=list',array());
		//$query = ApiSMWQuery::getQuery();
		//$res = ApiSMWQuery::getQueryResult($query);
		
		// check if the API Object was created correct 
		$desc = $smwApi->getDescription();
		echo PHP_EOL;
		echo 'SMW API Object Creation Testing...'.PHP_EOL. 'Version Info:           '. 
		$smwApi->getVersion() .PHP_EOL. 'API Module Description: '.reset($desc) .PHP_EOL.PHP_EOL;
		// var_dump( $smwApi );
		
		//self::rrr();
		//$id = Title::newFromText("Test_page")->getArticleId(); //Get the id for the article called Test_page
		//$myArticle = Article::newFromId($id); //Make an article object from that id
		//echo $myArticle->getRawText(); //Print the raw wiki text of the article
		//wfRunHooks('smwInitializeTables');
		$title = Title::newFromText('Z');
		$article = new Article($title);
		$qp = new SMWQueryParser();
		$desc = $qp->getQueryDescription('[[iamaproperty::andihasavalue]]');
		$query = new SMWQuery($desc);
		//$query = SMWQueryProcessor::createQuery('[[iamaproperty::andihasavalue]]',array());
		
		/*
		$res = smwfGetStore()->getQueryResult($query);
		$actual = QueryAnalyzer::analyzeSemanticQuery($query,'','',$res,0,0);
		$expected = new SMWQQueryGateway();
		$expected->setLimit(20);
		$expected->setFormat('list');		
		assertEquals($expected, $actual);
		*/
	}
	/**
	 *  @test
	 */
	public function testHandleOtherSMWObjects(){
		$SMWObject; 
		$smwqQuery;
		$andor;
		//category
		//property
		//singlepage
		//concept
	}
	/**
	 *  @test
	 */
	public static function testAnalyzeSemanticQueryConstraints(){
		$SMWDescription;
		$smwqQuery;
		$andor = null;
		//multiple values logged correctly?
		// FIXME: ApiSMWQuery requires SMW version 1.6.2 or higher -> do a check in the base PHPUnit 
		//        Test, if Semantic MediaWiki version is 1.6.2 or higher 
		// $query = ApiSMWQuery::getQuery('[[something::pretty]]',array());
		// ApiSMWQuery::getQueryResult($query);
	}
}
?>
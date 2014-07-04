<?php
require_once('SNECore_TestCase.php');
global $wgAPIModules;
global $wgArticle;
global $wgTitle;

class SNECore_QueryAnalyzerTest extends SNECore_TestCase {
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
		//$this->prepareTestDb();
	}
	
	/**
	 * @test
	 *
	 * NOTE: test Methods do NOT have arguments and should (/have to?) be named like: 
	 *       test<NameOfTestFunction>() {...}
	 */
	public function testAnalyzeSemanticQuery(){
		//preparing necessary 'standart' parameters to input in the function
		$title = Title::newFromText('UnitTestBaby');
		$article = new Article($title);
		$context = '';
		$format = 'table';
		$isSubquery = 0;
		$isConcept = 0;
		
		/*
		 * ACTUAL UNIT TESTS START HERE!
		 */
		//CHECK QUERYSTRING
		$query = SMWQueryProcessor::createQuery('[[something::nothing]]', array('format' => 'table'));
		$result = smwfGetStore()->getQueryResult($query);
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals('[[something::nothing]]',$smwqQuery->getQueryString());
		
		$query = SMWQueryProcessor::createQuery('[[SomEthing::noThiNg]]', array('format' => 'table'));
		$result = smwfGetStore()->getQueryResult($query);
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals('[[SomEthing::noThiNg]]',$smwqQuery->getQueryString());
		
		$query = SMWQueryProcessor::createQuery('[[ßümething::nöthing]]', array('format' => 'table'));
		$result = smwfGetStore()->getQueryResult($query);
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals('[[ßümething::nöthing]]',$smwqQuery->getQueryString());
		//CHECK QUERYSTRING WITH PRINTOUTS
		/*
		$printouts = array();
//		$data = SMWPropertyValue::makeUserProperty ('hello');
		$printout = new SMWPrintRequest('PRINT_PROP','hello');
		$printout->setParameter('hello','world');
		//print_r($printout);
		//print_r($printout->getSerialization());
		//self::rrr();
		//$printouts[] = $printout;
		$params = SMWQueryProcessor::getProcessedParams(array ('format' => 'table'),array ($printout)); 		
		$query = SMWQueryProcessor::createQuery('[[something::nothing]]', $params,'INLINE_QUERY', $format= '', $printouts);
		$result = smwfGetStore()->getQueryResult($query);
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals('[[something::nothing]]|?hello',$smwqQuery->getQueryString());
		
		$query = SMWQueryProcessor::createQuery('[[SomEthing::noThiNg]]', array('format' => 'table'));
		$result = smwfGetStore()->getQueryResult($query);
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals('[[SomEthing::noThiNg]]',$smwqQuery->getQueryString());
		
		$query = SMWQueryProcessor::createQuery('[[ßümething::nöthing]]', array('format' => 'table'));
		$result = smwfGetStore()->getQueryResult($query);
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals('[[ßümething::nöthing]]',$smwqQuery->getQueryString());
		*/
		//CHECK TYPE
		$query = SMWQueryProcessor::createQuery('[[something::nothing]]', array('format' => 'table'));
		$result = smwfGetStore()->getQueryResult($query);
		$context = 'ßchlong';
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals('ßchlong',$smwqQuery->getType());
		
		$context = 'ScHLong';
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals('ScHLong',$smwqQuery->getType());
		
		$context = 'schlong';
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals('schlong',$smwqQuery->getType());
		
		//CHECK FORMAT
		$query = SMWQueryProcessor::createQuery('[[something::nothing]]', array('format' => 'table'));
		$result = smwfGetStore()->getQueryResult($query);
		$format = 'table';
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals('table',$smwqQuery->getFormat());
		
		$format = 'rss';
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals('rss',$smwqQuery->getFormat());
		
		$format = 'ol';
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals('ol',$smwqQuery->getFormat());
		
		//check set/get format function
		$query = SMWQueryProcessor::createQuery('[[something::nothing]]', array('format' => 'table'));
		$query->setLimit(5);
		$result = smwfGetStore()->getQueryResult($query);
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals(5,$smwqQuery->getLimit());
		
		$query->setLimit('5');
		$result = smwfGetStore()->getQueryResult($query);
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals(5,$smwqQuery->getLimit());

		//CHECK COUNT
		/*	
		$title = Title::newFromText('TestPage1');
		$article = new Article($title);
		$articleContent = $article->fetchContent();
		$article->doEdit('[[something::nothing]]','[[something::nothing]]', 'EDIT_NEW');
		$article->forUpdate();
		global $wgParser, $wgOut;
		$parseroutput = $wgParser->parse($article->getContent(), $article->getTitle(), $wgOut->parserOptions());
		$content = $parseroutput->getText(); 
		$content = strip_tags( $content );
		//$article->purge();
		$title = Title::newFromText('TestPage2');
		$article = new Article($title);
		$articleContent = $article->fetchContent();
		$article->doEdit('[[something::nothing]]','[[something::nothing]]', 'EDIT_NEW');
		//$article->purge();
		$article->forUpdate();
		$parseroutput = $wgParser->parse($article->getContent(), $article->getTitle(), $wgOut->parserOptions());
		$content = $parseroutput->getText(); 
		$content = strip_tags( $content );
		
		
		$query = SMWQueryProcessor::createQuery('[[something::nothing]]', array('format' => 'table'));
		$result = smwfGetStore()->getQueryResult($query);
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		$this->assertEquals(2,$smwqQuery->getResults());
		$title = Title::newFromText('TestPage1');
		$article = new Article($title);
		$article->doDelete('unittests');
		$title = Title::newFromText('TestPage2');
		$article = new Article($title);
		$article->doDelete('unittests');
		*/
		
		//CHECK PAGE NAME
		//concept
		//TODO try different namespaces?
		/*
		$title = Title::newFromText ('Amazing', SMW_NS_CONCEPT );
		$article = new Article($title);
		$ns = $article->getTitle()->getNamespace();
		$nsname = MWNamespace::getCanonicalName($ns);
		print_r($nsname);
		//print_r($article);
		print_r($article->getTitle()->getPrefixedText());
		self::rrr();
		//$page = SMWDIWikiPage::newFromTitle($title);
		$desc = new SMWConceptDescription($page);
		$smwqQuery = QueryAnalyzer::analyzeSemanticQuery($query, $context, $format, $result, $article, $isSubquery, $isConcept);
		assertEqual('Concept:Amazing',$smwqQuery->getPage());
		//normal wikipage
		*/

		
		
		//hash function test hash("md5", $article->getTitle()->getText().$queryString)
		//format variable needs to be set to use SMWQueryProcessor::createQuery function!
		
		//QueryAnalyzer::logSemanticQuery($query, '', '', $result, $article, 0,0);
		
		
		
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
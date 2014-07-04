<?php
/**
 * class that analyzes the SMW Query object transmitted by the hooks and
 * converts it to an array of internal smwqquery/constraint/printout objects
 * @author spell
 *
 */

class QueryAnalyzer{
	//TODO use profiling
	/**
	 * main query logging method
	 * @param		$query		SMWQueryObject of the query
	 * @param		$context	context of the query
	 * @param		$format		the format that has been used in the query
	 * @param		$res		MediaWikis result query object
	 * @param		$article	title of the current page
	 * @param		$isSubquery boolean value if the query is a subquery
	 * @param		$isSubquery boolean value if the query is a concept
	 * @return		void
	 */
	
	public static function logSemanticQuery($query, $context, $format, $res, $article, $isSubquery = 0, $isConcept = 0){
		$smwqQuery = self::analyzeSemanticQuery($query, $context, $format, $res, $article, $isSubquery, $isConcept);
		$smwqConstraints = self::analyzeSemanticQueryConstraints($query->getDescription(), $smwqQuery);
		$smwqPrintouts = self::analyzeSemanticQueryPrintouts($query->getDescription(), $smwqQuery);		
		wfRunHooks('SNE_LogQueryExecution', array(&$smwqQuery, &$smwqConstraints, &$smwqPrintouts)); //SNE
		self::handleGatewayInsert($smwqQuery,$smwqConstraints,$smwqPrintouts);
	}
	
	
	public static function analyzeSemanticQuery($query, $context, $format, $res, $article, $isSubquery = 0, $isConcept = 0){
		$smwqQuery = new SMWQQuery();
		
		//get the true query string with printouts
		$queryString = $query->getQueryString();
		foreach($query->getDescription()->getPrintRequests() as $key => $printout){
			$queryString .= $printout->getSerialisation();
		}
		$smwqQuery->setQid(hash("md5", $article->getTitle()->getText().$queryString));
		$smwqQuery->setType($context);
		$smwqQuery->setFormat($format);
		$smwqQuery->setLimit($query->getlimit());
		$smwqQuery->setResults($res->getCount());
		
		//if wikipage is a concept page we need Concept:something as the page name
		if($isConcept){
			$smwqQuery->setPage($article->getTitle()->getPrefixedText());
		}
		//if wikipage is a normal page we use getText() to retrieve just the wikipage name
		else{
			$smwqQuery->setPage($article->getTitle()->getText());
		}
		
		$link = $res->getQueryLink();
		$smwqQuery->setQueryString($query->getQueryString());
		$smwqQuery->setLink($link->getURL());

		$smwqQuery->setUserId($article->getUser());
		$smwqQuery->setActive(1);
		$smwqQuery->setIsSubquery($isSubquery);
		$smwqQuery->setIsConcept($isConcept);
		return $smwqQuery;
	}
	
	/**
	 * dispatches the Query SMW Object to an appropriate function
	 * @param		$SMWDescription		SMWQueryObject Description of the query
	 * @return		$array				Array of internal SMWQGateway Objects representing the query
	 */
	
	public static function analyzeSemanticQueryConstraints($SMWDescription, $smwqQuery, $andor = null){
		$array = array();
		if($SMWDescription instanceof SMWConjunction || $SMWDescription instanceof SMWDisjunction){
			//block to determine the $andor based on the incoming object
			if($SMWDescription instanceof SMWConjunction){
				$andor = 'AND';
			}
			else{
				$andor = 'OR';
			}
			//block to distribute the the incoming objects to the appropriate methods
			foreach($SMWDescription->getDescriptions() as $key => $SMWObject){
				if($SMWObject instanceof SMWConjunction){
					$array = array_merge($array, self::analyzeSemanticQueryConstraints($SMWObject, $smwqQuery, $andor));
				}
				elseif($SMWObject instanceof SMWDisjunction){
					$array = array_merge($array, self::analyzeSemanticQueryConstraints($SMWObject, $smwqQuery, $andor));
				}
				else{
					$array = array_merge($array, self::analyzeSemanticQueryConstraints($SMWObject, $smwqQuery, $andor));
				}
			}
		}
		else{
			$array = array_merge($array, self::handleOtherSMWObjects($SMWDescription, $smwqQuery, $andor));
		}
		return $array;
	}

	/**
	 * handles SMW Objects that represent concepts, properties, categories, namespaces, single pages, etc..
	 * @param		$SMWObject			SMW Object representing concepts, namespaces, categories, etc..
	 * @return		$constraints		Array of internal SMWQGateway Objects representing the query
	 */
	
	public static function handleOtherSMWObjects($SMWObject, $smwqQuery, $andor){
		$constraints = array();
		if($SMWObject instanceof SMWSomeProperty){
			$property = new SMWQConstraint();
			$property->setQid($smwqQuery->getQid());
			$property->setAndor($andor);
			$property->setProperty($SMWObject->getProperty()->getLabel());
			if(($SMWObject->getDepth() > 1) && ($SMWObject->getDescription() instanceof SMWSomeProperty)){
				//subquery
				$query = new SMWQuery($SMWObject->getDescription());
				$result = smwfGetStore()->getQueryResult($query);
				$title = Title::newFromText($smwqQuery->getPage());
				$article = new Article($title);
				$property->setIsSubquery(1);
				$property->setValue(hash("md5", $smwqQuery->getPage().$SMWObject->getDescription()->getQueryString()));
				$constraints[] = $property;
				//if subquery call recursively the logSemanticQuery function
				//with the generated SMWQuery, SMWQueryResult and Article objects
				self::logSemanticQuery($query, '', '', $result, $article, $isSubquery = '1', $isConcept = '0');
				$constraints = array_merge((array) $constraints,(array) $subqueries);
			}
			elseif($SMWObject->getDescription() instanceof SMWThingDescription){
				//TODO parserfunctions!!
				$property->setValue('parserfunction');
				$constraints[] = $property;
			}
			elseif($SMWObject->getDescription() instanceof SMWValueDescription){
				$property->setExpression($SMWObject->getDescription()->getComparator());
				//property of type [[something::othersomething]] or [[something::!else]]
				if($SMWObject->getDescription()->getDataItem() instanceof SMWDIString){
					$property->setValue($SMWObject->getDescription()->getDataItem()->getString());
				}
				//property of type [[something::>23]] or [[something::40]]
				elseif($SMWObject->getDescription()->getDataItem() instanceof SMWDIWikiPage){
					$property->setValue($SMWObject->getDescription()->getDataItem()->getDBkey());					
				}
				$constraints[] = $property;
			}
			else{
				//debugging
				megaman::rrr();
			}
		}
		elseif($SMWObject instanceof SMWClassDescription){
			foreach($SMWObject->getCategories() as $key => $value){
				//create MWNamespace to later get the cannonical Name for Category
				$ns = new MWNamespace;
				$category = new SMWQConstraint();
				$category->setQid($smwqQuery->getQid());
				$category->setAndor($andor);
				$category->setIsCategory(1);
				$category->setProperty($ns->getCanonicalName($value->getNamespace()));
				$category->setValue($value->getTitle()->getText());
				$constraints[] = $category;
			}
		}
		elseif($SMWObject instanceof SMWConceptDescription){
			//when we have concept as a constraint
			$concept = new SMWQConstraint();
			$concept->setQid($smwqQuery->getQid());
			$concept->setAndor($andor);
			$concept->setIsConcept(1);
			
			$ns = new MWNamespace();
			$nsname = $ns->getCanonicalName($SMWObject->getConcept()->getNamespace());

			$concept->setProperty($nsname);
			$concept->setValue($SMWObject->getConcept()->getDBkey());
			
			$constraints[] = $concept;
		}
		elseif($SMWObject instanceof SMWNamespaceDescription){
			$ns = new MWNamespace();
			$nsname = $ns->getCanonicalName($SMWObject->getNamespace());
				
			$namespace = new SMWQConstraint();
			$namespace->setQid($smwqQuery->getQid());
			$namespace->setAndor($andor);
			$namespace->setIsNamespace(1);
			$namespace->setExpression('+');
			$namespace->setValue($nsname);

			$constraints[] = $namespace;
		}
		elseif($SMWObject instanceof SMWValueDescription){
			$sp = new SMWQConstraint();
			$sp->setQid($smwqQuery->getQid());
			$sp->setAndor($andor);
			$sp->setIsSinglePage(1);
			$sp->setValue($SMWObject->getDataValue()->getDBkey());
			
			$constraints[] = $sp;
		}
		else{
			//new undocumented constraints?
			print_r($SMWObject);
			self::megaman();
		}
		return $constraints;
	}
	
	/**
	 * retrieves and converts the SMWQuery Printouts into an array of SMWQPrintout objects
	 * @param 			$SMWDescription			SMWDescription object
	 * @param 			$printouts				array of SMWQPrintout objects
	 */
	
	private static function analyzeSemanticQueryPrintouts($SMWDescription, $smwqQuery){
		$printouts = array();
		foreach($SMWDescription->getPrintRequests() as $key => $value){
			$select = $value->getData();
			if($select instanceof SMWPropertyValue){
				$printout = new SMWQPrintout();
				$printout->setQid($smwqQuery->getQid());
				$printout->setVariable($select->getText());
				$printouts[] = $printout;
			}
		}
		return $printouts;
	}
	
	private static function handleGatewayInsert($smwqQuery, $smwqConstraints, $smwqPrintouts){
		if(!SMWQQueryMapper::exists($smwqQuery)){
			SMWQQueryMapper::insert($smwqQuery);
			$counter = 0;
			foreach($smwqConstraints as $key => $constraint){
				//assign the order of the constraints
				$constraint->setOrder($counter);
				SMWQConstraintMapper::insert($constraint);
				$counter++;
			}
			//check if we have printouts at all
			if(!empty($smwqPrintouts)){
				foreach($smwqPrintouts as $key => $printout){
					SMWQPrintoutMapper::insert($printout);
				}
			}
		}
	}
}
?>
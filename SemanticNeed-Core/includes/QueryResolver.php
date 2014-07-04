<?php
/**
 * class that interacts with the smw database and provides the
 * required information to retrieve the query matches
 * @author spell
 *
 */

abstract class QueryResolver{	
	
	/**
	 * Retrieves SMW Properties on a given page
	 * @param 		$title				MW Title object
	 * @return		$pageProperties		SMWQConstraintGateway Objects
	 */
	
	public static function getPageProperties($title){
		$pageProperties = array();
		$properties = smwfGetStore()->getProperties(SMWDIWikiPage::newFromTitle($title), $requestoptions=null);		
		foreach($properties as $key => $property){
			//concentrate on User Defined Properties
			if($property->isUserDefined()){
				$constraint = new SMWQConstraint();
				$constraint->setProperty($property->getLabel());
				
				//for some reason smwfGetStore()->getPropertyValues() returns an array;
				$value = smwfGetStore()->getPropertyValues(SMWDIWikiPage::newFromTitle ($title), $property);
				$value = array_pop($value);
				
				if($value instanceof SMWDIWikiPage){
					$constraint->setValue($value->getTitle()->getText());
				}
				elseif($value instanceof SMWDIString){
					$constraint->setValue($value->getDIType());
				}
				elseif($value instanceof SMWDINumber){
					$constraint->setValue($value->getNumber());
				}
				$pageProperties[] = $constraint;
			}
		}
		return $pageProperties;
	}

	/**
	 * Retrieves MW Categories on a given page
	 * @param 		$title				MW Title object
	 * @return		$pageCategories		SMWQConstraintGateway Objects
	 */
	
	protected static function getPageCategories($title){
		$pageCategories = array();
		foreach(smwfGetStore()->getSemanticData (SMWDIWikiPage::newFromTitle ( $title))->getPropertyValues (SMWDIProperty::	newFromUserLabel('_INST')) as $key => $value){
			$constraint = new SMWQConstraint();
			$constraint->setIsCategory(1);
			$constraint->setProperty(MWNamespace::getCanonicalName ($value->getNamespace()));
			$constraint->setValue(str_replace('_',' ',$value->getDbKey()));
			$pageCategories[] = $constraint;
		}
		return $pageCategories;
	}
	
	/**
	 * Retrieves SMWQ queries containing properties and/or categories
	 * present on the given page
	 * @param 		$title				MW Title object
	 * @return		array				SMWQQueryGateway Objects
	 */
	
	protected static function getQueries($title){
		$pageProperties = self::getPageProperties($title);
		$pageCategories = self::getPageCategories($title);
		return SMWQQueryMapper::findByConstraints(array_merge($pageProperties,$pageCategories));
	}

	/**
	 * Retrieves Queries that have the page as a result
	 * @param 		$title				MW Title object
	 * @return		array				SMWQQueryGateway Objects
	 */	
	
	public static function getQueryMatches($title){
		$queries = self::getQueries($title);
		$matches = array();
		foreach($queries as $key => $query){
			$SMWQueryParser = new SMWQueryParser();
			$SMWDescription = $SMWQueryParser->getQueryDescription($query->getQueryString());
			if($query->getIsConcept()){
				//concept
				$SMWQuery = new SMWQuery($SMWDescription, false, true);
			}
			else{
				//inline
				$SMWQuery = new SMWQuery($SMWDescription);
			}
			$SMWQueryResult = smwfGetStore()->getQueryResult($SMWQuery);
			foreach($SMWQueryResult->getResults() as $key => $result){
				if(($result instanceof SMWDIWikiPage) && ($title->getText() == $result->getTitle()->getText())){
					$matches[] = $query;
				}
				else{
					//not a match
				}	
			}
		}
		return $matches;	
	}
}
?>
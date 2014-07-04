<?php
/**
 * class that extends on the existing SNE-Core SMWQConstraintGateway class
 * to add more functionality
 * @author spell
 *
 */

class SNESMWQConstraintGateway extends SMWQConstraintGateway{
	protected $page;
	
	/** Overloads the standart method in SMWConstraintGateway
	 * because we have a new attribute in this one 
	 * @param		$stdObject			stdObject
	 * @return		void				
	**/
	
	public function instantiateFromDb($stdObject){
		$stdObject = (array) $stdObject;
		foreach($this as $key => $value){
			$fncSet = 'set'.$key;
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_query','smwq_constraint'));
			$table = preg_replace("/\`/", '', $smwq_constraint);
			//because we retrieve the page of this constraint
			//from the smwq_query table we need to have this
			//if-else construct
			if($key == 'page'){
				$table = preg_replace("/\`/", '', $smwq_query);
				$this->$fncSet($stdObject[$table.'_'.$key]);
			}
			else{
				$this->$fncSet($stdObject[$table.'_'.$key]);
			}
		}
	}
	
	/**
	 * instantiate a SNESMWQConstraintGateway from another object
	 * @param 		$SMWQConstraintGateway			SMWQConstraintGateway
	 */
	
	public function instantiateFromObject($SMWQConstraintGateway){
		foreach($SMWQConstraintGateway as $key => $value){
			$fncSet = 'set'.$key;
			$fncGet = 'get'.$key;
			$this->$fncSet($SMWQConstraintGateway->$fncGet()); 
		}
	}
	
	/**
	 * retrieves HTML code for headings of a table based on the
	 * variables that are set in the object
	 */	
	
	public function iterateForHeadings(){
		$headings = array();
		foreach($this as $key => $value){
			if($key == 'page'){
				//don't display page
			}
			else{
				$headings[] = HTML::rawElement('th', array(), $key);
			}	
		}
		$headings = implode('', $headings);
		return $headings;
	}
	
	/**
	 * retrieves HTML code for rows of a table based on the
	 * variables that are set in the object
	 */
	
	public function iterateForRows(){
		$rows = array();
		foreach($this as $key => $value){
			if($key == 'qid'){
				$page = Title::makeTitle(NS_SPECIAL, 'SNESemanticQueryInfo');
				$link = HTML::rawElement('a', array('href' => $page->getFullURL().'/'.$value), $value);
				$rows[] = HTML::rawElement('td', array(), $link);
			}
			elseif($key == 'value' && $this->getIsCategory()){
				$category = Category::newFromName($value); 
				$link = HTML::rawElement('a', array('href' => $category->getTitle()->getFullURL()), $value);
				$rows[] = HTML::rawElement('td', array(), $link);
			}
			elseif($key == 'property' && $this->getIsNamespace()){
				//TODO add support for namespaces
				$rows[] = HTML::rawElement('td', array(), $value);
			}
			elseif($key == 'property' && $this->getIsSinglePage()){
				$page = Title::makeTitle(NS_MAIN, $value);
				$link = HTML::rawElement('a', array('href' => $page->getFullURL()), $value);
				$rows[] = HTML::rawElement('td', array(), $link);
			}
			elseif($key == 'property' && $this->getIsConcept()){
				//TODO add support for concepts
				$rows[] = HTML::rawElement('td', array(), $value);
			}
			elseif($key == 'property' && $this->getIsSubquery()){
				//property link to attribute:property wikipage
				$property = SMWDIProperty::newFromUserLabel($value); 
				$link = HTML::rawElement('a', array('href' => $property->getDiWikiPage()->getTitle()->getFullURL()), $value);
				$rows[] = HTML::rawElement('td', array(), $link);
			}
			elseif($key == 'value' && $this->getIsSubquery()){
				//subquery link to SemanticQueryInfo
				$page = Title::makeTitle(NS_SPECIAL, 'SNESemanticQueryInfo');
				$link = HTML::rawElement('a', array('href' => $page->getFullURL().'/'.$value), $value);
				$rows[] = HTML::rawElement('td', array(), $link);
			}
			elseif($key == 'property' && !$this->getIsCategory() && !$this->getIsSubquery() && !$this->getIsConcept() && !$this->getIsSinglePage() && !$this->getIsNamespace()){
				//plain property
				$property = SMWDIProperty::newFromUserLabel($value); 
				$link = HTML::rawElement('a', array('href' => $property->getDiWikiPage()->getTitle()->getFullURL()), $value);
				$rows[] = HTML::rawElement('td', array(), $link);
			}
			elseif($key == 'page'){
				//don't display $page
			}
			else{
				$rows[] = HTML::rawElement('td', array(), $value);
			}
		}
		$rows = implode('', $rows);
		return $rows;
	}
	
	public function setPage($page){
		$this->page = $page;
	}
	
	public function getPage(){
		return $this->page;
	}
}
?>
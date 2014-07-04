<?php
/**
 * class that is used to display information about constraints or printouts
 * @author spell
 *
 */

class SNEVariableDisplay extends SMWQPrintoutGateway{
	protected $queries = array();
	protected $pages = array();
	
	/**
	 * instantiate a SNEVariableDisplay from another object
	 * @param 		$object				SNESMWQConstraintGateway/SNESMWQPrintoutGateway
	 */
	
	public function instantiateFromObject($object){
		if($object->getIsCategory()){
			$constraint = Category::newFromName($object->getValue());
		}
		else{
			$constraint = SMWDIProperty::newFromUserLabel($object->getProperty());
		}
		$this->setVariable($constraint);
		$this->addPage($object->getQid());
		$this->addQuery($object->getPage());
	}
	
	/**
	 * instantiate a SNEVariableDisplay from the database
	 * @param		 $stdObject					stdObject
	 */
	
	public function instantiateFromDb($stdObject){
		$stdObject = (array) $stdObject;
		$this->setVariable(SMWDIProperty::newFromUserLabel($stdObject[$this->getVariableDbField()]));
		$this->addPage($stdObject[$this->getPageDbField()]);
		$this->addQuery($stdObject[$this->getQidDbField()]);
	}
	
	/**
	 * retrieves HTML code for headings of a table based on the
	 * variables that are set in the object
	 */
	
	public function iterateForHeadings(){
		$headings = array();
		if($this->getVariable() instanceof Category){
			$headings[] = HTML::rawElement('th', array(), SNEUtil::getMsg('VariableDisplayHeadlineCategory'));
		}
		elseif($this->getVariable() instanceof SMWDIProperty){
			$headings[] = HTML::rawElement('th', array(), SNEUtil::getMsg('VariableDisplayHeadlineProperty'));
		}
		$headings[] = HTML::rawElement('th', array(), SNEUtil::getMsg('VariableDisplayHeadlineValue'));
		$headings = implode('', $headings);
		return $headings;
	}
	
	/**
	 * retrieves HTML code for rows of a table based on the
	 * variables that are set in the object   
	 */
	
	public function iterateForRows(){
		$rows = array();
		if($this->getVariable() instanceof Category){
			$link = HTML::rawElement('a', array('href' => $this->getVariable()->getTitle()->getFullURL()), $this->getVariable()->getName());
		}
		elseif($this->getVariable() instanceof SMWDIProperty){
			$link = HTML::rawElement('a', array('href' => $this->getVariable()->getDiWikiPage()->getTitle()->getFullURL()), $this->getVariable()->getLabel());
		}
		$rows[] = HTML::rawElement('td', array(), $link);
		$rows[] = HTML::rawElement('td', array(), SNEUtil::getMsg('VariableDisplayValue', count($this->getQueries()), count($this->getPages())));
		$rows = implode('', $rows);
		return $rows;
	}
	
	public function addQuery($qid){
		if(in_array($qid,$this->getQueries())){
			//do nothing
		}
		else{
			$this->queries[] = $qid;
		}
	}
	
	public function setQueries($queries){
		$this->queries = $queries;
	}
	
	public function getQueries(){
		return $this->queries;
	}
	
	public function addPage($page){
		if(in_array($page,$this->getPages())){
			//do nothing
		}
		else{
			$this->pages[] = $page;
		}
	}
	
	public function setPages($pages){
		$this->pages = $pages;
	}
	
	public function getPages(){
		return $this->pages;
	}
	
	protected function getPageDbField(){
		return "smwq_query_page";
	}
}
?>
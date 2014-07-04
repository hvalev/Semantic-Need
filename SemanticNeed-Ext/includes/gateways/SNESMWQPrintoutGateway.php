<?php
/**
 * class that extends on the existing SNE-Core SMWQPrintoutGateway class
 * to add more functionality
 * @author spell
 *
 */

class SNESMWQPrintoutGateway extends SMWQPrintoutGateway{
	
	/**
	 * instantiate a SNESMWQPrintoutGateway from another object
	 * @param 		$SMWQPrintoutGateway			SMWQPrintoutGateway
	 */
	
	public function instantiateFromObject($SMWQPrintoutGateway){
		foreach($this as $key => $value){
			$fncSet = 'set'.$key;
			$fncGet = 'get'.$key;
			$this->$fncSet($SMWQPrintoutGateway->$fncGet()); 
		}
	}
	
	/**
	 * retrieves HTML code for headings of a table based on the
	 * variables that are set in the object
	 */
	
	public function iterateForHeadings(){
		$headings = array();
		foreach($this as $key => $value){
			$headings[] = HTML::rawElement('th', array(), $key);
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
				$rows[] = HTML::rawElement('td', array(), HTML::rawElement('a', array('href' => $page->getFullURL().'/'.$value), $value));
			}
			elseif($key == 'variable'){
				$property = SMWDIProperty::newFromUserLabel($value); 
				$rows[] = HTML::rawElement('td', array(), HTML::rawElement('a', array('href' => $property->getDiWikiPage()->getTitle()->getFullURL()), $value));
			}
			else{
				$rows[] = HTML::rawElement('td', array(), $value);
			}
		}
		$rows = implode('', $rows);
		return $rows;
	}
}
?>
<?php
/**Gateway Class for Semantic Need Select Database table that
 * enables a separation layer between the database and the
 * in-memory objects through this Gateway Class.
 * @author Hristo Valev
 */

class SMWQPrintout{// implements ISMWQStructure{
	//TODO http://semantic-mediawiki.org/wiki/Help:Displaying_information
	//class needs to be expanded to be able to host different types of printouts
	protected $qid;
	protected $variable;
	
	public function toArray(){
		$array = array();
		foreach($this as $key => $value){
			$array[$key] = $value;
		}
		return $array;
	}
	
	/** Creates an instance of SMWQQueryGateway from a php stdObject 
	 * @param		$stdObject			stdObject
	 * @return		void				
	 */
	
	public function getVarName($var){
		foreach($this as $key => $value){
			if($var == $value){
				return $key;
			}
		}
		return;
	}
	
	public function instantiateFromDb(stdClass $stdObject){
		print_r($stdObject);
		self::rrr();
		$stdObject = (array) $stdObject;
		foreach($this as $key => $value){
			$fncSet = 'set'.$key;
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_select'));
			$table = preg_replace("/\`/", '', $smwq_select);
			$this->$fncSet($stdObject[$table.'_'.$key]); 
		}
	}
	
	public function setQid($qid){
		$this->qid = $qid;
	}
	
	public function getQid(){
		return $this->qid;
	}
	
	public function setVariable($var){
		$this->variable = $var;
	}
	
	public function getVariable(){
		return $this->variable;
	}
}
?>
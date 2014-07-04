<?php
/**Gateway Class for Semantic Need Constraint Database table that
 * enables a separation layer between the database and the
 * in-memory objects through this Gateway Class.
 * @author Hristo Valev
 */

class SMWQConstraint{// implements ISMWQStructure{
	protected $qid;
	protected $order = null;
	protected $andor = null;
	protected $isCategory = 0;
	protected $isNamespace = 0;
	protected $isSinglePage = 0;
	protected $isConcept = 0;
	protected $isSubquery = 0;
	protected $property;
	protected $expression;
	protected $value;
	
	public function toArray(){
		$array = array();
		foreach($this as $key => $value){
			$array[$key] = $value;
		}
		return $array;
	}
	
	public function getVarName($var){
		foreach($this as $key => $value){
			if($var == $value){
				return $key;
			}
		}
		return;
	}
	
	
	public function getSetVariables(){
		$array = array();
		$db = SNECoreConfig::getDB();
		extract($db->tableNames('smwq_constraint'));
		$table = preg_replace("/\`/", '', $smwq_constraint);
		foreach($this as $key => $value){
			if(isset($value)){
				$array[$table.'_'.$key] = $value;
			}
		}
		return $array;
	}
	
	
	/** Creates an instance of SMWQConstraintGateway from a php stdObject 
	 * @param		$stdObject			stdObject
	 * @return		void				
	**/
	
	public function instantiateFromDb($stdObject){
		$stdObject = (array) $stdObject;
		foreach($this as $key => $value){
			$fncSet = 'set'.$key;
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_constraint'));
			$table = preg_replace("/\`/", '', $smwq_constraint);
			$this->$fncSet($stdObject[$table.'_'.$key]); 
		}
	}
	
	
	public function setQid($qid){
		$this->qid = $qid;
	}
	
	public function getQid(){
		return $this->qid;
	}
	
	public function setOrder($order){
		$this->order = $order;
	}

	protected function getOrder(){
		return $this->order;
	}
	
	public function setAndor($andor){
		$this->andor = $andor;
	}
	
	protected function getAndor(){
		return $this->andor;
	}
	
	public function setIsCategory($bool){
		$this->isCategory = $bool;
	}
	
	public function getIsCategory(){
		return $this->isCategory;
	}
	
	public function setIsNamespace($bool){
		$this->isNamespace = $bool;
	}
	
	public function getIsNamespace(){
		return $this->isNamespace;
	}
	
	public function setIsSinglePage($bool){
		$this->isSinglePage = $bool;
	}
	
	public function getIsSinglePage(){
		return $this->isSinglePage;
	}
	
	public function setIsConcept($bool){
		$this->isConcept = $bool;
	}
	
	public function getIsConcept(){
		return $this->isConcept;
	}
	
	public function setIsSubquery($bool){
		$this->isSubquery = $bool;
	}
	
	public function getIsSubquery(){
		return $this->isSubquery;
	}
	
	public function setProperty($property){
		$this->property = $property;
	}
	
	public function getProperty(){
		return $this->property;
	}
	
	public function setExpression($expr){
		$this->expression = $expr;
	}
	
	public function getExpression(){
		return $this->expression;
	}
	
	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue(){
		return $this->value;
	}
}
?>
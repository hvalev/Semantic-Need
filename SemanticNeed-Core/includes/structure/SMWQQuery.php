<?php
/**Gateway Class for Semantic Need Query Database table that
 * enables a separation layer between the database and the
 * in-memory objects through this Gateway Class.
 * @author Hristo Valev
 */

class SMWQQuery{// implements ISMWQStructure{
	protected $qid;
	protected $type = null;
	protected $format = null;
	protected $limit = null;
	protected $results = null;
	protected $page = null;
	protected $queryString = null;
	protected $link = null;
	protected $alias = null;
	protected $isConcept = 0;
	protected $isSubquery = 0;
	protected $createdOn = null;
	protected $removedOn = null;
	protected $userId = null;
	protected $active = null;
	
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

	/** Creates an instance of SMWQQueryGateway from a php stdObject 
	 * @param		$stdObject			stdObject
	 * @return		void				
	 */
	
	public function instantiateFromDb($stdObject){
		$stdObject = (array) $stdObject;
		foreach($this as $key => $value){
			$fncSet = 'set'.$key;
			$db = SNECoreConfig::getDB();
			extract($db->tableNames('smwq_query'));
			$table = preg_replace("/\`/", '', $smwq_query);
			$this->$fncSet($stdObject[$table.'_'.$key]);
		}
	}
	

	
	public function setQid($qid){
		$this->qid = $qid;
	}
	
	public function getQid(){
		return $this->qid;
	}
	
	public function setType($type){
		$this->type = $type;
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function setFormat($format){
		$this->format = $format;
	}
	
	public function getFormat(){
		return $this->format;
	}
	
	public function setLimit($limit){
		$this->limit = $limit;
	}
	
	public function getLimit(){
		return $this->limit;
	}
	
	public function setResults($results){
		$this->results = $results;
	}
	
	public function getResults(){
		return $this->results;
	}
	
	public function setPage($page){
		$this->page = $page;
	}
	
	public function getPage(){
		return $this->page;
	}
	
	public function setQueryString($string){
		$this->queryString = $string;
	}
	
	public function getQueryString(){
		return $this->queryString;
	}
	
	public function setLink($link){
		$this->link = $link;
	}
	
	public function getLink(){
		return $this->link;
	}
	
	public function setAlias($alias){
		$this->alias = $alias;
	}
	
	public function getAlias(){
		return $this->alias;
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
	
	public function setCreatedOn($bool){
		$this->createdOn = $bool;
	}
	
	public function getCreatedOn(){
		return $this->createdOn;
	}
	
	public function setRemovedOn($bool){
		$this->removedOn = $bool;
	}
	
	public function getRemovedOn(){
		return $this->removedOn;
	}
	
	public function setUserId($id){
		$this->userId = $id;
	}
	
	public function getUserId(){
		return $this->userId;
	}
	
	public function setActive($active){
		$this->active = $active;
	}
	
	public function getActive(){
		return $this->active;
	}
}
?>
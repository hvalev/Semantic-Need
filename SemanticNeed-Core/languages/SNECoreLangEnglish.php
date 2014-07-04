<?php
class SNECoreLangEnglish implements SNECoreLang {
	
	public function getIsoCode() {
		return 'en';
	}

	public function getSpecialPages() {	
		return array(						// url-name									// description (shown on Special:SpecialPages)
			'Admin'							=> array('SNEAdmin',						'SemanticNeed Administrator Options'),
		);
	}
	
	public function getMWMessages(){
		return array(
			'specialpages-group-sneCore'			=> 'Semantic Need Core',
		);
	}

	public function getMessages() {
		return array(
			//Main
			'Title'								=>	'Semantic Need Core',
		
			//SNEAdmin
			'AdminTitle'						=>	'SemanticNeed Configuration Page',
			'AdminWelcome'						=> 	'Here you can set up the SNE extension to work with your wiki! :)',
			'AdminDBRunning'					=> 	'Tables Initialized',
			'AdminDBNotRunning'					=> 	'Tables Not Initialized',
			'AdminDBDrop'						=> 	'Drop SemanticNeed Database',
			'AdminDBDropMsg'					=> 	'$1 {{plural:$1|table|tables}} have been removed from the database.',
			'AdminDBInit'						=> 	'Initialize SemanticNeed Database',
			'AdminDBInitMsg'					=> 	'Created $1 {{plural:$1|table|tables}} in the database.',
			'AdminReindex'						=> 	'Reindex All Wikipages',
			'AdminReindexMsg'					=> 	'Refreshing all WikiPages. Please be patient.<br />',
			'AdminFindWikiPagesTime'			=> 	'start time: $1<br />',
			'AdminFindWikiPagesExtractPages'	=> 	'Querying pages from the database...<br />',
			'AdminFindWikiPagesParsingPages'	=> 	'Parsing $1 {{plural:$1|page|pages}} for ASK queries<br />',
			'AdminPurgeWikiPagesComplete'		=> 	'$1 {{plural:$1|wikipage|wikipages}} {{plural:$1|has|have}} been indexed<br />',
			'AdminPurgeWikiConceptsComplete'	=> 	'$1 {{plural:$1|concept|concepts}} {{plural:$1|has|have}} been indexed',
			
			//General
			'GeneralReturnTo'					=> 	'return to',	
		
			//Errors
			'ErrorAdminDBRights'				=> 	'Database error - check your access rights: $1'
		);
	}
}
?>
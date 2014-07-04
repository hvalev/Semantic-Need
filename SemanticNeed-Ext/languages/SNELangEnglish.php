<?php
class SNELangEnglish implements SNELang {
	
	public function getIsoCode() {
		return 'en';
	}

	public function getSpecialPages() {	
		return array(						// url-name									// description (shown on Special:SpecialPages)
			'MockMissingAnnotations'		=> array('SNEMockMissingAnnotations',		'Mock Missing Annotations'),
			'MockSemanticMatches'			=> array('SNEMockSemanticMatches',			'Mock Semantic Matches'),
			'Admin'							=> array('SNEAdmin',						'SemanticNeed Administrator Options'),
			'AskLog'						=> array('SNEAskLog',						'Log with Information about Ask Queries'),
			'SemanticMatches'				=> array('SNESemanticMatches',				'Semantic Matches'),
			'SemanticQueryInfo'				=> array('SNESemanticQueryInfo',			'Information about Semantic Queries'),
			'SpecialPages'					=> array('SNESpecialPages',					'This text should be hidden'),
		);
	}
	
	public function getMWMessages(){
		return array(
			'specialpages-group-sne'			=> 'Semantic Need',
		);
	}

	public function getMessages() {
		return array(
			//Main
			'Title'								=>	'Semantic Need',
		
			//SNEAskLog
			'AskLogTitle'						=> 	'List of all queries in the Wiki',
			'AskLogQueriesWithResultsHeader'	=> 	'Semantic queries',
			'AskLogSearchBoxMainLabel'			=> 	' Search Queries by ',
			'AskLogSearchBoxSubLabelGeneral'	=> 	' General ',
			'AskLogSearchBoxNumRes'				=> 	' Number of results ',
			'AskLogSearchBoxOnPage'				=> 	' on page ',
			'AskLogSearchBoxSubLabelProperties'	=> 	' Properties ',
			'AskLogSearchBoxSubLabelCategories'	=> 	' Categories ',
			'AskLogSearchBoxButton'				=> 	'Search',
			'AskLogBrowsingBegin'				=>	'Begin',
			'AskLogBrowsingEnd'					=>	'End',
			'AskLogBrowsingShow'				=>	'Show',
			'AskLogBrowsingPrevious'			=>	'previous',
			'AskLogBrowsingNext'				=>	'next',
			'AskLogQueriesWithResults'			=>	'There are currently no semantic queries in the wiki',
			'AskLogQueriesWithNoResultsHeader'	=>  'Semantic queries with no results',
			'AskLogQueriesWithNoResults'		=> 	'Currently there are no queries without results',
			
			//SNESemanticMatches
			'SemanticMatchesTitleSimple'		=> 	'Semantic Matches',
			'SemanticMatchesWelcome'			=> 	'Enter a wiki pagename:',
			'SemanticMatchesTitleAdvanced'		=> 	'Semantic Matches affecting $1',
			'SemanticMatchesButton'				=> 	'Look up page',
			'SemanticMatchesQueries'			=> 	'Part of the result set',
			'SemanticMatchesMissingQueries'		=> 	'No queries to display',
			'SemanticMatchesNearMatches'		=> 	'Almost part of the result set',
			'SemanticMatchesMissingProperties'	=>	'Missing Properties',
			'SemanticMatchesNoMissingProperties'=>	'No missing properties for this page',
			'SemanticMatchesMissingCategories'	=>	'Missing Categories',
			'SemanticMatchesNoMissingCategories'=>	'No missing categories for this page',
			'SemanticMatchesWantedProperties'	=> 	'Wanted Properties',
			'SemanticMatchesNoWantedProperties'	=>	'No wanted properties for this page',
			'SemanticMatchesWantedCategories'	=> 	'Wanted Categories',
			'SemanticMatchesNoWantedCategories'	=>	'No wanted categories for this page',
			
			//SNESemanticQueryInfo
			'SemanticQueryInfoTitle'			=> 	'Information about Semantic Queries',
			'SemanticQueryInfoWelcome'			=> 	'Enter a query hash value or query alias:',
			'SemanticQueryInfoButton'			=> 	'Look up query',
			'SemanticQueryInfoQueryFail'		=> 	'Query does not exist in the Database',
			'SemanticQueryInfoBasicQueryHeader'	=>	'Basic Query Information',
			'SemanticQueryInfoQueryInternalFail'=>	'Query seems to be faulty',
			'SemanticQueryInfoAdvancedQueryHeader'=>'Advanced Query Information',
			'SemanticQueryInfoAdvancedConstraints'=>'Constraints',
			'SemanticQueryInfoAdvancedPrintouts'=>	'Printouts',
			'SemanticQueryInfoAdvancedNoPrintouts'=>'there are no printouts for that query',
			
			//SNEVariableDisplay
			'VariableDisplayHeadlineProperty'	=> 	'Missing value for property',
			'VariableDisplayHeadlineCategory'	=> 	'Missing value for category',
			'VariableDisplayHeadlineValue'		=> 	'Value requested by',
			'VariableDisplayValue'				=> 	'$1 {{plural:$1|Query|Queries}} on $2 {{plural:$2|Page|Pages}}',	
		
			//Toolbox
			'ToolboxLink'						=> 	'Look up Semantic Matches for $1',
			
			//SNEBox
			'SNEBoxHeader'						=> 	'Missing Properties/Categories on $1',
			'SNEBoxLink'						=> 	'Requested by $1 {{plural:$1|Query|Queries}} on $2 {{plural:$2|Page|Pages}}',
			'SNEBoxButton'						=> 	'Save',
			
			//General
			'GeneralReturnTo'					=> 	'return to',
			'GeneralGoTo'						=> 	'go to',
			
			//Errors
			'ErrorAdminDBRights'				=> 	'Database error - check your access rights: $1',
			'DBFail'							=> 	'Database Tables do not exist. Please initialize them and reindex Wikipages.',
			'RedirectToAdmin'					=> 	'You can proceed with it on $1 page',
		);
	}
}
?>
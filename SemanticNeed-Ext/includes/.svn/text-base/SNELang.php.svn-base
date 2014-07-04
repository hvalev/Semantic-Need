<?php

/**
 * Interface for user interface text translations
 * See the \languages directory for implementations
 */
interface SNELang {
	/**
	 * @return	string, the iso-code for the language provided
	 */
	public function getIsoCode();
	
	/**
	 * @return	array, the translated special pages
	 */
	public function getSpecialPages();
	
	public function getMWMessages();
	
	/**
	 * Keys may not begin with 'Special'
	 * @return	array, the translated messages for that language, keys are defined in SNE_LangEnglish
	 */
	public function getMessages();
}
?>
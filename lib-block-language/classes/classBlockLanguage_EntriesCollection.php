<?php

class BlockLanguage_EntriesCollection implements IteratorAggregate {
	
	/**
	 * @var BlockLanguage_Entry[]
	 */
	private $entries = array();
	
	//************************************************************************************
	/**
	 * @return BlockLanguage_Entry[]
	 */
	public function getEntries() {
		return $this->entries;
	}
	
	//************************************************************************************
	/**
	 * @param string $name
	 * @return BlockLanguage_Entry[]
	 */
	public function getEntriesByName($name) {
		$arr = array();
		foreach($this->entries as $oEntry) {
			if ($oEntry->getName() == $name) {
				$arr[] = $oEntry;
			}
		}
		return $arr;
	}
	
	//************************************************************************************
	public function getIterator() {
		return new ArrayIterator($this->entries);
	}
	
	//************************************************************************************
	/**
	 * @param string $content
	 * @return BlockLanguage_EntriesCollection
	 */
	public static function Parse($content) {
		$oTokenizer = BlockLanguageParser_Tokenizer::Tokenize($content);
		if ($oTokenizer) {
			return self::ParseCollection($oTokenizer);
		} else {
			return null;
		}
	}
	
	//************************************************************************************
	/**
	 * @param BlockLanguageParser_Tokenizer $oTokenizer
	 * @return BlockLanguage_EntriesCollection
	 */
	private static function ParseCollection($oTokenizer) {
		if (!($oTokenizer instanceof BlockLanguageParser_Tokenizer)) throw new InvalidArgumentException('oTokenizer is not BlockLanguageParser_Tokenizer');
		
		$obj = new BlockLanguage_EntriesCollection();
		
		while(true) {
			$oEntry = self::ParseEntry($oTokenizer);
			if ($oEntry) {
				$obj->entries[] = $oEntry;
				continue;
			} else {
				break;
			}
		}
		
		return $obj;		
	}
	
	//************************************************************************************
	/**
	 * @param BlockLanguageParser_Tokenizer $oTokens
	 * @return BlockLanguage_Entry
	 */
	private static function ParseEntry($oTokenizer) {
		if (!($oTokenizer instanceof BlockLanguageParser_Tokenizer)) throw new InvalidArgumentException('oTokenizer is not BlockLanguageParser_Tokenizer');
		
		if (!$oTokenizer->isNext(BlockLanguageParser_Tokenizer::TOKEN_ID)) return null;
		$oTokenizer->markSet();
		
		$oEntry = new BlockLanguage_Entry($oTokenizer->popNextValue());

		while(true) {

			if ($oTokenizer->isNext(BlockLanguageParser_Tokenizer::TOKEN_SEMICOLON)) {
				$oTokenizer->popNextValue();
				$oTokenizer->markCancel();
				break;
			}
			elseif ($oTokenizer->isNext(BlockLanguageParser_Tokenizer::TOKEN_NUMBER)) {
				$oEntry->addArgumentNumber($oTokenizer->popNextValue());
				continue;
			}
			elseif ($oTokenizer->isNext(BlockLanguageParser_Tokenizer::TOKEN_STRING)) {
				$oEntry->addArgumentString($oTokenizer->popNextValue());
				continue;
			}
			elseif ($oTokenizer->isNext(BlockLanguageParser_Tokenizer::TOKEN_ID)) {
				$oEntry->addArgumentString($oTokenizer->popNextValue());
				continue;
			}
			elseif ($oTokenizer->isNext(BlockLanguageParser_Tokenizer::TOKEN_BLOCK_OPEN)) {
				$oTokenizer->popNextValue();
				
				$oCollection = self::ParseCollection($oTokenizer);
				
				if ($oTokenizer->isNext(BlockLanguageParser_Tokenizer::TOKEN_BLOCK_CLOSE)) {
					$oTokenizer->popNextValue();
					$oEntry->addArgumentBlock($oCollection);
					continue;
				} else {
					$oTokenizer->markBack();
					throw new BlockLanguageParser_Exception('Expecting }');
				}
			}
			else {
				throw new BlockLanguageParser_Exception(sprintf('Unexpected token %d', $oTokenizer->peekNextTokenType()));
			}
		}

		return $oEntry;
	}
	
}

?>
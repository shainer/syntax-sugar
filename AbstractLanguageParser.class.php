<?php
/*
 * Syntax sugar
 * AbstractLanguageParse.class.php: here the generic parser is located; it receives
 * the information for every language and performs general highlight operations:
 * string, comment and keyword highlight
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
include_once('AbstractDFA.class.php');
include_once('PhpRichString.class.php');
include_once('PhpRichFormat.class.php');

/*
 * Generic formats.
 */
define("NORMAL_F", 0);
define("GENERIC_COMMENT_F", 1);
define("STRING_F", 2);
define("KEYWORD_F", 3);
define("DIGITS_F", 4);

/*
 * Language-specific formats.
 */
define("PREPROCESSOR_F", 5);
define("ATTRIBUTE_F", 5);
define("TAG_F", 3);

/*
 * DFA generic states.
 */
define("NORMAL_S", 0);
define("GENERIC_COMMENT_STARTING_S", 1);
define("MULTILINE_COMMENT_BODY_S", 2);
define("MULTILINE_COMMENT_ENDING_S", 3);
define("SIMPLE_COMMENT_BODY_S", 4);
define("SIMPLE_COMMENT_ENDING_S", 5);
define("STRING_BODY_S", 6);
define("KEYWORD_S", 7);
define("DIGITS_S", 8);

/*
 * DFA language-specific states.
 */
define("PREPROCESSOR_S", 9);
define("HTML_TAG_STARTING_S", 9);
define("HTML_TAG_STARTING_1_S", 10);
define("HTML_TAG_BODY_S", 11);
define("HTML_COMMENT_STARTING_1_S", 12);
define("HTML_COMMENT_STARTING_2_S", 13);
define("HTML_COMMENT_BODY_S", 14);
define("HTML_COMMENT_ENDING_1_S", 15);
define("HTML_COMMENT_ENDING_2_S", 16);
define("HTML_ATTRIBUTE_STARTING_S", 17);
define("HTML_STRING_S", 18);

/*
 * Comment types.
 */
define("COMMENT_SIMPLE_ONLY", 0);
define("COMMENT_MULTILINE_ONLY", 1);
define("COMMENT_BOTH", 2);

abstract class AbstractLanguageParser extends AbstractDFA
{
	protected $States = array
	(
		NORMAL_S,
		GENERIC_COMMENT_STARTING_S,
		MULTILINE_COMMENT_BODY_S,
		MULTILINE_COMMENT_ENDING_S,
		SIMPLE_COMMENT_BODY_S,
		SIMPLE_COMMENT_ENDING_S,
		STRING_BODY_S,
		KEYWORD_S,
		DIGITS_S
	);
	
	protected $Colors = array
	(
		0x000000,
		0x999999,
		0xFFCC00,
		0x009900,
		0x993333
	);		

	protected $Keywords = null;
	protected $CommentType = null;
	protected $GenericCommentStart = null;
	protected $MultilineCommentEnd = null;
	protected $SimpleCommentStart = null;
	protected $StringDelimiter = null;

	final public function GetKeywords()
	{
		return $this->Keywords;
	}

	final public function GetCommentType()
	{
		if ($this->GetSimpleCommentStart() == null)
		{
			return COMMENT_MULTILINE_ONLY;
		}

		if ($this->GetCommentStart() == null)
		{
			return COMMENT_SIMPLE_ONLY;
		}

		return COMMENT_BOTH;
	}

	final public function GetCommentStart()
	{
		return $this->GenericCommentStart;
	}

	final public function GetCommentEnd()
	{
		return $this->MultilineCommentEnd;
	}

	final public function GetSimpleCommentStart()
	{
		return $this->SimpleCommentStart;
	}

	final public function GetStringDelimiters()
	{
		return $this->StringDelimiters;
	}

	final private function IsStringDelimiter($c, &$delimiter)
	{
		foreach ($this->GetStringDelimiters() as $d)
		{
			if ($c == $d)
			{
				$delimiter = $d;
				return true;
			}
		}

		return false;
	}
	
	/*
	 * Format creating function
	 */
	final protected function CreateFormat($key)
	{
		$format = new PhpRichFormat();
			
		try
		{
			$format->SetColor($this->Colors[$key]);
		}
		catch (Exception $e)
		{
			throw new Exception("Unknown format type");
		}
		
		return $format;
	}

	/*
	 * Token control
	 * Returns an array of matching keywords (e.g. those starting with $substring)
	 */
	final protected function FilterKeywordsArray($substring, $haystack)
	{
		$result = array();

		foreach ($haystack as $k)
		{
			if (self::StartsWith($substring, $k))
			{
				$result[] = $k;
			}
		}
		return $result;
	}
	
	final protected function FilterKeywords($substring)
	{
		return self::FilterKeywordsArray($substring, $this->GetKeywords());
	}

	final static private function StartsWith($needle, $haystack)
	{
		$n = strlen($needle);
		return (substr($haystack, 0, $n) == $needle);
	}

    /*
     * Given the input character (and its index),
     * properly changes state and perform highlight actions
     */
	public function StateTransition($c, $i)
	{
		static $partial;
		static $delimiter;
		static $escaping;
		static $partialKeys;

		switch ($this->GetState())
		{
			case NORMAL_S:
				$comment = $this->GetCommentStart();
				$partialKeys = self::FilterKeywords($c);

				if ($c == $this->GetSimpleCommentStart())
				{
					$this->Data->Append($c);
					$this->Data->SetFormatAt($i, $this->CreateFormat(GENERIC_COMMENT_F));
					$this->SetState(SIMPLE_COMMENT_BODY_S);
				}
				else if ($c == $comment[0])
				{
					$partial = $c;
					$this->SetState(GENERIC_COMMENT_STARTING_S);
				}
				else if ($this->IsStringDelimiter($c, $delimiter))
				{
					$this->Data->Append($c);
					$this->Data->SetFormatAt($i, $this->CreateFormat(STRING_F));
					$this->SetState(STRING_BODY_S);
				}
				else if (ctype_digit($c))
				{
					$this->Data->Append($c);
					$this->Data->SetFormatAt($i, $this->CreateFormat(DIGITS_F));
					$this->SetState(DIGITS_S);
				}
				else if (count($partialKeys) != 0) /* there's something that matches */
				{
					$partial = $c;
					$this->SetState(KEYWORD_S);
				}
				else
				{
					$this->Data->Append($c);
				}
				break;

			case GENERIC_COMMENT_STARTING_S:
				$partial .= $c;
				$multi = $this->GetCommentStart();
				$simple = $this->GetSimpleCommentStart();

				if ($c == $multi[1])
				{
					$this->SetState(MULTILINE_COMMENT_BODY_S);
					$this->Data->SetFormatAt($i-1, $this->CreateFormat(GENERIC_COMMENT_F));
				}
				else if ($c == $simple[1])
				{
					$this->SetState(SIMPLE_COMMENT_BODY_S);
					$this->Data->SetFormatAt($i-1, $this->CreateFormat(GENERIC_COMMENT_F));
				}
				else
				{
					$this->SetState(NORMAL_S);
				}

				$this->Data->Append($partial);
				$partial = null;
				break;

			/*
			 * Multine comments, like the one you are reading in this moment,
			 * need another delimiter to reach their end
			 */
			case MULTILINE_COMMENT_BODY_S:
				$this->Data->Append($c);
				$comment = $this->GetCommentEnd();

				if ($c == $comment[0])
				{
					$this->SetState(MULTILINE_COMMENT_ENDING_S);
				}
				break;

			case MULTILINE_COMMENT_ENDING_S:
				$this->Data->Append($c);
				$comment = $this->GetCommentEnd();

				if ($c == $comment[1])
				{
					$this->SetState(NORMAL_S);
					$this->Data->SetFormatAt($i+1, $this->CreateFormat(NORMAL_F));
				}
				else
				{
					$this->SetState(MULTILINE_COMMENT_BODY_S);
				}
				break;

			/*
			 * A simple comment ends with the end of the current line
			 */
			case SIMPLE_COMMENT_BODY_S:
				if ($c == "\n")
				{
					$this->SetState(NORMAL_S);
					$this->Data->SetFormatAt($i+1, $this->CreateFormat(NORMAL_F));
				}

				$this->Data->Append($c);
				break;

			case STRING_BODY_S:
				$this->Data->Append($c);

				if (($c == $delimiter) && (!$escaping))
				{
					$delimiter = null;
					$this->Data->SetFormatAt($i+1, $this->CreateFormat(NORMAL_F));
					$this->SetState(NORMAL_S);
				}

				/*
				 * Pays attention to the escape character \
				 */
				$escaping = ($c == "\\") ? ($escaping ^ true) : false;
				break;
				
			case DIGITS_S:
				if (ctype_digit($c) == false)
				{
					$this->Data->SetFormatAt($i, $this->CreateFormat(NORMAL_F));
					$this->SetState(NORMAL_S);
				}
				$this->Data->Append($c);
				break;

			case KEYWORD_S:
				if (ctype_alnum($c) || ($c == "_"))
				{
					$partial .= $c;
				}
				else
				{
					if (in_array($partial, $partialKeys))
					{
						$this->Data->SetFormatAt($i - strlen($partial), $this->CreateFormat(KEYWORD_F));
						$this->Data->Append($partial);
						$this->Data->SetFormatAt($i, $this->CreateFormat(NORMAL_F));
					}
					else
					{
						$this->Data->Append($partial);
					}

					$this->Data->Append($c);
					$partial = null; /* starts over looking for other tokens */
					$this->SetState(NORMAL_S);
				}
				break;
		}
	}

	public function ProcessInput($input)
	{
		$this->SetState(NORMAL_S);
		$length = strlen($input);

		$this->Data = new PhpRichString();
		$this->Data->SetFormatAt(0, $this->CreateFormat(NORMAL_F));

		for ($i = 0; $i < $length; $i++)
		{
			$this->StateTransition($input[$i], $i);
		}

		return $this->Data->GetHTML();
	}
}
?>

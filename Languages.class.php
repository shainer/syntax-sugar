<?php
/*
 * Syntax Sugar
 * Languages.class.php: defines the properties for each supported language.
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

include_once('AbstractLanguageParser.class.php');

class Python extends AbstractLanguageParser
{
	protected $Keywords = array('True', 'False', 'and', 'as', 'assert', 'break', 'class', 'continue', 'def', 'del', 'elif', 'else', 'except', 'exec', 'finally', 'for', 'from', 'global', 'if', 'import', 'in', 'is', 'lambda', 'not', 'or', 'pass', 'print', 'raise', 'return', 'try', 'while', 'with', 'yield');
	protected $SimpleCommentStart = '#';
	protected $StringDelimiters = array("'", '"');
}

class C extends AbstractLanguageParser
{
	protected $Keywords = array('auto', 'break', 'case', 'char', 'const', 'continue', 'default', 'do', 'double', 'else', 'enum', 'extern', 'float', 'for', 'goto', 'if', 'int', 'long', 'register', 'return', 'short', 'signed', 'sizeof', 'static', 'struct', 'switch', 'typedef', 'union', 'unsigned', 'void', 'volatile', 'while', 'wchar_t', 'int8_t', 'int16_t', 'int32_t', 'int64_t', 'uint8_t', 'uint16_t', 'uint32_t', 'uint64_t');
	protected $GenericCommentStart = '/*';
	protected $MultilineCommentEnd = '*/';
	protected $SimpleCommentStart = '//';
	protected $StringDelimiters = array("'", '"');

	function __construct()
	{
		array_push($this->States, PREPROCESSOR_S); /* add a new state */
		array_push($this->Colors, 0x66CCCC); /* add a new format */
		parent::__construct();
	}

	/*
	 * Defines language-specific features.
	 */
	function StateTransition($c, $i)
	{
		switch ($this->GetState())
		{
			case NORMAL_S:
				if ($c == '#')
				{
					$this->Data->Append($c);
					$this->Data->SetFormatAt($i, parent::CreateFormat(PREPROCESSOR_F));
					$this->SetState(PREPROCESSOR_S);
				}
				else
				{
					parent::StateTransition($c, $i);
				}
				break;

			case PREPROCESSOR_S:
				if ($c == "\n")
				{
					$this->Data->SetFormatAt($i+1, parent::CreateFormat(NORMAL_F));
          $this->SetState(NORMAL_S);
				}
				else if ($c == '/')
				{
					$partial = $c;
					$this->SetState(GENERIC_COMMENT_STARTING_S);
				}
				$this->Data->Append($c);
				break;

			/*
			 * If there is nothing interesting, calls the original transition procedure
			 */
			default:
				parent::StateTransition($c, $i);
		}
	}
}

class Cpp extends C
{
	function __construct()
	{
		$CppKeywords = array('and', 'and_eq', 'asm', 'auto', 'bitand', 'bitor', 'bool', 'class', 'compl', 'const_cast', 'delete', 'dynamic_cast', 'explicit', 'export', 'false', 'friend', 'mutable', 'namespace', 'new', 'not', 'not_eq', 'operator', 'or', 'or_eq', 'private', 'protected', 'public', 'reinterpret_cast', 'static_cast', 'template', 'this', 'throw', 'true', 'try', 'typedef', 'typeid', 'typename', 'using', 'virtual', 'xor', 'xor_eq');

		$this->Keywords = array_merge($this->Keywords, $CppKeywords);
		parent::__construct();
	}
}

class Java extends AbstractLanguageParser
{
	protected $Keywords = array('abstract', 'assert', 'boolean', 'break', 'byte', 'case', 'catch', 'char', 'class', 'const', 'continue', 'default', 'do', 'double', 'else', 'enum', 'extends', 'final', 'finally', 'float', 'for', 'goto', 'private', 'implements', 'import', 'instanceof', 'int', 'interface', 'long', 'native', 'new', 'package', 'this', 'protected', 'public', 'return', 'short', 'static', 'strictfp', 'super', 'switch', 'synchronized', 'throw', 'transient', 'try', 'void', 'volatile', 'while');
	protected $GenericCommentStart = '/*';
	protected $MultilineCommentEnd = '*/';
	protected $SimpleCommentStart = '//';
	protected $StringDelimiters = array("'", '"');
}

class HTML extends AbstractLanguageParser
{
	protected $Keywords = array('a', 'abbr', 'acronym', 'address', 'area', 'b', 'base', 'bdo', 'big', 'blockquote', 'body', 'br', 'button', 'caption', 'cite', 'code', 'colgroup', 'dd', 'del', 'dfn', 'div', 'dl', 'dt', 'em', 'fieldset', 'form', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html', 'i', 'iframe', 'img', 'input', 'ins', 'kbd', 'label', 'legend', 'li', 'link', 'map', 'noframes', 'noscript', 'object', 'ol', 'optgroup', 'option', 'p', 'pre', 'q', 'samp', 'script', 'select', 'small', 'span', 'strong', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'title', 'tr', 'tt', 'ul', 'var');
	protected $Attributes = array('abbr', 'accept', 'accesskey', 'action', 'alink', 'align', 'alt', 'archive', 'axis', 'background', 'bgcolor', 'border', 'cellpadding', 'cellspacing', 'char', 'charoff', 'charset', 'checked', 'cite', 'class', 'classid', 'clear', 'code', 'codebase', 'codetype', 'color', 'cols', 'colspan', 'content', 'coords', 'data', 'declare', 'datetime', 'defer', 'dir', 'disabled', 'enctype', 'face', 'for', 'frame', 'frameborder', 'headers', 'height', 'href', 'hreflang', 'hspace', 'http-equiv', 'id', 'ismap', 'label', 'lang', 'language', 'link', 'longdesc', 'marginheight', 'marginwidth', 'maxlength', 'media', 'method', 'multiple', 'name', 'nohref', 'noresize', 'noshade', 'nowrap', 'object', 'onblur', 'onchange', 'onclick', 'ondblclick', 'onfocus', 'onkeydown', 'onkeypress', 'onkeyup', 'onload', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onreset', 'onselect', 'onsubmit', 'onunload', 'profile', 'prompt', 'readonly', 'rel', 'rev', 'rows', 'rowspan', 'rules', 'scheme', 'scope', 'scrolling', 'selected', 'span', 'size', 'standby', 'src', 'start', 'style', 'summary', 'tabindex', 'target', 'text', 'type', 'usemap', 'valign', 'value', 'valuetype', 'version', 'vlink', 'vspace', 'width');
	protected $MultilineCommentStart = '<!--';
	protected $MultilineCommentEnd = '-->';
	protected $StringDelimiters = array("'", '"');
	
	function __construct()
	{
		array_push($this->States, HTML_TAG_STARTING_S);
		array_push($this->States, HTML_TAG_STARTING_1_S);
		array_push($this->States, HTML_TAG_BODY_S);
		array_push($this->States, HTML_COMMENT_STARTING_1_S);
		array_push($this->States, HTML_COMMENT_STARTING_2_S);
		array_push($this->States, HTML_COMMENT_BODY_S);
		array_push($this->States, HTML_COMMENT_ENDING_1_S);
		array_push($this->States, HTML_COMMENT_ENDING_2_S);
		array_push($this->States, HTML_ATTRIBUTE_STARTING_S);
		array_push($this->States, HTML_STRING_S);
		
		array_push($this->Colors, 0x660099);
		
		parent::__construct();
	}

	function StateTransition($c, $i)
	{
		static $partial;
		static $sTag;     /* an old index */
		static $failed;   /* boolean value if the last bunch of characters was a valid attribute or not */
		static $sString;

		switch ($this->GetState())
		{
			case NORMAL_S:
				$this->Data->Append($c);

				if ($c == '<')
				{
					$this->SetState(HTML_TAG_STARTING_S);
				}
				break;
			
			case HTML_TAG_STARTING_S:
				if ($c == '!')
				{
					$this->Data->Append($c);
					$this->SetState(HTML_COMMENT_STARTING_1_S);
				}
				else
				{
					if ($c != "/")
					{
						$partial = $c;
					}
					else
					{
						/*
						 * Otherwise this character will be missing at the end
						 */
						$this->Data->Append("/");
					}
					$sTag = $i; /* Saves the starting index for formatting */
					$this->SetState(HTML_TAG_STARTING_1_S);
				}
				break;
			
			case HTML_TAG_STARTING_1_S:
				$partialKeys = parent::FilterKeywords($partial);
				
				/*
				 * It isn't an accepted tag
				 */
				if (count($partialKeys) == 0)
				{
					$this->Data->Append($partial . $c);
					$partial = "";
					$this->SetState(NORMAL_S);
					break;
				}
				
				if ($c == ">" or $c == " ")
				{
					
					if (in_array($partial, $partialKeys))
					{
						$this->Data->SetFormatAt($sTag - 1, parent::CreateFormat(TAG_F));
						$this->Data->Append($partial . $c);
						$this->Data->SetFormatAt($i + 1, parent::CreateFormat(NORMAL_F));
						$partial = "";
						$this->SetState(NORMAL_S);
					}
					else
					{
						$this->Data->Append($partial . $c);
					}
					
					if ($c == " ")
					{
						$partial = "";
						$sTag = $i;
						$this->SetState(HTML_ATTRIBUTE_STARTING_S);
					}
				}
				else
				{
					$partial .= $c;
				}
				break;
			
			case HTML_COMMENT_STARTING_1_S:
				$this->Data->Append($c);
				if ($c == '-')
				{
					$this->SetState(HTML_COMMENT_STARTING_2_S);
				}
				else
				{
					$this->SetState(NORMAL_S);
				}
				break;
			
			case HTML_COMMENT_STARTING_2_S:
				$this->Data->Append($c);
				if ($c == '-')
				{
					$this->Data->SetFormatAt($i - 3, parent::CreateFormat(GENERIC_COMMENT_F));
					$this->SetState(HTML_COMMENT_BODY_S);
				}
				else
				{
					$this->SetState(NORMAL_S);
				}
				break;
				
			case HTML_COMMENT_BODY_S:
				if ($c == '-')
				{
					$this->SetState(HTML_COMMENT_ENDING_1_S);
				}

				$this->Data->Append($c);
				break;
				
			case HTML_COMMENT_ENDING_1_S:
				if ($c == '-')
				{
					$this->SetState(HTML_COMMENT_ENDING_2_S);
				}
				else
				{
					$this->SetState(HTML_COMMENT_BODY_S);
				}

				$this->Data->Append($c);
				break;
			
			case HTML_COMMENT_ENDING_2_S:
				$this->Data->Append($c);
				
				if ($c == '>')
				{
					$this->Data->SetFormatAt($i + 1, parent::CreateFormat(NORMAL_F));
					$this->SetState(NORMAL_S);
				}
				else
				{
					$this->SetState(HTML_COMMENT_BODY_S);
				}
				break;
			
			/*
			 * This is very similar to the tag recognition
			 * with the exception that we are redirected to a transition state later (HTML_TAG_BODY_S)
			 * because we have more than one attribute per tag
			 */
			case HTML_ATTRIBUTE_STARTING_S:
				$partialKeys = parent::FilterKeywordsArray($partial, $this->Attributes);
				
				if (count($partialKeys) == 0)
				{
					$this->Data->Append($partial . $c);
					$partial = "";
					$failed = true; /* useful later to know if we have to come back to this state when we meet a character */
					$this->SetState(HTML_TAG_BODY_S);
				}
				
				if ($c == '=' or $c == ' ' or $c == '/' or $c == '>')
				{
					if (in_array($partial, $partialKeys))
					{
						$this->Data->SetFormatAt($sTag + 1, parent::CreateFormat(ATTRIBUTE_F));
						$this->Data->Append($partial);
						$this->Data->SetFormatAt($sTag + strlen($partial) + 1, parent::CreateFormat(NORMAL_F));
						$partial = "";
					}
					
					if ($c == '=' or $c == ' ')
					{
						$failed = false;
						$this->SetState(HTML_TAG_BODY_S);
					}
					else if ($c == '>')
					{
						$this->Data->Append($c);
						$this->Data->SetFormatAt($i, parent::CreateFormat(TAG_F));
						$this->Data->SetFormatAt($i+1, parent::CreateFormat(NORMAL_F));
						$this->SetState(NORMAL_S);
						break;
					}
					$this->Data->Append($c);
				}
				else
				{
					$partial .= $c;
				}
				break;
			
			/*
			 * Transition state between attributes
			 */
			case HTML_TAG_BODY_S:
				if ($c == '>')
				{
					$this->Data->SetFormatAt($i, parent::CreateFormat(TAG_F)); /* highlights just the current char */
					$this->Data->Append($c);
					$this->Data->SetFormatAt($i+1, parent::CreateFormat(NORMAL_F));
					$this->SetState(NORMAL_S);
					break;
				}
				else if ($c == '"' or $c == "'")
				{
					$sString = $c; /* saves which character started the string! */
					$this->Data->SetFormatAt($i, parent::CreateFormat(STRING_F));
					$this->SetState(HTML_STRING_S);
				}
				else if ($c == ' ')
				{
					$failed = false; /* we don't care about the previous characters anymore */
				}		
				else if (ctype_alpha($c) and $failed == false) /* the character could be a new attribute */
				{
					$partial = $c;
					$sTag = $i-1;
					$this->SetState(HTML_ATTRIBUTE_STARTING_S);
					break;
				}
				$this->Data->Append($c);
				break;
			
			case HTML_STRING_S:
				if ($c == $sString)
				{
					$this->Data->SetFormatAt($i+1, parent::CreateFormat(NORMAL_F));
					$this->SetState(HTML_TAG_BODY_S);
				}
				$this->Data->Append($c);
				break;
					
			default:
				break;
		}
	}
				
}	

?>

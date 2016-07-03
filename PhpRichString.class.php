<?php
/*
 * Syntax Sugar
 * PhpRichString.class.php: Rich PHP string type. Able to store format information for a text buffer
 *                          and output its HTML representation natively.
 * It just outlines the "shape" of a generic DFA, consisting of a set of states,
 * a transition function and a local data area.
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

class PhpRichString implements ArrayAccess
{
	private $buffer;
	private $format;

	public function __construct($source = "")
	{
		$this->buffer = $source;
		$this->format = array();
	}

	public function __toString()
	{
		return $this->buffer;
	}

	/*
	 * ArrayAccess compatibility
	 */
	public function offsetExists($i)
	{
		if (!is_int($i))
		{
			throw new Exception("Invalid key value");
		}

		return isset($this->buffer[$i]);
	}

	public function offsetGet($i)
	{
		if (!is_int($i))
		{
			throw new Exception("Invalid key value");
		}

		return $this->GetCharAt($i);
	}

	public function offsetSet($i, $char)
	{
		if (!is_int($i))
		{
			throw new Exception("Invalid key value");
		}

		return $this->SetCharAt($i, $char);
	}

	public function offsetUnset($i)
	{
		if (!is_int($i))
		{
			throw new Exception("Invalid key value");
		}

		try
		{
			$this->DeleteCharAt($i);
			unset($this->format[$i]);
		}
		catch (Exception $e)
		{
			/*
			 * Ignore the exception
			 */
		}
	}

	/*
	 * Class code
	 */
	public function Append($string)
	{
		$this->buffer .= $string;
	}

	public function GetCharAt($i)
	{
		return $this->buffer[$i];
	}

	public function SetCharAt($i, $char)
	{
		$this->buffer[$i] = $char;
	}

	public function DeleteCharAt($i)
	{
		$l = $this->GetLength();

		if (($i < 0) || ($i >= $l))
		{
			throw new Exception("Invalid index value");
		}

		$left = substr($this->buffer, 0, max($i - 1, 0));
		$right = substr($this->buffer, min($i + 1, $l));
		$this->buffer = ($left . $right);
	}

	public function GetFormatAt($i)
	{
		if (!isset($this->format[$i]))
		{
			throw new Exception("No format specified at index {$i}");
		}

		return $this->format[$i];
	}

	public function SetFormatAt($i, $format)
	{
		$this->format[$i] = $format;
	}

	public function GetHTML()
	{
		try
		{
			$this->GetFormatAt(0);
		}
		catch (Exception $e)
		{
			$this->SetFormatAt(0, new PhpRichFormat());
		}

		ksort($this->format);
		reset($this->format);

		$output = "";
		$l = 0;

		foreach ($this->format as $i => $format)
		{
			$partial = substr($this->buffer, $l, $i - $l);
			$output .= htmlentities($partial, ENT_QUOTES);

			if ($i != $l)
			{
				$output .= "</span>";
			}

			$l = $i;
			$output .= "<span style=\"{$format}\">";
		}

		$partial = substr($this->buffer, $l);
		$output .= htmlentities($partial, ENT_QUOTES);
		$output .= "</span>";

		return $output;
	}

	public function GetLength()
	{
		return strlen($this->buffer);
	}
}
?>

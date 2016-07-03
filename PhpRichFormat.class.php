<?php
/*
 * Syntax Sugar
 * PhpRichFormat.class.php: Rich format descriptor for PhpRichString
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

class PhpRichFormat
{
	private static $_transparent_color = "transparent";

	private static $_acceptable_weights = array(
		"xx-small",
		"x-small",
		"small",
		"normal",
		"bold",
		"x-bold",
		"xx-bold"
	);

	private $weight;
	private $color;
	private $background;
	private $size;

	public function __construct($weight = "normal", $color = 0x000000, $background = null, $size = 14)
	{
		$this->SetWeight($weight);
		$this->SetColor($color);
		$this->SetBackgroundColor($background);
		$this->SetSize($size);
	}

	public function __toString()
	{
		return $this->GetInlineCSS();
	}

	public function GetWeight()
	{
		return $this->weight;
	}

	public function SetWeight($weight)
	{
		if (!in_array($weight, self::$_acceptable_weights))
		{
			throw new Exception("Invalid font weight requested");
		}

		$this->weight = $weight;
	}

	public function GetSize()
	{
		return $this->size;
	}

	public function SetSize($size)
	{
		if (!is_int($size))
		{
			throw new Exception("Invalid font size requested");
		}

		$this->size = $size;
	}

	public function GetColor()
	{
		if (is_null($this->color))
		{
			return self::$_transparent_color;
		}

		return $this->color;
	}

	public function GetHexColorCode()
	{
		if (is_string($this->GetColor()))
		{
			return $this->GetColor();
		}

		return sprintf("#%06X", $this->GetColor());
	}

	public function SetColor($color)
	{
		if (is_null($color))
		{
			$this->color = self::$_transparent_color;
		}
		else
		{
			$this->color = $color;
		}
	}

	public function GetBackgroundColor()
	{
		if (is_null($this->background))
		{
			return self::$_transparent_color;
		}

		return $this->background;
	}

	public function GetHexBackgroundColorCode()
	{
		if (is_string($this->GetBackgroundColor()))
		{
			return $this->GetBackgroundColor();
		}

		return sprintf("#%06X", $this->GetBackgroundColor());
	}

	public function SetBackgroundColor($color)
	{
		if (is_null($color))
		{
			$this->background = self::$_transparent_color;
		}
		else
		{
			$this->background = $color;
		}
	}

	public function GetInlineCSS()
	{
		return sprintf(
			"color: %s; background-color: %s; font-size: %u; font-weight: %s;",
			$this->GetHexColorCode(),
			$this->GetHexBackgroundColorCode(),
			$this->GetSize(),
			$this->GetWeight()
		);
	}
}
?>

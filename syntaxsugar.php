<?php
/*
 * Syntax Sugar
 * syntaxsugar.php: the equivalent of a main file. It contains the first methods
 * called by the user and computes some useful operations before the real work starts.
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

include_once('Languages.class.php');

class SyntaxSugar
{
	private $Parser;
	private $Code;
	private $Lines;

	function __construct($code, $language)
	{
		$language = strtolower($language);
		$this->Code = $code;

		switch ($language)
		{
			case "python":
				$this->Parser = new Python();
				break;

			case "c":
				$this->Parser = new C();
				break;

			case "cpp":
			case "c++":
				$this->Parser = new Cpp();
				break;

			case "java":
				$this->Parser = new Java();
				break;
			
			case "html":
				$this->Parser = new HTML();
				break;
			
			case "php":
				$this->Parser = new Php();
				break;

			default:
				throw new Exception("Language not supported");
		}

		$this->Highlight();
	}

	private function Highlight()
	{

		/*
		 * Indentation
		 */
		$lines = preg_split("/\r?\n/", $this->Code);
		$this->Lines = 0;

		foreach ($lines as $line)
		{
			$this->Lines++; /* A counter useful for the show method */
		}

		$this->Code = implode("\n", $lines);
		$this->Code = $this->Parser->ProcessInput($this->Code);
	}

	public function Show()
	{
		?>
<div class="codebox">
	<div class="leftpanel">
				<?php
				echo("<pre class=\"syntax numbers\">");

				for ($i = 1; $i <= $this->Lines; $i++)
				{
					echo("${i}\n");
				}

				echo("</pre>");
				?>
	</div>

	<div class="rightpanel">
				<?php
				echo("<pre class=\"syntax highlight\">");
				echo($this->Code);
				echo("</pre>");
				?>
	</div>
</div>
	<?php
	}
}

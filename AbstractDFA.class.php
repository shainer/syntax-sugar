<?php
/*
 * Syntax Sugar
 * AbstractDFA.class.php: Abstract class describing a Deterministic Finite Automaton (DFA).
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

abstract class AbstractDFA
{
	protected $States = array();
	protected $Colors = array();
	private $Current = null;
	private $Data = null;

	public function __construct()
	{
		reset($this->States);
		reset($this->Colors);

		$this->Current = array_slice($this->States, 0, 1);
		$this->Current = $this->Current[0];
	}

	/*
	 * Get the current state of the automaton.
	 */
	final public function GetState()
	{
		return $this->Current;
	}

	/*
	 * Set the new state of the automaton.
	 */
	final public function SetState($key)
	{
		try
		{
			$this->Current = $this->States[$key];
		}
		catch (Exception $e)
		{
			throw new Exception("Invalid state requested.");
		}
	}


	/*
	 * Generic function which receives each character of the code, processes
	 * it and moves to an eventually new state while performing some kind of
	 * operation.
	 */
	abstract public function StateTransition($c, $i);

	/*
	 * Generic function which processes the received input string using the
	 * StateTransition() function.
	 */
	abstract public function ProcessInput($data);
}
?>

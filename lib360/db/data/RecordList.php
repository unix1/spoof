<?php

namespace lib360\db\data;

/*
    This is Spoof.
    Copyright (C) 2011-2012  Spoof project.

    Spoof is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Spoof is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Spoof.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
*	A record list class.
*	This class extends PHP ArrayObject and represents a list of data records.
*	@see IRecordList
*	@see Record
*/

class RecordList extends \ArrayObject implements IRecordList
{

	/**
	*	Transforms object into XML representation.
	*	@return DOMDocument XML document object
	*/
	public function toXML()
	{
		$class = get_class($this);
		$xml = new \DOMDocument('1.0', 'utf-8');
		$xml->formatOutput = TRUE;
		$recordlistXML = $xml->appendChild($xml->createElement('recordlist'));
		$recordlistXML->setAttribute('type', $class);
		foreach ($this as $key => $record)
		{
			$recordXML = $this->offsetGet($key)->toXML();
			$recordlistXML->appendChild($xml->importNode($recordXML->firstChild, TRUE));
		}
		return $xml;
	}

}

?>
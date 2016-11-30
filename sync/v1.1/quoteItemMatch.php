<?php

class quoteItemMatch{
	var $oldID = array();
	var $newID = array();
	var $track = 0;

	function addPair($old, $new)
	{
		array_push($this->$oldID, $old);
		array_push($this->$newID, $new);
		$this->$track++;
	}

	function match($old)
	{
		$count = 0;
		foreach ($this->$oldID as $value) {
			if($value==$old)
				return $this->$newID[$count];
			$count++;
		}
		return '-1';
	}
}

?>
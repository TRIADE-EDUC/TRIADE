<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordSortName.php,v 1.4.2.1 2024/10/02 13:30:25 rtigero Exp $
namespace Pmb\DSI\Models\Sort\Entities\Record\RecordSortName;

use Pmb\DSI\Models\Sort\RootSort;

class RecordSortName extends RootSort
{
	
	protected $field = "tit1";
	
	protected $fieldType = "string";
	
	protected $direction;
	
	public function __construct($data = null)
	{
		$this->type = static::TYPE_QUERY;
		if (in_array($data->direction, static::DIRECTIONS)) {
			$this->direction = $data->direction;
		}
	}
	
	//Exemple du type de tri "autre", pas encore utilisé et enlevé ici pour améliorer les performances
	//Gardé pour la gloire et dans l'espoir que cet exemple serve un jour
	/*
	 public function __construct($data = null)
	 {
	 $this->type = static::TYPE_OTHER;
	 if (isset($data->direction) && in_array($data->direction, static::DIRECTIONS)) {
	 $this->direction = $data->direction;
	 }
	 }
	 
	 protected function sortData($records = array())
	 {
	 if ($this->direction == "ASC") {
	 asort($records, SORT_STRING);
	 } else {
	 arsort($records, SORT_STRING);
	 }
	 return $records;
	 }*/
}

<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Status.php,v 1.4 2022/10/18 13:45:59 qvarin Exp $

namespace Pmb\DSI\Models;

use Pmb\Common\Models\Model;
use Pmb\Common\Helper\Helper;

class Status extends Model implements CRUD
{
	public $name = "";

	public $active = false;
	
	public function __construct(int $id = 0)
	{
		$this->id = $id;
		$this->read();
	}
	
	public function check(object $data) 
	{
		if (empty($data->name) || !is_string($data->name)) {
			return [
				'error' => true,
				'errorMessage' => 'msg:data_errors'
			];
		}

		if (!is_numeric($data->active) && !is_bool($data->active)) {
			return [
				'error' => true,
				'errorMessage' => 'msg:data_errors'
			];
		}
		
		$fields = ['name' => $data->name];
		if (!empty($data->id)) {
			$fields[$this->ormName::$idTableName] = [
				'value' =>  $data->id,
				'operator' => '!='
			];
		}
		
		$result = $this->ormName::finds($fields);
		if (!empty($result)) {
			return [
				'error' => true,
				'errorMessage' => 'msg:status_duplicated'
			];
		}
		
		return [
			'error' => false,
			'errorMessage' => ''
		];
	}

	public function setFromForm(object $data) 
	{
		$this->name = $data->name;
		$this->active = boolval($data->active);
	}
	
	public function create()
	{
		$orm = new $this->ormName();
		$orm->name = $this->name;
		$orm->active = $this->active;
		$orm->save();
		
		$this->id = $orm->{$this->ormName::$idTableName};
		$this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
	}
	
	public function read()
	{
		$this->fetchData();
	}
	
	public function update()
	{
		$orm = new $this->ormName($this->id);
		$orm->name = $this->name;
		$orm->active = $this->active;
		$orm->save();
	}
	
	public function delete()
	{
		try {
			$orm = new $this->ormName($this->id);			
			$orm->delete();
		} catch(\Exception $e) {
			return [
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}
		
		$this->id = 0;
		$this->{Helper::camelize($orm::$idTableName)} = 0;
		$this->name = '';
		$this->active = '';
		
		return [
			'error' => false,
			'errorMessage' => ''
		];
	}
}
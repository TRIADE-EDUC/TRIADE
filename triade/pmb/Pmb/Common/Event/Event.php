<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Event.php,v 1.2 2022/09/29 13:57:24 qvarin Exp $
namespace Pmb\Common\Event;

global $class_path;
require_once $class_path . '/event/event.class.php';

class Event extends \event
{

	public const EMPTY_VALUE = null;

	protected $context = array();

	protected $data = self::EMPTY_VALUE;

	public function setData($data)
	{
		$this->data = $data;
	}

	public function getData()
	{
		return $this->data ?? self::EMPTY_VALUE;
	}

	public function setContext(array $context)
	{
		$this->context = $context;
	}

	public function getContext()
	{
		return $this->context;
	}
}
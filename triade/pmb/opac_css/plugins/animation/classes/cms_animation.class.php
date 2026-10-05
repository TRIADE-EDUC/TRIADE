<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_animation.class.php,v 1.2 2022/09/29 13:57:24 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], '.class.php')) {
	die('no access');
}

class cms_animation
{

	/**
	 *
	 * @var cms_editorial_data
	 */
	protected $instance;
	
	public const EMPTY_VALUE = null;

	public function __construct($instance)
	{
		$this->instance = $instance;
	}

	public function __get($attribute)
	{
		if (method_exists($this, "get_" . $attribute)) {
			return call_user_func_array(array($this, "get_" . $attribute), []);
		} elseif (isset($this->{$attribute})) {
			return $this->{$attribute};
		}
		return null;
	}
	
	public function get_animation () 
	{
		if (empty($this->instance->get_id()) || $this->instance->type == cms_editorial_data::TYPE_SECTION) {
			return self::EMPTY_VALUE;
		}
		$article = new article($this->instance->get_id());
		if (!$article->has_animation()) {
			return self::EMPTY_VALUE;
		}
		
		return $article->get_animation();
	}
	
}
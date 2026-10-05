<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: article.class.php,v 1.3.8.1 2025/04/25 11:52:14 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], '.class.php')) {
	die('no access');
}

use Pmb\Animations\Orm\AnimationOrm;
use Pmb\Animations\Models\AnimationModel;
use Pmb\Common\Models\DocsLocationModel;
use Pmb\Common\Models\CustomFieldModel;

require_once "$base_path/plugins/animation/classes/animation_conf.class.php";
require_once "$base_path/plugins/animation/classes/custom_field.class.php";

class article
{

	/**
	 * Identifient de l'article liés à l'animation
	 *
	 * @var int|string
	 */
	private $id_article = 0;

	/**
	 * Identifient de l'animation
	 *
	 * @var int
	 */
	private $animation_id = 0;

	/**
	 * Animation
	 *
	 * @var AnimationModel
	 */
	private $animation = null;

	/**
	 * champ perso
	 *
	 * @var custom_field
	 */
	private $custom_field = null;

	/**
	 * Liste des erreurs
	 *
	 * @var array
	 */
	private $errors = array();

	public $animation_conf;

	/**
	 *
	 * @param int $id_article
	 */
	public function __construct(int $id_article = 0)
	{
		$this->init($id_article);
	}

	/**
	 *
	 * @param int $id_article
	 */
	private function init(int $id_article)
	{
		$this->id_article = $id_article;

		$this->animation_conf = new animation_conf();
		$this->errors = array_merge($this->errors, $this->animation_conf->check_conf());

		$this->custom_field = new custom_field();
		$this->fetch_animation();
	}

	/**
	 * On vas chercher l'animation lié
	 *
	 * @return void
	 */
	public function fetch_animation()
	{
		if (empty($this->animation_id)) {
			$query = 'SELECT cms_editorial_custom_integer FROM cms_articles
                        JOIN cms_editorial_custom_values ON cms_editorial_custom_champ = "' . addslashes($this->custom_field->get_id_champ()) . '"
                        WHERE id_article = "' . addslashes($this->id_article) . '" AND cms_editorial_custom_origine=id_article';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$this->animation_id = pmb_mysql_result($result, 0, 0);
			}
		}
	}

	/**
	 * L'article est lié a une animation
	 *
	 * @return boolean
	 */
	public function has_animation()
	{
		return !empty($this->animation_id);
	}

	/**
	 * L'article est lié a une animation
	 *
	 * @return boolean
	 */
	public function get_animation()
	{
	    if (!$this->has_animation()) {
	        return null;
	    }

	    if (empty($this->animation)) {
	        $this->animation = new AnimationOrm($this->animation_id);
	        $this->animation->location = DocsLocationModel::getLocationAnimation($this->animation_id);
	        $this->animation->custom_champ = CustomFieldModel::getAllCustomFields('anim_animation', $this->animation_id);
	    }

	    return $this->animation;
	}
}
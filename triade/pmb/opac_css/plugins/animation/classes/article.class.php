<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: article.class.php,v 1.8 2023/03/14 15:16:11 gneveu Exp $

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
	 * @return boolean
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
	    global $opac_base_path;

	    if (!$this->has_animation()) {
	        return null;
	    }

	    if (empty($this->animation)) {
	        $this->animation = new AnimationOrm($this->animation_id);
	        $this->animation->location = DocsLocationModel::getLocationAnimation($this->animation_id);
	        $this->animation->custom_champ = CustomFieldModel::getAllCustomFields('anim_animation', $this->animation_id, true);

	        if (!isset($this->animation->animation_format_date)) {
	            $this->animation->animation_format_date = array();
	        }
	        $this->animation->animation_format_date =  $this->getFormatedDate($this->animation->event[0]);

	        if (!isset($this->animation->animation_format_quotas)) {
	            $this->animation->animation_format_quotas = array();
	        }
	        $this->animation->animation_format_quotas =  $this->getFormatedQuotas();

	        $logo = json_decode($this->animation->logo);
	        if (empty($logo)) {
	            return $this->animation;
	        }

	        $orgineSize = 0;
	        if (!empty($logo->filePath)) {
    	        $orgineSize = getimagesize($logo->filePath);
    	        $orgineSize = intval($orgineSize[0] ?? 0);
	        }

	        $animationLinkLogo = $opac_base_path . "animations_vign.php?animationId=" . intval($this->animation_id);
	        $this->animation->logo = [
	            "default" => $animationLinkLogo,
	            "small_vign" => $animationLinkLogo . "&size=16",
	            "vign" => $animationLinkLogo . "&size=100",
	            "small" => $animationLinkLogo . "&size=140",
	            "medium" => $animationLinkLogo . "&size=300",
	            "big" => $animationLinkLogo . "&size=600",
	            "large" => $animationLinkLogo . "&size=" . intval($orgineSize),
	            "alt" => $logo->alt ?? "",
	        ];
	    }

	    return $this->animation;
	}
	
	protected function getFormatedDate($event) {
	    $startDate = new \DateTime($event->start_date);
	    $endDate = new \DateTime($event->end_date);

        return [
            "start" => $startDate->format('d/m/Y'),
            "end" => $endDate->format('d/m/Y'),
            "startHour" => ($startDate->format('H:i') != "00:00") ? $startDate->format('H:i') :"",
            "endHour" => ($endDate->format('H:i') != "00:00") ? $endDate->format('H:i') :"",
        ];
	}

	protected function getFormatedQuotas() {
	    $quotasList = AnimationModel::getAllQuotas($this->animation->id_animation);

	    return [
	        "quotas_global" => $quotasList["animationQuotas"]["global"],
	        "quotas_internet" => $quotasList["animationQuotas"]["internet"],
	        "quotas_global_available" => $quotasList["availableQuotas"]["global"],
	        "quotas_internet_available" => $quotasList["availableQuotas"]["internet"],
	        "quotas_global_reserved" => $quotasList["reserved"]["global"],
	        "quotas_internet_reserved" => $quotasList["reserved"]["internet"],
	        "quotas_global_waiting" => $quotasList["waitingList"]["global"],
	        "quotas_internet_waiting" => $quotasList["waitingList"]["internet"],
	    ];
	}
}

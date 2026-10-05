<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: animation.class.php,v 1.12 2023/12/06 15:40:51 qvarin Exp $
use Pmb\Animations\Orm\AnimationOrm;
use Pmb\Animations\Models\AnimationModel;
use Pmb\Animations\Models\EventModel;

if (stristr($_SERVER['REQUEST_URI'], '.class.php')) {
	die('no access');
}

require_once "$base_path/plugins/animation/classes/animation_conf.class.php";
require_once "$base_path/plugins/animation/classes/custom_field.class.php";

class animation
{

	/**
	 * Format de la date
	 *
	 * @var string
	 */
	private const FORMAT_DATE = "d/m/Y";

	/**
	 * Format de l'heure
	 *
	 * @var string
	 */
	private const FORMAT_HOUR = "H:i";

	/**
	 *
	 * @var animation_conf
	 */
	private $animation_conf;

	/**
	 * Identifient de l'animation
	 *
	 * @var int
	 */
	private $animation_id = 0;

	/**
	 * Données de l'animation
	 *
	 * @var array
	 */
	private $animation_data = array();

	/**
	 * champ perso
	 *
	 * @var custom_field
	 */
	private $custom_field = null;

	/**
	 * Identifient de l'article liés à l'animation
	 *
	 * @var int|string
	 */
	private $id_article = 0;

	/**
	 * Liste des erreurs
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 *
	 * @param int $animation_id
	 */
	public function __construct(int $animation_id)
	{
		$this->init($animation_id);
	}

	/**
	 *
	 * @param int $animation_id
	 */
	private function init(int $animation_id)
	{
		if (! empty($animation_id)) {
			$this->animation_id = $animation_id;
			if (AnimationOrm::exist($animation_id)) {
				$this->get_animation_data();
			} else {
				$this->errors[] = plugins::get_message('animation', "animation_do_not_exist");
			}
		}

		$this->animation_conf = new animation_conf();
		$this->errors = array_merge($this->errors, $this->animation_conf->check_conf());

		if (! $this->type_is_config()) {
			$this->errors[] = plugins::get_message('animation', "animation_type_not_configurate");
		}

		$this->custom_field = new custom_field();
	}

	/**
	 * Créer/Modifie l'article liés à l'animation
	 */
	public function save_animation_to_article()
	{
		if (! $this->animation_has_article()) {
			$query = 'INSERT INTO ';
			$champ = 'article_creation_date = now(),
            		  article_logo = "",
            		  article_start_date = ""';
			$clause = '';
		} else {
			$query = 'UPDATE ';
			$champ = 'article_update_timestamp = now()';
			$clause = ' WHERE id_article = "' . addslashes($this->id_article) . '"';
		}

        $end_date = "0000-00-00 00:00:00";
        if ($this->can_enddate_article()) {
            if (0 == intval($this->animation_data['during_day'])) {
    		    $end_date = $this->animation_data['end_date'];
            } else {
    		    $end_date = $this->animation_data['start_date'];
    		}
        }

		$calendar_data = $this->animation_conf->get_calendar_data($this->animation_data['num_type']);
		$query .= 'cms_articles SET
            		article_title = "' . addslashes($this->animation_data['name']) . '",
            		article_contenu = "' . addslashes($this->animation_data['description']) . '",
            		article_resume = "' . addslashes($this->animation_data['comment']) . '",
            		article_publication_state = "' . addslashes($this->animation_conf->get_id_publication_state()) . '",
            		num_section = "' . addslashes($this->animation_conf->get_id_section_parent()) . '",
            		article_end_date = "' . addslashes($end_date) . '",
            		article_num_type = "' . addslashes($calendar_data['type']) . '",' . $champ;

		pmb_mysql_query($query . $clause);
		if (empty($this->id_article)) {
			$this->id_article = pmb_mysql_insert_id();
			audit::insert_creation(
				AUDIT_EDITORIAL_ARTICLE,
				$this->id_article,
				plugins::get_message('animation', "audit_create_article")
			);
		} else {
			audit::insert_modif(
				AUDIT_EDITORIAL_ARTICLE,
				$this->id_article,
				plugins::get_message('animation', "audit_update_article")
			);
		}

		$this->save_cp_article();
		$this->save_categ_article();
		$this->save_concept_article();

		$article = new cms_article($this->id_article);
		$article->maj_indexation();
	}

	/**
	 * Remplis les champ perso de l'article
	 */
	public function save_cp_article()
	{

		// Champ qui contient l'id de l'animation
		$query = 'SELECT 1 FROM cms_editorial_custom_values WHERE 
                    cms_editorial_custom_champ = "' . addslashes($this->custom_field->get_id_champ()) . '" AND 
                    cms_editorial_custom_origine = "' . addslashes($this->id_article) . '" AND 
                    cms_editorial_custom_integer = "' . addslashes($this->animation_id) . '"';
		$result = pmb_mysql_query($query);
		if (! pmb_mysql_num_rows($result)) {
			$query = 'INSERT INTO cms_editorial_custom_values SET 
                        cms_editorial_custom_champ = "' . addslashes($this->custom_field->get_id_champ()) . '",
                        cms_editorial_custom_origine = "' . addslashes($this->id_article) . '",
                        cms_editorial_custom_integer = "' . addslashes($this->animation_id) . '"';
			pmb_mysql_query($query);
		}
		$calendar_data = $this->animation_conf->get_calendar_data($this->animation_data['num_type']);

		// Champ qui contient la date de début de l'evenement
		if (! empty($calendar_data['start_date'])) {
			$query = 'SELECT 1 FROM cms_editorial_custom_values WHERE 
                        cms_editorial_custom_champ = "' . addslashes($calendar_data['start_date']) . '" AND 
                        cms_editorial_custom_origine = "' . addslashes($this->id_article) . '"';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$query = 'UPDATE ';
				$clause = ' WHERE cms_editorial_custom_origine = "' . addslashes($this->id_article) . '" AND cms_editorial_custom_champ = "' . addslashes($calendar_data['start_date']) . '"';
			} else {
				$query = 'INSERT INTO ';
				$clause = '';
			}

			$query .= 'cms_editorial_custom_values SET 
                            cms_editorial_custom_champ = "' . addslashes($calendar_data['start_date']) . '",
                            cms_editorial_custom_origine = "' . addslashes($this->id_article) . '",
                            cms_editorial_custom_date = "' . addslashes($this->animation_data['start_date']) . '"';
			pmb_mysql_query($query . $clause);
		}

		// Champ qui contient la date de fin de l'evenement
		$query = 'SELECT 1 FROM cms_editorial_custom_values WHERE
                        cms_editorial_custom_champ = "' . addslashes($calendar_data['end_date']) . '" AND
                        cms_editorial_custom_origine = "' . addslashes($this->id_article) . '"';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
		    $query = 'UPDATE ';
		    $clause = ' WHERE cms_editorial_custom_origine = "' . addslashes($this->id_article) . '" AND cms_editorial_custom_champ = "' . addslashes($calendar_data['end_date']) . '"';
		} else {
		    $query = 'INSERT INTO ';
		    $clause = '';
		}
		$query .= 'cms_editorial_custom_values SET
                cms_editorial_custom_champ = "' . addslashes($calendar_data['end_date']) . '",
                cms_editorial_custom_origine = "' . addslashes($this->id_article) . '",';
        if (!empty($this->animation_data['end_date']) && !$this->animation_data["during_day"] && ($this->animation_data['end_date'] !== $this->animation_data['start_date'])) {
			$query .= 'cms_editorial_custom_date = "' . addslashes($this->animation_data['end_date']) . '"';
        } else {
            $query .= 'cms_editorial_custom_date = null';
        }
		pmb_mysql_query($query . $clause);
	}

	/**
	 * L'animation à déjà un article lié
	 *
	 * @return boolean
	 */
	public function animation_has_article()
	{
		if (empty($this->id_article)) {
			$calendar_data = $this->animation_conf->get_calendar_data($this->animation_data['num_type']);
			if (empty($calendar_data)) {
				return false;
			}
			$query = 'SELECT id_article FROM cms_articles 
                        JOIN cms_editorial_custom_values ON cms_editorial_custom_champ = "' . addslashes($this->custom_field->get_id_champ()) . '" 
                        WHERE cms_editorial_custom_integer = "' . addslashes($this->animation_id) . '" AND 
                        cms_editorial_custom_origine=id_article';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$this->id_article = pmb_mysql_result($result, 0, 0);
			} else {
				return false;
			}
		}
		return true;
	}

	/**
	 * Retourne les données de l'animation
	 *
	 * @return array
	 */
	private function get_animation_data()
	{
		if (empty($this->animation_data)) {
			$query = 'SELECT anim_animations.name, anim_animations.comment, anim_animations.description, anim_animations.num_type, anim_events.start_date, anim_events.end_date, anim_events.during_day FROM anim_animations 
                        JOIN anim_events ON id_event=num_event  WHERE id_animation = "' . addslashes($this->animation_id) . '"';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$this->animation_data = pmb_mysql_fetch_assoc($result);
			}
		}
		return $this->animation_data;
	}

	/**
	 * Supprime l'article liés à l'animation
	 */
	public function delete_animation_to_article()
	{
		if ($this->animation_has_article()) {
			$article = new cms_article($this->id_article);
			$article->delete();
		} else {
			$this->errors[] = plugins::get_message('animation', "animation_error_no_article_id");
		}
	}

	/**
	 * Indique si on peut faire la mise à jour automatique
	 *
	 * @return boolean
	 */
	public function can_automatic_update()
	{
		if ($this->animation_conf->get_state_anim_update() == $this->animation_conf::OPTION_AUTOMATIQUE) {
			return true;
		}
		return false;
	}

	/**
	 * Indique si on peut faire la création automatique
	 *
	 * @return boolean
	 */
	public function can_automatic_create()
	{
		if ($this->animation_conf->get_state_anim_create() == $this->animation_conf::OPTION_AUTOMATIQUE) {
			return true;
		}
		return false;
	}

	/**
	 * Indique si on doit mettre la date de fin de l'animation dans la date de fin de l'article
	 *
	 * @return boolean
	 */
	public function can_enddate_article()
	{
	    if (intval($this->animation_conf->get_state_anim_enddate_article()) == $this->animation_conf::OPTION_ENDDATE_ARTICLE_YES) {
	        return true;
	    }
	    return false;
	}

	/**
	 * Indique si on a des erreurs
	 *
	 * @return boolean
	 */
	public function has_errors()
	{
		if (count($this->errors) > 0) {
			return true;
		}
		return false;
	}

	/**
	 * Retourne la liste des erreurs
	 *
	 * @return array
	 */
	public function get_errors()
	{
		return $this->errors;
	}

	/**
	 * Retourne l'identifiant de l'article
	 *
	 * @return array
	 */
	public function get_id_article()
	{
		return $this->id_article;
	}

	/**
	 * Retourne le template de bouton a utiliser
	 *
	 * @return string
	 */
	public function get_template()
	{
		global $animation_inputs, $animation_editorial;

		if (! animation_conf::animations_is_active()) {
			return array(
				'inputs' => "",
				'info_editorial' => ""
			);
		}

		$calendar_data = $this->animation_conf->get_calendar_data($this->animation_data['num_type']);
		if (empty($calendar_data)) {
			return array(
				'inputs' => "",
				'info_editorial' => ""
			);
		}

		// Template des boutons
		$inputs_template = "";
		if ($this->animation_has_article()) {
			$inputs_template = str_replace("!!article_id!!", $this->id_article, $animation_inputs["view_editorial_article"]);
		} elseif (! $this->can_automatic_create()) {
			$inputs_template = str_replace("!!animation_id!!", $this->animation_id, $animation_inputs["editorial_button"]);
		}
		if (! $this->can_automatic_update() && $this->animation_has_article()) {
			$inputs_template .= str_replace("!!animation_id!!", $this->animation_id, $animation_inputs["update_manuel_editorial_article"]);
		}

		// Template editorial
		$info_editorial_template = "";
		if ($this->animation_has_article()) {
			$last_edit = $this->get_last_edit_article();
			if ($last_edit['is_update']) {
				$info_editorial_template = str_replace('!!title!!', plugins::get_message('animation', 'animation_editorial_updated'), $animation_editorial);
			} else {
				$info_editorial_template = str_replace('!!title!!', plugins::get_message('animation', 'animation_editorial_created'), $animation_editorial);
			}
			$info_editorial_template = str_replace('!!date!!', empty($last_edit['date']) ? '' : $last_edit['date'], $info_editorial_template);
			$info_editorial_template = str_replace('!!hour!!', empty($last_edit['hour']) ? '' : $last_edit['hour'], $info_editorial_template);
		}

		return array(
			'inputs' => $inputs_template,
			'info_editorial' => $info_editorial_template
		);
	}

	/**
	 * Rempli les catégories de l'article
	 */
	public function save_categ_article()
	{
		$query = 'SELECT num_noeud, ordre_categorie FROM anim_animation_categories WHERE num_animation = "' . addslashes($this->animation_id) . '"';
		$result = pmb_mysql_query($query);

		if (pmb_mysql_num_rows($result)) {
			$query = 'DELETE FROM cms_articles_descriptors WHERE num_article = "' . addslashes($this->id_article) . '"';
			pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$query = 'INSERT INTO cms_articles_descriptors set num_article="' . $this->id_article . '", num_noeud = "' . addslashes($row['num_noeud']) . '", article_descriptor_order="' . addslashes($row['ordre_categorie']) . '"';
				pmb_mysql_query($query);
			}
		} else {
			$query = 'SELECT 1 FROM cms_articles_descriptors WHERE num_article = "' . addslashes($this->id_article) . '"';
			pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$query = 'DELETE FROM cms_articles_descriptors WHERE num_article = "' . addslashes($this->id_article) . '"';
				pmb_mysql_query($query);
			}
		}
	}

	/**
	 * Liés le concept de l'animation à l'article
	 */
	public function save_concept_article()
	{
		$query = 'SELECT num_concept, order_concept FROM index_concept WHERE num_object = "' . addslashes($this->animation_id) . '" AND type_object = "' . TYPE_ANIMATION . '"';
		$result = pmb_mysql_query($query);

		if (pmb_mysql_num_rows($result)) {
			$query = 'DELETE FROM index_concept WHERE num_object = "' . addslashes($this->id_article) . '" AND type_object = "' . TYPE_CMS_ARTICLE . '"';
			pmb_mysql_query($query);
			while ($row = pmb_mysql_fetch_assoc($result)) {
				$query = 'INSERT INTO index_concept set num_object="' . addslashes($this->id_article) . '", num_concept = "' . addslashes($row['num_concept']) . '", type_object = "' . TYPE_CMS_ARTICLE . '", order_concept="' . $row['order_concept'] . '", comment="", comment_visible_opac="0"';
				pmb_mysql_query($query);
			}
		} else {
			$query = 'SELECT 1 FROM index_concept WHERE num_object = "' . addslashes($this->id_article) . '" AND type_object = "' . TYPE_CMS_ARTICLE . '"';
			$result = pmb_mysql_query($query);
			if (pmb_mysql_num_rows($result)) {
				$query = 'DELETE FROM index_concept WHERE num_object = "' . addslashes($this->id_article) . '" AND type_object = "' . TYPE_CMS_ARTICLE . '"';
				pmb_mysql_query($query);
			}
		}
	}

	/**
	 * Retourne la date de la dernière mise à jour/création de l'article
	 */
	public function get_last_edit_article()
	{
		$date = "";
		$hour = "";
		$is_update = false;

		if ($this->animation_has_article()) {
			$query = 'SELECT article_update_timestamp, article_creation_date FROM cms_articles WHERE id_article = "' . addslashes($this->id_article) . '"';
			$query_result = pmb_mysql_query($query);
			$result = pmb_mysql_fetch_assoc($query_result);

			$article_date = new DateTime($result['article_update_timestamp']);
			if ($result['article_creation_date'] != $result['article_update_timestamp']) {
				$is_update = true;
			}

			$date = $article_date->format(self::FORMAT_DATE);
			$hour = $article_date->format(self::FORMAT_HOUR);
		}

		return array(
			'date' => $date,
			'hour' => $hour,
			'is_update' => $is_update
		);
	}

	/**
	 * Indique si le type d'animation est configuré
	 *
	 * @return boolean
	 */
	public function type_is_config()
	{
		$id_type = $this->animation_data['num_type'];
		$calendar_data = $this->animation_conf->get_calendar_data($id_type);
		return (! empty($calendar_data) && ! empty($calendar_data['type'])) == true;
	}
}
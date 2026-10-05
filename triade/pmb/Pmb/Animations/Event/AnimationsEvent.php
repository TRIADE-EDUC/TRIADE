<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationsEvent.php,v 1.1 2021/03/01 10:02:15 qvarin Exp $
namespace Pmb\Animations\Event;

use Pmb\Common\Event\Event;

class AnimationsEvent extends Event
{

    /**
     * Mise à jour  manuelle
     *
     * @var boolean
     */
    public const MANUAL_UPDATE = 0;

    /**
     * Création manuelle
     *
     * @var boolean
     */
    public const MANUAL_CREATE = 3;

    /**
     * Mise à jour automatique
     *
     * @var boolean
     */
    public const AUTOMATIC_UPDATE = 1;

    /**
     * Création automatique
     *
     * @var boolean
     */
    public const AUTOMATIC_CREATE = 2;

    /**
     * Id de l'animation
     *
     * @var integer
     */
    protected $animation_id = 0;
    
    /**
     * Id de l'article
     *
     * @var integer
     */
    protected $article_id = 0;

    /**
     * Contient le template du buttons éditorialisé
     *
     * @var string
     */
    protected $inputs_template = "";
    
    /**
     * @var string
     */
    protected $info_editorial_template = "";

    /**
     * Permet de savoir si c'est une action automatique ou lancé par l'utilisateur
     *
     * @var string
     */
    protected $action = self::MANUAL_UPDATE;

    /**
     * Liste des erreurs
     *
     * @var array
     */
    protected $errors = array();
    
    /**
     * Retourne l'id de l'animation
     *
     * @return number
     */
    public function get_animation_id()
    {
        return intval($this->animation_id);
    }

    /**
     * Définis l'id de l'animation
     *
     * @param int|string $animation_id
     */
    public function set_animation_id($animation_id)
    {
        $this->animation_id = intval($animation_id);
    }
    
    /**
     * Retourne l'id de l'animation
     *
     * @return number
     */
    public function get_article_id()
    {
        return intval($this->article_id);
    }

    /**
     * Définis l'id de l'animation
     *
     * @param int|string $article_id
     */
    public function set_article_id($article_id)
    {
        $this->article_id = intval($article_id);
    }

    /**
     * Retourne l'action permettent de savoir
     * si c'est une action manuel/Automatique
     *
     * @return string
     */
    public function get_action()
    {
        return $this->action;
    }

    /**
     * Definis l'action
     * (Voir les constantes MANUAL_UPDATE, AUTOMATIC_UPDATE, AUTOMATIC_CREATE)
     *
     * @param int $action
     */
    public function set_action(int $action = self::MANUAL_UPDATE)
    {
        $this->action = $action;
    }

    /**
     * Defnir le template des inputs
     *
     * @param string $inputs
     */
    public function set_inputs_template(string $inputs)
    {
        $this->inputs_template = $inputs;
    }
    
    /**
     * Retourne le template des inputs
     *
     * @return string
     */
    public function get_inputs_template()
    {
        return $this->inputs_template;
    }
    
    /**
     * @param string $inputs
     */
    public function set_info_editorial_template(string $info_editorial)
    {
        $this->info_editorial_template = $info_editorial;
    }
    
    /**
     * @return string
     */
    public function get_info_editorial_template()
    {
        return $this->info_editorial_template;
    }

    /**
     * Definir les erreurs
     *
     * @param array $errors
     */
    public function set_errors(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Retourne les erreurs
     *
     * @return array
     */
    public function get_errors()
    {
        return $this->errors;
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
}
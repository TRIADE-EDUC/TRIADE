<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.class.php,v 1.1 2024/02/28 16:14:51 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

use Pmb\AI\Models\AiSessionSemanticModel;

class ai_search
{
    public $id;
    public $n_ligne;
    public $params;

    /**
     * Recherche parente
     *
     * @var search
     */
    public $search;

    /**
     * Constructeur
     *
     * @param mixed $id
     * @param mixed $n_ligne
     * @param mixed $params
     * @param search $search
     */
    public function __construct($id, $n_ligne, $params, &$search)
    {
        $this->id = $id;
        $this->n_ligne = $n_ligne;
        $this->params = $params;
        $this->search = &$search;
    }

    /**
     * Fonction de récupération des opérateurs disponibles pour ce champ spécial
     * (renvoie un tableau d'opérateurs)
     *
     * @return array Opérateurs disponibles
     */
    public function get_op()
    {
        $operators = array();
        if ($_SESSION["nb_queries"]!=0) {
            $operators["EQ"]="=";
        }
        return $operators;
    }

    /**
     * Retourne de la valeur de saisie
     *
     * @return string
     */
    protected function get_value()
    {
        $valeur_=$this->get_field_name();
        global ${$valeur_};

        $fields = ${$valeur_};
        $value = $fields[0] ?? null;
        return is_string($value) ? $value : '';
    }

    /**
     * Retourne le nom du champ
     *
     * @return string
     */
    protected function get_field_name()
    {
        return "field_{$this->n_ligne}_s_{$this->id}";
    }

    /**
     * Fonction de récupération de l'affichage de la saisie du critère
     *
     * @return string Chaine html
     */
    public function get_input_box()
    {
        global $msg;

        $template = '<label for="%s">%s</label>';
        $template .= '<select name="%s" id="%s">%s</select>';

        $options = '';
        $option_format = '<option value="%s" %s>%s</option>';
        $option_opt_format = '<optgroup label="%s">%s</optgroup>';
        foreach(AiSessionSemanticModel::findAll() as $ai_session_semantic) {

            $options_template = '';
            foreach($ai_session_semantic->aiSessionSemantiqueQuestions as $index => $question) {
                $option_value = "{$ai_session_semantic->idAiSessionSemantique}_{$index}";
                $selected = ($option_value == $this->get_value()) ? 'selected' : '';
                $options_template .= sprintf($option_format, $option_value, $selected, $question);
            }

            $options = sprintf($option_opt_format, $ai_session_semantic->aiSessionSemantiqueName, $options_template);
        }

        return sprintf(
            $template,
            $this->get_field_name(),
            $msg['ai_search_criteria_rmc'],
            $this->get_field_name() . "[]",
            $this->get_field_name(),
            $options
        );
    }

    /**
     * Fonction de vérification du champ saisi ou sélectionné
     *
     * @param int $valeur Champ saisi ou sélectionné
     * @return boolean true si vide
     */
    public function is_empty($valeur)
    {
        return empty($valeur) && AiSessionSemanticModel::exist($valeur);
    }

    public function make_human_query()
    {
        global $msg;

        $value = $this->get_value();
        $values = explode('_', $value);
        $values = array_map('intval', $values);

        $idAiSessionSemantique = $values[0];
        $indexQuestion = $values[1];

        if (!AiSessionSemanticModel::exist($idAiSessionSemantique)) {
            return "";
        }

        $ai_session_semantic = new AiSessionSemanticModel($idAiSessionSemantique);
        if (empty($ai_session_semantic->aiSessionSemantiqueQuestions[$indexQuestion])) {
            return "";
        }

        return [
            sprintf(
                $msg['ai_search_human_query'],
                $ai_session_semantic->aiSessionSemantiqueQuestions[$indexQuestion]
            )
        ];
    }

    public function make_search()
    {
        $value = $this->get_value();
        $values = explode('_', $value);
        $values = array_map('intval', $values);

        $idAiSessionSemantique = $values[0];
        $indexQuestion = $values[1];

        if (!AiSessionSemanticModel::exist($idAiSessionSemantique)) {
            return "";
        }

        $ai_session_semantic = new AiSessionSemanticModel($idAiSessionSemantique);
        if (
            empty($ai_session_semantic->aiSessionSemantiqueQuestions[$indexQuestion]) ||
            empty($ai_session_semantic->aiSessionSemantiqueNumObjects[$indexQuestion])
        ) {
            return "";
        }

        $result = $ai_session_semantic->aiSessionSemantiqueNumObjects[$indexQuestion];

        $table_name = "ai_search_" . md5(microtime(true));
        $query = "CREATE TEMPORARY TABLE IF NOT EXISTS ".$table_name." (
            notice_id int(11) NOT NULL,
            pert decimal(16,1) default 1
        ) ENGINE=".$this->search->current_engine;
		pmb_mysql_query($query);

        $query = "ALTER TABLE ".$table_name." ADD INDEX notice_id (notice_id)";
		pmb_mysql_query($query);

        $ids = [];
        $query = "INSERT INTO ".$table_name." (notice_id, pert) VALUES";

        foreach ($result as $object) {
            if (!in_array($object['id'], $ids)) {
                $ids[] = $object['id'];
                $query .= "(" . $object['id'] . ", " . $object['score'] . "),";
            }
        }
        $query = trim($query, ',');
		pmb_mysql_query($query);

        return $table_name;
    }
}
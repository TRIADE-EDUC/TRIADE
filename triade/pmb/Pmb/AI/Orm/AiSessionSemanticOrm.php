<?php

// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiSessionSemanticOrm.php,v 1.3.2.1 2024/06/19 13:40:39 qvarin Exp $

namespace Pmb\AI\Orm;

use Pmb\AI\Models\AiSessionSemanticModel;
use Pmb\Common\Orm\Orm;

class AiSessionSemanticOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "ai_session_semantique";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_ai_session_semantique";

    /**
     *
     * @var integer
     */
    public $id_ai_session_semantique = 0;

    /**
     *
     * @var string
     */
    public $ai_session_semantique_name = "";

    /**
     *
     * @var string
     */
    public $ai_session_semantique_questions = "[]";

    /**
     *
     * @var string
     */
    public $ai_session_semantique_reponses = "[]";

    /**
     *
     * @var string
     */
    public $ai_session_semantique_num_objects = "[]";

    /**
     * SESSID de la session anonyme (Faut null si pas de session anonyme)
     *
     * @var string|null
     */
    protected $ai_session_semantique_anonyme_sessid = "";

    /**
     *
     * @var int
     */
    protected $ai_session_semantique_type = 0;

    public function save()
    {
        if ($_SESSION["user_code"]) {
            $this->ai_session_semantique_anonyme_sessid = "";
        } else {
            $this->ai_session_semantique_anonyme_sessid = SESSid;
        }
        parent::save();
    }

    public function setAiSessionSemantiqueAnonymeSessidIndex($value)
    {
        if ($_SESSION["user_code"]) {
            $this->ai_session_semantique_anonyme_sessid = "";
        } else {
            $this->ai_session_semantique_anonyme_sessid = SESSid;
        }
    }

    public function delete()
    {
        if ($this->ai_session_semantique_type == AiSessionSemanticModel::TYPE_SHARED_LIST) {
            $sessionSharedList = AiSessionSharedListOrm::finds([
                'num_ai_session_semantique' => $this->id_ai_session_semantique
            ]);

            array_walk($sessionSharedList, function ($sessionSharedList) {
                $sessionSharedList->delete();
            });
        }

        parent::delete();
        static::deleteExpiredAnonymeSessid();
    }

    public static function deleteExpiredAnonymeSessid()
    {
        $query = "DELETE FROM ai_session_semantique
        WHERE ai_session_semantique_anonyme_sessid != ''
        AND ai_session_semantique_anonyme_sessid NOT IN (SELECT sessid FROM sessions WHERE SESSNAME = 'PmbOpac')";
        pmb_mysql_query($query);
    }
}

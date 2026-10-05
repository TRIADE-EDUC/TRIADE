<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiSharedListDocnumModel.php,v 1.1.2.3 2024/06/19 07:52:23 qvarin Exp $

namespace Pmb\AI\Models;

use Pmb\AI\Orm\AiSharedListDocnumOrm;
use Pmb\Common\Models\Model;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AiSharedListDocnumModel extends Model
{
    public const TYPE_EXPLNUM_ID = "docnum";

    protected $ormName = "Pmb\AI\Orm\AiSharedListDocnumOrm";

    public $idAiSharedListDocnum = 0;
    public $nameAiSharedListDocnum = "";
    public $contentAiSharedListDocnum = "";
    public $mimetypeAiSharedListDocnum = "";
    public $extfileAiSharedListDocnum = "";
    public $pathAiSharedListDocnum = "";
    public $hashNameAiSharedListDocnum = "";
    public $hashBinaryAiSharedListDocnum = "";
    public $numListAiSharedListDocnum = 0;
    public $flagAiSharedListDocnum = 0;

    public function __construct(int $id = 0)
    {
        $this->id = intval($id);
        $this->fetchData();
    }

    public function save()
    {
        $orm = new $this->ormName();

        $orm->id_ai_shared_list_docnum = $this->idAiSharedListDocnum;
        $orm->name_ai_shared_list_docnum = $this->nameAiSharedListDocnum;
        $orm->content_ai_shared_list_docnum = $this->get_content();
        $orm->mimetype_ai_shared_list_docnum = $this->mimetypeAiSharedListDocnum;
        $orm->extfile_ai_shared_list_docnum = $this->extfileAiSharedListDocnum;
        $orm->path_ai_shared_list_docnum = $this->pathAiSharedListDocnum;
        $orm->hash_name_ai_shared_list_docnum = $this->hashNameAiSharedListDocnum;
        $orm->hash_binary_ai_shared_list_docnum = $this->hashBinaryAiSharedListDocnum;
        $orm->num_list_ai_shared_list_docnum = $this->numListAiSharedListDocnum;
        $orm->flag_ai_shared_list_docnum = $this->flagAiSharedListDocnum;

        $orm->save();
    }

	private function get_content(){
		global $base_path, $class_path;

        if(!empty($this->contentAiSharedListDocnum)) {
            return $this->contentAiSharedListDocnum;
        }

        if(empty($this->mimetypeAiSharedListDocnum)) {
            return "";
        }

        $path = $base_path;
        if(!defined('GESTION')) {
            $path = $base_path . "/../";
        }

		$parse = new \XMLlist("$path/catalog/explnum/index_docnum/index_doc.xml");
        $parse->analyser();

        $className = $parse->table[$this->mimetypeAiSharedListDocnum];
        if(empty($className)) {
            return "";
        }

        require_once($path . "/catalog/explnum/index_docnum/" . $className . ".class.php");

        $filename = $this->pathAiSharedListDocnum . $this->hashNameAiSharedListDocnum;
        $class = new $className($filename, $this->mimetypeAiSharedListDocnum, $this->extfileAiSharedListDocnum);

        $this->contentAiSharedListDocnum = $class->get_text($filename);

        return $this->contentAiSharedListDocnum;
	}

    /**
     * Compte le nombre de enregistrements qui ne sont pas indexés.
     *
     * @param int $idList L'id de la liste
     * @return int Le nombre d'enregistrements non indexés
     */
    public static function countNotIndexedDocnum(int $idList)
    {
        $docnums = AiSharedListDocnumOrm::finds([
            'num_list_ai_shared_list_docnum' => $idList,
            'flag_ai_shared_list_docnum' => 0
        ]);

        return count($docnums);
    }

    /**
     * Récupère les docnums en fonction de l'id d'une liste.
     *
     * @param int $idList L'id de la liste
     * @param int $limit Le nombre maximum d'enregistrements à récupérer
     * @return AiSharedListDocnumOrm[]
     */
    public static function getDocnumsByListId(int $idList, $limit = 0)
    {
        return AiSharedListDocnumOrm::finds([
            'num_list_ai_shared_list_docnum' => $idList,
            'flag_ai_shared_list_docnum' => 0
        ], '', 'AND', $limit);
    }

    /**
     * Récupère tous les docnums en fonction de l'id d'une liste.
     *
     * @param int $idList L'id de la liste
     * @return AiSharedListDocnumOrm[]
     */
    public static function fetchAllDocnumsByListId(int $idList)
    {
        return AiSharedListDocnumOrm::finds([
            'num_list_ai_shared_list_docnum' => $idList
        ]);
    }

    public static function getEntityDataAi(int $idList, object $indexation_choice, $limit = 0)
    {
        $return = array();

        $docnums = self::getDocnumsByListId($idList, $limit);
        foreach ($docnums as $docnum) {
            $tab = [
                "entity_data" => [
                    //"object_id" => $docnum->id_ai_shared_list_docnum,
                    "shared_list_id" => $idList,
                    "docnum_id" => $docnum->id_ai_shared_list_docnum,
                ]
            ];

            $hasContent = false;
            if ($indexation_choice->docnum) {
                $content = $docnum->content_ai_shared_list_docnum;
                if (!empty($content)) {
                    $tab["content"] = $content;
                    $tab["type"] = self::TYPE_EXPLNUM_ID;

                    $hasContent = true;
                }
            }

            if ($hasContent) {
                $return[] = $tab;
            }
        }

        return $return;
    }

    /**
     * Marque le champ 'flag_ai_shared_list_docnum' à 1 pour spécifier qu'un enregistrement est indexé
     *
     * @param int $idList L'id de la liste.
     * @param int $limit Le nombre maximum d'enregistrements à mettre à jour
     * @param int $flag La valeur du flag à définir
     * @return void
     */
    public static function setDocnumFlag(int $idList, $limit = 0, int $flag = 0)
    {
        $docnums = self::getDocnumsByListId($idList, $limit);
        if(empty($docnums)) {
           return;
        }

        foreach ($docnums as $docnum) {
            $docnum->flag_ai_shared_list_docnum = $flag;
            $docnum->save();
        }
    }
}

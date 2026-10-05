<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiSharedListDocnumOrm.php,v 1.1.2.5 2024/07/10 08:19:49 qvarin Exp $

namespace Pmb\AI\Orm;

use Pmb\Common\Orm\Orm;
use Pmb\Common\Orm\UploadFolderOrm;

class AiSharedListDocnumOrm extends Orm
{
    /**
     * Table name
     *
     * @var string
     */
    public static $tableName = "ai_shared_list_docnum";

    /**
     * Primary Key
     *
     * @var string
     */
    public static $idTableName = "id_ai_shared_list_docnum";

    /**
     *
     * @var integer
     */
    protected $id_ai_shared_list_docnum = 0;

    /**
     *
     * @var string
     */
    protected $name_ai_shared_list_docnum = "";

    /**
     *
     * @var string
     */
    protected $content_ai_shared_list_docnum = "";

    /**
     *
     * @var string
     */
    protected $mimetype_ai_shared_list_docnum = "";

    /**
     *
     * @var string
     */
    protected $extfile_ai_shared_list_docnum = "";

    /**
     *
     * @var string
     */
    protected $path_ai_shared_list_docnum = "";

    /**
     *
     * @var string
     */
    protected $hash_name_ai_shared_list_docnum = "";

    /**
     *
     * @var string
     */
    protected $hash_binary_ai_shared_list_docnum = "";

    /**
     *
     * @var integer
     */
    protected $num_list_ai_shared_list_docnum = 0;

    /**
     *
     * @var integer
     */
    protected $flag_ai_shared_list_docnum = 0;

    /**
     *
     * @var \ReflectionClass
     */
    protected static $reflectionClass = null;

    /**
     * Recherche tous les AiSharedListDocnum d'un liste de lectures
     *
     * @param integer $numList Identifiant de la liste
     * @param boolean $returnInstance
     * @return AiSharedListDocnumOrm[]|array{id:string,name:string}
     */
    public static function findAllByNumList(int $numList, bool $returnInstance = false): array
    {
        $query = 'SELECT id_ai_shared_list_docnum, name_ai_shared_list_docnum, num_list_ai_shared_list_docnum FROM ai_shared_list_docnum';
        $query .= ' WHERE num_list_ai_shared_list_docnum = ' . $numList;
        $query .= ' ORDER BY name_ai_shared_list_docnum';

        $result = pmb_mysql_query($query);

        $rows = [];
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                if ($returnInstance) {
                    $rows[] = new self($row['id_ai_shared_list_docnum']);
                } else {
                    $urlParameters = http_build_query([
                        'module' => 'ajax',
                        'categ' => 'liste_lecture',
                        'quoifaire' => 'show_docnum',
                        'id_liste' => $row['num_list_ai_shared_list_docnum'],
                        'docnum_id' => $row['id_ai_shared_list_docnum']
                    ]);

                    $rows[] = [
                        'id' => intval($row['id_ai_shared_list_docnum']),
                        'name' => $row['name_ai_shared_list_docnum'],
                        'url' => './ajax.php?' . $urlParameters
                    ];
                }
            }
            pmb_mysql_free_result($result);
        }
        return $rows;
    }

    /**
     * Retourne le repertoire d'upload
     *
     * @return UploadFolderOrm
     */
    public function getUploadFolder()
    {
        $aiSharedListOrm = new AiSharedListOrm();
        return $aiSharedListOrm->getUploadFolder();
    }

    /**
     * Retourne le chemin complet du fichier
     *
     * @return string
     * @throws \Exception
     */
    public function getPath()
    {
        $uploadFolderOrm = $this->getUploadFolder();
        $uploadPath = $uploadFolderOrm->repertoire_path;
        if (empty($uploadPath)) {
            throw new \Exception('No upload path');
        }

        return join(DIRECTORY_SEPARATOR, [
            rtrim($uploadPath, '/'),
            $this->num_list_ai_shared_list_docnum,
            $this->hash_name_ai_shared_list_docnum
        ]);
    }

    /**
     * Retourne l'url
     *
     * @return string
     */
    public function getUrl()
    {
        $urlParameters = http_build_query([
            'module' => 'ajax',
            'categ' => 'liste_lecture',
            'quoifaire' => 'show_docnum',
            'id_liste' => $this->num_list_ai_shared_list_docnum,
            'docnum_id' => $this->id_ai_shared_list_docnum
        ]);

        return './ajax.php?' . $urlParameters;
    }

    /**
     * Avant la suppression, on supprime le fichier associé
     *
     * @return boolean
     */
    protected function checkBeforeDelete()
    {
        try {
            $path = $this->getPath();
            if (is_file($path)) {
                $success = unlink($path);
            } else {
                $success = true;
            }

            if ($success) {
                $this->deleteUploadFolder();
            }
        } catch (\Exception $e) {
            $success = false;
        } finally {
            return $success;
        }
    }

    /**
     * Supprime le dossier de l'upload d'une liste de lecture s'il est vide
     *
     * @return boolean
     */
    public function deleteUploadFolder()
    {
        $uploadFolderOrm = $this->getUploadFolder();
        $uploadPath = $uploadFolderOrm->repertoire_path;
        if (empty($uploadPath)) {
            return false;
        }

        $resources = glob($uploadPath . "/*.pdf");
        if (!empty($resources)) {
            // Des documents existent, le supprime pas
            return false;
        }

        $pathDir = join(DIRECTORY_SEPARATOR, [
            rtrim($uploadPath, '/'),
            $this->num_list_ai_shared_list_docnum
        ]);

        $success = true;
        if (is_dir($pathDir)) {
            $success = rmdir($pathDir);
        }
        return $success;
    }
}

<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiApiSharedListController.php,v 1.1.2.7 2024/06/26 13:18:57 qvarin Exp $

namespace Pmb\AI\Opac\Controller;

use encoding_normalize;
use Pmb\AI\Library\Api;
use Pmb\AI\Models\AiSessionSemanticModel;
use Pmb\AI\Models\AiSharedListDocnumModel;
use Pmb\AI\Models\SharedListModel;
use Pmb\AI\Orm\AiSessionSemanticOrm;
use Pmb\AI\Orm\AiSharedListDocnumOrm;
use Pmb\AI\Orm\AiSharedListOrm;
use Pmb\Common\Helper\UrlEntities;
use Pmb\Common\Library\PDF\PDFChecker;
use Pmb\Common\Orm\OpacListeLectureOrm;
use Pmb\Common\Orm\UploadFolderOrm;
use record_datas;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AiApiSharedListController extends AiApiController
{
    public const AI_SEMANTIC_TYPE = AiSessionSemanticModel::TYPE_SHARED_LIST;

    public function __construct(object $data = null)
    {
        if (empty($data)) {
            $this->data = new \stdClass();
        } else {
            $this->data = $data;
        }

        try {
            $aiSharedListOrm = new AiSharedListOrm();
            $settings = $aiSharedListOrm->getAiSettingActive();

            if (empty($settings)) {
                throw new \Exception('No active AI settings');
            }
        } catch (\Exception $e) {
            $this->ajaxError($e->getMessage(), 500);
        }

        $this->api = new Api($settings);
    }

    /**
     * Indexe une liste partagée pour le traitement AI.
     *
     * @throws \Exception Si aucune configuration d'IA n'est trouvée
     * @return void
     */
    public function sharedlistIndexation()
    {
        global $ai_index_nb_elements;

        session_write_close();

        if (empty($this->data->id) || !is_integer($this->data->id)) {
            $this->ajaxError('No id or invalid id', 400);
        }

        if(empty($this->data->type)) {
            $this->ajaxError('No type', 400);
        }

        $response = $this->api->indexationSharedList($this->data->id, $this->data->type, $ai_index_nb_elements);

        if (false !== $response) {
            $response = encoding_normalize::json_decode($response->body);

            switch($this->data->type) {
                case 'records':
                    SharedListModel::setRecordFlagIAInList($this->data->id, $ai_index_nb_elements, 1);
                    break;
                case 'docnums':
                    AiSharedListDocnumModel::setDocnumFlag($this->data->id, $ai_index_nb_elements, 1);
                    break;
                default:
                    break;
            }

            $this->ajaxJsonResponse([
                'error' => false,
                'data' => $response->indexation
            ]);
        } else {
            $this->ajaxError('API error', 500);
        }
    }

    /**
     * Upload un document de liste partagée.
     */
    public function sharedListUploadFile()
    {
        global $idList, $msg, $ai_upload_max_size;

        $MAX_FILE_SIZE = 1024 * 1024 * intval($ai_upload_max_size);

        $idList = intval($idList);
        if (empty($idList)) {
            return $this->ajaxError('No list ID', 500);
        }

        // Validate upload folder ID
        $uploadFolderId = intval($this->api->aiSettings->settings_ai_shared_list->upload_folder);
        if (empty($uploadFolderId)) {
            return $this->ajaxError('No upload folder', 500);
        }

        // Retrieve upload folder path
        $uploadFolderOrm = UploadFolderOrm::findById($uploadFolderId);
        $uploadPath = $uploadFolderOrm->repertoire_path;
        if (empty($uploadPath)) {
            return $this->ajaxError('No upload path', 500);
        }

        $uploadDir = rtrim($uploadPath, '/') . '/' . $idList . '/';

        // Validate file upload
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return $this->ajaxError('Upload error', 500);
        }

        // File info
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = basename($_FILES['file']['name']); // Prevent path traversal
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate file type
        if (!PDFChecker::isPDF($_FILES['file']['tmp_name'])) {
            return $this->ajaxError($msg["sharedlist_upload_type_file_error"], 500);
        }

        // Validate file size
        if ($fileSize > $MAX_FILE_SIZE) {
            return $this->ajaxError(sprintf($msg["sharedlist_upload_to_large_file_error"], $ai_upload_max_size), 500);
        }

        // Generate new file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        // Ensure upload directory exists
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            return $this->ajaxError('Unable to create upload directory', 500);
        }

        // Move uploaded file
        if (!move_uploaded_file($fileTmpPath, $destPath)) {
            return $this->ajaxError('File cannot be moved', 500);
        }

        // Save file info to database
        $AiSharedListDocnum = new AiSharedListDocnumModel(0);
        $AiSharedListDocnum->nameAiSharedListDocnum = pathinfo($fileName, PATHINFO_FILENAME);
        $AiSharedListDocnum->contentAiSharedListDocnum = "";
        $AiSharedListDocnum->mimetypeAiSharedListDocnum = $fileType;
        $AiSharedListDocnum->extfileAiSharedListDocnum = $fileExtension;
        $AiSharedListDocnum->pathAiSharedListDocnum = $uploadDir;
        $AiSharedListDocnum->hashNameAiSharedListDocnum = $newFileName;
        $AiSharedListDocnum->hashBinaryAiSharedListDocnum = md5_file($destPath);
        $AiSharedListDocnum->flagAiSharedListDocnum = 0;
        $AiSharedListDocnum->numListAiSharedListDocnum = $idList;
        $AiSharedListDocnum->save();

        // Return success response
        return $this->ajaxJsonResponse([
            'error' => false,
            'data' => [
                'fileName' => $newFileName,
                'fileSize' => $fileSize,
                'fileType' => $fileType,
                'filePath' => $destPath
            ]
        ]);
    }

    /**
     * Retourne la liste des documents de liste de lecture.
     *
     * @return void
     */
    public function docnums()
    {
        session_write_close();

        if (!OpacListeLectureOrm::exist($this->data->id)) {
            $this->ajaxError('No access', 404);
        }

        if (!OpacListeLectureOrm::has_access($this->data->id, $_SESSION['id_empr_session'])) {
            $this->ajaxError('No access', 403);
        }

        $this->ajaxJsonResponse([
            'list' => AiSharedListDocnumOrm::findAllByNumList($this->data->id)
        ]);
    }

    public function removeDocnum()
    {
        session_write_close();

        if (!AiSharedListDocnumOrm::exist($this->data->id)) {
            $this->ajaxError('No access', 404);
        }

        $aiSharedListDocnum = new AiSharedListDocnumOrm($this->data->id);
        if (!OpacListeLectureOrm::has_access($aiSharedListDocnum->num_list_ai_shared_list_docnum, $_SESSION['id_empr_session'])) {
            $this->ajaxError('No access', 403);
        }

        try {
            $structure = SharedListModel::getStructureToDeleteIndexation("deleteDocnumInList", $aiSharedListDocnum->num_list_ai_shared_list_docnum, intval($this->data->id));
            $this->api->cleanElementsContainer($structure);

            $aiSharedListDocnum->delete();

            $this->ajaxJsonResponse([ 'success' => true ]);
        } catch (\Exception $e) {
            $this->ajaxError($e->getMessage(), 500);
        }
    }

    public function renameDocnum()
    {
        session_write_close();

        if (empty($this->data->id) && empty($this->data->name)) {
            $this->ajaxError('Invalid data', 422);
        }

        if (!AiSharedListDocnumOrm::exist($this->data->id)) {
            $this->ajaxError('No access', 404);
        }

        $aiSharedListDocnum = new AiSharedListDocnumOrm($this->data->id);
        if (!OpacListeLectureOrm::has_access($aiSharedListDocnum->num_list_ai_shared_list_docnum, $_SESSION['id_empr_session'])) {
            $this->ajaxError('No access', 403);
        }

        $aiSharedListDocnum->name_ai_shared_list_docnum = $this->data->name;
        $aiSharedListDocnum->save();

        $this->ajaxJsonResponse([ 'success' => true ]);
    }


    /**
     * Renvoie la liste des sessions
     *
     * @return void
     */
    public function sessionList()
    {
        session_write_close();

        if (!OpacListeLectureOrm::exist($this->data->id)) {
            $this->ajaxError('No access', 404);
        }

        if (!OpacListeLectureOrm::has_access($this->data->id, $_SESSION['id_empr_session'])) {
            $this->ajaxError('No access', 403);
        }

        $this->ajaxJsonResponse([
            'error' => false,
            'data' => AiSessionSemanticModel::findAllSessionsSharedList($this->data->id)
        ]);
    }

    /**
     * Renvoie la derniere session
     *
     * @return void
     */
    public function sessionLast()
    {
        session_write_close();

        if (!OpacListeLectureOrm::exist($this->data->id)) {
            $this->ajaxError('No access', 404);
        }

        if (!OpacListeLectureOrm::has_access($this->data->id, $_SESSION['id_empr_session'])) {
            $this->ajaxError('No access', 403);
        }

        $this->ajaxJsonResponse([
            'error' => false,
            'data' => AiSessionSemanticModel::findLastSessionsSharedList($this->data->id)
        ]);
    }


    /**
     * Permet de supprimer une session
     *
     * @return void
     */
    public function SessionDelete()
    {
        session_write_close();

        if (!OpacListeLectureOrm::exist($this->data->id)) {
            $this->ajaxError('No access', 404);
        }

        if (!OpacListeLectureOrm::has_access($this->data->id, $_SESSION['id_empr_session'])) {
            $this->ajaxError('No access', 403);
        }

        try {
            $aiSessionSemanticModel = new AiSessionSemanticOrm($this->data->idSession);
            $aiSessionSemanticModel->delete();

            $this->ajaxJsonResponse([
                'error' => false,
                'message' => '',
            ]);
        } catch (\Exception $e) {
            $this->ajaxError($e->getMessage(), 500);
        }
    }

    /**
     * Permet de renommer une session
     *
     * @return void
     */
    public function SessionRename()
    {
        session_write_close();

        if (empty($this->data->name) || !is_string($this->data->name)) {
            $this->ajaxError('No name or invalid name', 400);
        }

        if (!OpacListeLectureOrm::exist($this->data->id) || !AiSessionSemanticOrm::exist($this->data->idSession)) {
            $this->ajaxError('No access', 404);
        }

        if (!OpacListeLectureOrm::has_access($this->data->id, $_SESSION['id_empr_session'])) {
            $this->ajaxError('No access', 403);
        }

        try {
            $setting = new AiSessionSemanticOrm($this->data->idSession);
            $setting->ai_session_semantique_name = $this->data->name;
            $setting->save();
            $this->ajaxJsonResponse([
                'error' => false,
                'message' => '',
            ]);
        } catch (\Exception $e) {
            $this->ajaxError($e->getMessage());
        }
    }

    /**
     * Parse la réponse générée par l'API pour mettre des liens
     *
     * @param string $response
     * @param AiSessionSemanticModel $aiSessionModel
     * @return string
     */
    protected function parseAiResponse($response, $aiSessionModel)
    {
        return preg_replace_callback("(#(\d+))", function ($matches) use ($aiSessionModel) {
            global $charset;

            [$pattern, $num] = $matches;

            $askResult = $aiSessionModel->aiSessionSemantiqueNumObjects[$this->data->indexQuestion];

            $id = $askResult[$num - 1]["id"] ?? 0;
            if (strpos($id, 'docnum_') === 0) {
                $id = substr($id, 7);
                $id = intval($id);
                if (empty($id) || !AiSharedListDocnumOrm::exist($id)) {
                    return "";
                }

                $aiSharedListDocnumOrm = new AiSharedListDocnumOrm($id);

                $title = $aiSharedListDocnumOrm->name_ai_shared_list_docnum;
                $lien = './visionneuse.php?driver=pmb_document&lvl=afficheur&cms_type=shared_list&id=' . $id;

            } else {
                $id = intval($id);
                if (empty($id)) {
                    return "";
                }

                $notice = new record_datas($id);

                $title = $notice->get_tit1();
                $lien = UrlEntities::getOpacRealPermalink(TYPE_NOTICE, $id);
            }


            return "<a href='$lien' target='_blank' title='" . htmlentities($title, ENT_QUOTES, $charset) . "'>
                        ". htmlentities($pattern, ENT_QUOTES, $charset) ."
                    </a>";
        }, $response);
    }
}

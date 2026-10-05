<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SignatureModel.php,v 1.6 2023/05/04 09:36:37 gneveu Exp $
namespace Pmb\Digitalsignature\Models;

use Pmb\Common\Models\Model;
use Pmb\Common\Models\UploadFolderModel;
use Pmb\Digitalsignature\Orm\SignatureOrm;
use Pmb\Common\Orm\UploadFolderOrm;

class SignatureModel extends Model
{

    public $num_cert = 0;

    public $type;

    public $fields;

    public $name;

    public $uploadFolder;

    public $numCert;

    public $certificate;

    protected const ENTITY_TYPE = [
        TYPE_EXPLNUM
    ];

    protected $ormName = "\Pmb\Digitalsignature\Orm\SignatureOrm";

    public static function getSignatureList()
    {
        $list = SignatureOrm::findAll();
        return self::toArray($list);
    }

    public function fetchArrayCertificate()
    {
        $this->certificate = self::toArray($this->certificate);
    }

    public static function getFormData($id = 0)
    {
        global $pmb_digital_signature_folder_id;

        $signature = new SignatureModel($id);
        $signature->fetchAllFields();
        $signature->fetchArrayCertificate();

        return [
            'signature' => $signature,
            'uploadFolder' => $pmb_digital_signature_folder_id,
            'certificateList' => CertificateModel::getCertificateList(),
            'types' => self::getEntitiesType()
        ];
    }

    public function fetchAllFields()
    {
        if (! empty($this->fields)) {
            $this->fields = json_decode($this->fields);
        } else {
            $this->fields = array();
        }
    }

    public static function getEntitiesType()
    {
        $entitiesTab = array();

        $labels = \entities::get_entities_labels();
        foreach (self::ENTITY_TYPE as $type) {
            $entitiesTab[$type] = "";
            if (isset($labels[$type])) {
                $entitiesTab[$type] = $labels[$type];
            }
        }
        return $entitiesTab;
    }

    public static function updateSignature($data)
    {
        global $pmb_digital_signature_folder_id;

        $signatureOrm = new SignatureOrm(intval($data->id));
        $signatureOrm->name = $data->name ?? "";
        $signatureOrm->num_cert = $data->numCert ?? 0;
        $signatureOrm->upload_folder = $pmb_digital_signature_folder_id;
        $signatureOrm->fields = $data->fields ?? "";
        $signatureOrm->type = intval($data->type) ?? 0;
        $signatureOrm->save();
    }

    public static function deleteSignature($id)
    {
        $signatureOrm = new SignatureOrm($id);
        $signatureOrm->delete();
    }

    public function getUploadFolder()
    {
        global $pmb_digital_signature_folder_id;

        if ($this->uploadFolder) {
            return UploadFolderOrm::findById($pmb_digital_signature_folder_id);
        }
        return null;
    }
}
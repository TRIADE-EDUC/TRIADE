<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CertificateModel.php,v 1.3 2023/05/04 09:36:37 gneveu Exp $
namespace Pmb\Digitalsignature\Models;

use Pmb\Digitalsignature\Orm\CertificateOrm;
use Pmb\Common\Models\Model;
use Pmb\Digitalsignature\Orm\SignatureOrm;

class CertificateModel extends Model
{

    public $name = "";

    private $private_key = "";

    public $cert = "";

    public $privateKey;

    protected $ormName = "\Pmb\Digitalsignature\Orm\CertificateOrm";

    public static function getCertificateList()
    {
        $list = CertificateOrm::findAll();
        return self::toArray($list);
    }

    public static function updateCertificate($data)
    {
        $certificate = new CertificateOrm(intval($data->id));
        $certificate->name = $data->name ?? "";
        $certificate->private_key = $data->privateKey ?? "";
        $certificate->cert = $data->cert ?? "";

        $certificate->save();
    }

    public static function deleteCertificate($id)
    {
        global $msg;
        $certificate = new CertificateOrm($id);
        $tab = SignatureOrm::find("num_cert", $id);
        if (count($tab)) {
            return error_message($msg["admin_certifate_error"], $msg["admin_certifate_error_no_supp"]);
        }
        $certificate->delete();
    }
}
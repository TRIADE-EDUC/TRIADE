<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DocnumCertifiedFields.php,v 1.5 2022/05/20 08:13:16 gneveu Exp $
namespace Pmb\Digitalsignature\Models;

use Pmb\Common\Orm\EmprOrm;
use Pmb\Common\Helper\Helper;

class DocnumCertifiedFields implements CertifiedFieldsInterface
{

    public $docnumId = 0;

    public $recordId = 0;

    public function getFields($signatureFields): string
    {
        global $lang;

        $formatedFields = [];
        $tabFields = json_decode($signatureFields);

        foreach ($tabFields as $field) {
            $query = "
                    SELECT value FROM notices_fields_global_index
					WHERE id_notice = " . $this->recordId . "
					AND code_champ = " . $field->field->id . "
					AND code_ss_champ = " . $field->subField->id . "
					AND lang in ('','" . $lang . "','" . substr($lang, 0, 2) . "')
				";
            $result = pmb_mysql_query($query);
            $name = $field->field->label;
            $name .= !empty($field->subField->label) ? (" / " . $field->subField->label) : "";
            $formatedFields[$name] = [];
            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_assoc($result)) {
                    $formatedFields[$name][] = $row["value"];
                }
            }
        }
        //$formatedFields["data"] = ["signed_at" => date("Y-m-d H:i:s"), "signer" => EmprOrm::find("empr_cb", $PMBuserid)];
        return json_encode($formatedFields);
    }

    public static function getData()
    {
        $allFields = [];
        
        $type = \entities::get_string_from_const_type(TYPE_EXPLNUM);
        $facette = \facettes_controller::get_facette_search_opac_instance($type);
        $fieldsSort = $facette->fields_sort();
        foreach ($fieldsSort as $key => $field) {
            $allFields[$key] = [
                "label" => $field,
                "subfields" => $facette->array_subfields($key)
            ];
        }

        return $allFields;
    }

    public function setRecordId($RecordId)
    {
        $this->recordId = $RecordId;
    }

    public function setDocnumId($DocnumId)
    {
        $this->docnumId = $DocnumId;
    }
}
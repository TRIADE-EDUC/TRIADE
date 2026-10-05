<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PriceTypeModel.php,v 1.18.2.1 2024/07/31 08:43:58 gneveu Exp $
namespace Pmb\Animations\Models;

use Pmb\Animations\Orm\PriceTypeOrm;
use Pmb\Common\Models\Model;
use Pmb\Common\Models\CustomFieldModel;
use Pmb\Animations\Orm\PriceOrm;

class PriceTypeModel extends Model
{

    protected $ormName = "\Pmb\Animations\Orm\PriceTypeOrm";

    public $idPriceType;

    public $name;

    public $defaultValue;

    public $modeEdition;

    public $prices;

    public $customFields;

    public static function getPricesTypeList()
    {
        $pricesTypesList = PriceTypeOrm::findAll();

        foreach ($pricesTypesList as $key => $priceType) {
            $pt = new PriceTypeModel($priceType->id_price_type);
            $pt->fetchCustomFields();
            $pricesTypesList[$key] = $pt;
        }

        return self::toArray($pricesTypesList);
    }

    public static function getPriceType(int $id)
    {
        $priceType = new PriceTypeOrm($id);
        $priceType->customFields = self::fetchCustomFields();
        return $priceType->toArray();
    }

    public static function deletePriceType(int $id)
    {
        $priceType = PriceTypeOrm::findById($id);
        $pricesList = PriceOrm::find('num_price_type', $id);
        if (! empty($pricesList)) {
            foreach ($pricesList as $price) {
                $p = new PriceOrm($price->id_price);
                $p->delete();
            }
        }
        $priceType->delete();
    }

    public static function addPriceType(object $data)
    {
        $priceType = new PriceTypeOrm();
        if (empty($data->name) && empty($data->defaultValue)) {
            return false;
        }
        $priceType->name = $data->name;
        $priceType->default_value = $data->defaultValue;

        $priceType->save();

        if (! empty($data->customFields)) {
            CustomFieldModel::updateCustomFields($data->customFields, $priceType->id_price_type, 'anim_price_type');
        }

        return $priceType->toArray();
    }

    public static function updatePriceType(int $id, object $data)
    {
        $priceType = new PriceTypeOrm($id);
        if (! empty($data->name)) {
            $priceType->name = $data->name;
        }
        if (! empty($data->defaultValue)) {
            $priceType->default_value = $data->defaultValue;
        }

        if (! empty($data->customFields)) {
            CustomFieldModel::updateCustomFields($data->customFields, $id, 'anim_price_type');
        }

        $priceType->save();
    }

    public function fetchCustomFields()
    {
        if (! empty($this->customFields)) {
            return $this->customFields;
        }
        $this->customFields = CustomFieldModel::getAllCustomsFieldPriceType('anim_price_type', $this->id);
        return $this->customFields;
    }

    public function getEditAddData()
    {
        $this->fetchCustomFields();
        return $this;
    }

    public static function checkPriceTypeUse($id)
    {
        if (empty(PriceOrm::find('num_price_type', $id))) {
            return false;
        }
        return true;
    }
}
<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PriceModel.php,v 1.23.4.2 2025/03/24 12:40:04 jparis Exp $
namespace Pmb\Animations\Models;

use Pmb\Common\Models\Model;
use Pmb\Animations\Orm\PriceOrm;
use Pmb\Common\Models\CustomFieldModel;

class PriceModel extends Model
{

    public const RETURN_PRICE = 0;

    public const RETURN_ID = 1;

    protected $ormName = "\Pmb\Animations\Orm\PriceOrm";

    public $idPrice;

    public $name;

    public $value;

    public $numPrice;

    public $numAnimation;

    public $priceType;

    public $numPriceType;

    public static function updatePrice(int $id = 0, ?object $priceUpdate = null): array
    {
        if (! isset($priceUpdate)) {
            $priceUpdate = new \stdClass();
        }

        $price = new PriceOrm($id);

        if (! empty($priceUpdate->numPriceType)) {
            $price->num_price_type = $priceUpdate->numPriceType;
        } else if (! empty($priceUpdate->priceType->id_price_type)) {
            $price->num_price_type = $priceUpdate->priceType->id_price_type;
        }

        if (! empty($price->customFields)) {
            CustomFieldModel::updateCustomFields($priceUpdate->customFields, $id, 'anim_price_type');
        }
        $price->value = floatval($priceUpdate->value);
        $price->name = $priceUpdate->name;
        $price->num_animation = $priceUpdate->numAnimation;

        $price->save();

        // return self::toArray($price);
        return [
            $price
        ];
    }

    public static function updatePriceList(array $prices, int $returnableItem = self::RETURN_PRICE, int $id_animation = 0): array
    {
        $pricesList = array();

        if (empty($prices)) {
            return $pricesList;
        }

        $animationPrices = PriceOrm::find("num_animation", $id_animation);

        foreach ($prices as $price) {
            if (empty($price->value) && empty($price->defaultValue) && empty($price->name)) {
                continue;
            }

            if (empty($price->numAnimation)) {
                $price->numAnimation = $id_animation;
            }

            $price = self::updatePrice(intval($price->id), $price);

            switch ($returnableItem) {
                case self::RETURN_ID:
                    $pricesList[] = $price[PriceOrm::$idTableName];
                    break;

                default:
                case self::RETURN_PRICE:
                    $pricesList[] = $price;
                    break;
            }
        }

        foreach ($animationPrices as $animationPrice) {
            $toDelete = true;
            foreach ($pricesList as $price) {
                if ($price[0]->id_price == $animationPrice->id_price) {
                    $toDelete = false;
                }
            }
            if ($toDelete) {
                $animationPrice->delete();
            }
        }

        return $pricesList;
    }

    public function fetchAnimation()
    {
        if (! empty($this->animation)) {
            return $this->animation;
        }
        $this->animation = null;
        if (! empty($this->numAnimation)) {
            $this->animation = new AnimationModel($this->numAnimation);
        }
        return $this->animation;
    }

    public function fetchPriceType()
    {
        if (! empty($this->priceType)) {
            return self::toArray($this->priceType);
        }
        $this->priceType = null;
        if (! empty($this->numPriceType)) {
            $this->priceType = new PriceTypeModel($this->numPriceType);
            $this->priceType->fetchCustomFields();
        }
        return self::toArray($this->priceType);
    }

    public static function getPrices(int $id_animation, bool $duplicate = false)
    {
        $prices_tab = [];
        $price_ORM = new PriceOrm();
        $prices = $price_ORM->find('num_animation', $id_animation);

        foreach ($prices as $price) {
            $p = new PriceModel($price->id_price);
            $p->fetchPriceType();
            if ($duplicate) {
                $p->id = 0;
                $p->idPrice = 0;
                $p->numAnimation = 0;
            }
            $prices_tab[] = $p;
        }
        return $prices_tab;
    }

    public static function deleteAnimationPrices(int $id)
    {
        $pricesList = PriceOrm::find("num_animation", $id);
        foreach ($pricesList as $price) {
            $price = new PriceOrm($price->id_price);
            $price->delete();
        }
    }

    public static function addPriceRepeatAnimation($prices, $id_animation)
    {
        foreach ($prices as $price) {
            $priceORM = new PriceOrm();
            $priceORM->num_animation = $id_animation;
            $priceORM->name = $price->name;
            $priceORM->value = $price->value;
            $priceORM->num_price_type = $price->numPriceType;
            $priceORM->save();
        }
    }
}
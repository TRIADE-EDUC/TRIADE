<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RegistredPersonModel.php,v 1.35.2.1.2.1 2025/03/07 10:44:56 jparis Exp $
namespace Pmb\Animations\Models;

use Pmb\Common\Models\Model;
use Pmb\Animations\Orm\RegistredPersonOrm;
use Pmb\Common\Models\CustomFieldModel;
use Pmb\Animations\Orm\PriceOrm;
use Pmb\Animations\Orm\PriceTypeCustomFieldValueOrm;
use Pmb\Common\Models\EmprModel;
use Pmb\Common\Helper\GlobalContext;

class RegistredPersonModel extends Model
{

    protected $ormName = "\Pmb\Animations\Orm\RegistredPersonOrm";

    public $idPerson;

    public $personName;

    public $numEmpr;

    public $numPrice;

    public $numRegistration;

    public $price;

    public $personCustomsFields;

    public $name;

    public $barcode;

    public $registration;

    public $unsubscribeLink;
    
    public $is_contact;

    public static function getRegistredPersons()
    {
        $registredPersons = RegistredPersonOrm::findAll();
        return self::toArray($registredPersons);
    }

    public static function getRegistredPerson(int $id)
    {
        $registredPerson = new RegistredPersonOrm($id);
        return $registredPerson->toArray();
    }

    public static function deleteRegistredPerson(int $id)
    {
        $registredPerson = new RegistredPersonOrm($id);
        PriceTypeCustomFieldValueOrm::deleteWhere("anim_price_type_custom_origine", $registredPerson->id_person);
        $registredPerson->delete();
    }

    public static function deleteRegistrationRegistredPerson(int $registrationId)
    {
        $registredPersonList = RegistredPersonOrm::find("num_registration", $registrationId);
        foreach ($registredPersonList as $registredPerson) {
            self::deleteRegistredPerson($registredPerson->id_person);
        }
    }

    public static function addRegistredPerson(object $data)
    {
        if (empty($data->name)) {
            return false;
        }

        $registredPerson = new RegistredPersonOrm(0);
        $registredPerson->num_empr = $data->numEmpr ?? 0;
        $registredPerson->num_price = $data->numPrice;
        $registredPerson->person_name = $data->name;
        $registredPerson->num_registration = $data->numRegistration;
        $registredPerson->save();
        if (! empty($data->personCustomsFields)) {
            $price = new PriceOrm($data->numPrice);
            CustomFieldModel::updateCustomFieldsPriceType($data->personCustomsFields, $registredPerson->id_person, $price->num_price_type);
        }
        return $registredPerson->toArray();
    }

    public static function updateRegistredPerson(int $id, object $data)
    {
        $registredPerson = new RegistredPersonOrm($id);

        if (! empty($data->numEmpr)) {
            $registredPerson->num_empr = $data->numEmpr;
        }
        if (! empty($data->numPrice)) {
            $registredPerson->num_price = $data->numPrice;
        }
        if (! empty($data->numRegistration)) {
            $registredPerson->num_registration = $data->numRegistration;
        }
        $registredPerson->save();
    }

    public function fetchEmpr()
    {
        if (! empty($this->empr)) {
            return $this->empr;
        }
        $this->empr = null;
        if (! empty($this->numEmpr)) {
            $this->empr = new EmprModel($this->numEmpr);
        }
        return $this->empr;
    }

    public function fetchPrice()
    {
        if (! empty($this->price)) {
            return $this->price;
        }
        $this->price = null;
        if (! empty($this->numPrice)) {
            $this->price = new PriceModel($this->numPrice);
        }
        return $this->price;
    }

    public function fetchRegistration()
    {
        if (! empty($this->registration)) {
            return $this->registration;
        }
        $this->registration = null;
        if (! empty($this->numRegistration)) {
            $this->registration = new RegistrationModel($this->numRegistration);
        }
        return $this->registration;
    }

    public static function getListPersonFromRegistration(int $idRegistration)
    {
        $registrationListPerson = RegistredPersonOrm::find('num_registration', $idRegistration);
        
        $intances = array();
        foreach ($registrationListPerson as $registredPerson) {
            $person = new RegistredPersonModel($registredPerson->id_person);
            $empr = new EmprModel($person->numEmpr);
            $person->fetchPrice();
            $person->price->fetchPriceType();
            $person->fetchCustomFields();
            $person->name = $person->personName;
            $person->barcode = $empr->emprCb ?? "";
            $intances[] = $person;
            $person->getUnsubscribeLink();
            $person->is_contact = false;
            if ($person->numEmpr === $empr->idEmpr) {
                $person->is_contact = true;
            }
        }
        return $intances;
    }

    public function fetchCustomFields()
    {
        $personCustomFieldList = PriceTypeCustomFieldValueOrm::find('anim_price_type_custom_origine', $this->idPerson);

        $customFieldList = array();
        if (isset($this->price->priceType)) {
            $customFieldList = $this->price->priceType->fetchCustomFields();
        }

        $this->personCustomsFields = array();
        $length = count($customFieldList);
        for ($i = 0; $i < $length; $i ++) {
            $j = 0;
            foreach ($personCustomFieldList as $personCustomField) {
                if ($customFieldList[$i]['customField']['id'] == $personCustomField->champ) {
                    $value = 'anim_price_type_custom_' . $customFieldList[$i]['customField']['datatype'];
                    $customFieldList[$i]['customValues'][$j]['value'] = $personCustomField->$value;
                    $j ++;
                }
            }
            $this->personCustomsFields[] = $customFieldList[$i];
        }
        return $this->personCustomsFields;
    }

    public static function getRegistredPersonsByEmpr($emprId)
    {
        $registredPersons = RegistredPersonOrm::find("num_empr", $emprId);
        return self::toArray($registredPersons);
    }

    public static function getListRegistredPersons($idRegistration)
    {
        // On récupere la liste d'inscrit
        $registredPersons = self::getListPersonFromRegistration($idRegistration);

        // On met en forme la reponse
        $listRegistredPersons = "";
        foreach ($registredPersons as $person) {

            switch ($person->fetchRegistration()->numRegistrationStatus) {
                case RegistrationModel::WAITING_LIST:
                    $listRegistredPersons .= sprintf(GlobalContext::msg("animation_mail_registration_waiting"), $person->name);
                    break;

                case RegistrationModel::PENDING_VALIDATION:
                    $listRegistredPersons .= sprintf(GlobalContext::msg("animation_mail_registration_pending_validation"), $person->name);
                    break;

                case RegistrationModel::VALIDATED:
                    $listRegistredPersons .= sprintf(GlobalContext::msg("animation_mail_registration_validate"), $person->name);
                    break;

                default:
                    throw new \Exception("Unknown RegistrationModel::numRegistrationStatus");
                    break;
            }
        }
        return $listRegistredPersons;
    }

    public function getUnsubscribeLink()
    {
        global $opac_url_base;

        if (! empty($this->unsubscribeLink)) {
            return $this->unsubscribeLink;
        }

        if (empty($this->registration)) {
            $this->fetchRegistration();
        }

        $this->unsubscribeLink = $opac_url_base . "index.php?lvl=registration&action=delete&id_registration=" . intval($this->registration->idRegistration);
        $this->unsubscribeLink .= "&id_person=" . intval($this->idPerson);
        if (empty($this->registration->hash)) {
            $this->registration->generateHash();
        }
        $this->unsubscribeLink .= "&hash=" . $this->registration->hash;

        return $this->unsubscribeLink;
    }

    public static function getRegistredPersonByEmprAndRegistration($emprId, $registrationId)
    {
        $registredPerson = RegistredPersonOrm::finds([
            "num_empr" => $emprId,
            "num_registration" => $registrationId
        ]);

        if (! empty($registredPerson[0])) {
            return new RegistredPersonModel($registredPerson[0]->id_person);
        }

        return new RegistredPersonModel(0);
    }
}
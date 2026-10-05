<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AnimationModel.php,v 1.132.2.7.2.4 2025/06/04 12:39:32 qvarin Exp $
namespace Pmb\Animations\Models;

use encoding_normalize;
use Pmb\Animations\Event\AnimationsEvent;
use Pmb\Animations\Orm\AnimationCalendarOrm;
use Pmb\Animations\Orm\AnimationOrm;
use Pmb\Animations\Orm\AnimationTypesOrm;
use Pmb\Animations\Orm\EventOrm;
use Pmb\Animations\Orm\MailingAnimationOrm;
use Pmb\Animations\Orm\MailingTypeOrm;
use Pmb\Animations\Orm\RegistrationOrm;
use Pmb\Animations\Orm\RegistredPersonOrm;
use Pmb\Autorities\Models\CategoryModel;
use Pmb\Autorities\Models\ConceptModel;
use Pmb\Common\Helper\GlobalContext;
use Pmb\Common\Helper\Helper;
use Pmb\Common\Library\Image\Image;
use Pmb\Common\Models\CustomFieldModel;
use Pmb\Common\Models\DocsLocationModel;
use Pmb\Common\Models\Model;
use Pmb\Common\Models\UploadFolderModel;
use Pmb\Common\Models\EmprModel;
use Pmb\Common\Models\MailtplModel;

class AnimationModel extends Model
{

    public $idAnimation;

    public $name;

    public $comment;

    public $description;

    public $globalQuota;

    public $allowWaitingList;

    public $internetQuota;

    public $autoRegistration;

    public $expirationDelay;

    public $registrationRequired;

    public $numStatus;

    public $numEvent;

    public $numParent;

    public $numCart;

    public $numType;

    public $numCalendar;

    public $unique_registration;

    public $logo;

    public $parent;

    public $hasChildrens;

    public $customFields;

    public $prices;

    public $location;

    public $mailing;

    public $mailingType;

    public $concepts;

    public $categories;

    public $gotCustomFieldsValues;

    public $calendar;

    public $internetAvailable;

    public $hasQuotas;

    public $allQuotas;

    public $type;

    public $status;

    public $event;

    public $animationFormatQuotas;

    public $animationFormatDate;

    public $uniqueRegistration;

    public $customChamp;

    public $childrens;

    protected $ormName = "\Pmb\Animations\Orm\AnimationOrm";

    /**
     * pagination
     *
     * @param integer $start (default 0)
     * @param integer $max (default 20)
     * @param boolean $basicInfos  (default false)
     * @return array
     */
    public static function pagination(int $start = 0, int $max = 20, bool $basicInfos = false)
    {
        $pagination = AnimationOrm::pagination($start, $max);
        foreach ($pagination['instances'] as $key => $animation) {
            $anim = new AnimationModel($animation->id_animation);
            $evt = $anim->fetchEvent();

            if (! $evt->duringDay) {
                $today = new \DateTime();
                $evenementDate = new \DateTime($evt->endDate);

                if ($today > $evenementDate) {
                    unset($animationsList[$key]);
                    continue;
                }
            }
            if (! empty($evt)) {
                $anim->event = $anim->getFormatDate($evt);
            }

            if ($basicInfos == false) {
                $anim->fetchParent();
                $anim->fetchStatus();
                $anim->fetchType();
                $anim->fetchLocation(true);
                $anim->fetchQuotas();
                $anim->checkChildrens();
                $anim->fetchCalendar();
                $anim->prices = PriceModel::getPrices($animation->id_animation);
            }

            $animationsList[$key] = $anim;
        }

        usort($animationsList, function ($a, $b) {

            if (empty($a->event->rawStartDate)) {
                $a->event = $a->getFormatDate($a->event);
            }
            if (empty($b->event->rawStartDate)) {
                $b->event = $b->getFormatDate($b->event);
            }

            if ($a->event->rawStartDate == $b->event->rawStartDate) {
                return 0;
            }
            return ($a->event->rawStartDate < $b->event->rawStartDate) ? - 1 : 1;
        });

        $pagination['instances'] = array_values($animationsList);
        return $pagination;
    }

    public static function getAnimationsList($basicInfos = false)
    {
        $animationsList = AnimationOrm::findAll();

        foreach ($animationsList as $key => $animation) {
            $anim = new AnimationModel($animation->id_animation);
            $evt = $anim->fetchEvent();

            if (! $evt->duringDay) {
                $today = new \DateTime();
                $evenementDate = new \DateTime($evt->endDate);

                if ($today > $evenementDate) {
                    unset($animationsList[$key]);
                    continue;
                }
            }
            if (! empty($evt)) {
                $anim->event = $anim->getFormatDate($evt);
            }

            if ($basicInfos == false) {
                $anim->fetchParent();
                $anim->fetchStatus();
                $anim->fetchType();
                $anim->fetchLocation(true);
                $anim->fetchQuotas();
                $anim->checkChildrens();
                $anim->fetchCalendar();
                $anim->prices = PriceModel::getPrices($animation->id_animation);
            }

            $animationsList[$key] = $anim;
        }

        usort($animationsList, function ($a, $b) {

            if (empty($a->event->rawStartDate)) {
                $a->event = $a->getFormatDate($a->event);
            }
            if (empty($b->event->rawStartDate)) {
                $b->event = $b->getFormatDate($b->event);
            }

            if ($a->event->rawStartDate == $b->event->rawStartDate) {
                return 0;
            }
            return ($a->event->rawStartDate < $b->event->rawStartDate) ? - 1 : 1;
        });

        return array_values($animationsList);
    }

    public static function getAnimation(int $id)
    {
        $anim = new AnimationModel($id);

        $evt = $anim->fetchEvent();
        if ($evt) {
            $anim->event = $anim->getFormatDate($evt);
        }

        $anim->fetchParent();
        $anim->fetchStatus();
        $anim->fetchType();
        $anim->fetchLocation(true);
        $anim->fetchQuotas();
        $anim->fetchCalendar();
        $anim->checkChildrens();

        $anim->prices = PriceModel::getPrices($id);
        return $anim;
    }

    public static function deleteAnimation(int $id, bool $delChildrens = false)
    {
        // Revoir la suppression d'un animation
        // Si on la supprime ou si on la passe dans un statut special
        $results = AnimationOrm::find('num_parent', $id);

        if (! empty($results) && $delChildrens == false) {
            return false;
        }

        global $class_path, $animations_active_image_cache;

        require_once ($class_path . '/event/events_handler.class.php');
        $event = new AnimationsEvent("animations", "delete");
        $evth = \events_handler::get_instance();
        $event->set_animation_id($id);
        $evth->send($event);

        $animation = new AnimationOrm($id);

        if ($animation->num_event) {
            $evt = new EventOrm($animation->num_event);
            $dateEndAnimation = new \DateTime($evt->start_date);
            if ("0000-00-00 00:00:00" !== $evt->end_date && !$evt->during_day) {
                $dateEndAnimation = new \DateTime($evt->end_date);
            }
            $evt->delete();
        }

        // Si la date de l'animation est passe on n'envoie pas de notification
        $date = new \DateTime();

        if ($dateEndAnimation > $date) {
            foreach(RegistrationModel::getRegistrations($id, false) as $registration) {
                $maillingTypeOrm = MailingTypeOrm::find("periodicity", MailingTypeModel::MAILING_ANNULATION);
                if (!empty($maillingTypeOrm) && !empty($maillingTypeOrm[0])) {
                    $template = MailtplModel::getMailtpl($maillingTypeOrm[0]->num_template);
                    $temp = array();

                    MailingAnimationModel::sendMail(
                        [$registration],
                        $registration->numAnimation,
                        $template,
                        $temp,
                        $maillingTypeOrm[0]->num_sender,
                        RegistrationModel::generateICal($registration, true),
                    );
                }
            }
        }

        if ($delChildrens) {
            $childrens = AnimationOrm::find('num_parent', $animation->id_animation);
            if (! empty($childrens) && count($childrens)) {
                foreach ($childrens as $animation_children) {
                    AnimationModel::deleteAnimation($animation_children->id_animation, $delChildrens);
                }
            }
        }

        AnimationModel::deleteLocationList($animation->id_animation);
        PriceModel::deleteAnimationPrices($animation->id_animation);
        RegistrationModel::deleteAnimationRegistration($animation->id_animation);

        if($animations_active_image_cache) {
            AnimationModel::cleanImgCache($id);
        }

        $animation->delete();

        if ($event->get_article_id()) {
            return $event->has_errors() ? false : true;
        }
    }

    public static function addAnimation(object $data)
    {
        global $thesaurus_concepts_active;

        $animation = new AnimationOrm();
        if (empty($data) || empty($data->name)) {
            return false;
        }

        $animation->name = $data->name;
        $animation->global_quota = $data->globalQuota;
        $animation->internet_quota = $data->internetQuota;
        $animation->allow_waiting_list = $data->allowWaitingList;
        $animation->auto_registration = $data->autoRegistration;

        $animation->expiration_delay = 1;
        $animation->registration_required = false;

        $animation->num_status = $data->numStatus;
        $animation->num_parent = $data->numParent;
        $animation->num_cart = $data->numCart;
        $animation->comment = $data->comment;
        $animation->description = $data->description;

        $data->numType = intval($data->numType);
        if (! AnimationTypesOrm::exist($data->numType)) {
            $data->numType = AnimationOrm::DEFAULT_TYPE;
        }
        $animation->num_type = $data->numType;

        $data->numCalendar = intval($data->numCalendar);
        if (! AnimationCalendarOrm::exist($data->numCalendar)) {
            $data->numCalendar = AnimationOrm::DEFAULT_CALENDAR;
        }
        $animation->num_calendar = $data->numCalendar;

        $animation->num_event = EventModel::addEvent($data);
        if (0 != $data->logo->uploadFolder && "" != $data->logo->filename) {
			if(0 != $data->idDuplicate) {
		        $animation->logo = static::setLogoFromDuplicate($data);
		    } else {
	            $animation->logo = json_encode([
	                "uploadFolder" => intval($data->logo->uploadFolder),
	                "filename" => $data->logo->filename,
	                "filePath" => AnimationModel::getPathAnimationLogo($data->image, intval($data->logo->uploadFolder)),
	                "alt" => $data->logo->alt
	            ]);
		    }
        }

        $animation->unique_registration = $data->uniqueRegistration;

        $animation->save();

        if (! empty($data->categories)) {
            CategoryModel::updateAnimationCategories($data->categories, $animation->id_animation);
        }

        if (! empty($data->concepts) && $thesaurus_concepts_active) {
            ConceptModel::updateAnimationConcepts($data->concepts, $animation->id_animation);
        }

        if (! empty($data->location)) {
            AnimationModel::insertLocationList($data->location, $animation->id_animation);
        }

        if (! empty($data->mailingType)) {
            AnimationModel::insertMailingTypeList($data->mailingType, $animation->id_animation);
        }

        if (! empty($data->prices)) {
            PriceModel::updatePriceList($data->prices, 0, $animation->id_animation);
        }

        if (! empty($data->customFields)) {
            CustomFieldModel::updateCustomFields($data->customFields, $animation->id_animation, 'anim_animation');
        }
        return $animation->toArray();
    }

    public static function updateAnimation(int $id, object $data)
    {
        global $animations_active_image_cache;

        $animation = new AnimationOrm($id);
        if (! empty($data->name)) {
            $animation->name = $data->name;
        }

        if (isset($data->globalQuota)) {
            $animation->global_quota = $data->globalQuota;
        }

        if (isset($data->allowWaitingList)) {
            $animation->allow_waiting_list = $data->allowWaitingList;
        }

        if (isset($data->autoRegistration)) {
            $animation->auto_registration = $data->autoRegistration;
        }

        $interneQuotas = 0;
        if (isset($data->internetQuota)) {
            $animation->internet_quota = $data->internetQuota;
            $interneQuotas = $data->internetQuota;
        }

        if (isset($data->expirationDelay)) {
            $animation->expiration_delay = $data->expirationDelay;
        }

        if (isset($data->registrationRequired)) {
            $animation->registration_required = $data->registrationRequired;
        }

        if (isset($data->numStatus)) {
            $animation->num_status = $data->numStatus;
        }

        if (isset($data->numType)) {
            $data->numType = intval($data->numType);
            if (! AnimationTypesOrm::exist($data->numType)) {
                $data->numType = AnimationOrm::DEFAULT_TYPE;
            }
            $animation->num_type = $data->numType;
        }

        if (isset($data->numCalendar)) {
            $data->numCalendar = intval($data->numCalendar);
            if (! AnimationCalendarOrm::exist($data->numCalendar)) {
                $data->numCalendar = AnimationOrm::DEFAULT_CALENDAR;
            }
            $animation->num_calendar = $data->numCalendar;
        }

        if (isset($data->numParent)) {
            $animation->num_parent = $data->numParent;
        }

        if (isset($data->numCart)) {
            $animation->num_cart = $data->numCart;
        }

        if (isset($data->comment)) {
            $animation->comment = $data->comment;
        }

        if (isset($data->numParent)) {
            $animation->description = $data->description;
        }

        if (isset($data->numEvent)) {
            $animation->num_event = EventModel::updateEvent($data->numEvent, $data);
            foreach(RegistrationModel::getRegistrations($animation->id_animation, false) as $registration) {

                $maillingTypeOrm = MailingTypeOrm::find("periodicity", MailingTypeModel::MAILING_REGISTRATION);
                if (! empty($maillingTypeOrm) && ! empty($maillingTypeOrm[0])) {
                    $template = MailtplModel::getMailtpl($maillingTypeOrm[0]->num_template);
                    $temp = array();

                    MailingAnimationModel::sendMail(
                        [ $registration ],
                        $registration->numAnimation,
                        $template,
                        $temp,
                        $maillingTypeOrm[0]->num_sender,
                        RegistrationModel::generateICal($registration),
                    );
                }
            }
        }

        if (isset($data->categories)) {
            CategoryModel::updateAnimationCategories($data->categories, $id);
        }

        if (isset($data->concepts)) {
            ConceptModel::updateAnimationConcepts($data->concepts, $id);
        }

        if (! empty($data->prices)) {
            PriceModel::updatePriceList($data->prices, 0, $id);
        }

        if (! empty($data->location)) {
            AnimationModel::insertLocationList($data->location, $id);
        }

        if (! empty($data->mailingType)) {
            AnimationModel::insertMailingTypeList($data->mailingType, $id);
        }

        if (! empty($data->customFields)) {
            CustomFieldModel::updateCustomFields($data->customFields, $id, 'anim_animation');
        }

        if (0 != $data->logo->uploadFolder && "" != $data->logo->filename) {
            if (isset($data->image) && "undefined" !== $data->image) {
                $filePath = AnimationModel::getPathAnimationLogo($data->image, intval($data->logo->uploadFolder));

                if($animations_active_image_cache) {
                    AnimationModel::cleanImgCache($id);
                }

            } else {
                $filePath = $data->logo->filePath;
            }

            $animation->logo = json_encode([
                "uploadFolder" => intval($data->logo->uploadFolder),
                "filename" => $data->logo->filename,
                "filePath" => $filePath,
                "alt" => $data->logo->alt
            ]);
        }

        $animation->unique_registration = $data->uniqueRegistration;

        $animation->save();

        // On recupere la liste des inscriptions en attente pour l'animation
        $waitingList = RegistrationModel::getRegistrationsWaitingList($animation->id_animation);
        foreach ($waitingList as $waitingRegistration) {
            $registration = new RegistrationOrm($waitingRegistration->idRegistration);
            $interneQuotas = RegistrationModel::reviewAnimationRegistration($registration, $interneQuotas);
            if (! $interneQuotas) {
                return false;
            }
        }

        return $animation->toArray();
    }

    public static function getGlobalsSearch($searchFields)
    {
        $searchGlobals = [];
        $dateDone = false;
        foreach ($searchFields as $searchField => $searchValue) {
            if (is_array($searchValue)) {
                if (empty($searchValue[0])) {
                    continue;
                }
            } elseif (empty($searchValue)) {
                continue;
            }
            switch ($searchField) {
                case 'tlc':
                    $searchGlobals['f_1'] = [
                        'BOOLEAN' => $searchValue
                    ];
                    break;
                case 'dateStart':
                case 'dateEnd':
                    if (! $dateDone) {
                        if ($searchFields->dateEnd != '' && $searchFields->dateStart && ! $searchFields->inputSearchExactDate) {
                            $searchGlobals['f_2'] = [
                                'BETWEEN' => [
                                    $searchValue,
                                    $searchFields->dateEnd
                                ]
                            ];
                        } else {
                            if ($searchFields->inputSearchExactDate) {
                                $searchGlobals['f_2'] = [
                                    'EQ' => $searchValue
                                ];
                            } elseif ($searchFields->dateEnd) {
                                $searchGlobals['f_2'] = [
                                    'LT' => $searchFields->dateEnd
                                ];
                            } else {
                                $searchGlobals['f_2'] = [
                                    'GT' => $searchValue
                                ];
                            }
                        }
                        $dateDone = true;
                    }
                    break;
                default:
                    break;
            }
        }
        return $searchGlobals;
    }

    public static function getFormData($id = 0)
    {
        global $pmb_gestion_devise, $thesaurus_concepts_active;
        global $deflt_animation_calendar, $deflt_animation_waiting_list, $deflt_animation_automatic_registration, $deflt_animation_communication_type, $deflt_animation_unique_registration;

        return [
            'locations' => DocsLocationModel::getLocationList(),
            'mailingTypes' => MailingTypeModel::getMailingsTypeListForAnimation(),
            'status' => AnimationStatusModel::getAnimationStatusList(),
            'types' => AnimationTypesModel::getAnimationTypesList(),
            'calendar' => AnimationCalendarModel::getAnimationCalendarList(),
            'priceType' => PriceTypeModel::getPricesTypeList(),
            'prefColorUser' => $deflt_animation_calendar,
            'prefWaitingListUser' => $deflt_animation_waiting_list,
            'prefAutoRegistrationUser' => $deflt_animation_automatic_registration,
            'prefCommunicationTypeUser' => $deflt_animation_communication_type,
            'prefUniqueRegistrationUser' => $deflt_animation_unique_registration,
            'uploadFolder' => UploadFolderModel::getUploadForlderList(),
            'urlBase' => GlobalContext::get("url_base"),
            'img' => [
                'plus' => get_url_icon('plus.gif'),
                'minus' => get_url_icon('minus.gif'),
                'expandAll' => get_url_icon('expand_all'),
                'collapseAll' => get_url_icon('collapse_all'),
                'tick' => get_url_icon('tick.gif'),
                'error' => get_url_icon('error.png'),
                'patience' => get_url_icon('patience.gif'),
                'sort' => get_url_icon('sort.png'),
                'iconeDragNotice' => get_url_icon('icone_drag_notice.png')
            ],
            'globals' => [
                'conceptsActive' => html_entity_decode($thesaurus_concepts_active),
                'pmbDevise' => html_entity_decode($pmb_gestion_devise)
            ]
        ];
    }

    public function fetchMailing(bool $duplicate = false)
    {
        if (! empty($this->mailing)) {
            return $this->mailing;
        }
        $this->mailing = MailingAnimationModel::getMailings($this->id, $duplicate);
        return $this->mailing;
    }

    public function fetchParent()
    {
        if (! empty($this->parent)) {
            return $this->parent;
        }

        $num_parent = 0;
        if (! empty($this->numParent)) {
            $num_parent = $this->numParent;
        }

        $this->parent = new AnimationModel($num_parent);
        return $this->parent;
    }

    public function fetchPrices(bool $duplicate = false)
    {
        if (! empty($this->prices)) {
            return $this->prices;
        }
        $this->prices = PriceModel::getPrices($this->id, $duplicate);
        return $this->prices;
    }

    public function fetchEvent()
    {
        if (! empty($this->event)) {
            return $this->event;
        }

        $num_event = 0;
        if (! empty($this->numEvent)) {
            $num_event = $this->numEvent;
        }

        $this->event = new EventModel($num_event);
        return $this->event;
    }

    public function fetchStatus()
    {
        if (! empty($this->status)) {
            return $this->status;
        }

        $num_status = 0;
        if (! empty($this->numStatus)) {
            $num_status = $this->numStatus;
        }

        $this->status = new AnimationStatusModel($num_status);
        return $this->status;
    }

    public function fetchType()
    {
        if (! empty($this->type)) {
            return $this->type;
        }

        $num_type = AnimationOrm::DEFAULT_TYPE;
        if (! empty($this->numType) && AnimationTypesOrm::exist(intval($this->numType))) {
            $num_type = $this->numType;
        }

        $this->type = AnimationTypesModel::toArray(new AnimationTypesModel($num_type));
        return $this->type;
    }

    public function fetchCalendar()
    {
        if (! empty($this->calendar)) {
            return $this->calendar;
        }

        $num_calendar = AnimationOrm::DEFAULT_CALENDAR;
        if (! empty($this->numCalendar) && AnimationCalendarOrm::exist(intval($this->numCalendar))) {
            $num_calendar = $this->numCalendar;
        }

        $this->calendar = new AnimationCalendarModel($num_calendar);
        return $this->calendar;
    }

    public function fetchLocation($withInformations = false)
    {
        if (! empty($this->location)) {
            return $this->location;
        }

        $this->location = [];
        $query = "SELECT * FROM anim_animation_locations WHERE num_animation = $this->id";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                if (! empty($row->num_location)) {
                    if ($withInformations) {
                        $loc = new DocsLocationModel($row->num_location);
                        $this->location[] = self::toArray($loc);
                    } else {
                        $this->location[] = $row->num_location;
                    }
                }
            }
        }
        return $this->location;
    }

    public function fetchMailingType($withInformations = false)
    {
        if (empty($this->mailing)) {
            $this->fetchMailing();
        }

        if (empty($this->mailingType)) {
            $this->mailingType = array();
            if (! empty($this->mailing)) {
                foreach ($this->mailing as $mailing) {
                    if ($withInformations) {
                        $this->mailingType[] = new MailingTypeModel($mailing->numMailingType);
                    } else {
                        $this->mailingType[] = $mailing->numMailingType;
                    }
                }
            }
        }

        return $this->mailingType;
    }

    public function fetchConcepts()
    {
        if (! empty($this->concepts)) {
            return $this->concepts;
        }

        $this->concepts = [];
        $query = "SELECT * FROM index_concept WHERE num_object = $this->id AND type_object = '" . TYPE_ANIMATION . "'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $this->concepts[] = ConceptModel::getConcept($row->num_concept);
            }
        }
    }

    public function fetchCategories()
    {
        if (! empty($this->categories)) {
            return $this->categories;
        }

        $this->categories = [];
        $query = "SELECT * FROM anim_animation_categories WHERE num_animation = $this->id ORDER BY ordre_categorie";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_object($result)) {
                $this->categories[] = CategoryModel::getCategory($row->num_noeud);
            }
        }
    }

    public function fetchCustomFields()
    {
        if (! empty($this->customFields)) {
            return $this->customFields;
        }
        $this->customFields = CustomFieldModel::getAllCustomFields('anim_animation', $this->id);
        $this->gotCustomFieldsValues = false;
        foreach ($this->customFields as $field) {
            if (! empty($field['customField']['values'])) {
                $this->gotCustomFieldsValues = true;
            }
        }
    }

    public function getFormatDate($event)
    {
        $evt = new \stdClass();

        if (! empty($event->rawStartDate)) {
            // event est déjà formaté
            return $event;
        }

        $date = new \DateTime();

        $sd = new \DateTime($event->startDate ?? 'now');
        $evt->startDate = $sd->format("d/m/Y");
        $evt->startHour = $sd->format("H:i");
        $evt->rawStartDate = $event->startDate ?? $sd;

        $evt->endDate = "00/00/0000";
        $evt->endHour = "00:00";
        $evt->rawEndDate = "0000-00-00 00:00:00";

        if (! empty($event) && ! $event->duringDay) {
            $ed = new \DateTime($event->endDate ?? 'now');
            $evt->endDate = $ed->format("d/m/Y");
            $evt->endHour = $ed->format("H:i");
            $evt->rawEndDate = $event->endDate ?? $ed;
        }

        $evt->dateExpired = false;
        if (isset($ed) && $ed < $date && (! empty($event) && ! $event->duringDay)) {
            $evt->dateExpired = true;
        }

        $evt->duringDay = $event->duringDay ?? 0;
        return $evt;
    }

    public function formatEventDateHTML()
    {
        $this->event->startHour = '';
        if (! empty($this->event->startDate)) {
            $sd = new \DateTime($this->event->startDate);
            $this->event->startDate = $sd->format("Y-m-d");
            $this->event->startHour = $sd->format("H:i");
        }

        $this->event->endHour = '';
        if (! empty($this->event->endDate)) {
            $ed = new \DateTime($this->event->endDate);
            $this->event->endDate = $ed->format("Y-m-d");
            $this->event->endHour = $ed->format("H:i");
        }

        return $this->event;
    }

    public static function insertLocationList(array $locations, int $idAnimation = 0)
    {
        $query = "DELETE FROM `anim_animation_locations` WHERE `anim_animation_locations`.`num_animation` = $idAnimation";
        pmb_mysql_query($query);

        foreach ($locations as $locationId) {
            $query = "INSERT INTO `anim_animation_locations` (`num_animation`, `num_location`) VALUES ($idAnimation, $locationId)";
            pmb_mysql_query($query);
        }
    }

    public static function insertMailingTypeList(array $mailingType, int $idAnimation = 0)
    {
        $mailingType = array_map('intval', $mailingType);

        $animationModel = new AnimationModel($idAnimation);
        $animationModel->fetchMailing();

        $already_create = array();
        if ($animationModel->mailing) {
            foreach ($animationModel->mailing as $mailing) {
                if (in_array($mailing->numMailingType, $mailingType)) {
                    $already_create[] = $mailing->numMailingType;
                } else {
                    $mailing->delete();
                }
            }
        }

        foreach ($mailingType as $mailingTypeId) {
            if (! empty($mailingTypeId) && $mailingTypeId != 0 && ! in_array($mailingTypeId, $already_create) && MailingTypeOrm::exist($mailingTypeId)) {
                $mailingOrm = new MailingAnimationOrm();
                $mailingOrm->num_animation = intval($idAnimation);
                $mailingOrm->num_mailing_type = intval($mailingTypeId);
                $mailingOrm->save();
            }
        }
    }

    public static function deleteLocationList(int $id)
    {
        $query = "DELETE FROM `anim_animation_locations` WHERE `anim_animation_locations`.`num_animation` = $id";
        pmb_mysql_query($query);
    }

    public static function saveParentChild($data)
    {
        $animation = new AnimationOrm($data->idChildren);
        if (0 === $data->idParent) {
            $animation->num_parent = 0;
        } else {
            $animation->num_parent = $data->idParent;
        }
        $animation->save();
    }

    public static function getAnimationsDNDList()
    {
        $animationsList = AnimationOrm::findAll();

        $list = [];
        $listToUnset = [];

        $animTemps = new AnimationModel();

        foreach ($animationsList as $key => $animation) {
            $newAnimation = new \stdClass();
            $event = new EventModel($animation->num_event);
            $newAnimation->id = $animation->id_animation;
            $newAnimation->key = $key;
            $newAnimation->name = $animation->name;
            $newAnimation->event = $animTemps->getFormatDate($event);
            $newAnimation->numParent = $animation->num_parent;
            $newAnimation->nested = [];

            $list[$animation->id_animation] = $newAnimation;
        }

        foreach ($list as $id => $anim) {
            if (! empty($anim->numParent)) {
                if (isset($list[$anim->numParent])) {
                    $list[$anim->numParent]->nested[] = $anim;
                    $listToUnset[] = $list[$id];
                }
            }
        }

        foreach ($listToUnset as $animation) {
            unset($list[$animation->id]);
        }

        self::sortRecursiveAnimationsByDate($list);

        return self::toArray(array_values($list));
    }

    protected static function sortRecursiveAnimationsByDate(&$animations)
    {
        usort($animations, function ($a, $b) {
            $startDateA = strtotime($a->event->rawStartDate);
            $startDateB = strtotime($b->event->rawStartDate);

            return $startDateA - $startDateB;
        });

        foreach($animations as $animation) {
            if(count($animation->nested)) {
                self::sortRecursiveAnimationsByDate($animation->nested);
            }
        }
    }

    public function getEditAddData(bool $duplicate = false)
    {
        $this->fetchEvent();
        if (! empty($this->numEvent)) {
            $this->formatEventDateHTML();
        }
        $this->fetchPrices($duplicate);
        $this->fetchLocation();
        $this->fetchMailingType();
        $this->fetchConcepts();
        $this->fetchCategories();
        $this->fetchCustomFields();
        $this->fetchParent();

        if (empty($this->logo)) {
            $this->logo = json_encode([
                "uploadFolder" => 0,
                "filename" => "",
                "filePath" => "",
                "alt" => ""
            ]);
        }
        // $this->allowWaitingList = 0;

        // TODO : Revoir la duplication en passant par une mÃ©thode dans l'ORM pour supprimer les relations
        if ($duplicate) {
            // Remise a ZÃ©ro des id et numAnimation
            $this->idDuplicate = $this->idAnimation;
            $this->idAnimation = 0;
            $this->id = 0;
            $this->event->id = 0;
            $this->event->idEvent = 0;
            $this->numEvent = 0;
            $this->hasChildrens = false;
        } else {
            $this->hasChildrens = $this->checkChildrens();
        }
        return $this;
    }

    public function checkChildrens()
    {
        if (isset($this->idAnimation)) {
            $results = AnimationOrm::find('num_parent', $this->idAnimation);
            if (! empty($results) && count($results)) {
                $this->hasChildrens = true;
                return true;
            }
        }
        $this->hasChildrens = false;
        return false;
    }

    public function getViewData(bool $paginateChildren = false)
    {
        $this->fetchEvent();
        if (! empty($this->event)) {
            $this->event = $this->getFormatDate($this->event);
        }
        $this->fetchParent();
        $this->fetchPrices();
        $this->fetchLocation(true);
        $this->fetchStatus();
        $this->fetchType();
        $this->fetchConcepts();
        $this->fetchCategories();
        $this->fetchCustomFields();
        $this->fetchQuotas();
        $this->fetchCalendar();

        $this->hasChildrens = $this->checkChildrens();
        if ($this->hasChildrens) {
            if ($paginateChildren) {
                $this->childrens = AnimationModel::paginationDaughterList($this->idAnimation);
            } else {
                $this->childrens = $this->getDaughterList($this->idAnimation);
            }
        } else {
            if ($paginateChildren) {
                $this->childrens = [
                    'currentPage' => 1,
                    'total' => 0,
                    'nbPerPage' => 0,
                    'instances' => []
                ];
            } else {
                $this->childrens = [];
            }
        }



        return $this;
    }

    public function getSimpleSearchData()
    {
        $this->fetchEvent();
        if (! empty($this->event)) {
            $this->event = $this->getFormatDate($this->event);
        }
        $this->fetchLocation(true);
        $this->fetchStatus();
        $this->fetchType();
        $this->fetchQuotas();
        $this->checkChildrens();
        $this->fetchcalendar();
        $this->fetchMailingType(true);

        return $this;
    }

    public function fetchQuotas()
    {
        if (! empty($this->allQuotas)) {
            return $this->allQuotas;
        }

        $this->allQuotas = AnimationModel::getAllQuotas($this->id);
        $this->hasQuotas = false;

        if ((! isset($this->globalQuota) || $this->globalQuota >= 0) || (! isset($this->internetQuota) || $this->internetQuota >= 0)) {
            $this->hasQuotas = true;
        }

        $this->internetAvailable = false;
        if ($this->allQuotas['availableQuotas']['global'] >= $this->allQuotas['availableQuotas']['internet']) {
            $this->internetAvailable = true;
        }

        return $this->allQuotas;
    }

    public static function getBaseQuotas($id)
    {
        $animations = new AnimationOrm($id);
        $quotas = [
            "global" => intval($animations->global_quota),
            "internet" => intval($animations->internet_quota)
        ];
        return $quotas;
    }

    public static function getAllQuotas(int $id)
    {
        // Renvoie le nombre de place reserves
        $registrations = RegistrationModel::getRegistrationPlaceForAnimation($id);

        // Renvoie le nombre de place reserves sur liste d'attente
        $waitingList = RegistrationModel::getRegistrationWaitingList($id);

        // Renvoie le quotas de place de l'animation
        $quotasAnimation = AnimationModel::getBaseQuotas($id);

        // calcul et formatage de donnees des places reservees
        $globalReservedPlace = 0;
        $internetReservedPlace = 0;
        if (count($registrations) > 0) {
            $result = static::countNbRegistredPerson($registrations);
            $globalReservedPlace = $result['global'];
            $internetReservedPlace = $result['internet'];
        }

        // calcul et formatage de donnees des places sur liste d'attente
        $globalWaitingList = 0;
        $internetWaitingList = 0;
        if (count($waitingList) > 0) {
            $result = static::countNbRegistredPerson($waitingList);
            $globalWaitingList = $result['global'];
            $internetWaitingList = $result['internet'];
        }

        $availablePlace = [];
        $availablePlace['global'] = 0;
        $availablePlace['internet'] = 0;

        // On y passe seulement dans le cas ou le quotas est limite
        if ($quotasAnimation['global'] > 0) {
            $availablePlace['global'] = $quotasAnimation['global'] - $globalReservedPlace - $internetReservedPlace;
        }
        if ($quotasAnimation['internet'] > 0) {
            $availablePlace['internet'] = $quotasAnimation['internet'] - $internetReservedPlace;
        }

        // Dans le cas ou il n'y a plus de places global disponibles, le nombre de places internet disponbibles est remis a 0
        if ($quotasAnimation['global'] > 0 && $availablePlace['global'] == 0) {
            $availablePlace['internet'] = 0;
        } elseif ($quotasAnimation['global'] > 0 && $availablePlace['global'] < $availablePlace['internet']) {
            // Dans le cas ou le compteur de place restante global est inferieure au compteur de place restante sur internet
            $availablePlace['internet'] = $availablePlace['global'];
        }

        $quotas = [];
        $quotas['animationQuotas'] = $quotasAnimation;
        $quotas['availableQuotas'] = $availablePlace;
        $quotas['reserved'] = [
            'global' => $globalReservedPlace,
            'internet' => $internetReservedPlace
        ];
        $quotas['waitingList'] = [
            'global' => $globalWaitingList,
            'internet' => $internetWaitingList
        ];
        return $quotas;
    }

    protected static function countNbRegistredPerson($registredList)
    {
        $global = 0;
        $internet = 0;
        if ($registredList['global']) {
            foreach ($registredList['global'] as $registredGlobal) {
                $global += intval($registredGlobal->nb_registered_persons);
            }
        }
        if ($registredList['internet']) {
            foreach ($registredList['internet'] as $registredInternet) {
                $internet += intval($registredInternet->nb_registered_persons);
            }
        }
        return [
            "global" => $global,
            "internet" => $internet
        ];
    }

    public static function getDaughterList($idAnimation)
    {
        $daugtherList = [];
        $daughterORM = AnimationOrm::find('num_parent', $idAnimation);
        foreach ($daughterORM as $orm) {
            $id_animation = $orm->id_animation;
            if (! empty($id_animation) && $id_animation != 0) {
                $daugtherList[] = self::toArray(self::getAnimation($id_animation));
            }
        }

        return $daugtherList;
    }

    public static function paginationDaughterList(int $idAnimation, int $start = 0, int $max = 20)
    {
        $pagination = AnimationOrm::paginationWhere($start, $max, [
            'num_parent' => $idAnimation
        ]);

        foreach ($pagination['instances'] as $key => $orm) {
            $id_animation = $orm->id_animation;
            if (! empty($id_animation) && $id_animation != 0) {
                $pagination['instances'][$key] = self::toArray(self::getAnimation($id_animation));
            }
        }

        return $pagination;
    }

    public function getSummaryPerson()
    {
        $priceTab = array();

        $registrationList = RegistrationOrm::find('num_animation', $this->id);
        foreach ($registrationList as $registration) {
            $personList = RegistredPersonOrm::find('num_registration', $registration->id_registration);
            foreach ($personList as $person) {
                $personModel = new RegistredPersonModel($person->id_person);
                $price = $personModel->fetchPrice();
                if (empty($priceTab[$price->name])) {
                    $priceTab[$price->name]["nb_person"] = 0;
                }
                $priceTab[$price->name]["nb_person"] += 1;
            }
            $priceTab[$price->name]["price"] = $price->value;
            $priceTab[$price->name]["total"] = $priceTab[$price->name]["nb_person"] * $price->value;
        }
        return $priceTab;
    }

    public static function deleteAnimationCartNum($numCart)
    {
        $animationOrm = AnimationOrm::find('num_cart', $numCart)[0];
        if ($animationOrm != null) {
            $animationOrm->num_cart = 0;
            $animationOrm->save();
        }

        return true;
    }

    public static function getIdAnimationFromNumCaddie($idEmprCaddie)
    {
        $animationOrm = AnimationOrm::find("num_cart", $idEmprCaddie);
        return $animationOrm[0]->id_animation;
    }

    public static function getAnimationForMailing(int $id)
    {
        $anim = new AnimationModel($id);
        $anim->fetchEvent();
        $anim->fetchLocation(true);
        $anim->fetchMailing();
        return $anim;
    }

    public static function getAllAnimations()
    {
        return AnimationOrm::findAll();
    }

    public function getCmsStructure(string $prefixVar = "", bool $children = false)
    {
        global $msg;

        $cmsStructure = parent::getCmsStructure($prefixVar, $children);

        if (! empty($cmsStructure[0]['children'])) {
            foreach ($cmsStructure[0]['children'] as $key => $props) {
                if ($props['var'] == $prefixVar . ".event") {
                    $event = $this->getFormatDate(null);
                    if (! empty($event)) {
                        $cmsStructure[0]['children'][$key]['children'] = array();
                        foreach ($event as $propName => $value) {
                            $length = count($cmsStructure[0]['children'][$key]['children']);
                            $cmsStructure[0]['children'][$key]['children'][$length]['var'] = $props['var'] . "." . $propName;

                            $msgVar = str_replace(".", "_", $props['var'] . "." . $propName);
                            switch (true) {
                                case isset($msg['cms_module_common_datasource_desc_' . $msgVar]):
                                    $desc = $msg['cms_module_common_datasource_desc_' . $msgVar];
                                    break;

                                case isset($msg[$msgVar]):
                                    $desc = $msg[$msgVar];
                                    break;

                                default:
                                    $desc = addslashes($msgVar);
                                    break;
                            }
                            $cmsStructure[0]['children'][$key]['children'][$length]['desc'] = $desc;
                        }
                    }
                }
            }
        }

        return $cmsStructure;
    }

    public function getCmsData()
    {
        $data = [
            'id' => $this->id
        ];

        if (! empty($this->structure)) {
            foreach ($this->structure as $prop) {
                $data[addslashes($prop)] = Helper::toCmsData($this->{$prop});
            }
        }

        $reflect = new \ReflectionClass($this);
        $methods = $reflect->getMethods();
        if (! empty($methods)) {
            foreach ($methods as $method) {
                if (substr($method->name, 0, 5) == "fetch") {
                    $prop = $this->{$method->name}();
                    if (! empty($prop)) {
                        $key = strtolower(str_replace("fetch", "", $method->name));
                        if ($key == "event") {
                            $event = $this->getFormatDate($this->event);
                            if (! empty($event)) {
                                $data[addslashes($key)] = Helper::toCmsData($event);
                            }
                        } elseif (is_array($prop)) {
                            $data[addslashes($key)] = Helper::toCmsData($prop);
                        } elseif (method_exists($prop, "getCmsData")) {
                            if ($key == "parent" && ($prop->id == 0 || $prop->id == $this->id)) {
                                continue;
                            }
                            $data[addslashes($key)] = $prop->getCmsData();
                        } else {
                            $data[addslashes($key)] = Helper::toCmsData($prop);
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function emprAlreadyRegistred(int $id_empr)
    {
        if (! empty($this->emprAlreadyRegistred)) {
            return $this->emprAlreadyRegistred;
        }

        $registrationList = RegistrationModel::getEmprRegistrationsList($id_empr);
        foreach ($registrationList as $registration) {
            if ($registration->animation->id == $this->id) {
                $this->emprAlreadyRegistred = true;
                return true;
            }
        }

        $this->emprAlreadyRegistred = false;
        return false;
    }

    public static function getPathAnimationLogo($file, $idFolder)
    {
        if (empty($file['tmp_name'])) {
            return '';
        }

        $file_temp_path = $file['tmp_name'];
        if (! file_exists($file_temp_path)) {
            return '';
        }
        $upload_directory = new \upload_folder($idFolder);
        $rep_path = $upload_directory->repertoire_path;

        $blob = file_get_contents($file_temp_path);
        $filename = $file['name'];

        file_put_contents($rep_path . $filename, $blob);

        return $rep_path . $filename;
    }

    public static function printLogo(int $id, $size = null)
    {
        global $animations_active_image_cache;

        $animation = AnimationOrm::findById($id);
        $logo = json_decode($animation->logo);

        if (isset($logo->filePath) && file_exists($logo->filePath)) {
            $max_size = $size ?? GlobalContext::get("pmb_notice_img_pics_max_size");

            // On récupère le logo dans le cache si il est présent
            $extension = pathinfo($logo->filePath, PATHINFO_EXTENSION);
            $cache_path = self::getImgCachePathPrefix($id, $max_size) . "." . $extension;
            if ($animations_active_image_cache && is_file($cache_path)) {
                $image = imagecreatefromstring(file_get_contents($cache_path));

                // On active la transparence pour les png
                if ($extension == "png") {
                    // On gere la transparence
                    imageSaveAlpha($image, true);
                    imageAlphaBlending($image, false);
                }

                Image::printPNG($image);
                exit();
            }

            $image = file_get_contents($logo->filePath);
            $imgResize = Image::resize($image, $max_size);

            // On active la transparence pour les png
            if ($extension == "png") {
                // On gere la transparence
                imageSaveAlpha($imgResize, true);
                imageAlphaBlending($imgResize, false);
            }

            if($animations_active_image_cache) {
                // On initialise le répertoire de cache
                self::initCachePath();

                // On enregistre le logo dans le cache
                imagepng($imgResize, $cache_path, 9, defined('PNG_ALL_FILTERS') ? PNG_ALL_FILTERS : null);
            }

            Image::printPNG($imgResize);
            exit();
        } else {
            $fp = fopen("./images/mimetype/jpg-dist.png", "r");
            $contenu_vignette = fread($fp, filesize("./images/mimetype/jpg-dist.png"));
            fclose($fp);

            header('Content-Type: image/png');
            print $contenu_vignette;
            exit();
        }
    }

    /**
     * Methode pour H2o pour recuperer le logo
     *
     * @return NULL|[]
     */
    public function getFormatlogo()
    {
        global $opac_url_base;

        $logo = json_decode($this->logo);
        if (empty($logo)) {
            return null;
        }

        $orgineSize = 0;
        if (! empty($logo->filePath)) {
            $orgineSize = getimagesize($logo->filePath);
            $orgineSize = intval($orgineSize[0] ?? 0);
        }
        $animationLinkLogo = $opac_url_base . "animations_vign.php?animationId=" . intval($this->id);
        return [
            "default" => $animationLinkLogo,
            "small_vign" => $animationLinkLogo . "&size=16",
            "vign" => $animationLinkLogo . "&size=100",
            "small" => $animationLinkLogo . "&size=140",
            "medium" => $animationLinkLogo . "&size=300",
            "big" => $animationLinkLogo . "&size=600",
            "large" => $animationLinkLogo . "&size=" . intval($orgineSize),
            "alt" => $logo->alt ?? ""
        ];
    }

    /**
     * Permet d'exporter la liste des inscriptions pour une animation
     *
     * @param string $exportType
     * @param array $param
     */
    public function exportPrint(string $exportType, array $param = [])
    {
        switch ($exportType) {
            case "excel":
            default:
                $this->getExcelSummarize();
                break;
        }
    }

    protected function getExcelSummarize()
    {
        global $msg;

        $worksheet = new \spreadsheetPMB();
        $title = str_replace("%s", $this->name, $msg["animation_export_title"]);
        $worksheet->write(0, 0, $title);

        $i = 2;
        $j = 2;

        $worksheet->write($i, $j++, $msg["animation_export_name"]);
        $worksheet->write($i, $j++, $msg["animation_export_person_barcode"]);
        $worksheet->write($i, $j++, $msg["animation_export_person_price"]);
        $worksheet->write($i, $j++, $msg["animation_export_date_registration"]);

        $worksheet->write($i, $j++, $msg["animation_export_name_contact"]);
        $worksheet->write($i, $j++, $msg["animation_export_email_contact"]);
        $worksheet->write($i, $j++, $msg["animation_export_phone_contact"]);
        $worksheet->write($i, $j++, $msg["animation_export_barcode_contact"]);

        $i++;

        $resgistrationList = RegistrationModel::getRegistrations($this->id);

        foreach ($resgistrationList as $registration) {
            if(!$registration["validated"]) {
                continue;
            }

            $personList = $registration["registrationListPerson"];
            foreach($personList as $person) {
                $j = 2;

                $worksheet->write($i, $j++, $person["name"]);
                $worksheet->write($i, $j++, !empty($person["barcode"]) ? $person["barcode"] : $msg["animation_export_no_barcode"]);
                $worksheet->write($i, $j++, $person["price"]["name"] ?? $msg["animation_export_no_price"]);
                $worksheet->write($i, $j++, $registration["date"]);

                $worksheet->write($i, $j++, $registration["name"]);
                $worksheet->write($i, $j++, !empty($registration["email"]) ? $registration["email"] : "no_email");
                $worksheet->write($i, $j++, !empty($registration["phoneNumber"]) ? $registration["phoneNumber"] : "no_phone");
                $barcode = EmprModel::getBarcode($registration["numEmpr"]);
                $worksheet->write($i, $j++, !empty($barcode) ? $barcode : $msg["animation_export_no_barcode"]);

                $i++;
            }
        }
        $name = "liste_inscription_" . str_replace(" ", "_", $this->name) . ".xls";
        $worksheet->download($name);
    }

    public function repeatEventAnimation(object $data)
    {
        global $thesaurus_concepts_active, $class_path;
        require_once ($class_path . '/event/events_handler.class.php');

        foreach ($data->selectedDays as $years) {
            foreach ($years as $year => $months) {
                foreach ($months as $month => $days) {
                    foreach ($days as $day) {
                        // Ici on créer nos animations
                        $animation = new AnimationOrm();
                        $animation->name = $data->animationName ?? $this->name;
                        $animation->comment = $this->comment ?? "";
                        $animation->description = $this->description ?? "";
                        $animation->global_quota = $this->globalQuota ?? 0;
                        $animation->internet_quota = $this->internetQuota ?? 0;
                        $animation->num_status = $this->numStatus ?? 1;
                        $animation->num_event = EventModel::getRepeatEventId($data->event, $year, $month, $day);
                        $animation->num_parent = $this->id;
                        $animation->expiration_delay = $this->expirationDelay;
                        $animation->registration_required = $this->registrationRequired;
                        $animation->auto_registration = $this->autoRegistration;
                        $animation->allow_waiting_list = $this->allowWaitingList;
                        $animation->num_cart = $this->numCart;
                        $animation->num_type = $this->numType;
                        $animation->num_calendar = $this->numCalendar;
                        $animation->logo = $this->logo;
                        $animation->unique_registration = $this->uniqueRegistration;
                        $animation->save();

                        if (! empty($this->categories)) {
                            CategoryModel::updateAnimationCategories(json_decode(json_encode($this->categories)), $animation->id_animation);
                        }

                        if (! empty($this->concepts) && $thesaurus_concepts_active) {
                            ConceptModel::updateAnimationConcepts(json_decode(json_encode($this->concepts)), $animation->id_animation);
                        }

                        AnimationModel::insertLocationList([
                            "0" => $data->location
                        ], $animation->id_animation);

                        if (! empty($this->mailingType)) {
                            AnimationModel::insertMailingTypeList($this->mailingType, $animation->id_animation);
                        }

                        if (! empty($this->prices)) {
                            PriceModel::addPriceRepeatAnimation($this->prices, $animation->id_animation);
                        }

                        if (! empty($this->customFields)) {
                            CustomFieldModel::updateCustomFields(json_decode(json_encode($this->customFields)), $animation->id_animation, 'anim_animation');
                        }

                        $event = new AnimationsEvent("animations", "save");
                        $evth = \events_handler::get_instance();
                        $event->set_animation_id($animation->id_animation);
                        if (! empty($animation->id_animation)) {
                            $event->set_action($event::AUTOMATIC_UPDATE);
                        } else {
                            $event->set_action($event::AUTOMATIC_CREATE);
                        }
                        $evth->send($event);
                    }
                }
            }
        }
    }

    public function getFetchAnimation()
    {
        $this->fetchEvent();

        if (! empty($this->event)) {
            $this->event = $this->getFormatDate($this->event);
        }

        $this->fetchParent();
        $this->fetchPrices();
        $this->fetchLocation(true);
        $this->fetchStatus();
        $this->fetchType();
        $this->fetchConcepts();
        $this->fetchCategories();
        $this->fetchCustomFields();
        $this->fetchQuotas();
        $this->fetchCalendar();
    }

    public static function initAnimationToArticle()
    {
        global $class_path;

        require_once ($class_path . '/event/events_handler.class.php');
        $animList = AnimationOrm::findAll();

        foreach ($animList as $anim) {
            $event = new AnimationsEvent("animations", "save");
            $evth = \events_handler::get_instance();
            $event->set_animation_id($anim->id_animation);

            if (! empty($anim->id_animation)) {
                $event->set_action($event::AUTOMATIC_UPDATE);
            } else {
                $event->set_action($event::AUTOMATIC_CREATE);
            }
            $evth->send($event);

            if ($event->has_errors()) {
                $errors = '<div>Error messages<ul>';
                foreach ($event->get_errors() as $error) {
                    $errors .= "<li>" . $error . "</li>";
                }
                $errors .= '</ul></div>';
                echo $errors;
            }
        }

        return [
            "error" => $errors
        ];
    }
    /**
     * Retourne le logo de l'animation dupliquee
     * Si le nom du logo a ete change on recree tout
     * Sinon on retourne le logo de l'animation dupliquee
     * @param $duplicate
     * @return string
     */
    protected static function setLogoFromDuplicate($duplicate)
    {
        $animation = new AnimationOrm($duplicate->idDuplicate);
        $logo = encoding_normalize::json_decode($animation->logo);
        if($duplicate->logo->filename == $logo->filename) {
            return json_encode($duplicate->logo);
        }
        //Si le nom est différent on garde le nouveau fichier
        //Sinon on reprend celui de l'animation dupliquée
        return json_encode([
            "uploadFolder" => intval($duplicate->logo->uploadFolder),
            "filename" => $duplicate->logo->filename,
            "filePath" => AnimationModel::getPathAnimationLogo($duplicate->image, intval($duplicate->logo->uploadFolder)),
            "alt" => $duplicate->logo->alt
        ]);
    }

    /**
     * Initialise le chemin du cache des logos
     *
     * @return void
     */
    private static function initCachePath()
    {
        global $base_path, $database;

        $cache_path = $base_path . "/temp/animations_vign";
        if (! is_dir($cache_path)) {
            mkdir($cache_path);
        }
        if (! is_dir($cache_path . "/" . $database)) {
            mkdir($cache_path . "/" . $database);
        }
        if (! is_dir($cache_path . "/" . $database)) {
            mkdir($cache_path . "/" . $database);
        }
    }

    /**
     * Retourne le chemin du cache des logos
     *
     * @param string $mode
     * @return string
     */
    public static function getImgCachePathPrefix(int $id, int $size)
    {
        global $base_path, $database;

        if(empty($id)) {
            return "";
        }

        $cache_path = [$base_path, "temp", "animations_vign"];

        if(!empty($database)) {
            $cache_path[] = $database;
        }

        $filepath = "animation_" . $id;
        if (!empty($size)) {
            $filepath .= "_" . $size;
        }

        $cache_path[] = $filepath;

        return join(DIRECTORY_SEPARATOR, $cache_path);
    }

    /**
     * Permet de supprimer le cache d'un logo
     *
     * @param integer $id Identifiant du logo
     * @return void
     */
    private static function cleanImgCache(int $id)
    {
        $path = self::getImgCachePathPrefix($id, 0);

        $resources = glob($path . "*", GLOB_NOSORT);
        foreach ($resources as $resource) {
            if (in_array(basename($resource), ["CVS", ".", ".."])) {
                continue;
            }

            if (is_file($resource)) {
                unlink($resource);
            }
        }
    }

	/**
	 * Vide le cache
	 *
	 * @return void
	 */
	public static function cleanCache() {
		global $base_path, $database;

		$cache_dirs = [
			$base_path.'/temp/animations_vign/'.$database,
			$base_path.'/opac_css/temp/animations_vign/'.$database,
		];

		foreach($cache_dirs as $dir) {
			if (is_dir($dir)) {
				self::rmdirFiles($dir);
			}
		}
	}

    /**
	 * Delete all files in a directory
	 *
	 * @param string $dir
	 * @return void
	 */
	private static function rmdirFiles($dir) {
		foreach(glob($dir . '/*') as $file) {
			if (is_dir($file)) {
				self::rmdirFiles($file);
			} else {
				@unlink($file);
			}
		}
		@rmdir($dir);
	}
}
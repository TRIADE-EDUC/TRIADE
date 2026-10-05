<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Diffusion.php,v 1.84.2.5.2.2 2025/05/26 12:13:30 rtigero Exp $

namespace Pmb\DSI\Models;

use Pmb\Common\Helper\GlobalContext;
use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\Channel\RootChannel;
use Pmb\DSI\Models\Event\RootEvent;
use Pmb\DSI\Models\Item\RootItem;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;
use Pmb\DSI\Models\SubscriberList\Subscribers\Subscriber;
use Pmb\DSI\Models\View\RootView;
use Pmb\DSI\Models\View\WYSIWYGView\WYSIWYGView;
use Pmb\DSI\Orm\ChannelOrm;
use Pmb\DSI\Orm\DiffusionDescriptorsOrm;
use Pmb\DSI\Orm\DiffusionHistoryOrm;
use Pmb\DSI\Orm\DiffusionOrm;
use Pmb\DSI\Orm\DiffusionProductOrm;
use Pmb\DSI\Orm\EventDiffusionOrm;
use Pmb\DSI\Orm\ItemOrm;
use Pmb\DSI\Orm\SubscriberListOrm;
use Pmb\DSI\Orm\ViewOrm;

class Diffusion extends Root implements CRUD
{
    public const TAG_TYPE = 1;

    protected $ormName = "Pmb\DSI\Orm\DiffusionOrm";

    protected $idDiffusion = 0;

    public $name = "";

    /**
     * Parameters
     *
     * @var string|object
     */
    public $settings = "";

    public $status = null;

    public $channel = null;

    public $view = null;

    public $item = null;

    public $subscriberList = null;

    public $tags = null;

    public $automatic = 0;

    public $numChannel = 0;

    public $numView = 0;

    public $numItem = 0;

    public $numSubscriberList = 0;

    public $numStatus = 0;

    protected $diffusionProducts = null;

    public $events = null;

    protected $diffusionHistory = null;

    protected $diffusionDescriptors = null;

    protected $descriptors = [];

    protected $products = [];

    /**
     * Contient la vue detaile
     * Voir getDetail()
     *
     * @var null|string
     */
    protected $detail;

    /**
     * Contient le nombre d'abonnes
     * Voir getNbSubscribers()
     *
     * @var null|int
     */
    public $nbSubscribers;

    /**
     * Contient le date de la dernier diffusion
     * Voir getLastDiffusion()
     *
     * @var null|string
     */
    public $lastDiffusion;

    public $currentHistory;

    public function __construct(int $id = 0)
    {
        $this->id = $id;
        $this->read();
        $this->fetchUserDefaultStatus();
    }

    public function create()
    {
        $orm = new $this->ormName();
        $orm->name = $this->name;
        $orm->settings = json_encode($this->settings);
        $orm->num_status = $this->numStatus;
        $orm->num_channel = $this->numChannel ?? 0;
        $orm->num_view = $this->numView ?? 0;
        $orm->num_item = $this->numItem ?? 0;
        $orm->num_subscriber_list = $this->numSubscriberList ?? 0;
        $orm->automatic = $this->automatic;
        $orm->save();

        $this->id = $orm->{$this->ormName::$idTableName};
        $this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};

        $this->updatedDiffusionDescriptors();
    }

    public function check(object $data)
    {
        if (empty($data->name) || !is_string($data->name)) {
            return [
                'error' => true,
                'errorMessage' => 'msg:data_errors',
            ];
        }

        // $fields = ['name' => $data->name];
        // if (!empty($data->id)) {
        //     $fields[$this->ormName::$idTableName] = [
        //         'value' => $data->id,
        //         'operator' => '!=',
        //     ];
        // }

        // $result = $this->ormName::finds($fields);
        // if (!empty($result)) {
        //     return [
        //         'error' => true,
        //         'errorMessage' => 'msg:diffusion_duplicated',
        //     ];
        // }

        return [
            'error' => false,
            'errorMessage' => '',
        ];
    }

    public function setFromForm(object $data)
    {
        $this->name = $data->name;
        $this->settings = $data->settings;
        $this->numStatus = intval($data->numStatus);
        $this->numChannel = $data->numChannel ?? 0;
        $this->numView = $data->numView ?? 0;
        $this->numItem = isset($data->numView) ? $data->numItem : 0;
        $this->numSubscriberList = $data->numSubscriberList;
        $this->automatic = $data->automatic;
        $this->descriptors = $data->descriptors ?? [];
    }

    public function read()
    {
        $this->fetchData();

        if (empty($this->settings->nb_history_saved)) {
            $this->settings->nb_history_saved = 1;
        }

        // On controle que le channel, la vue, l'item et la liste d'abonnés existent
        // Si ce n'est pas le cas, on le met à zero
        if (!empty($this->numChannel) && !ChannelOrm::exist($this->numChannel)) {
            $this->numChannel = 0;
        }
        if (!empty($this->numView) && !ViewOrm::exist($this->numView)) {
            $this->numView = 0;
        }
        if (!empty($this->numItem) && !ItemOrm::exist($this->numItem)) {
            $this->numItem = 0;
        }
        if (!empty($this->numSubscriberList) && !SubscriberListOrm::exist($this->numSubscriberList)) {
            $this->numSubscriberList = 0;
        }

        // $this->fetchRelations();
    }

    public function update()
    {
        $orm = new $this->ormName($this->id);
        $orm->name = $this->name;
        $orm->settings = json_encode($this->settings);
        $orm->num_status = $this->numStatus;
        $orm->num_channel = $this->numChannel;
        $orm->num_view = $this->numView;
        $orm->num_item = $this->numItem;
        $orm->num_subscriber_list = $this->numSubscriberList;
        $orm->automatic = $this->automatic;
        $orm->save();

        $this->updatedDiffusionDescriptors();

        if ($this->automatic) {
            $diffusionHistories = DiffusionHistoryOrm::finds([
                "state" => DiffusionHistory::INITIALIZED,
                "num_diffusion" => $this->id,
            ]);
            foreach ($diffusionHistories as $diffusionHistory) {
                $diffusionHistory->delete();
            }
        }
    }

    public function delete()
    {
        try {
            if (!empty($this->settings->diffusionModel)) {
                return [
                    "error" => true,
                    "errorMessage" => ""
                ];
            }
            $this->fetchRelations();
            $orm = new $this->ormName($this->id);
            //Suppression des liens
            if (!empty($this->subscriberList)) {
                $this->subscriberList->lists->delete();
                $this->subscriberList->source->delete();
            }

            foreach ($this->events as $diffusionEvent) {
                $diffusionEvent->delete();
            }

            if (!empty($this->view)) {
                $this->view->delete();
            }

            if (!empty($this->item)) {
                $this->item->delete();
            }

            if (!empty($this->channel)) {
                $this->channel->delete();
            }

            foreach ($this->diffusionProducts as $diffusionProduct) {
                $diffusionProduct = new DiffusionProductOrm(["num_diffusion" => $diffusionProduct->ids["num_diffusion"], "num_product" => $diffusionProduct->ids["num_product"]]);
                $diffusionProduct->delete();
            }

            foreach ($this->diffusionDescriptors as $diffusionDescriptor) {
                $diffusionDescriptor->delete();
            }

            foreach ($this->diffusionHistory as $diffusionHistory) {
                $diffusionHistory->delete();
            }

            $this->removeAttachments();
            $this->removeEntityTags();

            $orm->delete();
        } catch (\Exception $e) {
            return [
                'error' => true,
                'errorMessage' => $e->getMessage(),
            ];
        }

        $this->id = 0;
        $this->{Helper::camelize($orm::$idTableName)} = 0;
        $this->name = '';
        $this->status = '';

        return [
            'error' => false,
            'errorMessage' => '',
        ];
    }

    public function fetchRelations()
    {
        $this->fetchView();
        $this->fetchItem();
        $this->fetchChannel();
        $this->fetchSubscriberList();
        $this->fetchEvents();
        $this->fetchDiffusionHistory();
        $this->fetchDiffusionDescriptors();
    }

    public function fetchChannel()
    {
        if (!isset($this->channel)) {
            $this->channel = RootChannel::getInstance($this->numChannel);
        }
    }

    public function fetchView()
    {
        if (!isset($this->view)) {
            $this->view = RootView::getInstance($this->numView);
        }
    }

    public function fetchItem()
    {
        if (!isset($this->item)) {
            $this->item = RootItem::getInstance($this->numItem);
        }
    }

    public function fetchSubscriberList()
    {
        //Pas de subscriber list si on est sur le portail
        // if (!defined('GESTION')) {
        // 	$this->numSubscriberList = 0;
        // }

        if (!isset($this->subscriberList)) {
            $this->subscriberList = RootSubscriberList::getDiffusionSubscribers($this->id, $this->numSubscriberList);
            if ($this->id && !$this->numSubscriberList) {
                $this->subscriberList->source->create();
                $this->numSubscriberList = $this->subscriberList->source->id;
                $this->update();
            }
        }
    }

    public function fetchEvents()
    {
        if (!isset($this->events)) {
            $events = EventDiffusionOrm::finds(["num_diffusion" => $this->id]);

            $this->events = [];
            foreach ($events as $event) {
                $instance = RootEvent::getInstance($event->num_event);
                $instance->callerType = "diffusion";
                $this->events[] = $instance;
            }
        }
    }

    public function fetchDiffusionHistory()
    {
        if (!isset($this->diffusionHistory)) {
            $diffusionsHistory = DiffusionHistoryOrm::finds(["num_diffusion" => $this->id], "id_diffusion_history");

            $this->diffusionHistory = [];
            foreach ($diffusionsHistory as $diffusionHistory) {
                $this->diffusionHistory[] = DiffusionHistory::getInstance($diffusionHistory->id_diffusion_history, $this);
            }
        }
    }

    /**
     * Permet d'aller chercher les produits associes à la diffusion
     * /!\ ne pas faire a la fonction read() risque de boucle infini /!\
     *
     * @return void
     */
    public function fetchProducts()
    {
        if (!isset($this->products)) {
            $this->products = [];
            foreach ($this->diffusionProducts as $diffusionProduct) {
                $this->products[] = new Product($diffusionProduct->num_product);
            }
        }
    }

    /**
     * Retourne le rendu de la vue associee a la diffusion
     * @return string
     */
    public function renderView()
    {
        $this->fetchView();
        $this->fetchItem();

        if ($this->view instanceof RootView && $this->item instanceof RootItem) {
            $limit = $this->view->settings->limit ?? 0;
            return $this->view->render($this->item, $this->id, $limit, "diffusions");
        }
        return "";
    }

    public function previewView()
    {
        $this->fetchView();
        $this->fetchItem();

        if ($this->view instanceof RootView && $this->item instanceof RootItem) {
            $limit = $this->view->settings->limit ?? 0;
            return $this->view->preview($this->item, $this->id, $limit, "diffusions");
        }
        return "";
    }

    public function previewAttachmentView($selectedAttachment)
    {
        $attachment = $this->settings->attachments[$selectedAttachment];

        $view = RootView::getInstance($attachment->view);
        $item = RootItem::getInstance($attachment->item);

        $limit = $view->settings->limit ?? 0;

        return $view->preview($item, $this->id, $limit, "previewAttachment");
    }

    /**
     * Teste les declencheurs + envoi.
     * Utilise dans le planificateur (pmbesDSI_diffuseBannette)
     *
     * @return boolean
     */
    public function trigger()
    {
        global $msg;

        $this->fetchEvents();
        $this->fetchView();
        $this->fetchItem();

        $events = $this->events;
        //Retiré car il vaut mieux traiter les produits a part sinon on ne prend pas en compte le flag activate dans le produit
        // if (empty($events)) {
        //     $this->fetchProducts();
        //     foreach ($this->products as $product) {
        //         if (!empty($product->events)) {
        //             // On recupere les declencheurs du premier porduit qui en contient
        //             $events = $product->events;
        //             break;
        //         }
        //     }
        // }

        foreach ($events as $event) {
            if (!$event->canTrigger($this->view, $this->item)) {
                return $msg['dsi_diffusion_conditions_not_allowed'];
            }

            if ($event->trigger()) {
                $result = $this->send();
                return $result['error'] ?? true;
            }
        }
        return $msg['dsi_diffusion_no_triggers_activated'];
    }

    /**
     * Permet de faire l'envoi d'une diffusion (prend en compte l'historique)
     *
     * @return mixed
     */
    public function send($idHistory = 0)
    {
        $history = new DiffusionHistory($idHistory);

        if ($idHistory == 0) {
            $history->setDiffusion($this);

            if (isset($this->diffusionHistory)) {
                $this->diffusionHistory[] = $history;
            }
            $history->state(DiffusionHistory::TO_VALIDATE);
            if (!$this->automatic) {
                // On est pas en automatique on vas pas plus loin
                return true;
            }

            $history->state(DiffusionHistory::VALIDATED);
        }

        $result = $history->state(DiffusionHistory::SENT);
        if (isset($result['error'])) {
            $history->delete();

            return [
                "error" => true,
                "errorMessage" => $result['errorMessage'],
            ];
        }

        return [
            "error" => false,
            "idHistory" => $history->idDiffusionHistory,
        ];
    }
    /**
     * Duplication de la diffusion
     * @param $param array | null Tableau des éléments de la diffusion à dupliquer
     */
    public function duplicate($param = null)
    {
        if (!isset($param)) {
            $param = array("view", "item", "channel", "subscriberList", "events", "diffusionDescriptors");
        }
        $newDiffusion = parent::duplicate();
        if ($newDiffusion === false) {
            return false;
        }

        if ($this->numItem != 0 && in_array("item", $param)) {
            $this->fetchItem();
            $newDiffusion->item = $this->item->duplicate();
            $newDiffusion->numItem = $newDiffusion->item->id;
        }

        if ($this->numView != 0 && in_array("view", $param)) {
            $this->fetchView();
            //Cas particulier de la vue wysiwyg
            if ($this->view instanceof WYSIWYGView && !is_null($newDiffusion->item)) {
                $this->view->setAssociatedItem($newDiffusion->item);
            }
            $newDiffusion->view = $this->view->duplicate();
            $newDiffusion->numView = $newDiffusion->view->id;
        }

        if ($this->numChannel != 0 && in_array("channel", $param)) {
            $this->fetchChannel();
            $newDiffusion->channel = $this->channel->duplicate();
            $newDiffusion->numChannel = $newDiffusion->channel->id;
        }

        if ($this->numSubscriberList != 0 && in_array("subscriberList", $param)) {
            $this->fetchSubscriberList();
            $newDiffusion->subscriberList = new \stdClass();
            if (!empty($this->subscriberList->source)) {
                $newDiffusion->subscriberList->source = $this->subscriberList->source->duplicate();
                $newDiffusion->numSubscriberList = $newDiffusion->subscriberList->source->id;
            }
            if (!empty($this->subscriberList->lists)) {
                $newDiffusion->subscriberList->lists = $this->subscriberList->lists->duplicate($newDiffusion->id);
            }
        }
        if (in_array("events", $param)) {
            $this->fetchEvents();
            if (!empty($this->events)) {
                $newDiffusion->events = [];
                foreach ($this->events as $event) {
                    $newDiffusion->events[] = $event->duplicate($newDiffusion->id);
                }
            }
        }
        if (in_array("diffusionDescriptors", $param)) {
            $this->fetchDiffusionDescriptors();
            if (!empty($this->diffusionDescriptors)) {
                $newDiffusion->descriptors = [];
                foreach ($this->diffusionDescriptors as $diffusionDescriptors) {
                    $newDiffusionDescriptors = new \StdClass();
                    $newDiffusionDescriptors->id = $diffusionDescriptors->getNumNoeud();
                    $newDiffusion->descriptors[$diffusionDescriptors->getOrder()] = $newDiffusionDescriptors;
                }
            }
        }

        $newDiffusion->diffusionHistory = [];
        $newDiffusion->update();
        $newDiffusion->getLastDiffusion();
        $newDiffusion->nbSubscribers = $newDiffusion->getNbSubscribers();

        return $newDiffusion;
    }

    public function getDetail()
    {
        global $include_path;

        if (isset($this->detail)) {
            return $this->detail;
        }

        $template = "{$include_path}/templates/dsi/diffusion_detail_subst.tpl.html";
        if (!is_file($template)) {
            $template = "{$include_path}/templates/dsi/diffusion_detail.tpl.html";
        }
        if (is_file($template)) {
            $h2o = \H2o_collection::get_instance($template);
            $this->detail .= $h2o->render([
                "diffusion" => $this,
            ]);
        }
        return $this->detail;
    }

    public function getNbSubscribers()
    {
        return RootSubscriberList::getNbSubscribersFromDiffusion($this->numSubscriberList, $this->idDiffusion);
    }

    public function getLastDiffusion()
    {
        if (isset($this->lastDiffusion)) {
            $this->fetchLastDiffusion();
        }
        return $this->lastDiffusion;
    }

    public function fetchLastDiffusion()
    {
        if (isset($this->lastDiffusion)) {
            return true;
        }

        $diffusionsHistory = DiffusionHistoryOrm::finds([
            "num_diffusion" => $this->id,
            "state" => [
                "operator" => "in",
                "value" => [DiffusionHistory::SENT, DiffusionHistory::NODATA]
            ]
        ], "date DESC");

        if (empty($diffusionsHistory)) {
            $this->lastDiffusion = GlobalContext::msg('dsi_diffusion_never_send');
        } else {
            $date = new \DateTime($diffusionsHistory[0]->date);
            $this->lastDiffusion = $date->format(GlobalContext::msg('dsi_format_date'));
        }
        return true;
    }

    public function fetchDiffusionDescriptors()
    {
        if (!isset($this->diffusionDescriptors)) {
            $diffusionDescriptors = DiffusionDescriptorsOrm::finds([
                "num_diffusion" => $this->id
            ], "diffusion_descriptor_order");

            $this->diffusionDescriptors = [];
            foreach ($diffusionDescriptors as $diffusionDescriptors) {
                $diffusionDescriptors = new DiffusionDescriptors($diffusionDescriptors->num_noeud, $this->id);
                $this->diffusionDescriptors[] = $diffusionDescriptors;

                $descriptor = new \StdClass();
                $descriptor->id = $diffusionDescriptors->getNumNoeud();
                $descriptor->displayLabel = $diffusionDescriptors->getDescriptorLabel();
                $this->descriptors[] = $descriptor;
            }
        }
    }

    protected function updatedDiffusionDescriptors()
    {
        $this->fetchDiffusionDescriptors();

        foreach ($this->diffusionDescriptors as $diffusionDescriptor) {
            $diffusionDescriptor->delete();
        }

        $this->diffusionDescriptors = [];
        foreach ($this->descriptors as $order => $descriptor) {
            if (!empty($descriptor->id)) {
                $diffusionDescriptors = new DiffusionDescriptors();
                $diffusionDescriptors->setNumNoeud($descriptor->id);
                $diffusionDescriptors->setNumDiffusion($this->id);
                $diffusionDescriptors->setOrder($order);
                $diffusionDescriptors->create();
            }
        }
    }

    public function removeAttachments()
    {
        if (isset($this->settings->attachments)) {
            foreach ($this->settings->attachments as $attachment) {
                if ($attachment->view != 0) {
                    $view = RootView::getInstance($attachment->view);
                    $view->delete();
                }

                if ($attachment->item != 0) {
                    $item = RootItem::getInstance($attachment->item);
                    $item->delete();
                }
            }
        }
    }

    /**
     * Vas chercher la dernier diffusion envoyée (en fonction d'un canal ou non)
     * Retourne null par defaut
     *
     * @param string $namespaceChannel
     * @return DiffusionHistory|null
     */
    public function getLastHistorySent(?string $namespaceChannel = null)
    {
        $this->fetchDiffusionHistory();
        if (!empty($this->diffusionHistory)) {
            foreach (array_reverse($this->diffusionHistory) as $diffusionHistory) {
                if (
                    $diffusionHistory->state === DiffusionHistory::SENT &&
                    (
                        null === $namespaceChannel ||
                        $diffusionHistory->getChannel() instanceof $namespaceChannel
                    )
                ) {
                    return $diffusionHistory;
                }
            }
        }
        return null;
    }

    /**
     * Permet d'ajouter un historique "a initialise" dans les historiques
     * @return boolean
     */
    public function init()
    {
        if ($this->automatic) {
            return false;
        }

        $result = DiffusionHistoryOrm::finds([
            "state" => DiffusionHistory::INITIALIZED,
            "num_diffusion" => $this->id
        ]);

        if (empty($result)) {
            $history = new DiffusionHistory();
            $history->setDiffusion($this);
            $history->state(DiffusionHistory::INITIALIZED);

            if (isset($this->diffusionHistory)) {
                $this->diffusionHistory[] = $history;
            }
        }

        return true;
    }

    public function getSubscribers()
    {
        $this->fetchSubscriberList();
        $subscriberList = $this->subscriberList;
        if ($subscriberList->nbSubscribers <= 0) {
            $this->fetchProducts();
            foreach ($this->products as $product) {
                if ($product->subscriberList->nbSubscribers > 0) {
                    $subscriberList = $product->subscriberList;
                    // On recupere les subscribers du premier porduit qui en contient
                    break;
                }
            }
        }
        return $subscriberList;
    }

    /**
     * Vérifié le nombre d'historiques envoyé sauvegardé et supprime le surplus
     *
     * @return void
     */
    public function checkCountHistorySaved()
    {
        $this->fetchDiffusionHistory();
        if (empty($this->diffusionHistory)) {
            return;
        }

        $historySend = DiffusionHistoryOrm::finds([
            "state" => DiffusionHistory::SENT,
            "num_diffusion" => $this->id,
        ], "date");

        $historySendCount = count($historySend);
        if ($historySendCount > $this->settings->nb_history_saved) {
            $countDelete = $historySendCount - $this->settings->nb_history_saved;
            for ($i = 0; $i < $countDelete; $i++) {
                $diffusionHistory = new DiffusionHistory($historySend[$i]->id_diffusion_history);
                $diffusionHistory->delete();
            }
        }
    }

    public function fetchStatus()
    {
        if (!isset($this->status)) {
            $this->status = new DiffusionStatus($this->numStatus);
        }
    }

    /**
     * Recherche le statut par defaut defini dans les preferences utilisateur
     * @return void
     */
    public function fetchUserDefaultStatus()
    {
        //Si on n'a pas de status, on va recuperer le parametrage utilisateur
        if ($this->numStatus == 0) {
            global $PMBuserid;
            $query = "SELECT deflt_dsi_diffusion_default_status FROM users WHERE userid = '$PMBuserid'";
            $this->numStatus = intval(pmb_mysql_result(pmb_mysql_query($query), 0, 0));
            //Si on a encore 0 on met le statut par défaut
            if ($this->numStatus == 0) {
                $this->numStatus = 1;
            }
        }
    }

    /**
     * Indique si un abonné est inscrit à cette diffusion
     * Ne fonctionne pour le moment qu'à partir d'un id d'emprunteur pmb
     * @param int $idEmpr
     */
    public function isSubscribed($idEmpr)
    {
        if (empty($this->subscriberList)) {
            $this->fetchSubscriberList();
        }

        foreach ($this->subscriberList->source->subscribers as $subscriber) {
            if (($subscriber->getIdEmpr() == $idEmpr) && ($subscriber->updateType == Subscriber::UPDATE_TYPE_SUBSCRIBER)) {
                return true;
            }
        }
        foreach ($this->subscriberList->lists->subscribers as $subscriber) {
            if (($subscriber->getIdEmpr() == $idEmpr) && ($subscriber->updateType == Subscriber::UPDATE_TYPE_SUBSCRIBER)) {
                return true;
            }
        }
        return false;
    }

    public static function getDiffusionPrivateModel()
    {
        $modelSearch = DiffusionOrm::finds([
            "settings" => [
                "value" => "%\"diffusionModel\":true%",
                "operator" => "LIKE",
                "inter" => "AND"
            ]
        ]);
        if (!count($modelSearch) == 1) {
            return null;
        }

        $model = new self($modelSearch[0]->id_diffusion);
        //Test des propriétés minimum à avoir pour le modèle
        if (!empty($model->settings->selectedItem)) {
            return $model;
        }
        return null;
    }

    /**
     * Retourne la liste des diffusions publiques
     * @return array
     */
    public function getFilteredList()
    {
        $list = $this->getList();
        for ($i = 0; $i < count($list); $i++) {
            if (!empty($list[$i]->settings->isPrivate)) {
                array_splice($list, $i, 1);
                $i--;
                continue;
            }
        }
        return $list;
    }

    /**
     * Supprime toutes les diffusions privées d'un emprunteur
     * @param int $idEmpr
     */
    public static function deleteEmprDiffusionsPrivate(int $idEmpr = 0)
    {
        if ($idEmpr == 0) {
            return;
        }

        $params = [
            "settings" => [
                "value" => "%\"idEmpr\":" . $idEmpr . "%",
                "operator" => "LIKE",
                "inter" => "AND"
            ]
        ];
        $diffusions = DiffusionOrm::finds($params);

        foreach ($diffusions as $diffusion) {
            $diffusionModel = new static($diffusion->id_diffusion);
            if (!empty($diffusionModel->settings->isPrivate)) {
                $diffusionModel->delete();
            }
        }
    }

    public function addPortalDiffusion()
    {
        global $msg;
        $this->fetchChannel();
        //On vérifie qu'on n'est pas déjà sur un canal portail
        if ($this->channel->type == RootChannel::IDS_TYPE["Pmb\DSI\Models\Channel\Portal\PortalChannel"]) {
            return false;
        }
        //On duplique la diffusion
        $newDiffusion = $this->duplicate();
        $newDiffusion->name = $newDiffusion->name . " (" . $msg["dsi_add_portal_diffusion_name_label"] . ")";
        $newDiffusion->update();

        //On modifie le canal
        $newDiffusion->channel->type = RootChannel::IDS_TYPE["Pmb\DSI\Models\Channel\Portal\PortalChannel"];
        $newDiffusion->channel->settings = new \stdClass();
        $newDiffusion->channel->numModel = 0;
        $newDiffusion->channel->update();

        return $newDiffusion;
    }
}

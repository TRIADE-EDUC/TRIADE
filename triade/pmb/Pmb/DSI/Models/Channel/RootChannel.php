<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootChannel.php,v 1.30.2.1.2.1 2025/03/05 08:23:56 jparis Exp $

namespace Pmb\DSI\Models\Channel;

use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\Root;
use Pmb\DSI\Models\Stats;
use Pmb\DSI\Orm\ChannelOrm;
use Pmb\DSI\Orm\DiffusionOrm;

class RootChannel extends Root implements Channel
{
    protected const EXCLUDED_PROPERTIES = [
        "idChannel",
        'numModel',
        'model',
    ];

    public const TAG_TYPE = 2;

    public const IDS_TYPE = [
        "Pmb\DSI\Models\Channel\Mail\MailChannel" => 1,
        "Pmb\DSI\Models\Channel\SMS\SMSChannel" => 2,
        "Pmb\DSI\Models\Channel\Portal\PortalChannel" => 3,
        "Pmb\DSI\Models\Channel\HumHub\HumHubChannel" => 4,
        "Pmb\DSI\Models\Channel\RSS\RssChannel" => 5,
        "Pmb\DSI\Models\Channel\Export\ExportChannel" => 6,
        "Pmb\DSI\Models\Channel\Cart\CartChannel" => 7,
    ];

    protected $ormName = "Pmb\DSI\Orm\ChannelOrm";

    public $id = 0;
    public $name = "";
    public $type = 0;
    public $model = false;
    public $settings = "";
    public $tags = null;

    // ORM props
    protected $idChannel = 0;
    public $numModel = 0;

    protected $title = "";

    public function __construct(int $id = 0)
    {
        $this->id = intval($id);
        
        if($this->id) {
            $this->read();
        } else {
            $this->settings = new \stdClass();
        }
    }

    public static function getInstance(int $id = 0)
    {
        if (!empty($id)) {
            $channel = ChannelOrm::findById($id);
            if (!empty($channel)) {
                foreach (self::IDS_TYPE as $key => $value) {
                    if (self::IDS_TYPE[$key] == $channel->type) {
                        return new $key($id);
                    }
                }
            }
        }
        return new RootChannel($id);
    }

    public function read()
    {
        $this->fetchData();
    }

    public function check(object $data)
    {
        if (!is_string($data->name)) {
            return [
                'error' => true,
                'errorMessage' => 'msg:data_errors',
            ];
        }

        if (!empty($data->name)) {
            $fields = ['name' => $data->name, 'model' => $data->model];
            if (!empty($data->id)) {
                $fields[$this->ormName::$idTableName] = [
                    'value' =>  $data->id,
                    'operator' => '!=',
                ];
            }
            $result = $this->ormName::finds($fields);
            if (!empty($result)) {
                return [
                    'error' => true,
                    'errorMessage' => 'msg:diffusion_duplicated',
                ];
            }
        }

        return [
            'error' => false,
            'errorMessage' => '',
        ];
    }

    public function create()
    {
        $orm = new $this->ormName();
        $orm->name = $this->name;
        $orm->type = $this->type;
        $orm->model = $this->model;
        $orm->settings = json_encode($this->settings);
        $orm->num_model = $this->numModel;
        $orm->save();

        $this->id = $orm->{$this->ormName::$idTableName};
        $this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
    }

    public function update()
    {
        $orm = new $this->ormName($this->id);
        $orm->name = $this->name;
        $orm->type = $this->type;
        $orm->model = $this->model;
        $orm->settings = json_encode($this->settings);
        $orm->num_model = $this->numModel;
        $orm->save();
    }

    public function delete()
    {
        try {
            if (!$this->checkBeforeDelete()) {
                return [
                    'error' => true,
                    'errorMessage' => "msg:model_check_use",
                ];
            }

            $orm = new $this->ormName($this->id);
            
            // Sécuriter pour ne pas supprimer un channel utilisé dans une autre diffusion, ce qui ne devrait pas arriver
            $diffusionsUseChannel = DiffusionOrm::find("num_channel", $this->id);
            if(is_countable($diffusionsUseChannel)) {
                if(count($diffusionsUseChannel) <= 1) {
                    $this->removeEntityTags();
                    $orm->delete();
                }
            }
            
        } catch(\Exception $e) {
            return [
                'error' => true,
                'errorMessage' => $e->getMessage(),
            ];
        }

        $this->id = 0;
        $this->{Helper::camelize($orm::$idTableName)} = 0;
        $this->name = '';
        $this->type = '';
        $this->model = false;
        $this->settings = [];
        $this->numModel = null;

        return [
            'error' => false,
            'errorMessage' => '',
        ];
    }

    public function setFromForm(object $data)
    {
        $this->name = $data->name;
        $this->type = intval($data->type);
        $this->model = $data->model;
        $this->settings = $data->settings;
        $this->numModel = $data->numModel;
    }

    public function send($subscriberList, $renderedView, $diffusion = null)
    {
        // Derivate
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }


    public static function fetchStats(Stats $stats)
    {
        return [];
    }

    /**
     * Permet de savoir si on peut l'envoyer manuellement
     *
     * @return bolean
     */
    public function sendManually()
    {
        $manifest = static::getManifest();
        return $manifest->manually == 1;
    }
}

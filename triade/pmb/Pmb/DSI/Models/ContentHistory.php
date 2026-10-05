<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ContentHistory.php,v 1.10 2023/10/23 14:36:16 jparis Exp $

namespace Pmb\DSI\Models;

use Pmb\Common\Models\Model;
use Pmb\Common\Helper\Helper;
use Pmb\DSI\Models\Stats;

/**
 *
 * @author rtigero
 */
class ContentHistory extends Model implements CRUD
{
    public const CONTENT_TYPES_SUBSCRIBER = 1;

    public const CONTENT_TYPES_ITEM = 2;

    public const CONTENT_TYPES_VIEW = 3;

    public const CONTENT_TYPES_RENDER_VIEW = 4;

    public const CONTENT_TYPES_CHANNEL = 5;

    public const CONTENT_TYPES = [
        "subscribers" => ContentHistory::CONTENT_TYPES_SUBSCRIBER,
        "item" => ContentHistory::CONTENT_TYPES_ITEM,
        "view" => ContentHistory::CONTENT_TYPES_VIEW,
        "render_view" => ContentHistory::CONTENT_TYPES_RENDER_VIEW,
        "channel" => ContentHistory::CONTENT_TYPES_CHANNEL,
    ];

    protected $ormName = "Pmb\DSI\Orm\ContentHistoryOrm";

    public $idContentHistory = 0;

    public $type = 0;

    public $content = [];

    public $numDiffusionHistory = 0;

    public $diffusionHistory = 0;

    /**
     *
     * @param int $id
     *
     */
    public function __construct(int $id = 0)
    {
        $this->id = $id;
        $this->read();
    }

    public function create()
    {
        $orm = new $this->ormName();
        $orm->num_diffusion_history = $this->numDiffusionHistory;
        $orm->type = $this->type;
        $orm->content = json_encode($this->content);

        $orm->save();
        $this->id = $orm->{$this->ormName::$idTableName};
        $this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
    }

    public function update()
    {
        $orm = new $this->ormName($this->id);
        $orm->num_diffusion_history = $this->numDiffusionHistory;
        $orm->type = $this->type;
        $orm->content = json_encode($this->content);

        $orm->save();
    }

    public function read()
    {
        $this->fetchData();
        if (! empty($this->content)) {
            $this->content = json_decode($this->content);

            if (
                $this->type == ContentHistory::CONTENT_TYPES_CHANNEL &&
                !empty($this->content) &&
                !empty($this->content->settings->stats)
            ) {
                if(defined("GESTION")) {
                    $stats = $this->content->settings->stats;
                    $statsInstance = new Stats($stats->channelType, Helper::toObject($stats->settings));
                    $statsInstance->setReport($stats->report->type, $stats->report->data);
                    $statsInstance->fetchStats();
                    $this->content->settings->stats = $statsInstance;
                }
            }
        }
    }

    public function delete()
    {
        $orm = new $this->ormName($this->id);
        $orm->delete();
    }

    public function fetchDiffusionHistory()
    {
        if (!isset($this->diffusionHistory)) {
            $this->diffusionHistory = DiffusionHistory::getInstance($this->numDiffusionHistory);
        }
    }
}

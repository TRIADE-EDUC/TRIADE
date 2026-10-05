<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ContentBuffer.php,v 1.6 2023/07/28 11:49:28 qvarin Exp $

namespace Pmb\DSI\Models;

use Pmb\Common\Models\Model;
use Pmb\Common\Helper\Helper;

class ContentBuffer extends Model implements CRUD
{
    public const CONTENT_TYPES_SUBSCRIBER = 1;

    public const CONTENT_TYPES_ITEM = 2;

    public const CONTENT_TYPES_VIEW = 3;

    public const CONTENT_TYPES_RENDER_VIEW = 4;

    public const CONTENT_TYPES_CHANNEL = 5;

    public const CONTENT_TYPES = [
        "subscribers" => ContentBuffer::CONTENT_TYPES_SUBSCRIBER,
        "item" => ContentBuffer::CONTENT_TYPES_ITEM,
        "view" => ContentBuffer::CONTENT_TYPES_VIEW,
        "render_view" => ContentBuffer::CONTENT_TYPES_RENDER_VIEW,
        "channel" => ContentBuffer::CONTENT_TYPES_CHANNEL,
    ];

    protected $ormName = "Pmb\DSI\Orm\ContentBufferOrm";

    public $idContentBuffer = 0;

    public $type = 0;

    public $content = [];

    public $modified = 0;

    public $numDiffusionHistory = 0;

    public $diffusionHistory = null;

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
        $orm->modified = $this->modified;

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
        }
    }

    public function fetchDiffusionHistory()
    {
        if (!isset($this->diffusionHistory)) {
            $this->diffusionHistory = DiffusionHistory::getInstance($this->numDiffusionHistory);
        }
    }

    public function delete()
    {
        $orm = new $this->ormName($this->id);
        $orm->delete();
    }

    public function setContent($content)
    {
        if (!is_object($content)) {
            $content = is_string($content) ? $content : json_encode($content);
            $content = json_decode($content);

            if (!is_object($content) && !is_array($content)) {
                throw new \InvalidArgumentException("[ContentBuffer] Content invalid !");
            }
        }
        $this->content = $content;
    }
}
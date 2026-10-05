<?php

namespace Pmb\DSI\Models\View\RssView\EntitiesTemplates;

use Pmb\DSI\Models\Diffusion;

class ItemWatchTemplate extends RootTemplate implements TemplateInterface
{
    public const CONST_TYPE = TYPE_DOCWATCH;

    protected $id = 0;

    protected $instance;

    public const TITLE_TEMPLATE = "itemwatch_for_rss_title.tpl.html";

    public const DESCRIPTION_TEMPLATE = "itemwatch_for_rss_description.tpl.html";

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->instance = (new \docwatch_item($this->id))->get_normalized_item();
    }

    public function getPubDate()
    {
        return $this->instance['publication_date'];
    }

    public function getLink($tplLink)
    {
        return $this->instance['url'];
    }
}
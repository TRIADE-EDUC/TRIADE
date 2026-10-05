<?php

namespace Pmb\DSI\Models\View\RssView\EntitiesTemplates;

use Pmb\DSI\Models\Diffusion;

class DiffusionTemplate extends RootTemplate implements TemplateInterface
{
    public const CONST_TYPE = TYPE_DSI_DIFFUSION;

    protected $id = 0;

    protected $instance;

    public const TITLE_TEMPLATE = "diffusion_for_rss_title.tpl.html";

    public const DESCRIPTION_TEMPLATE = "diffusion_for_rss_description.tpl.html";

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->instance = new Diffusion($id);
    }

    public function getPubDate()
    {
        return $this->instance->getLastDiffusion();
    }
}
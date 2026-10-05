<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DiffusionDescriptors.php,v 1.1 2023/05/24 12:48:25 qvarin Exp $

namespace Pmb\DSI\Models;

use Pmb\Common\Helper\Helper;
use Pmb\Common\Models\Model;
use Pmb\DSI\Models\CRUD;

class DiffusionDescriptors extends Model implements CRUD
{
    protected $ormName = "Pmb\DSI\Orm\DiffusionDescriptorsOrm";

    protected $numDiffusion = 0;

    protected $numNoeud = 0;

    protected $diffusionDescriptorOrder = 0;

    protected $descriptorLabel = null;

    public function __construct(int $numNoeud = 0, int $numDiffusion = 0)
    {
        $this->numNoeud = $numNoeud;
        $this->numDiffusion = $numDiffusion;
        $this->read();
    }

    public function create()
    {
        $orm = new $this->ormName();
        $orm->num_diffusion = $this->numDiffusion;
        $orm->num_noeud = $this->numNoeud;
        $orm->diffusion_descriptor_order = $this->diffusionDescriptorOrder;

        $orm->save();
        $this->id = $orm->{$this->ormName::$idTableName};
        $this->{Helper::camelize($this->ormName::$idTableName)} = $orm->{$this->ormName::$idTableName};
    }

    public function update()
    {
        // Make a delete and create
    }

    public function delete()
    {
        $orm = new $this->ormName([
            "num_noeud" => $this->numNoeud,
            "num_diffusion" => $this->numDiffusion,
        ]);
        $orm->delete();
    }

    public function read()
    {
        $this->fetchData();
    }

    protected function fetchData()
    {
        if (
            !$this->datafetch &&
            $this->ormName::exist([
                "num_noeud" => $this->numNoeud,
                "num_diffusion" => $this->numDiffusion,
            ])
        ) {
            $orm = new $this->ormName([
                "num_noeud" => $this->numNoeud,
                "num_diffusion" => $this->numDiffusion,
            ]);

            $reflect = new \ReflectionClass($orm);
            $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);

            foreach ($props as $prop) {
                if (in_array($prop->getName(), ['structure'])) {
                    continue;
                }

                if (!$prop->isStatic() && !method_exists($this, Helper::camelize("fetch_".$prop->getName()))) {
                    $this->structure[] = Helper::camelize($prop->getName());
                    $this->{Helper::camelize($prop->getName())} = $orm->{$prop->getName()};
                }
            }

            $this->datafetch = true;
        }
    }

    public function getDescriptorLabel()
    {
        if (isset($this->descriptorLabel)) {
            return $this->descriptorLabel;
        }

        $category = new \category($this->numNoeud);
        $this->descriptorLabel = $category->get_isbd();
        return $this->descriptorLabel;
    }

    public function getNumNoeud()
    {
        return $this->numNoeud;
    }

    public function getNumDiffusion()
    {
        return $this->numDiffusion;
    }

    public function getOrder()
    {
        return $this->diffusionDescriptorOrder;
    }

    public function setNumNoeud(int $numNoeud)
    {
        $this->numNoeud = $numNoeud;
    }

    public function setNumDiffusion(int $numDiffusion)
    {
        $this->numDiffusion = $numDiffusion;
    }

    public function setOrder(int $order = 0)
    {
        $this->diffusionDescriptorOrder = $order;
    }
}

<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_diffusionslistabon_datasource_diffusionslistabon.class.php,v 1.1.4.2 2025/04/11 10:10:09 jparis Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

use Pmb\DSI\Orm\TagOrm;
use Pmb\DSI\Models\Tag;
use Pmb\DSI\Orm\DiffusionOrm;
use Pmb\DSI\Models\Diffusion;

class cms_module_diffusionslistabon_datasource_diffusionslistabon extends cms_module_common_datasource_list
{
    
    public function __construct($id = 0)
    {
        parent::__construct($id);

        $this->sortable = true;
        $this->limitable = false;
    }
    
    /*
     * Definition des selecteurs utilisables pour cette source de donnees
     */
    public function get_available_selectors(): array
    {
        return ["cms_module_common_selector_diffusions_generic"];
    }
    
    /*
     * Definition des criteres de tri utilisable pour cette source de donnees
     */
    protected function get_sort_criterias(): array
    {
        return [
            "id_diffusion",
            "name"
        ];
    }

    /*
     * Recuperation des donnees de la source
     */
    public function get_datas()
    {
        $selector = $this->get_selected_selector();
        $selectedValue = [];

        if ($selector && is_countable($selector->get_value()) && count($selector->get_value()) > 0) {
            foreach ($selector->get_value() as $value) {
                $selectedValue[] = $value;
            }
        }

        if (count($selectedValue)) {

            $orderBy = "";
            if (!empty($this->parameters["sort_by"])) {
                $orderBy .= addslashes($this->parameters["sort_by"]);
                if (!empty($this->parameters["sort_order"])) {
                    $orderBy .= " " . addslashes($this->parameters["sort_order"]);
                }
            }

            $tagsOrm = TagOrm::findAll();
            
            $tags = [];
            foreach ($tagsOrm as $tagOrm) {
                $tags[] = $this->formatTag($tagOrm->id_tag);
            }

            $diffusionsOrm = DiffusionOrm::finds([
                "id_diffusion" => [
                    "operator" => "in",
                    "value" => $selectedValue
                ]
            ], $orderBy);

            $diffusions = [];

            foreach ($diffusionsOrm as $diffusionOrm) {
                $diffusionModel = new Diffusion($diffusionOrm->id_diffusion);
                $diffusionModel->fetchLastDiffusion();
                $diffusionModel->fetchItem();

                $diffusion = $this->formatDiffusion($diffusionModel);
                
                foreach($tags as &$tag) {
                    if(is_array($diffusionModel->tags)) {
                        foreach($diffusionModel->tags as $diffusionTag) {
                            if($diffusionTag->id == $tag['id']) {
                                $tag["diffusions"][] = $diffusion;
                            }
                        }
                    }
                }
            
                $diffusions[] = $diffusion;
            }
            
            return [
                "diffusions" => $diffusions,
                "tags" => $tags
            ];
        }

        return false;
    }

    private function formatTag(int $idTag): array
    {
        $tagModel = new Tag($idTag);
    
        return [
            'id' => $tagModel->id,
            'name' => $tagModel->name,
            'diffusions' => [],
        ];
    }

    private function formatDiffusion($diffusionModel)
	{
        global $id_empr;

        return [
            'id' => $diffusionModel->idDiffusion,
            'name' => $diffusionModel->name,
            'lastDiffusion' => $diffusionModel->lastDiffusion,
            'nbResults' => $diffusionModel->item->getNbResults(),
            'isSubscribed' => $diffusionModel->isSubscribed($id_empr),
        ];
	}
}

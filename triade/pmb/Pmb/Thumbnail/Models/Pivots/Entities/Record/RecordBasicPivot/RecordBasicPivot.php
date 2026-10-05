<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RecordBasicPivot.php,v 1.11 2024/04/08 13:56:12 tsamson Exp $
namespace Pmb\Thumbnail\Models\Pivots\Entities\Record\RecordBasicPivot;

use Pmb\Thumbnail\Models\Pivots\RootPivot;
use Pmb\Thumbnail\Orm\SourcesEntitiesOrm;

class RecordBasicPivot extends RootPivot
{
    /**
     * correspondance par defaut
     * @var integer
     */
    public const DEFAULT_MATCH = 0;
    
    /**
     * correspondance sur le type de document et le niveau bibliographique
     * @var integer
     */
    public const TYPEDOC_NIVBIBLIO_MATCH = 1;
    
    /**
     * correspondance sur le niveau bibliographique uniquement
     * @var integer
     */
    public const NIVBIBLIO_MATCH = 2;
    
    /**
     * nom du composant a utiliser
     * @var string
     */
    public const COMPONENT_NAME = "record_basic_pivot";

    /**
     * donnees par defaut du pivot
     * @var string
     */
    public const DEFAULT_PIVOT = '{"typedoc": "", "nivbiblio": ""}';

    /**
     * type d'entite du pivot
     * @var string
     */
    protected $type = TYPE_NOTICE;
    
    /**
     * type d'entite statique du pivot
     * @var string
     */
    protected static $staticType = TYPE_NOTICE;

    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Pivots\RootPivot::getPivotData()
     */
    public function getPivotData(object $entity) : array
    {
        return [
            "typedoc" => $entity->get_typdoc() ?? "",
            "nivbiblio" => $entity->get_niveau_biblio() ?? ""
        ];
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Pivots\RootPivot::setDataFromForm()
     */
    public function setDataFromForm(object $pivot) : void
    {
        $data = new \stdClass();
        $data->typedoc = $pivot->typedoc ?? "";
        $data->nivbiblio = $pivot->nivbiblio ?? "";
        $this->pivot = \encoding_normalize::json_encode($data);
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Pivots\RootPivot::check()
     */
    public function check(object $entity) : bool
    {
        $entityData = $this->getPivotData($entity);
        $data = \encoding_normalize::json_decode($this->getData(), true);
        if ($data["typedoc"] == $entityData["typedoc"] && $data["nivbiblio"] == $entityData["nivbiblio"]) {
            return true;
        }
        return false;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Pivots\RootPivot::getDataForForm()
     */
    public function getDataForForm() : array
    {
        return [
            "sources" => $this->getSourceClass(),
            "pivot" => \encoding_normalize::json_decode($this->getData(), true)
        ];
    }

    /**
     * donnees pour la vue
     * @return array
     */
    public static function getViewData() : array
    {
        $typedoc = new \marc_list(\marc_list::TYPE_DOCTYPE);
        $nivbiblio = new \marc_list(\marc_list::TYPE_NIVEAU_BIBLIO);
        
        return array_merge(parent::getViewData(), [
            "typedoc" => $typedoc->table ?? [],
            "nivbiblio" => $nivbiblio->table ?? [],
        ]);
    }
    
    /**
     * sources en fonction de l'id d'objet
     * @param int $objectId
     * @return array
     */
    public static function getSourcesFromObjectId(int $objectId) : array
    {
        $recordDatas = new \record_datas($objectId);
        if (!$recordDatas->is_existing_record()) {
            return [];
        }
        $sourcesEntities = SourcesEntitiesOrm::finds(["pivot_class" => static::class], "ranking");
        $pivotsOrm = static::getMatchPivot($sourcesEntities, $recordDatas, RecordBasicPivot::TYPEDOC_NIVBIBLIO_MATCH);
        $pivotsOrm = array_merge($pivotsOrm, static::getMatchPivot($sourcesEntities, $recordDatas, RecordBasicPivot::NIVBIBLIO_MATCH));
        $pivotsOrm = array_merge($pivotsOrm, static::getMatchPivot($sourcesEntities, $recordDatas));
        
        $sources = [];
        foreach ($pivotsOrm as $pivotOrm) {
            $pivotInstance = new static($pivotOrm->id);
            $sources[] = $pivotInstance->getSourceClass();
        }
        return $sources;
    }
    
    /**
     * retourne les pivots correspondants
     * @param array $sourcesEntities
     * @param \record_datas $recordDatas
     * @param int $match
     * @return array
     */
    protected static function getMatchPivot(array $sourcesEntities, \record_datas $recordDatas, int $match = RecordBasicPivot::DEFAULT_MATCH) : array
    {
        $matches = array();
        switch ($match) {
            case RecordBasicPivot::DEFAULT_MATCH:
                foreach ($sourcesEntities as $sourcesEntitie) {
                    $pivot = \encoding_normalize::json_decode($sourcesEntitie->pivot, true);
                    if (empty($pivot['typedoc']) || empty($pivot['nivbiblio'])) {
                        $matches[] = $sourcesEntitie;
                    }
                }
                break;
                
            case RecordBasicPivot::NIVBIBLIO_MATCH:
                foreach ($sourcesEntities as $sourcesEntitie) {
                    $pivot = \encoding_normalize::json_decode($sourcesEntitie->pivot, true);
                    if (empty($pivot['typedoc']) || empty($pivot['nivbiblio'])) {
                        continue;
                    }
                    
                    if ($pivot['nivbiblio'] == $recordDatas->get_niveau_biblio()) {
                        $matches[] = $sourcesEntitie;
                    }
                }
                break;
                
            case RecordBasicPivot::TYPEDOC_NIVBIBLIO_MATCH:
                foreach ($sourcesEntities as $sourcesEntitie) {
                    $pivot = \encoding_normalize::json_decode($sourcesEntitie->pivot, true);
                    if (empty($pivot['typedoc']) || empty($pivot['nivbiblio'])) {
                        continue;
                    }
                    
                    if ($pivot['typedoc'] == $recordDatas->get_typdoc() && $pivot['nivbiblio'] == $recordDatas->get_niveau_biblio()) {
                        $matches[] = $sourcesEntitie;
                    }
                }
                break;
                
            default:
                return [];
        }
        return $matches;
    }
}


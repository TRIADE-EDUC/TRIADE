<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RootPivot.php,v 1.7 2023/10/27 14:09:50 tsamson Exp $
namespace Pmb\Thumbnail\Models\Pivots;

use Pmb\Common\Helper\ParserMessage;
use Pmb\Common\Models\Model;
use Pmb\Thumbnail\Orm\SourcesEntitiesOrm;
use Pmb\Common\Orm\Orm;
use Pmb\Thumbnail\Models\Sources\RootThumbnailSource;

abstract class RootPivot extends Model implements Pivot
{
    use ParserMessage;

    /**
     * nom du composant
     * @var string
     */
    public const COMPONENT_NAME = "";
    
    /**
     * pivot par defaut
     * @var string
     */
    public const DEFAULT_PIVOT = '{}';
    
    /**
     * nom de l'orm a utiliser pour gerer les donnees
     * @var Orm
     */
    protected $ormName = SourcesEntitiesOrm::class;

    /**
     * nom de la classe de source
     * @var string
     */
    protected $sourceClass = "";
    
    /**
     * nom de la classe de pivot
     * @var string
     */
    protected $pivotClass = "";

    /**
     * type de pivot
     * @var integer
     */
    protected $type = 0;
    
    /**
     * donnees du pivot au format json
     * @var string
     */
    protected $pivot = "";
    
    /**
     * rang du pivot
     * @var integer
     */
    protected $ranking = 0;
   
    /**
     * type statique du pivot a utiliser dans les methodes statiques (getViewData)
     * @var integer
     */
    protected static $staticType = 0;

    /**
     * classe gerant un pivot de donnees
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct($id);
        if (empty($this->pivotClass)) {
            $this->pivotClass = static::class;
        }
        if (empty($this->pivot)) {
            $this->pivot = static::DEFAULT_PIVOT;
        }
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Pivots\Pivot::getName()
     */
    public function getName() : string
    {
        $messages = static::getMessages();
        return $messages['name'] ?? '';
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Pivots\Pivot::getPivotData()
     */
    public function getPivotData(object $entity) : array
    {
        return [];
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Pivots\Pivot::setDataFromForm()
     */
    public function setDataFromForm(object $pivot) : void
    {
        # code here...
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Pivots\Pivot::check()
     */
    public function check(object $entity) : bool
    {
        return false;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Pivots\Pivot::getData()
     */
    public function getData()
    {
        return $this->pivot;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Thumbnail\Models\Pivots\Pivot::getDataForForm()
     */
    public function getDataForForm() : array
    {
        return [];
    }
    
    /**
     * enregistrement des donnees du pivot
     * @return bool
     */
    public function save() : bool
    {
        $orm = new $this->ormName($this->id);
        $orm->source_class = $this->getSourceClass();
        $orm->pivot_class = $this->pivotClass;
        $orm->type = $this->getType();
        $orm->pivot = $this->getData();
        $orm->ranking = $this->getRanking();
        $orm->save();
        
        $this->id = $orm->id;
        return !empty($this->id);
    }
    
    /**
     * recuperation du nom de la classe
     * @return string
     */
    public function getSourceClass() : string
    {
        return $this->sourceClass;
    }

    /**
     * recuperation du type
     * @return int
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * recuperation du rang
     * @return int
     */
    public function getRanking() : int
    {
        return $this->ranking;
    }

    /**
     * definition la classe utilisee
     * @param string $sourceClass
     */
    public function setSourceClass(string $sourceClass) : void
    {
        $this->sourceClass = $sourceClass;
    }

    /**
     * definition du rang
     * @param int $ranking
     */
    public function setRanking(int $ranking) : void
    {
        $this->ranking = $ranking;
    }
    
    /**
     * definition du type
     * @param int $type
     */
    public function setType(int $type) : void
    {
        $this->type = $type;
    }
    
    /**
     * donnees pour la vue
     * @return array
     */
    public static function getViewData() : array
    {
        $pivotsOrm = SourcesEntitiesOrm::finds([
            "pivot_class" => static::class,
            "type" => static::$staticType
        ]);
        
        $pivots = [];
        foreach ($pivotsOrm as $pivotOrm) {
            $instance = new static($pivotOrm->id);
            $formdata = $instance->getDataForForm();
            
            if (!isset($pivots[md5($instance->getData())])) {
                $pivots[md5($instance->getData())] = $formdata;
            }
        }
        
        if (empty($pivots)) {
            $pivots[] = ["pivot" => \encoding_normalize::json_decode(static::DEFAULT_PIVOT, true)];
        }
        
        return [
            "namespace" => static::class,
            "component" => static::COMPONENT_NAME,
            "messages" => static::getMessages(),
            "pivots" => array_values($pivots) ?? []
        ];
    }
    
    /**
     * sources en fonction de l'id d'objet
     * @param int $objectId
     * @return array
     */
    public static function getSourcesFromObjectId(int $objectId) : array
    {
        return [];
    }
}
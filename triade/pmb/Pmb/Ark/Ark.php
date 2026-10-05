<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Ark.php,v 1.6 2022/09/02 13:22:44 rtigero Exp $
namespace Pmb\Ark;

use Pmb\Ark\Entities\ArkEntity;

class Ark
{

    /**
     *
     * @var int
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $arkIdentifier;

    /**
     *
     * @var int
     */
    protected $naan;

    /**
     *
     * @var string
     */
    protected $shoulder;

    /**
     *
     * @var string
     */
    protected $qualifiers;

    /**
     *
     * @var ArkEntity
     */
    protected $arkEntity;

    /**
     *
     * @var string
     */
    protected $entityType;
    
    /**
     * 
     * @var array
     */
    protected $metadata;

    /**
     *
     * @const array
     */
    const RULES = [
        "authorized" => "0123456789bcdfghjkmnpqrstvwxz",
        "length" => 10
    ];

    /**
     *
     * @const string
     */
    const LABEL = "ark:";

    /**
     *
     * @param string $ark
     * @param int $entityId
     */
    public function __construct(string $ark = "", int $entityId = 0)
    {
        if (!empty($ark) && $this->isValid()) {
            $this->arkIdentifier = $ark;
        }
        $this->fetchData();
    }

    /**
     */
    private function fetchData()
    {
        $query = "SELECT * FROM ark WHERE identifier = '$this->arkIdentifier'";

        if (!$this->id && isset($this->arkEntity)) {
            $this->id = $this->arkEntity->getArkId();
            $query = "SELECT * FROM ark WHERE id = '$this->id'";
        }

        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result) > 0) {
            $ark = pmb_mysql_fetch_assoc($result);
            $this->metadata = json_decode(stripslashes($ark['metadata']), true);
            $this->arkIdentifier = $ark['identifier'];
            $this->id = $ark['id'];
            $this->entityType = $ark['entity_type'];
        }
    }

    /**
     *
     * @return string
     */
    private function getShoulder()
    {
        if (! isset($this->arkEntity)) {
            return "";
        }
        $type = $this->arkEntity->getArkTypeObject();
        switch ($type) {
            case 1:
                return "nt";
            default:
                return "";
        }
    }

    /**
     *
     * @return int
     */
    private function getNaan()
    {
    	if(empty($this->naan)) {
	    	global $pmb_ark_naan;
	    	if(isset($pmb_ark_naan)) {
	    		$naans = \json_decode($pmb_ark_naan);
	    		$this->naan = $naans[0];
	    	}
    	}
    	return $this->naan;
    }

    /**
     *
     * @return string
     */
    private function getQualifiers()
    {
    	if(empty($this->qualifiers)) {
    		$this->qualifiers = $this->arkEntity->getQualifiers();
    	}
    	return $this->qualifiers;
    }

    /**
     *
     * @return string
     */
    private function generateArkIdentifier()
    {
        $ark = $this->getShoulder();
        $length = self::RULES["length"] - strlen($ark);

        for ($i = 0; $i < $length; $i ++) {
            $index = rand(0, strlen(self::RULES["authorized"]) - 1);
            $ark .= self::RULES["authorized"][$index];
        }
        $query = "SELECT * FROM ark WHERE identifier = '$ark'";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result) > 0) {
            $ark = $this->generateArkIdentifier();
        }
        return $ark;
    }

    /**
     *
     * @return string
     */
    public function getArkIdentifier()
    {
        if (isset($this->arkIdentifier)) {
            return $this->arkIdentifier;
        }
        $this->arkIdentifier = $this->generateArkIdentifier();

        return $this->arkIdentifier;
    }

    /**
     *
     * @param
     *            ArkEntity
     */
    public function setArkEntity($arkEntity)
    {
        if ($arkEntity instanceof ArkEntity) {
            $this->arkEntity = $arkEntity;
        }
        $this->fetchData();
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     *
     * @return int
     */
    public function getEntityType()
    {
        return $this->entityType;
    }
    /**
     *
     * @return number
     */
    public function save()
    {
        $query = "
                INSERT INTO ark
                (identifier, metadata, entity_type)
                VALUES
                ('{$this->arkIdentifier}', '', '')";
        pmb_mysql_query($query);
        $this->id = pmb_mysql_insert_id();
        $this->arkEntity->setArkId($this->id);
        $this->arkEntity->save();
        return $this->id;
    }

    /**
     *
     * @return bool
     */
    public function isValid()
    {
        return true;
        $parsedIdentifier = explode('/', $this->arkIdentifier);
        if (($parsedIdentifier[0] == $this->getNaan()) && (strpbrk($parsedIdentifier[1], $this->RULES["forbidden"]))) {
            return true;
        }
        return false;
    }
    
    public function getOpacUrl()
    {
        return "";
    }
    
    public function getReplacedBy() 
    {
        if(isset($this->metadata['replaced']) && !empty($this->metadata['replaced']['replaced_by'])){
            return $this->metadata['replaced']['replaced_by'];
        }
        return "";
    }
    
    public function getArkLink() {
    	global $opac_url_base;
    	$naan = $this->getNaan();
    	if(empty($naan) || empty($this->arkIdentifier)) {
    		return "";
    	}
    	$arkLink = $opac_url_base . self::LABEL . "/" . $naan . "/" .$this->arkIdentifier;
    	$qualifiers = $this->getQualifiers();
    	if(! empty($qualifiers)) {
    		$arkLink .= "/" . $qualifiers;
    	}
    	return $arkLink;
    }
}
<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArkAuthority.php,v 1.3 2023/01/09 15:44:23 gneveu Exp $
namespace Pmb\Ark\Entities;

class ArkAuthority extends ArkEntityPmb
{

    /**
     *
     * @var \authority
     */
    private $authority;
    
    /**
     *
     * @param int $authorityId
     */
    public function __construct(int $authorityId)
    {
        $this->authority = \authorities_collection::get_authority(AUT_TABLE_AUTHORITY, $authorityId);
        $this->arkTypeObject = \authority::aut_const_to_type_const($this->authority->get_type_object());
        parent::__construct($authorityId);
    }

   /**
    * 
    * {@inheritDoc}
    * @see \Pmb\Ark\Entities\ArkEntityPmb::updateMetadata()
    */
    protected function updateMetadata()

    {
        global $pmb_type_audit;
        parent::updateMetadata();
        $this->metadata['type'] = \authority::aut_const_to_string($this->authority->get_type_object());

        if (isset($this->metadata['last_updated'])) {
            /**
             * Récuperation des infos dans l'audit
             */
            if ($pmb_type_audit > 0) {
                $audit = new \audit($this->authority->get_audit_type(), $this->entityId);
                $audit->get_all();
                $last = $audit->get_last();
                if (is_object($last)) {
                    $this->metadata['user'] = $last->prenom_nom;
                    $this->metadata['last_updated'] = $last->quand;
                }
            }
        }
    }
    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\Ark\Entities\ArkEntityPmb::getOpacUrl()
     */
    public function getOpacUrl()
    {
        switch($this->arkTypeObject){
            case TYPE_AUTHOR:
                $this->lvl = "author_see";
                break;
            case TYPE_CATEGORY:
                $this->lvl = "categ_see";
                break;
            case TYPE_PUBLISHER:
                $this->lvl = "publisher_see";
                break;
            case TYPE_COLLECTION:
                $this->lvl = "coll_see";
                break;
            case TYPE_SUBCOLLECTION:
                $this->lvl = "subcoll_see";
                break;
            case TYPE_SERIE:
                $this->lvl = "serie_see";
                break;
            case TYPE_TITRE_UNIFORME:
                $this->lvl = "titre_uniforme_see";
                break;
            case TYPE_INDEXINT:
                $this->lvl = "indexint_see";
                break;
            case TYPE_AUTHPERSO:
                $this->lvl = "authperso_see";
                break;
            case TYPE_CONCEPT:
                $this->lvl = "concept_see";
                break;
        }
        return self::OPAC_ENTRY_POINT."?lvl=".$this->lvl."&id=".$this->authority->get_num_object();
    }
}
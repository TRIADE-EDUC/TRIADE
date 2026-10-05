<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArkBulletin.php,v 1.1 2022/03/01 08:26:35 tsamson Exp $
namespace Pmb\Ark\Entities;

class ArkBulletin extends ArkEntityPmb
{

    /**
     *
     * @var int
     */
    protected $arkTypeObject = TYPE_BULLETIN;
    
    /**
     * 
     * @var string
     */
    protected $lvl = "bulletin_display";

    /**
     */
    protected function updateMetadata()
    {
        global $pmb_type_audit;
        parent::updateMetadata();
        $this->metadata['type'] = 'bulletin';
        if (isset($this->metadata['last_updated'])) {
            /**
             * Récuperation des infos dans l'audit
             */
            $last_updated = "";
            if ($pmb_type_audit > 0) {
                $audit = new \audit(AUDIT_BULLETIN, $this->entityId);
                $audit->get_all();
                $last = $audit->get_last();
                if (is_object($last)) {
                    $this->metadata['user'] = $last->prenom_nom;
                    $last_updated = $last->quand;
                }
            } else {
                $last_updated = date("Y-m-d H:i:s");
            }
            $this->metadata['last_updated'] = $last_updated;
        }
    }
}
<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArkRecord.php,v 1.2 2023/06/07 10:27:46 tsamson Exp $
namespace Pmb\Ark\Entities;

use Pmb\Thumbnail\Models\ThumbnailSourcesHandler;

class ArkRecord extends ArkEntityPmb
{
    /**
     * 
     * @var int
     */
    protected $arkTypeObject = TYPE_NOTICE;
    
    /**
     * 
     * @var string
     */
    protected $lvl = "notice_display";
    /**
     *
     * @param int $noticeId
     */
    public function __construct(int $noticeId)
    {
        parent::__construct($noticeId);
    }

    /**
     */
    protected function updateMetadata()
    {
        global $pmb_type_audit;
        parent::updateMetadata();

        $this->metadata['type'] = 'notice';

        if (isset($this->metadata['last_updated'])) {
            /**
             * Récuperation des infos dans l'audit ou dans la table notices
             */
            $last_updated = "";
            if ($pmb_type_audit > 0) {
                $audit = new \audit(AUDIT_NOTICE, $this->entityId);
                $audit->get_all();
                $last = $audit->get_last();
                if (is_object($last)) {
                    $this->metadata['user'] = $last->prenom_nom;
                    $last_updated = $last->quand;
                }
            } else {
                $query = "
                    SELECT update_date FROM notices WHERE notice_id = '$this->entityId'
                ";
                $result = pmb_mysql_query($query);
                if (pmb_mysql_num_rows($result) > 0) {
                    $last_updated = pmb_mysql_result($result, 0, 0);
                }
            }
            $this->metadata['last_updated'] = $last_updated;
        }
    }
    
    public function getThumbnail() {
        $thumbnailSourcesHandler = new ThumbnailSourcesHandler();
        return $thumbnailSourcesHandler->generateUrl(TYPE_NOTICE, $this->entityId);
    }
}
<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DigitalSignatureEvent.php,v 1.1 2022/05/24 08:45:24 jparis Exp $
namespace Pmb\Digitalsignature\Event;

use Pmb\Common\Event\Event;
use Pmb\Digitalsignature\Models\DocnumCertifier;

class DigitalSignatureEvent extends Event
{
    /**
     * Instance d'un document numerique
     *
     * @var explnum|null
     */
    protected $explnum = null;
    
    /**
     * Instance du certifier
     *
     * @var DocnumCertifier
     */
    protected $certifier = null;
    
    /**
     * Metadonnees a ajoute a la signature du document numerique
     *
     * @var array
     */
    protected $meta = array();
    
    protected $errors = "";
    
    /**
     * @return explnum|null
     */
    public function get_explnum()
    {
        return $this->explnum;
    }
    
    
    /**
     * @return DocnumCertifier|null
     */
    public function get_certifier()
    {
        return $this->certifier;
    }
    
    /**
     * @return array
     */
    public function get_meta()
    {
        return $this->meta;
    }

    /**
     * @param explnum $explnum
     */
    public function set_explnum(&$explnum)
    {
        $this->explnum = $explnum;
    }
    
    /**
     * @param string $certifier
     */
    public function set_certifier($certifier)
    {
        $this->certifier = $certifier;
    }
    
    /**
     * @param array $meta
     */
    public function set_meta($meta)
    {
        $this->meta = $meta;
    }
   
    /**
     * @param string $errors
     */
    public function set_errors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return string
     */
    public function get_errors()
    {
        return $this->errors;
    }
    
    /**
     * Indique si on a des erreurs
     *
     * @return boolean
     */
    public function has_errors()
    {
        if (count($this->errors) > 0) {
            return true;
        }
        return false;
    }
}
<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MFAOtp.php,v 1.2 2023/06/22 14:23:09 jparis Exp $

namespace Pmb\MFA\Models;
use Pmb\Common\Models\Model;

class MFAOtp extends Model 
{
    protected $ormName = "Pmb\MFA\Orm\MFAOtpOrm";
    
    public $context = "";
    public $hashMethod = "sha1";
    public $lifetime = 30;
    public $lengthCode = 6;
    
    public function create()
    {
        $orm = $this->save();
        $this->id = $orm->{$this->ormName::$idTableName};
    }
    
    public function update()
    {   
        $this->save();
    }
    
    public function delete()
    {
        $orm = new $this->ormName($this->id);
        $orm->delete();
    }
    
    protected function save()
    {
        $orm = new $this->ormName($this->id);
        
        $orm->context = $this->context;
        $orm->hash_method = $this->hashMethod;
        $orm->lifetime = $this->lifetime;
        $orm->length_code = $this->lengthCode;
        
        $orm->save();
        
        return $orm;
    }
    
    public function setFromForm(object $data)
    {
        $this->context = $data->context;
        $this->hashMethod = $data->hashMethod;
        $this->lifetime = $data->lifetime;
        $this->lengthCode = $data->lengthCode;
    }
}
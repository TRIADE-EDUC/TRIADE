<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MFASms.php,v 1.3 2023/07/13 13:17:02 jparis Exp $

namespace Pmb\MFA\Models;
use Pmb\Common\Models\Model;
use translation;

class MFASms extends Model 
{
    protected $ormName = "Pmb\MFA\Orm\MFASmsOrm";
    
    public $context = "";
    public $content = "";
    
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
        $orm->content = $this->content;
        
        $orm->save();
        
        return $orm;
    }
    
    public function setFromForm(object $data)
    {
        $this->context = $data->context;
        $this->content = $data->content;
    }

    public function getTranslatedContent() {
        return translation::get_translated_text($this->id, "mfa_sms", "content", $this->content);
    }
}
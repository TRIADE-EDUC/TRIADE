<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MFAServices.php,v 1.4 2023/07/18 08:51:42 jparis Exp $

namespace Pmb\MFA\Models;
use Pmb\Common\Models\Model;
use translation;

class MFAServices extends Model 
{
    protected $ormName = "Pmb\MFA\Orm\MFAServicesOrm";
    
    public $context = "";
    public $application = false;
    public $mail = false;
    public $sms = false;
    public $required = false;
    public $suggestMessage = "";

    public function __construct(int $id = 0)
    {
        global $msg;

        if(!empty($msg)) {
            $this->suggestMessage = stripcslashes($msg["mfa_default_suggest_message"]);
        }

        parent::__construct($id);
    }
    
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
        $orm->application = $this->application;
        $orm->mail = $this->mail;
        $orm->sms = $this->sms;
        $orm->required = $this->required;
        $orm->suggest_message = $this->suggestMessage;
        
        $orm->save();
        
        return $orm;
    }
    
    public function setFromForm(object $data)
    {
        $this->context = $data->context;
        $this->application = $data->application;
        $this->mail = $data->mail;
        $this->sms = $data->sms;
        $this->required = $data->required;
        $this->suggestMessage = $data->suggestMessage;
    }

    public function getTranslatedSuggestMessage() {
        return translation::get_translated_text($this->id, "mfa_services", "suggest_message", $this->suggestMessage);
    }
}
<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DocnumCertifier.php,v 1.24.4.1 2025/03/24 12:47:31 rtigero Exp $
namespace Pmb\Digitalsignature\Models;

use Pmb\Common\Orm\UploadFolderOrm;
use Pmb\Digitalsignature\Event\DigitalSignatureEvent;

class DocnumCertifier implements SignInterface
{

    protected $formatedFields = array();

    protected $signature;

    protected $entity = null;

    public $prefix = "sign_docnum";

    public function __construct($entity)
    {
        /**
         *
         * @var \explnum $entity
         */
        $this->entity = $entity;
    }

    /**
     *
     * @param int $signature
     * @return void
     *
     * @see \Pmb\Digitalsignature\Models\SignInterface::sign()
     */
    public function sign($signature): void
    {
        global $msg;

        if ($this->checkSignExists()) {
            return;
        }

        if (empty($this->entity->explnum_nomfichier)) {
            return;
        }

        // Publie un evenement au plugin avant la signature
        $event_before = new DigitalSignatureEvent("digital_signature", "before_sign");
        $evth = \events_handler::get_instance();
        $event_before->set_explnum($this->entity);
        $event_before->set_certifier($this);
        $evth->send($event_before);

        $plugins = \plugins::get_instance();
        if (isset($plugins->get_plugins()["digital_signature"])) {
            if (! empty($event_before->get_errors())) {
                error_message("pb_signature", $event_before->get_errors(), true, "./catalog.php?categ=edit_explnum&id=" . $this->entity->explnum_notice . "&explnum_id =" . $this->entity->explnum_id);
                die();
            }
        }

        $name = $this->getCmsFilePath();
        $nameJson = $name . ".json";

        $this->signature = new SignatureModel($signature);

        $folder = new \upload_folder($this->entity->explnum_repertoire);
        $filePath = $folder->repertoire_path . $this->entity->explnum_path . $this->entity->explnum_nomfichier;
        $filePath = str_replace('//', '/', $filePath);

        if (! file_exists($filePath)) {
            return;
        }

        $certificate = $this->signature->certificate[0];
        $exec = exec("openssl cms -sign -in " . $filePath . " -binary -signer " . $certificate->cert . " -inkey " . $certificate->private_key . " -outform PEM -out " . $name);
        if (false === $exec) {
            return;
        }

        if (! file_exists($name)) {
            return;
        }

        $content = file_get_contents($name);

        $dcf = new DocnumCertifiedFields();
        $dcf->setRecordId($this->entity->explnum_notice);
        $dcf->setDocnumId($this->entity->explnum_id);

        $data = json_decode($dcf->getFields($this->signature->fields));
        $data->signature = $content;

        // On test si le plugin de signature est present et on rajoute des metadonnees
        if (isset($plugins->get_plugins()["digital_signature"])) {
            if (! empty($event_before->get_meta())) {
                $data->extra = $event_before->get_meta();
            }
        }
        $content = json_encode($data, JSON_UNESCAPED_SLASHES);
        $fpc = file_put_contents($nameJson, $content);
        if (false === $fpc) {
            return;
        }

        $exec = exec("openssl cms -sign -in " . $nameJson . " -binary -signer " . $certificate->cert . " -inkey " . $certificate->private_key . " -outform PEM -nodetach -out " . $name);
        if (false === $exec) {
            return;
        }

        if (file_exists($nameJson)) {
            unlink($nameJson);
        }

        // Publie un evenement au plugin apres la signature
        $event_after = new DigitalSignatureEvent("digital_signature", "after_sign");
        $event_after->set_explnum($this->entity);
        $event_after->set_certifier($this);
        $evth->send($event_after);
    }

    public function check(): array
    {
        global $base_path;
        if (file_exists($this->getCmsFilePath())) {
            $name = $this->getCmsFilePath();
            $nameTmp = $base_path . "/temp/verify.cms";
            $nameJson = $name . ".json";

            $folder = new \upload_folder($this->entity->explnum_repertoire);
            $filePath = $folder->repertoire_path . $this->entity->explnum_path . $this->entity->explnum_nomfichier;
            $filePath = str_replace('//', '/', $filePath);

            $exec = exec("openssl cms -verify -in " . $name . " -inform PEM -binary -noverify -out " . $nameJson . " 2>&1", $output);
            $check = false;
            if (strpos(implode("\n", $output), "Verification successful") !== false) {
                $json = json_decode(file_get_contents($nameJson), true);
                file_put_contents($nameTmp, $json['signature']);
                $exec = exec("openssl cms -verify -in " . $nameTmp . " -inform PEM -content " . $filePath . " -binary -noverify -out /dev/null 2>&1", $output_pmb);
                if (strpos(implode("\n", $output_pmb), "Verification successful") !== false) {
                    unlink($nameTmp);
                    if (file_exists($nameJson)) {
                        unlink($nameJson);
                    }

                    $check = true;
                }
            }

            if (file_exists($nameTmp)) {
                unlink($nameTmp);
            }
            if (file_exists($nameJson)) {
                unlink($nameJson);
            }
            unset($json['signature']);

            $json['meta'] = $this->getMetadata();
            return [
                "check" => $check,
                "data" => $json
            ];
        }

        return [
            "check" => false,
            "data" => []
        ];
    }

    public function getMetadata(): array
    {
        global $base_path, $msg;
        $folder = new \upload_folder($this->entity->explnum_repertoire);
        $filePath = $folder->repertoire_path . $this->entity->explnum_nomfichier;

        $nameTmp = $base_path . "/temp/verify_info.txt";
        $exec = exec("openssl cms -verify -in " . $this->getCmsFilePath() . " -cmsout -print -inform PEM 2>&1 -out " . $nameTmp);

        $content = file_get_contents($nameTmp);

        $data = [];
        if (preg_match("/issuer:.*emailAddress=(.*)/", $content, $matchesMail)) {
            $data['issuer'] = $matchesMail[1];
        }

        if (preg_match("/UTCTIME:(.*GMT)/", $content, $matchesDate)) {
            $date = new \DateTime($matchesDate[1]);
            $format = $msg['1005'] ?? $msg['date_format'];
            $data['date'] = $date->format($format . " H:i:s");
        }
        return $data;
    }

    public function getFieldsFromFile(): string
    {
        $folder = new \upload_folder($this->entity->explnum_repertoire);
        $filePath = $folder->repertoire_path . $this->entity->explnum_nomfichier;

        $end = "-----END CMS-----";
        $data = file_get_contents($this->getCmsFilePath());

        $data = substr($data, strpos($data, $end) + strlen($end), strlen($data));
        return $data;
    }

    public function save(): void
    {}

    public function getCmsFilePath($suffix = "")
    {
        global $pmb_digital_signature_folder_id;
        if ($this->entity instanceof \explnum) {
            if ($pmb_digital_signature_folder_id) {
                $rep = $this->entity->explnum_path == "/" ? $pmb_digital_signature_folder_id : $this->entity->explnum_repertoire;
                $folder = new \upload_folder($rep);
                $path = $folder->repertoire_path . $this->entity->explnum_path . $this->prefix . "_" . $this->entity->explnum_id . $suffix . ".cms";
                $path = str_replace('//', '/', $path);
                return $path;
            }
        }

        return "";
    }

    public static function getJsCheck()
    {
        global $msg;
        $js = "
            <script>
                if(typeof Certifier === 'undefined'){
                    class Certifier {
                        showDialog(event, data, id, opac, link) {
                            if(!document.getElementById('sign_info_dialog')) {
                                var dialog = document.createElement('div');
                                dialog.setAttribute('id','sign_info_dialog');
                                dialog.style.background = '#f3f3f3'
                                dialog.style.border = '1px solid rgb(172, 172, 172)'
                                dialog.style.color = '#333'
                                dialog.style.position = 'absolute'
                                dialog.style.top = (event.pageY) + 'px'
                                dialog.style.left = event.pageX + 'px'
                                dialog.style.padding = '5px'
                                
                                var sign = document.createElement('p')
                                sign.innerHTML = '<p style=\'margin: 0px;\'>" . $msg["digital_signature_date_metadata"] . "' + data.meta.date + '</p>'
                                sign.innerHTML += '<p style=\'margin: 0px;\'>" . $msg["digital_signature_signer_metadata"] . "' + data.meta.issuer + '</p>'
                                sign.innerHTML += '<u>" . $msg["digital_signature_metadata"] . ":</u>'
    
                                var list = document.createElement('ul')
                                list.classList = 'sign_info_list sign_info_list_first'
                                list.style.margin = '0px'
            
                                for(var meta in data) {
                                    if(meta != 'meta') {
                                        var child = document.createElement('li')
                                        child.innerText = meta
                                        
                                        list.appendChild(child)
    
                                        var listChild = document.createElement('ul')
                                        listChild.style.margin = '0px'
                                        listChild.classList = 'sign_info_list sign_info_list_first'
    
                                        list.appendChild(listChild)

                                        if(meta != 'extra') {
                                            for(var field of data[meta]) {
                                                var subChild = document.createElement('li')
                                                subChild.innerText = field
                                                listChild.appendChild(subChild)
                                            }
                                        }else {
                                            for(var extra in data.extra) {
                                                var child = document.createElement('li')
                                                child.innerText = extra
                                                listChild.appendChild(child)

                                                var listSubChild = document.createElement('ul')
                                                child.appendChild(listSubChild)

                                                var subChild = document.createElement('li')
                                                subChild.innerText = data.extra[extra]

                                                listSubChild.appendChild(subChild)
                                            }
                                        }
                                    }
                                }
    
                                sign.appendChild(list)
                                sign.style.margin = '0px'
    
                                var download = document.createElement('a')
                                download.href = './doc_num.php?explnum_id=' + id + '&get_sign=1'
                                download.innerHTML = '<button>" . $msg['digital_signature_download_button'] . "</button>'
                                    
                                dialog.appendChild(sign)
                                dialog.appendChild(download)
    
                                //Bouton supprimer
//                                 if(opac == false) {
//                                     var delSign = document.createElement('button')
//                                     delSign.style.marginLeft = '10px'
//                                     delSign.innerHTML = '" . $msg['digital_signature_delete_button_catal'] . "'
//                                     delSign.addEventListener('click', (event) => {this.delSign(id)});
//                                     dialog.appendChild(delSign)
//                                 }
    
                                document.body.appendChild(dialog)
                                this.dialog = dialog;
                                window.addEventListener('click', function(e){
                                    if(e.target == dialog || e.target == dialog.firstChild || e.target == event.target) {
                                        return;
                                    }
                                    dialog.remove();
                                    
                                }, dialog, event, this.dialog);
                            }
                            
                        }
                        
                        delSign(id) {
                            if(confirm('" . $msg['digital_signature_delete_confirm_button_catal'] . "')){
                                var data = {id, type : 'docnum'};
                                var url = './ajax.php?module=admin&categ=digital_signature&sub=signature&action=deleteDocSign&data='+JSON.stringify(data);
        
                                fetch(url, {
                                    method : 'GET',
                                }).then((response) => {
                                    if (response.ok) {
                                        window.location = window.location.href
                                    }
                                }).catch(function(error) {
                                    console.log('Fetch : ' + error);
                                });
                            }
                        }
                
                        chksign(id, type, opac = false, link = ''){
                            var wait = document.createElement('img');
                            wait.setAttribute('id','check_sign_'+id);
                            wait.setAttribute('src','" . get_url_icon('patience.gif') . "');
                            wait.setAttribute('align','top');
    
                            var span = document.getElementById('docnum_check_sign_'+id);
                            span.appendChild(wait);
    
                            var data = {id, type};
                            var url = './ajax.php?module=admin&categ=digital_signature&sub=signature&action=check&data='+JSON.stringify(data);
                            if(opac) {
                                url = './ajax.php?module=digital_signature&sub=signature&action=check&data='+JSON.stringify(data);
                            }
                        
                            fetch(url, {
                                method : 'GET',
                            }).then((response) => {
                                if (response.ok) {
                                    response.json().then((data) => {
                                        var src = '';
                                        if(data.check.check) {
                                            src = '" . get_url_icon('tick.gif') . "';
                                        }else{
                                            src = '" . get_url_icon('error.png') . "';
                                            wait.setAttribute('style','height:1.5em;');
                                        }
                                        wait.addEventListener('click', (event) => {this.showDialog(event, data.check.data, id, opac, link)});
                                        span.removeChild(wait);
                                        wait.setAttribute('src',src);
                                        span.appendChild(wait);
                                    });
                                }
                            }).catch(function(error) {
                                console.log(error);
                            });
                            
                        }
                    }
    
                    var certifier = new Certifier();
                }

            </script>
        ";
        return $js;
    }

    /**
     *
     * @param \explnum $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     *
     * @return bool
     */
    public function checkSignExists(): bool
    {
        if (file_exists($this->getCmsFilePath())) {
            return true;
        }
        return false;
    }

    public function removeFiles()
    {
        global $pmb_digital_signature_folder_id;
        if ($pmb_digital_signature_folder_id) {
            $folder = UploadFolderOrm::findById($pmb_digital_signature_folder_id);
            $name = $folder->repertoire_path . "sign_docnum" . "_" . $this->entity->explnum_id . ".cms";
            if (file_exists($name)) {
                unlink($name);
            }
        }
    }

    public static function hasSignedDocnumFromNoticeId($id)
    {
        $query = "SELECT explnum_id FROM explnum WHERE explnum_notice = $id";
        $result = pmb_mysql_query($query);
        while ($row = pmb_mysql_fetch_assoc($result)) {
            $explnum = new \explnum($row['explnum_id']);
            $docnum = new DocnumCertifier($explnum);

            if ($docnum->checkSignExists()) {
                return true;
            }
        }
        return false;
    }
}
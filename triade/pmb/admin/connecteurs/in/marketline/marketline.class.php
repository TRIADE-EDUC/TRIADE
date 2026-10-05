<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: marketline.class.php,v 1.4.10.1 2025/04/16 12:16:51 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;

require_once "{$class_path}/encoding_normalize.class.php";


class marketline extends connector {

 	protected $marketline_ftp_url ='';
 	protected $marketline_ftp_port = 21;
 	protected $marketline_ftp_timeout = 10;
 	protected $marketline_ftp_login = '';
 	protected $marketline_ftp_pwd = '';
 	protected $marketline_ftp_dir = '';
 	protected $marketline_files_filter = '';
 	protected $marketline_files = [];
 	protected $marketline_downloaded_files = [];
 	protected $marketline_temp_dir = '';

 	protected $marketline_ftp_conn = false;
 	protected $marketline_progress_msg = '';

 	protected $marketline_xsl_proc = null;

 	protected $marketline_xsl_path = '';

 	protected $marketline_n_recu = 0;
 	protected $marketline_n_total = 0;

 	public function __construct($connector_path="")
 	{
 		parent::__construct($connector_path);
 		global $base_path;
 		$this->marketline_temp_dir = $base_path."/temp/";
 		$this->marketline_xsl_path = __DIR__."/xslt/marketline.xsl";
 		if (is_readable(__DIR__."/xslt/marketline_subst.xsl")) {
 			$this->marketline_xsl_path = __DIR__."/xslt/marketline_subst.xsl";
 		}
 	}

 	/**
 	 *
 	 * {@inheritDoc}
 	 * @see connector::get_id()
 	 */
 	public function get_id()
 	{
    	return "marketline";
    }

    /**
     *
     * {@inheritDoc}
     * @see connector::is_repository()
     */
    public function is_repository()
    {
        return connector::REPOSITORY_YES;
    }

    protected function unserialize_source_params($source_id) {

    	$params = parent::unserialize_source_params($source_id);
    	if(!empty($params['PARAMETERS']['marketline_ftp_url'])) {
    		$this->marketline_ftp_url = $params['PARAMETERS']['marketline_ftp_url'];
    	}
    	if(!empty($params['PARAMETERS']['marketline_ftp_login'])) {
    		$this->marketline_ftp_login = $params['PARAMETERS']['marketline_ftp_login'];
    	}
    	if(!empty($params['PARAMETERS']['marketline_ftp_pwd'])) {
    		$this->marketline_ftp_pwd = $params['PARAMETERS']['marketline_ftp_pwd'];
    	}
    	if(!empty($params['PARAMETERS']['marketline_ftp_dir'])) {
    		$this->marketline_ftp_dir = $params['PARAMETERS']['marketline_ftp_dir'];
    	}
    	if(!empty($params['PARAMETERS']['marketline_files_filter'])) {
    		$this->marketline_files_filter = $params['PARAMETERS']['marketline_files_filter'];
    	}
    	return $params;
    }

    //Formulaire des propriétés générales
    public function source_get_property_form($source_id) {

        global $charset;

        $this->unserialize_source_params($source_id);

        $form = "
			<div class='row'>&nbsp;</div>
			<h3>".$this->msg['marketline_ftp']."</h3>
 			<div class='row'>&nbsp;</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='marketline_ftp_url'>".$this->msg["marketline_ftp_url"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='marketline_ftp_url' id='marketline_ftp_url' class='saisie-80em' value='".htmlentities($this->marketline_ftp_url,ENT_QUOTES,$charset)."'/>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='marketline_ftp_login' >".$this->msg["marketline_ftp_login"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='marketline_ftp_login' id='marketline_ftp_login' class='saisie-20em' value='".htmlentities($this->marketline_ftp_login,ENT_QUOTES,$charset)."'  />
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='marketline_ftp_pwd' >".$this->msg["marketline_ftp_pwd"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='password' name='marketline_ftp_pwd' id='marketline_ftp_pwd' class='saisie-20em' autocomplete='off' value='".htmlentities($this->marketline_ftp_pwd,ENT_QUOTES,$charset)."'  />
					<span class='fa fa-eye' title='".$this->msg['marketline_pwd_see']."' onclick='toggle_password(this, \"marketline_ftp_pwd\");' ></span>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='marketline_ftp_dir' >".$this->msg["marketline_ftp_dir"]."</label>
				</div>
				<div class='colonne_suite'>
					<input type='text' name='marketline_ftp_dir' id='marketline_ftp_dir' class='saisie-30em' value='".htmlentities($this->marketline_ftp_dir,ENT_QUOTES,$charset)."'  />
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='marketline_files_filter' >".$this->msg["marketline_files_filter"]."</label>".$this->msg["marketline_files_filter_format"]."
				</div>
				<div class='colonne_suite'>
					<input type='text' name='marketline_files_filter' id='marketline_files_filter' class='saisie-30em' value='".htmlentities($this->marketline_files_filter,ENT_QUOTES,$charset)."'  />
				</div>
			</div>
			<div class='row'></div>";
        return $form;
    }

    public function make_serialized_source_properties($source_id) {

    	global $marketline_ftp_url, $marketline_ftp_login, $marketline_ftp_pwd, $marketline_ftp_dir, $marketline_files_filter;

    	if(empty($marketline_ftp_url)) {
    		$marketline_ftp_url = '';
    	}
    	if(empty($marketline_ftp_login)) {
    		$marketline_ftp_login = '';
    	}
    	if(empty($marketline_ftp_pwd)) {
    		$marketline_ftp_pwd = '';
    	}
    	if(empty($marketline_ftp_dir)) {
    		$marketline_ftp_dir = '';
    	}
    	if(empty($marketline_files_filter)) {
    		$marketline_files_filter = '';
    	}
    	$this->sources[$source_id]['PARAMETERS'] = serialize(
    			[
    				'marketline_ftp_url'		=> stripslashes($marketline_ftp_url),
    				'marketline_ftp_login'		=> stripslashes($marketline_ftp_login),
    				'marketline_ftp_pwd'		=> stripslashes($marketline_ftp_pwd),
    				'marketline_ftp_dir'		=> stripslashes($marketline_ftp_dir),
    				'marketline_files_filter'	=> stripslashes($marketline_files_filter),
    			]
    		);
    }

    //Récupération  des proriétés globales par défaut du connecteur (timeout, retry, repository, parameters)
    public function fetch_default_global_values() {
            parent::fetch_default_global_values();
            $this->repository=1;
    }

    public function progress() {

    	$callback_progress=$this->callback_progress;
    	$percent = 0;
    	$nlu = '';
    	$ntotal = $this->msg['marketline_progress_unknown'];
    	$message ='';

    	if($this->marketline_progress_msg) {
    		$message = $this->marketline_progress_msg;
    	}
    	if ($this->marketline_n_total) {
    		$percent =($this->marketline_n_recu / $this->marketline_n_total);
    		$nlu = $this->marketline_n_recu;
    		$ntotal = $this->marketline_n_total;
    	} else {
    		$nlu = $this->marketline_n_recu;
    	}

	    call_user_func($callback_progress, $percent, $nlu, $ntotal, $message);
    }

    public function maj_entrepot($source_id,$callback_progress="",$recover=false,$recover_env="") {

    	$this->callback_progress = $callback_progress;
    	$this->unserialize_source_params($source_id);
    	$this->marketline_n_recu = 0;
    	$this->marketline_n_total = 0;

    	//connexion FTP
    	$this->marketline_progress_msg = $this->msg['marketline_progress_connect'];
    	$this->progress();
    	$c = $this->ftp_connect();
    	if($c === false) {
    		return 0;
    	}

		//recuperation liste des fichiers
    	$f = $this->ftp_list_files();
    	$this->marketline_progress_msg = count($this->marketline_files);
    	if($f === false) {
    		return 0;
    	}

    	$n = count($this->marketline_files);
    	if($n==0) {
    		return 0;
    	}

    	//telechargement de fichier
    	$i = 1;
    	foreach ($this->marketline_files as $file) {
    		$this->marketline_progress_msg = sprintf($this->msg['marketline_progress_dl_file'], $file, $i, $n);
    		$this->progress();
			$this->ftp_download_file($file);
    		$i++;
    	}

    	//conversion de fichier en pmb xml unimarc
    	$i = 1;
    	foreach ($this->marketline_downloaded_files as $filename_hash=>$file) {
    		$this->marketline_progress_msg = sprintf($this->msg['marketline_progress_convert_file'], $file['name'], $i, $n);
    		$this->progress();
    		if($file['downloaded'] === true) {
    			$this->convert_file($filename_hash);
    		}
    		@unlink($this->marketline_temp_dir.'/'.$filename_hash.'.xml');
    		$i++;
    	}
    	//enregistrement des donnees du fichier dans l'entrepot
    	$i=1;
    	foreach ($this->marketline_downloaded_files as $filename_hash=>$file) {
    		$this->marketline_progress_msg = sprintf($this->msg['marketline_progress_import_file'], $file['name'], $i, $n);
    		$this->progress();
    		if($file['converted'] === true) {
    			$this->rec_records($source_id, $filename_hash);
    		}
    		@unlink($this->marketline_temp_dir.'/'.$filename_hash.'.uni.xml');
    		$i++;
    	}
    	return $this->marketline_n_recu;
    }

    /**
     *
     * @param int $source_id : identifiant source connecteur
     * @param string $filename_hash : hash fichier pmb_xml_marc
     */
    protected function rec_records($source_id, $filename_hash) {
    	$xr = new XMLReader();
    	$xr->open($this->marketline_temp_dir.'/'.$filename_hash.".uni.xml");

    	$records = [];

    	$headers = [];
    	$content = [];
    	$field = '';
    	$subfield = '';
    	$value = '';
    	$field_order = 0;
    	$subfield_order = 0;

     	while ($xr->read()) {

    		switch(true) {

    			case ($xr->nodeType == XMLReader::ELEMENT && $xr->name == 'notice') :
    				$content = [];
    				$headers = [];
    				$field = '';
    				$subfield = '';
    				$value = '';
    				$field_order = 0;
    				$subfield_order = 0;
    				break;

    			case ($xr->nodeType == XMLReader::END_ELEMENT && $xr->name == 'notice') :
    				$records[] = ['headers'=>$headers, 'content'=>$content];
    				$content = [];
    				$headers = [];
    				$field = '';
    				$subfield = '';
    				$value = '';
    				$field_order = 0;
    				$subfield_order = 0;
    				break;

    			case ($xr->nodeTypt == XMLReader::ELEMENT && $xr->name == 'rs') :
    			case ($xr->nodeType == XMLReader::ELEMENT && $xr->name == 'ru') :
    			case ($xr->nodeType == XMLReader::ELEMENT && $xr->name == 'el') :
    			case ($xr->nodeType == XMLReader::ELEMENT && $xr->name == 'bl') :
    			case ($xr->nodeType == XMLReader::ELEMENT && $xr->name == 'hl') :
    			case ($xr->nodeType == XMLReader::ELEMENT && $xr->name == 'dt') :
    				$value = '';
    				break;

    			case ($xr->nodeType == XMLReader::END_ELEMENT && $xr->name == 'rs') :
    			case ($xr->nodeType == XMLReader::END_ELEMENT && $xr->name == 'ru') :
    			case ($xr->nodeType == XMLReader::END_ELEMENT && $xr->name == 'el') :
    			case ($xr->nodeType == XMLReader::END_ELEMENT && $xr->name == 'bl') :
    			case ($xr->nodeType == XMLReader::END_ELEMENT && $xr->name == 'hl') :
    			case ($xr->nodeType == XMLReader::END_ELEMENT && $xr->name == 'dt') :
    				$headers[$xr->name] = $value;
    				break;

    			case ($xr->nodeType == XMLReader::ELEMENT && $xr->name == 'f') :
    				$c = $xr->getAttribute('c');
    				if(!empty($c)) {
    					$field = $c;
    					$field_order++;
    					$subfield_order = 0;
    				}
    				$subfield = '';
    				$value = '';
    				break;

    			case ($xr->nodeType == XMLReader::END_ELEMENT  && $xr->name == 'f' && $subfield == '') :
    				$ind = $field . (($subfield)?$subfield:'');
    				$content[$ind][] = [
						'f' => $field,
    					's' => $subfield,
    					'v' => $value,
    					'fo' => $field_order,
    					'so' => $subfield_order,
    				];
    				$subfield_order = 0;
    				break;

    			case ($xr->nodeType == XMLReader::ELEMENT && $xr->name == 's') :
    				$c = $xr->getAttribute('c');
    				if(!empty($c)) {
    					$subfield = $c;
    				}
    				$value = '';
    				break;

    			case ($xr->nodeType == XMLReader::END_ELEMENT  && $xr->name == 's') :
    				$ind = $field . (($subfield)?$subfield:'');
    				$content[$ind][] = [
    				'f' => $field,
    				's' => $subfield,
    				'v' => $value,
    				'fo' => $field_order,
    				'so' => $subfield_order,
    				];

   					$subfield_order++;
    				break;

     			case ($xr->nodeType == XMLReader::TEXT) :
    				$value.= $xr->value;
    				break;

    		}

    	}

    	$this->marketline_n_total += count($records);
      	foreach($records as $record) {
    		$this->rec_record($record, $source_id);
    	}

    }

    /**
     *
     * @param array $record :
		 Array
        (
            [headers] => Array
                (
                    [rs] => *
                    [ru] => *
                    [el] => 1
                    [bl] => m
                    [hl] => 0
                    [dt] => a
                )
            [content] => Array
                (
                    [001] => Array
                        (
                            [0] => Array
                                (
                                    [f] => 001
                                    [s] =>
                                    [v] => ML1023CY
                                    [fo] => 1
                                    [so] => 0
                                )

                        )

                    [010a] => ...
                )
        )
     * @param int $source_id : identifiant source connecteur
     *
     * @return void
     */
    protected function rec_record($record, $source_id) {

    	if( !is_array($record) || empty(($record)) ) {
    		return;
    	}
    	//ID et titre
    	if( empty($record['content']['001'][0]['v']) || empty($record['content']['200a'][0]['v']) ) {
    		return;
    	}
    	$ref = $record['content']['001'][0]['v'];
    	$ref_exists = $this->has_ref($source_id, $ref);

    	//Suppression notice existante
    	if($ref_exists) {
    		$this->delete_from_entrepot($source_id, $ref);
    		$this->delete_from_external_count($source_id, $ref);
    	}

    	//Récupération d'un ID
    	$recid = $this->insert_into_external_count($source_id, $ref);
    	$date_import=date("Y-m-d H:i:s",time());

    	foreach($record['headers'] as $k=>$v) {
    		$this->insert_header_into_entrepot($source_id, $ref, $date_import, $k, $v, $recid);
    	}
    	foreach($record['content'] as $field) {
    		foreach($field as $k=>$v) {
    			$this->insert_content_into_entrepot($source_id, $ref, $date_import, $v['f'], $v['s'], $v['fo'], $v['so'], $v['v'], $recid);
    		}
    	}

    	$this->insert_origine_into_entrepot($source_id, $ref, $date_import, $recid);
    	$this->rec_isbd_record($source_id, $ref, $recid);

    	$this->marketline_n_recu ++;
    	$this->progress();
    	return;

    }

    protected function ftp_connect() {

    	// Connexion ftp
    	$this->marketline_ftp_conn = ftp_connect($this->marketline_ftp_url, $this->marketline_ftp_port, $this->marketline_ftp_timeout);
    	if(!$this->marketline_ftp_conn) {
    		$this->error = true;
    		$this->error_msg = $this->msg['marketline_ftp_connect_error'];
    		return false;
    	}

    	// Authentification
    	if (!ftp_login($this->marketline_ftp_conn, $this->marketline_ftp_login, $this->marketline_ftp_pwd)) {
    		$this->error = true;
    		$this->error_msg = $this->msg['marketline_ftp_auth_error'];
    		$this->ftp_close();
    		return false;
    	}

    	// Passage en mode passif
    	if(!ftp_pasv($this->marketline_ftp_conn, true)) {
    		$this->error = true;
    		$this->error_msg = $this->msg['marketline_ftp_pasv_error'];
    		$this->ftp_close();
    		return false;
    	}

    	return true;
    }

    protected function ftp_list_files() {

    	if($this->marketline_ftp_conn === false) {
    		$this->error = true;
    		$this->error_msg = $this->msg['marketline_ftp_connect_error'];
    		return false;
    	}

    	//Changement repertoire
    	if(!ftp_chdir($this->marketline_ftp_conn, $this->marketline_ftp_dir)) {
    		$this->error = true;
    		$this->error_msg = $this->msg['marketline_ftp_chdir_error'];
    		$this->ftp_close();
    		return false;
    	}

    	//Recuperation du contenu du dossier
    	$files_to_process = ftp_nlist($this->marketline_ftp_conn, ".");
    	if($files_to_process === false) {
    		$this->error = true;
    		$this->error_msg = $this->msg['marketline_ftp_nlist_error'];
    		$this->ftp_close();
    		return false;
    	}

    	//liste vide ?
    	if(empty($files_to_process)) {
    		$this->marketline_files = [];
    		$this->ftp_close();
    		return true;
    	}

    	//Filtre des fichiers
    	if($this->marketline_files_filter == '') {
    		$this->marketline_files = $files_to_process;
    		return true;
    	}
    	$try = @preg_grep($this->marketline_files_filter, $files_to_process);
    	if($try === false) {
    		$this->marketline_files = $files_to_process;
    	} else {
    		$this->marketline_files = $try;
    	}
   		return true;
    }

    /**
     *
     * @param string $filename
     *
     * @return void
     */
    protected function ftp_download_file($filename) {

    	$filename_hash = md5($filename);
    	@unlink($this->marketline_temp_dir.'/'.$filename_hash);

    	$r = ftp_get($this->marketline_ftp_conn, $this->marketline_temp_dir.'/'.$filename_hash.".xml", $filename, FTP_BINARY);
    	$this->marketline_downloaded_files[$filename_hash] = ['name'=>$filename, 'downloaded'=>$r];
    	return ;
    }

	protected function ftp_download_files() {
    	$this->marketline_downloaded_files = [];

    	if(empty($this->marketline_files)) {
    		return;
    	}
    	foreach($this->marketline_files as $filename) {
    		$this->ftp_download_file($filename);
    	}
    	return;
    }

    protected function ftp_close() {
    	ftp_close($this->marketline_ftp_conn);
    }

    /*
     * Convert file using xsl
     *
     * @param array $filename_hash = md5 filename
     *
     * @return void
     */
    protected function convert_file($filename_hash) {

    	if(is_null($this->marketline_xsl_proc)) {
    		$this->marketline_xsl_proc = new XSLTProcessor();;
    		$this->marketline_xsl_proc->setSecurityPrefs(0);
    		$this->marketline_xsl_proc->registerPHPFunctions();

    		$xsl = new DOMDocument();
    		$xsl->load($this->marketline_xsl_path, LIBXML_COMPACT);
    		$this->marketline_xsl_proc->importStylesheet($xsl);
    	}

    	@unlink($this->marketline_temp_dir.'/'.$filename_hash.".uni.xml");
    	$this->marketline_downloaded_files[$filename_hash]['converted'] = false;

    	$raw_in = file_get_contents($this->marketline_temp_dir.'/'.$filename_hash.".xml");
    	$raw_in_encoding = encoding_normalize::detect_encoding($raw_in);
    	if($raw_in_encoding == false) {
    		$raw_in_encoding = 'utf-8';
    	}
    	$raw_in_utf8 = mb_convert_encoding($raw_in, 'utf-8', $raw_in_encoding);
    	if(empty($raw_in_utf8)) {
    		return;
    	}

    	$in = new DOMDocument();
    	$in->loadXML($raw_in_utf8, LIBXML_COMPACT);
    	$out = $this->marketline_xsl_proc->transformToXml($in);
    	if($out !== false) {
    		$r = file_put_contents($this->marketline_temp_dir.'/'.$filename_hash.".uni.xml", $out);
    		if($r !== false){
    			$this->marketline_downloaded_files[$filename_hash]['converted'] = true;
    		}
    	}
    	return;
    }
}

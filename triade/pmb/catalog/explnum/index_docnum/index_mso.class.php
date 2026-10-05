<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_mso.class.php,v 1.6 2023/08/28 14:04:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * Classe qui permet la gestion de l'indexation des fichiers microsoft office
 */

abstract class index_mso {

	protected $filename='';
	protected $mimetype='';
	protected $extension='';
	protected $convert_to='';
	protected $tmp_filename='';
	protected $to_filename='';
	protected $text='';
	protected $params=array();
	
	public function __construct($filename, $mimetype='', $extension='', $convert_to='') {
		$this->filename=realpath($filename);
		if ($mimetype)$this->mimetype=$mimetype;
		if ($extension) $this->extension=$extension;
		$this->get_parameters();
	}
	
	public function get_parameters() {
		global $pmb_indexation_docnum_ext;
		$all_params=array();
		$all_params=explode(';',$pmb_indexation_docnum_ext);
		if (count($all_params)) {
			foreach($all_params as $v) {
				$ext_params=explode('=',$v);
				if (count($ext_params)) {
					switch($ext_params[0]) {
						case 'pyodconverter_cmd' :
						case 'jodconverter_cmd' :
						case 'pdftotext_cmd' :
						case 'jodconverter_url' :	
							$this->params[$ext_params[0]]=$ext_params[1];
							break;
					}
				}
			}
		}
		if (!$this->params['pdftotext_cmd']) {
			$this->params['pdftotext_cmd']="/usr/bin/pdftotext -enc UTF-8 %1s -";
		}
	}
	
	public function get_text($filename){
		global $charset;
		$done=false;
		if ($this->params['pyodconverter_cmd']) {
			$this->to_filename=$this->filename.'.'.$this->convert_to;
			$cmd = sprintf($this->params['pyodconverter_cmd'], $this->filename,$this->to_filename);
			@exec($cmd);
			if(file_exists($this->to_filename)) {
				$this->text=file_get_contents($this->to_filename);
			}
			@unlink($this->to_filename);
			$done=true;
		}
		if (!$done && $this->params['jodconverter_cmd']) {		
			$this->tmp_filename=$this->filename.'.'.$this->extension;
			@copy($this->filename, $this->tmp_filename);
			$this->to_filename=$this->filename.'.'.$this->convert_to;
			$cmd = sprintf($this->params['jodconverter_cmd'], $this->tmp_filename,$this->to_filename);
			@exec($cmd);
			if(file_exists($this->to_filename)) {
				$this->text=file_get_contents($this->to_filename);
			}
			@unlink($this->tmp_filename);
			@unlink($this->to_filename);
			$done=true;
		}
		if (!$done && $this->params['jodconverter_url']) {	
			$this->tmp_filename=$this->filename.'.'.$this->extension;
			@copy($this->filename, $this->tmp_filename);
			$this->to_filename=$this->filename.'.'.$this->convert_to;
			$url=sprintf($this->params['jodconverter_url'],$this->to_filename);
			if(function_exists("curl_file_create")){
				$post=array("inputDocument"=>curl_file_create($this->tmp_filename));
			}else{
				$post=array('inputDocument'=>'@'.$this->tmp_filename);
			}
			$res='';
			$ch=curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$res=curl_exec($ch);
			curl_close($ch);
			if ($res && substr($res,0,6)!=='<html>') {
				$this->text=$res;
				$done=true;
			}
			@unlink($this->tmp_filename);
			@unlink($this->to_filename);
		}	
		if($done && $charset != 'utf-8'){
			$this->text=encoding_normalize::utf8_decode($this->text);
		} 
		return $this->text;
	}
	
}


class index_mso_doc extends index_mso {
	
	protected $mimetype='application/msword';
	protected $extension='doc';
	protected $convert_to='txt';
}

class index_mso_xls extends index_mso {
	
	protected $mimetype='application/vnd.ms-excel';
	protected $extension='xls';
	protected $convert_to='csv';
}

class index_mso_ppt extends index_mso {
	
	protected $mimetype='application/vnd.ms-powerpoint';
	protected $extension='ppt';
	protected $convert_to='pdf';
	
	public function get_text($filename){
		global $charset;
		$done=false;
		if ($this->params['pyodconverter_cmd']) {
			$this->to_filename=$this->filename.'.'.$this->convert_to;
			$cmd = sprintf($this->params['pyodconverter_cmd'], $this->filename,$this->to_filename);
			@exec($cmd);
			if(file_exists($this->to_filename)) {
				$cmd=sprintf($this->params['pdftotext_cmd'], $this->to_filename);
				$fp = popen($cmd, "r");
				while(!feof($fp)){
					$line = fgets($fp,4096); 
					$this->text.= $line;
				}
				pclose($fp);
			}
			@unlink($this->to_filename);
			$done=true;
		}
		if (!$done && $this->params['jodconverter_cmd']) {		
			$this->tmp_filename=$this->filename.'.'.$this->extension;
			@copy($this->filename, $this->tmp_filename);
			$this->to_filename=$this->filename.'.'.$this->convert_to;
			$cmd = sprintf($this->params['jodconverter_cmd'], $this->tmp_filename,$this->to_filename);
			@exec($cmd);
			if(file_exists($this->to_filename)) {
				$cmd=sprintf($this->params['pdftotext_cmd'], $this->to_filename);
				$fp = popen($cmd, "r");
				while(!feof($fp)){
					$line = fgets($fp,4096); 
					$this->text.= $line;
				}
				pclose($fp);
			}
			@unlink($this->tmp_filename);
			@unlink($this->to_filename);
			$done=true;
		}
		if (!$done && $this->params['jodconverter_url']) {	
			$this->tmp_filename=$this->filename.'.'.$this->extension;
			@copy($this->filename, $this->tmp_filename);
			$this->to_filename=$this->filename.'.'.$this->convert_to;
			$url=sprintf($this->params['jodconverter_url'],$this->to_filename);
			if(function_exists("curl_file_create")){
				$post=array("inputDocument"=>curl_file_create($this->tmp_filename));
			}else{
				$post=array('inputDocument'=>'@'.$this->tmp_filename);
			}
			$res='';
			$ch=curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$res=curl_exec($ch);
			curl_close($ch);
			if ($res && substr($res,0,6)!=='<html>') {
				file_put_contents($this->to_filename,$res);
				if(file_exists($this->to_filename)) {
					$cmd=sprintf($this->params['pdftotext_cmd'], $this->to_filename);
					$fp = popen($cmd, "r");
					while(!feof($fp)){
						$line = fgets($fp,4096); 
						$this->text.= $line;
					}
					pclose($fp);
				}
				$done=true;
			}
			@unlink($this->tmp_filename);
			@unlink($this->to_filename);
			
		}	
		if($done && $charset != 'utf-8'){
			$this->text=encoding_normalize::utf8_decode($this->text);
		} 
		return $this->text;
	}
		
}


<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: converter_factory.class.php,v 1.6 2023/07/24 16:11:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * Classe qui permet la gestion de la transformation en pdf
 */

class converter_factory {

	public static function make($filename, $filepath, $mimetype = '', $extension = '', $convert_to = '', $tmp_dir = '' , $parameters = []) {
		if (!$extension) {
			$extension=substr($filename,strrpos($filename,'.')*1+1);
		}
		if (!$extension) {
			$extension=substr($filepath,strrpos($filepath,'.')*1+1);
		}
		$in='';
		switch($convert_to) {
			case 'swf' :
				switch($extension) {
					case 'pdf':
						$in='pdf';
						break;
					case 'odt' :
					case 'sxw' :
					case 'ods' :
					case 'sxc' :
					case 'doc' :
					case 'docx':
					case 'xls':
					case 'xlsx':
						$in='odt';
						break;
					case 'odp':
					case 'sxi':
					case 'ppt':
					case 'pptx':
						$in='odp';
						break;
					case '':
						break;
				}
			default :
				break;
		}
		if ($in) {
			$classname= 'convert_'.$in.'_to_'.$convert_to;
			return new $classname($filename, $filepath, $mimetype, $extension, $convert_to, $tmp_dir, $parameters);
		} else {
			return false;
		}
	}
}


abstract class convert_to {

	protected $filename='';
	protected $filepath='';
	protected $mimetype='';
	protected $extension='';
	protected $convert_to='';
	protected $params=array();
	protected $tmp_dir='';

	public function __construct($filename, $filepath, $mimetype = '', $extension = '', $convert_to = '', $tmp_dir = '', $parameters = []) {
		$this->filename=$filename;
		$this->filepath=$filepath;
		if ($mimetype)$this->mimetype=$mimetype;
		if ($extension) $this->extension=$extension;
		if ($convert_to) $this->convert_to=$convert_to;
		$this->tmp_dir=$tmp_dir;
		$this->params['pyodconverter_cmd']=$parameters['pyodconverter_cmd'];
		$this->params['jodconverter_cmd']=$parameters['jodconverter_cmd'];
		$this->params['jodconverter_url']=$parameters['jodconverter_url'];
		$this->params['pdftotext_cmd']=$parameters['pdftotext_cmd'];
		$this->params['pdf2swf_cmd']=$parameters['pdf2swf_cmd'];
	}

	abstract public function convert();

	abstract public function remove_tmp_files();
}


class convert_pdf_to_swf extends convert_to {

	function convert($file_content='') {
		file_put_contents($this->tmp_dir.$this->filename.'.'.$this->extension,$file_content);
		$cmd = sprintf($this->params['pdf2swf_cmd'],$this->tmp_dir.$this->filename.'.'.$this->extension, $this->tmp_dir.$this->filename.'.'.$this->convert_to);
		@exec($cmd);
		return true;
	}

	function remove_tmp_files() {
		@unlink($this->tmp_dir.$this->filename.'.'.$this->extension);
		@unlink($this->tmp_dir.$this->filename.'.'.$this->convert_to);
	}

}


class convert_odt_to_swf extends convert_to {

	function convert($file_content='') {
		$done=false;
		if ($this->params['pyodconverter_cmd']) {
			file_put_contents($this->tmp_dir.$this->filename.'.'.$this->extension,$file_content);
			$cmd = sprintf($this->params['pyodconverter_cmd'], $this->tmp_dir.$this->filename.'.'.$this->extension, $this->tmp_dir.$this->filename.'.pdf');
			@exec($cmd);
			$done=true;
		}
		if (!$done && $this->params['jodconverter_cmd']) {
			file_put_contents($this->tmp_dir.$this->filename.'.'.$this->extension,$file_content);
			$cmd = sprintf($this->params['jodconverter_cmd'], $this->tmp_dir.$this->filename.'.'.$this->extension, $this->tmp_dir.$this->filename.'.pdf');
			@exec($cmd);
			$done=true;
		}
		if (!$done && $this->params['jodconverter_url']) {
			file_put_contents($this->tmp_dir.$this->filename.'.'.$this->extension,$file_content);
			$url=sprintf($this->params['jodconverter_url'],$this->filename.'.pdf');
			if(function_exists("curl_file_create")){
				$post=array("inputDocument"=>curl_file_create($this->tmp_dir.$this->filename.'.'.$this->extension));
			}else{
				$post=array('inputDocument'=>'@'.$this->tmp_dir.$this->filename.'.'.$this->extension);
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
				file_put_contents($this->tmp_dir.$this->filename.'.pdf',$res);
				$done=true;
			}
		}
		if ($done) {
			$cmd = sprintf($this->params['pdf2swf_cmd'],$this->tmp_dir.$this->filename.'.pdf', $this->tmp_dir.$this->filename.'.'.$this->convert_to);
			@exec($cmd);
			return true;
		}
		return false;

	}

	function remove_tmp_files() {
		@unlink($this->tmp_dir.$this->filename.'.'.$this->extension);
		@unlink($this->tmp_dir.$this->filename.'.pdf');
		@unlink($this->tmp_dir.$this->filename.'.'.$this->convert_to);
	}
}


class convert_odp_to_swf extends convert_to {

	function convert($file_content='') {
		$done=false;
		if ($this->params['pyodconverter_cmd']) {
			file_put_contents($this->tmp_dir.$this->filename.'.'.$this->extension,$file_content);
			$cmd = sprintf($this->params['pyodconverter_cmd'], $this->tmp_dir.$this->filename.'.'.$this->extension, $this->tmp_dir.$this->filename.'.'.$this->convert_to);
			@exec($cmd);
			$done=true;
		}
		if (!$done && $this->params['jodconverter_cmd']) {
			file_put_contents($this->tmp_dir.$this->filename.'.'.$this->extension,$file_content);
			$cmd = sprintf($this->params['jodconverter_cmd'], $this->tmp_dir.$this->filename.'.'.$this->extension, $this->tmp_dir.$this->filename.'.'.$this->convert_to);
			@exec($cmd);
			$done=true;
		}
		if (!$done && $this->params['jodconverter_url']) {
			file_put_contents($this->tmp_dir.$this->filename.'.'.$this->extension,$file_content);
			$url=sprintf($this->params['jodconverter_url'],$this->filename.'.'.$this->convert_to);
			if(function_exists("curl_file_create")){
				$post=array("inputDocument"=>curl_file_create($this->tmp_dir.$this->filename.'.'.$this->extension));
			}else{
				$post=array('inputDocument'=>'@'.$this->tmp_dir.$this->filename.'.'.$this->extension);
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
				file_put_contents($this->tmp_dir.$this->filename.'.'.$this->convert_to,$res);
				$done=true;
			}
		}
		if ($done) {
			return true;
		}
		return false;
	}
	function remove_tmp_files() {
		@unlink($this->tmp_dir.$this->filename.'.'.$this->extension);
		@unlink($this->tmp_dir.$this->filename.'.'.$this->convert_to);
	}
}



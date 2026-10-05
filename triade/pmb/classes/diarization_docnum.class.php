<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: diarization_docnum.class.php,v 1.3 2017/08/10 09:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path."/voxilab/voxilabDiarization.class.php";
require_once $class_path.'/progress_bar.class.php';

/**
 * Classe de gestion de la segmentation des documents numériques
 */
class diarization_docnum {
	/**
	 * Instance de explnum
	 * @var explnum
	 */
	private $explnum;
	
	/**
	 * Chemin du fichier à envoyer au serveur
	 * @var string
	 */
	private $file = "";
	
	/**
	 * Instance de segmentation
	 * @var voxilabDiarization
	 */
	private $diarization;
	
	/**
	 * Instance de fichier segmenté
	 * @var voxilabSpeechfile
	 */
	private $speechFile;
	
	/**
	 * @param explnum explnum
	 */
	public function __construct($explnum) {
		$this->explnum = $explnum;
		
		$options = array(
				'url' => "http://speechweb.sigb.net:8080/files.json"
				);
		
		$this->diarization = new voxilabDiarization("http", $options);
	}
	
	/**
	 * Renvoie le chemin du fichier à envoyer au serveur
	 * @return string
	 */
	public function getFile() {
		global $base_path;
		if (!$this->file) {
			if ($this->explnum->infos_docnum['contenu'] || $this->explnum->explnum_data) {
				// Si c'est un nouveau document ou un document stocké en base, on définit un nom unique dans le dossier temporaire
				if ($this->explnum->infos_docnum['userfile_name']) {
					$nom_temp = session_id().microtime(true).".".$this->explnum->infos_docnum['userfile_name'];
				} else {
					$nom_temp = session_id().microtime(true).".".$this->explnum->explnum_extfichier;
				}
				
				$this->file = $base_path."/temp/".$nom_temp;
				
				if ($this->explnum->infos_docnum['contenu']) {
					file_put_contents($this->file, $this->explnum->infos_docnum['contenu']);
				} else {
					file_put_contents($this->file, $this->explnum->explnum_data);
				}
			} else {
				$this->file = $this->explnum->explnum_rep_path.$this->explnum->explnum_nomfichier;
			}
		}
		return $this->file;
	}
	
	/**
	 * Segmente le document numérique et stocke le résultat en base de données
	 */
	public function diarize() {
		// On commence par supprimer
		$query = "delete from explnum_segments where explnum_segment_explnum_num = ".$this->explnum->explnum_id;
		pmb_mysql_query($query);
		
		$query = "delete from explnum_speakers where explnum_speaker_explnum_num = ".$this->explnum->explnum_id;
		pmb_mysql_query($query);
		
		// Gestion de la progress_bar
		$progress_bar = new progress_bar("upload to server");
		$progress_bar->set_percent(0);
		
		$this->speechFile = $this->diarization->sendFile($this->getFile());
// 		$this->speechFile = $this->diarization->getFile(67);
		
		$status = $this->speechFile->getStatus();
		while ($status != "diarization_phase7") {
			if ($status == "uploaded") {
				$progress_bar->set_percent(round((1/8)*100));
			} else {
				$nb = str_replace("diarization_phase", "", $status);
				$progress_bar->set_percent(round((($nb+1)/8)*100));
			}
			$progress_bar->set_text($status);
		
			$status = $this->speechFile->getStatus();
			sleep(0.5);
		}
		sleep(10);
		$progress_bar->hide();
		
		$speakers = $this->speechFile->getSpeakers();
		
		$speakers_ids = array();	// Tableau associant l'identifiant du speaker avec son identifiant dans la table
		
		foreach ($speakers as $speaker) {
			$query = "insert into explnum_speakers (explnum_speaker_explnum_num, explnum_speaker_speaker_num, explnum_speaker_gender) values (".$this->explnum->explnum_id.", '".$speaker->getID()."', '".$speaker->getGender()."')";
			pmb_mysql_query($query);
			$speakers_ids[$speaker->getID()] = pmb_mysql_insert_id();
		}
		
		$segments = $this->speechFile->getSegments();
		
		foreach ($segments as $segment) {
			$query = "insert into explnum_segments (explnum_segment_explnum_num, explnum_segment_speaker_num, explnum_segment_start, explnum_segment_duration, explnum_segment_end) values (".$this->explnum->explnum_id.", '".$speakers_ids[$segment->getSpeaker()->getID()]."', ".$segment->getStart().", ".$segment->getDuration().", ".$segment->getEnd().")";
			pmb_mysql_query($query);
		}
	}
}
?>
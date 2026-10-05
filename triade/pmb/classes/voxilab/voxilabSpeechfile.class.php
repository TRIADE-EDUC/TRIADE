<?php
/***********************************************************************************************
 Ce fichier fait partie du projet voxilabPHP, implémentation PHP de
l'API Voxilab (https://github.com/voxilab)

Cette  implémentation développée par © 2013- PMB Services.

Ce programme est régi par la licence CeCILL soumise au droit français et
respectant les principes de diffusion des logiciels libres. Vous pouvez
utiliser, modifier et/ou redistribuer ce programme sous les conditions
de la licence CeCILL telle que diffusée par le CEA, le CNRS et l'INRIA
sur le site "http://www.cecill.info".

En contrepartie de l'accessibilité au code source et des droits de copie,
de modification et de redistribution accordés par cette licence, il n'est
offert aux utilisateurs qu'une garantie limitée.  Pour les mêmes raisons,
seule une responsabilité restreinte pèse sur l'auteur du programme,  le
titulaire des droits patrimoniaux et les concédants successifs.

A cet égard  l'attention de l'utilisateur est attirée sur les risques
associés au chargement,  à l'utilisation,  à la modification et/ou au
développement et à la reproduction du logiciel par l'utilisateur étant
donné sa spécificité de logiciel libre, qui peut le rendre complexe à
manipuler et qui le réserve donc à des développeurs et des professionnels
avertis possédant  des  connaissances  informatiques approfondies.  Les
utilisateurs sont donc invités à charger  et  tester  l'adéquation  du
logiciel à leurs besoins dans des conditions permettant d'assurer la
sécurité de leurs systèmes et ou de leurs données et, plus généralement,
à l'utiliser et l'exploiter dans les mêmes conditions de sécurité.

Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
pris connaissance de la licence CeCILL, et que vous en avez accepté les
termes.
***********************************************************************************************/

/*
 * A diarized sound or video file
*
* $Id: voxilabSpeechfile.class.php,v 1.1 2014/01/10 15:46:42 apetithomme Exp $
*/

if (stristr ($_SERVER['REQUEST_URI'], ".class.php"))
	die ("no access");

require_once 'voxilabSpeaker.class.php';
require_once 'voxilabSegment.class.php';

class voxilabSpeechfile
{
	 /*** Attributes: ***/

	/**
	 * File identifier
	 * @access private
	 */
	private $id;
	
	/**
	 * Instance of voxilabDiarization
	 * @var voxilabDiarization
	 */
	private $diarization;

	/**
	 * File status
	 * @access private
	 */
	private $status;
	
	/**
	 * Array of segments
	 * @var array
	 * @access private
	 */
	private $segments = array();
	
	/**
	 * Array of speakers
	 * @var array
	 * @access private
	 */
	private $speakers = array();


	/**
	 * 
	 *
	 * @param int id Identifiant du fichier
	 * 
	 * @param string url URL to call for diarization

	 * @param string name Nom du fichier

	 * @return void
	 * @access public
	 */
	public function __construct( $id, $diarization ) {
		$this->id = $id;
		$this->diarization = $diarization;
	} // end of member function __construct

	/**
	 * Get all informations about the file
	 * 
	 * @return array
	 * @access private
	 */
	private function getInfos() {
		if ($this->status == "diarization_phase7") {
			if (!count($this->speakers)) {
				$speakers = $this->diarization->sendCommand($this->id, 'speakers');
		
				foreach ($speakers as $speaker) {
					$this->speakers[$speaker->id] = new voxilabSpeaker($speaker->id, $speaker->gender);
				}
			}
			if (!count($this->segments) && count($this->speakers)) {
				$segments = $this->diarization->sendCommand($this->id, 'segments');
				
				foreach ($segments as $segment) {
					$voxilabSegment = new voxilabSegment($segment->start, $segment->duration, $this->speakers[$segment->speaker->id]);
					$this->segments[] = $voxilabSegment;
					$this->speakers[$segment->speaker->id]->addSegment($voxilabSegment);
				}
			}
		}
	}
	
	/**
	 * Get an array of speakers
	 *
	 * @return array
	 * @access public
	 */
	public function getSpeakers( ) {
		if (!count($this->speakers)) {
			$this->getInfos();
		}
		return $this->speakers;
	} // end of member function getSpeakers

	/**
	 * Get an array of segments
	 *
	 * @return array
	 * @access public
	 */
	public function getSegments( ) {
		if (!count($this->segments)) {
			$this->getInfos();
		}
		return $this->segments;
	} // end of member function getSegments

	/**
	 * Get file status
	 *
	 * @return string
	 * @access public
	 */
	public function getStatus( ) {
		if ($this->status != "diarization_phase7") {
			$result = $this->diarization->sendCommand($this->id, 'status');
			
			$this->status = $result->status;
		}
		return $this->status;
	} // end of member function getStatus

	/**
	 * Get a speaker with an id
	 *
	 * @param int id Speaker identifier

	 * @return voxilabSpeaker
	 * @access public
	 */
	public function getSpeakerByID( $id ) {
		if (!count($this->speakers)) {
			$this->getInfos();
		}
		if ($this->speakers[$id]) {
			return $this->speakers[$id];
		} else return false;
	} // end of member function getSpeaker

	/**
	 * Get a segment with a time
	 *
	 * @param float time Time (hundredth of a second)

	 * @return voxilabSegment
	 * @access public
	 */
	public function getSegmentByTime( $time ) {
		if (!count($this->segments)) {
			$this->getInfos();
		}
		foreach ($this->segments as $segment) {
			if (($segment->getStart() <= $time) && ($time < $segment->getEnd())) {
				return $segment;
			} else continue;
		}
		return false;
	} // end of member function getSegmentByTime

	/**
	 * Get segments in an interval
	 *
	 * @param float begin Beginning of the interval

	 * @param float end End of the interval

	 * @return voxilabSegment
	 * @access public
	 */
	public function getSegmentsInInterval( $begin,  $end ) {
		if (!count($this->segments)) {
			$this->getInfos();
		}
		$return = array();
		foreach ($this->segments as $segment) {
			if (($segment->getEnd() > $begin) && ($segment->getStart() <= $end)) {
				$return[] = $segment;
			}
		}
		return $return;
	} // end of member function getSegmentsInInterval
	
	/**
	 * Get segments with a speaker identifier
	 * 
	 * @param int speaker Speaker identifier
	 * 
	 * @return voxilabSegment
	 */
	public function getSegmentsBySpeaker($speaker) {
		if (!count($this->speakers)) {
			$this->getInfos();
		}
		return $this->speakers[$speaker]->getSegments();
	}
	
	public function getID() {
		return $this->id;
	}

} // end of voxilabSpeechfile
?>

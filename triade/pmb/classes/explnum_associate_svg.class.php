<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_associate_svg.class.php,v 1.10 2023/11/30 11:07:35 qvarin Exp $


if (stristr ($_SERVER['REQUEST_URI'], ".class.php"))
	die ("no access");

require_once($include_path."/templates/explnum_associate.tpl.php");

/**
 * Classe pour la génération de la structure svg pour l'interface d'association des locuteurs
 */
class explnum_associate_svg {
	/**
	 * @var int
	 */
	private $explnum_id;

	/**
	 * @var string
	 */
	private $svg;

	/**
	 * @var string
	 */
	private $js = "";

	/**
	 * @var array
	 */
	private $speakers =  array();

	/**
	 * @var array
	 */
	private $segments =  array();

	/**
	 * Tableau des dimensions des éléments svg
	 * @var array
	 */
	private $dimensions = array();

	/**
	 * Durée du document
	 * @var int
	 */
	private $duration = 0;

	/**
	 * @param int explnum_id Identifiant du document numérique
	 */
	public function __construct($explnum_id) {
		$this->explnum_id = intval($explnum_id);
	}

	/**
	 * Renvoie le code svg généré
	 * @param boolean edit true pour activer la possibilité d'édition
	 * @return string
	 */
	public function getSvg($edit = false) {
		$this->getDimensions();
		$this->svg = "<svg id='speech_timeline_svg' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' height='!!height!!' width='".($this->dimensions['totalWidth'])."' >";
		$this->getBackground();
		$this->getTimeScale();
		$this->getSpeakers($edit);
		$this->getSegments();
		$this->getCursor();
		if ($edit) {
			$this->getLeftCursor();
			$this->getRightCursor();
		}
		$this->svg .= "</svg>";
		return $this->svg;
	}

	/**
	 * Définit le tableau des dimensions
	 */
	private function getDimensions() {
		global $explnum_associate_speakers_svg_height;

		$this->dimensions = array(
				'totalWidth' => 1500,												// Largeur totale
				// Dimensions fond
				'backgroundPadding' => 2,											// Padding du background
				// Dimensions de la barre de graduations
				'scaleTextY' => 15,													// Ordonnée du texte de la barre de graduations
				'scaleTextFontSize' => 12,											// Taille de police du texte de la barre de graduations
				'scaleBottom' => 40,												// Ordonnée de la base de la barre de graduations
				'scaleTop' => 20,													// Ordonnée du sommet des grandes barres
				'scaleMiddle' => 30,												// Ordonnée du sommet des petites barres
				// Dimensions locuteurs
				'speakerLeft' => 5,													// Abscisse de gauche de la colonne speaker
				'speakerTop' => 42,													// Ordonnée du haut de la colonne speaker
				'speakerWidth' => 150,												// Largeur de la colonne speaker
				'speakerHeight' => $explnum_associate_speakers_svg_height,			// Hauteur d'une case speaker
				'speakerMarginBottom' => 2,											// Marge entre chaque speaker
				'speakerTextFontSize' => 12,										// Taille de police de texte
				'speakerTextX' => 3,												// Abscisse de début de texte
				'speakerTextY' => 17,												// Ordonnée du texte
				'speakerMarginRight' => 2,											// Marge à droite
				);
	}

	/**
	 * Construit la barre de graduation
	 */
	private function getTimeScale() {
		if (!count($this->segments)) {
			$this->getDatas();
		}
		global $explnum_associate_timescale_svg;
		global $explnum_associate_timescale_svg_posX;
		global $explnum_associate_timescale_svg_posY;
		global $explnum_associate_timescale_svg_width;
		global $explnum_associate_timescale_svg_height;
		global $msg;

		// Un champ texte pour donner l'unité de temps
		$this->svg .= '<text transform="matrix(1 0 0 1 '.($this->dimensions['speakerLeft'] + ($this->dimensions['speakerWidth'] / 2)).' '.$this->dimensions['scaleTextY'].')" font-family="\'LiberationSans-Regular\'" text-anchor="middle" font-size="'.$this->dimensions['scaleTextFontSize'].'">'.$msg['explnum_associate_minutes'].'</text>';

		$timescaleSvg = '<g transform="!!transform!!">'.$explnum_associate_timescale_svg.'</g>';

		$x = $this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + $this->dimensions['backgroundPadding'];

		// Calcul de la largeur totale disponible pour le ratio
		$availableWidth = $this->dimensions['totalWidth'] - ($this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + (2 * $this->dimensions['backgroundPadding']));

		// Temps total
		$duration = $this->duration;

		// On cherche l'intervalle idéal
		$interval = 1;
		// Calcul de la largeur pour l'intervalle
		$widthForInterval = ($availableWidth*$interval*100) / $duration;
		while (($widthForInterval*2) < 15) {
			$interval = $interval*2;
			$widthForInterval = ($availableWidth*$interval*100) / $duration;
		}

		$tps = 0;
		$cpt = 0;

		while ($x < $this->dimensions['totalWidth']) {
			// On regarde le compteur pour savoir la taille de la barre et si on affiche le texte
			$width = 10 * $widthForInterval / $explnum_associate_timescale_svg_width;
			if (!$cpt) {
				$transform = 'translate('.$x.', '.($this->dimensions['scaleBottom'] - $this->dimensions['scaleTop']).') scale('.$width.', '.($this->dimensions['scaleTop'] / $explnum_associate_timescale_svg_height).') translate('.(0 - $explnum_associate_timescale_svg_posX).', '.(0 - $explnum_associate_timescale_svg_posY).')';

				$currentTimescaleSvg = str_replace("!!transform!!", $transform, $timescaleSvg);

				$this->svg .= $currentTimescaleSvg;
			}
			if (!$cpt || $cpt == 5) {
				$this->svg .= '<text transform="matrix(1 0 0 1 '.$x.' '.$this->dimensions['scaleTextY'].')" font-family="\'LiberationSans-Regular\'" text-anchor="middle" font-size="'.$this->dimensions['scaleTextFontSize'].'">'.$this->getTimeToDisplay($tps).'</text>';
			}

			$cpt++;
			if ($cpt == 10) $cpt = 0;
			$tps += $interval;
			$x += $widthForInterval;
		}
	}

	/**
	 * Renvoie une chaine correspondant au temps à afficher (min:sec)
	 *
	 * @param int tps Temps en secondes
	 */
	private function getTimeToDisplay($tps) {
		$sec = $tps % 60;
		$min = ($tps - $sec) / 60;

		$sec = str_pad($sec, 2, '0', STR_PAD_LEFT);
		return $min.":".$sec;
	}

	/**
	 * Construit les blocs locuteurs
	 * @param boolean edit true pour activer la possibilité d'édition
	 */
	private function getSpeakers($edit) {
		if (!count($this->speakers)) {
			$this->getDatas();
		}
		global $explnum_associate_speakers_svg;
		global $explnum_associate_speakers_svg_posX;
		global $explnum_associate_speakers_svg_posY;
		global $explnum_associate_speakers_svg_width;
		global $msg;

		$speakerSvg = '<g id="!!id!!" transform="!!transform!!">'.$explnum_associate_speakers_svg.'</g>';

		foreach ($this->speakers as $id => $speaker) {
			$y = $speaker['posY'];

			$transform = "translate(".$this->dimensions['speakerLeft'].", ".$y.") scale(".($this->dimensions['speakerWidth'] / $explnum_associate_speakers_svg_width).", 1) translate(".(0 - $explnum_associate_speakers_svg_posX).", ".(0 - $explnum_associate_speakers_svg_posY).")";
			$currentSpeakerSvg = str_replace("!!transform!!", $transform, $speakerSvg);

			$currentSpeakerSvg = str_replace("!!id!!", "speaker_svg_".$id, $currentSpeakerSvg);

			if ($edit) $no_author_message = $msg['explnum_associate_author'];
			else $no_author_message = $msg['explnum_associate_no_author'];

			$this->svg .= $currentSpeakerSvg.'
				<text transform="matrix(1 0 0 1 '.($this->dimensions['speakerLeft'] + $this->dimensions['speakerTextX']).' '.($y + $this->dimensions['speakerTextY']).')" font-family="\'Arial\'" font-size="'.$this->dimensions['speakerTextFontSize'].'">
					'.($edit ? $speaker['id'] : '').'
				</text>
				<text id="explnum_associate_author_libelle_'.$id.'" transform="matrix(1 0 0 1 '.($this->dimensions['speakerLeft'] + $this->dimensions['speakerTextX']).' '.($y + 2 * $this->dimensions['speakerTextY']).')" font-family="\'Arial\'" font-size="'.$this->dimensions['speakerTextFontSize'].'" title="'.$msg['explnum_associate_author'].'" style="cursor:pointer;">
					'.($speaker['author_libelle'] ? $speaker['author_libelle'] : $no_author_message).'
				</text>';
			if ($edit) {
				$this->svg .= '
				<image id="explnum_del_associate_speaker_'.$id.'" title="'.$msg['explnum_del_associate_speaker'].'" xlink:href="'.get_url_icon('trash.gif').'" y="'.($y + $this->dimensions['speakerTextY'] - $this->dimensions['speakerTextFontSize']).'" x="'.($this->dimensions['speakerWidth'] - 12).'" width="12" height="12" style="cursor:pointer;"/>';
			}
		}

		$height = ($y + $this->dimensions['speakerHeight'] + $this->dimensions['speakerMarginBottom']);
		$this->svg = str_replace("!!height!!", $height, $this->svg);
	}

	/**
	 * Construit les segments
	 */
	private function getSegments() {
		if (!count($this->segments)) {
			$this->getDatas();
		}
		global $explnum_associate_segments_svg;
		global $explnum_associate_segments_svg_posX;
		global $explnum_associate_segments_svg_posY;
		global $explnum_associate_segments_svg_width;
		global $explnum_associate_segments_svg_height;

		$segmentSvg = '<g id="!!id!!" transform="!!transform!!">'.$explnum_associate_segments_svg.'</g>';

		// Calcul de la largeur totale disponible pour le ratio
		$availableWidth = $this->dimensions['totalWidth'] - ($this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + (2 * $this->dimensions['backgroundPadding']));

		// Temps total
		$duration = $this->duration;

		// Calcul ratio
		$ratio = $availableWidth / $duration;

		foreach ($this->segments as $id => $segment) {
			$x = $this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + $this->dimensions['backgroundPadding'] + ($segment['start'] * $ratio);
			$y = $this->speakers[$segment['speaker']]['posY'];
			$width = ($segment['duration'] * $ratio) / $explnum_associate_segments_svg_width;

			$transform = 'translate('.$x.', '.$y.') scale('.$width.', '.($this->dimensions['speakerHeight'] / $explnum_associate_segments_svg_height).') translate('.(0 - $explnum_associate_segments_svg_posX).', '.(0 - $explnum_associate_segments_svg_posY).')';
			$currentSegmentSvg = str_replace("!!transform!!", $transform, $segmentSvg);

			$currentSegmentSvg = str_replace("!!id!!", "segment_svg_".$id, $currentSegmentSvg);

			$this->svg .= $currentSegmentSvg;
		}
	}

	/**
	 * Consulte la base de données
	 */
	private function getDatas() {
		$query = "select explnum_speaker_id, explnum_speaker_speaker_num, explnum_speaker_gender, explnum_speaker_author, author_name, author_rejete from explnum_speakers left join authors on explnum_speaker_author = author_id where explnum_speaker_explnum_num = ".$this->explnum_id;
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			$i = 0;
			while ($speaker = pmb_mysql_fetch_object($result)) {
				$this->speakers[$speaker->explnum_speaker_id] = array(
						'id' => $speaker->explnum_speaker_speaker_num,
						'gender' => $speaker->explnum_speaker_gender,
						'author' => $speaker->explnum_speaker_author,
						'author_libelle' => $speaker->author_name.($speaker->author_rejete ? ', '.$speaker->author_rejete : ''),
						'posY' => $this->dimensions['speakerTop'] + ($i * ($this->dimensions['speakerHeight'] + $this->dimensions['speakerMarginBottom']))
						);
				$i++;
			}
		}

		$query = "select explnum_segment_id, explnum_segment_speaker_num, explnum_segment_start, explnum_segment_duration, explnum_segment_end from explnum_segments where explnum_segment_explnum_num = ".$this->explnum_id;
		$result = pmb_mysql_query($query);
		if ($result && pmb_mysql_num_rows($result)) {
			while ($segment = pmb_mysql_fetch_object($result)) {
				$this->segments[] = array(
						'db_id' => $segment->explnum_segment_id,
						'speaker' => $segment->explnum_segment_speaker_num,
						'start' => $segment->explnum_segment_start,
						'duration' => $segment->explnum_segment_duration,
						'end' => $segment->explnum_segment_end
						);
				if ($segment->explnum_segment_end > $this->duration) $this->duration = $segment->explnum_segment_end;
			}
		}
	}

	/**
	 * Construit le fond
	 */
	private function getBackground() {
		if (!count($this->speakers)) {
			$this->getDatas();
		}
		global $explnum_associate_background_svg;
		global $explnum_associate_background_svg_posX;
		global $explnum_associate_background_svg_posY;
		global $explnum_associate_background_svg_width;
		global $explnum_associate_background_svg_height;

		$backgroundSvg = '<g id="background_svg" transform="!!transform!!">'.$explnum_associate_background_svg.'</g>';

		$x = ($this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight']);
		$y = $this->dimensions['scaleBottom'];
		$width = ($this->dimensions['totalWidth'] - ($this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'])) / $explnum_associate_background_svg_width;
		$height = ($this->dimensions['speakerTop'] - $this->dimensions['scaleBottom'] + count($this->speakers) * ($this->dimensions['speakerHeight'] + $this->dimensions['speakerMarginBottom'])) / $explnum_associate_background_svg_height;

		$transform = "translate(".$x.", ".$y.") scale(".$width.", ".$height.") translate(".(0 - $explnum_associate_background_svg_posX).", ".(0 - $explnum_associate_background_svg_posY).")";

		$backgroundSvg = str_replace("!!transform!!", $transform, $backgroundSvg);

		$this->svg .= $backgroundSvg;
	}

	/**
	 * Construit le curseur
	 */
	private function getCursor() {
		global $explnum_associate_cursor_svg;
		global $explnum_associate_cursor_svg_posX;
		global $explnum_associate_cursor_svg_posY;
		global $explnum_associate_cursor_svg_width;
		global $explnum_associate_cursor_svg_height;

		$cursorSvg = '<g id="cursor_svg" transform="!!transform!!" title="0:00">'.$explnum_associate_cursor_svg.'</g>';

		$x = ($this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + $this->dimensions['backgroundPadding']) - ($explnum_associate_cursor_svg_width / 2);
		$height = ($this->dimensions['speakerTop'] + count($this->speakers) * ($this->dimensions['speakerHeight'] + $this->dimensions['speakerMarginBottom'])) / $explnum_associate_cursor_svg_height;

		$transform = "translate(".$x.", 0) scale(1, ".$height.") translate(".(0 - $explnum_associate_cursor_svg_posX).", ".(0 - $explnum_associate_cursor_svg_posY).")";

		$cursorSvg = str_replace("!!transform!!", $transform, $cursorSvg);

		$this->svg .= $cursorSvg;
	}

	/**
	 * Construit le taquet de gauche
	 */
	private function getLeftCursor() {
		global $explnum_associate_left_cursor_svg;
		global $explnum_associate_left_cursor_svg_posX;
		global $explnum_associate_left_cursor_svg_posY;
		global $explnum_associate_left_cursor_svg_width;
		global $explnum_associate_left_cursor_svg_height;

		$leftCursorSvg = '<g id="left_cursor_svg" transform="!!transform!!" title="0:00" time="0">'.$explnum_associate_left_cursor_svg.'</g>';

		$x = ($this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + $this->dimensions['backgroundPadding']) - ($explnum_associate_left_cursor_svg_width);
		$height = ($this->dimensions['speakerTop'] + count($this->speakers) * ($this->dimensions['speakerHeight'] + $this->dimensions['speakerMarginBottom'])) / $explnum_associate_left_cursor_svg_height;

		$transform = "translate(".$x.", 0) scale(1, ".$height.") translate(".(0 - $explnum_associate_left_cursor_svg_posX).", ".(0 - $explnum_associate_left_cursor_svg_posY).")";

		$leftCursorSvg = str_replace("!!transform!!", $transform, $leftCursorSvg);

		$this->svg .= $leftCursorSvg;
	}

	/**
	 * Construit le taquet de droite
	 */
	private function getRightCursor() {
		global $explnum_associate_right_cursor_svg;
		global $explnum_associate_right_cursor_svg_posX;
		global $explnum_associate_right_cursor_svg_posY;
		global $explnum_associate_right_cursor_svg_width;
		global $explnum_associate_right_cursor_svg_height;

		$rightCursorSvg = '<g id="right_cursor_svg" transform="!!transform!!" title="0:00" time="0">'.$explnum_associate_right_cursor_svg.'</g>';

		$x = ($this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + $this->dimensions['backgroundPadding']);
		$height = ($this->dimensions['speakerTop'] + count($this->speakers) * ($this->dimensions['speakerHeight'] + $this->dimensions['speakerMarginBottom'])) / $explnum_associate_right_cursor_svg_height;

		$transform = "translate(".$x.", 0) scale(1, ".$height.") translate(".(0 - $explnum_associate_right_cursor_svg_posX).", ".(0 - $explnum_associate_right_cursor_svg_posY).")";

		$rightCursorSvg = str_replace("!!transform!!", $transform, $rightCursorSvg);

		$this->svg .= $rightCursorSvg;
	}

	/**
	 * Retourne la chaine JavaScript
	 * @param boolean edit true pour activer la possibilité d'édition
	 * @return string
	 */
	public function getJs($edit = false) {
		if ((!count($this->speakers)) || (!count($this->segments))) {
			$this->getDimensions();
			$this->getDatas();
		}

		// Calcul de la largeur totale disponible pour le ratio
		$availableWidth = $this->dimensions['totalWidth'] - ($this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + (2 * $this->dimensions['backgroundPadding']));

		// Temps total
		$duration = $this->duration / 100;

		// Calcul ratio
		$ratio = $availableWidth / $duration;

		// Récupération des variables en js
		$this->js .= "
		var duration = ".$duration.";
		var ratio = ".$ratio.";
		var player = videojs('videojs');
		var segments = ".json_encode($this->segments).";";

		// Déplacement du curseur
		$this->js .= "
		function update_cursor(){
			document.getElementById('cursor_svg').transform.baseVal.getItem(0).setTranslate(".($this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + $this->dimensions['backgroundPadding'])." + (player.currentTime() * ratio), 0);
			document.getElementById('cursor_svg').setAttribute('title', get_time_to_display(player.currentTime()));
		}

		function get_time_to_display(tps){
			tps = Math.round(tps);
			var sec = tps % 60;
			var min = (tps - sec) / 60;

			sec = '' + sec;
			while (sec.length < 2) {
				sec = '0' + sec;
			}

			return min + ':' + sec;
		}

		player.on('timeupdate', update_cursor);
		";

		// Accès au début d'un segment en passant son id
		$this->js .= "
		function move_cursor_on_segment(id) {
			for (var i in segments) {
				if (segments[i].db_id == id) {
					player.currentTime(segments[i].start / 100);
					return segments[i].start / 100;
				}
			}
			return 0;
		}";

		// Drag du curseur
		$this->js .= "
		function start_drag_cursor(event) {
			event.preventDefault();
			document.addEventListener('mouseup', stop_drag_cursor, false);
			document.addEventListener('mousemove', drag_cursor, false);
		}

		function drag_cursor(event) {
			update_video_time(event);
		}

		function stop_drag_cursor() {
			document.removeEventListener('mousemove', drag_cursor, false);
			document.removeEventListener('mouseup', stop_drag_cursor, false);
		}
		";

		// Mise à jour de la vidéo
		$this->js .= "
		function update_video_time(event) {
			event.preventDefault();
			player.currentTime((event.clientX - (findPos(document.getElementById('speech_timeline'))[0] + ". ($this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + 2 * $this->dimensions['backgroundPadding']).")) / ratio);
		}";

		// Ajout du listener sur le background
		$this->js .= "
		document.getElementById('background_svg').addEventListener('click', update_video_time, false);";

		// Ajout du listener sur le curseur
		$this->js .= "
		document.getElementById('cursor_svg').addEventListener('mousedown', start_drag_cursor, false);";

		if ($edit) {
			$this->getJsEdit();
		} else {
			// Ajout du listener sur un segment
			$this->js .= "
			for (var i in segments) {
				document.getElementById('segment_svg_' + i).addEventListener('click', update_video_time, false);
			}";
		}

		// Positionnement du curseur
		$this->js .= "
		update_cursor();";

		return $this->js;
	}

	/**
	 * Portion de la chaine JavaScript gérant l'édition
	 */
	private function getJsEdit() {
		global $base_path;
		global $msg;

		// Récupération du tableau de locuteurs en js
		$this->js .= "
		var speakers = ".json_encode($this->utf8_normalize($this->speakers)).";
		var segments_between_lr_cursors = new Array();";

		// Positionnement des taquets
		global $explnum_associate_left_cursor_svg_width;
		$this->js .= "
		function update_lr_cursor(cursor_id) {
			var cursor = document.getElementById(cursor_id);
			var time = document.getElementById(cursor_id + '_pos').value.split(':');
			var sec = 0;

			if (time.length == 2) sec = time[0]*60 + time[1]*1;
			else sec = time[0];

			document.getElementById(cursor_id + '_pos').value = get_time_to_display(sec);

			var x = sec * ratio + ".($this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + $this->dimensions['backgroundPadding']).";
			if (cursor_id == 'left_cursor_svg') {
				x = x - ".$explnum_associate_left_cursor_svg_width.";

				if (x <= document.getElementById('right_cursor_svg').transform.baseVal.getItem(0).matrix.e) {
					cursor.transform.baseVal.getItem(0).setTranslate(x, cursor.transform.baseVal.getItem(0).matrix.f);
					cursor.setAttribute('title', document.getElementById(cursor_id + '_pos').value);
					cursor.setAttribute('time', sec);
					get_segments_between_lr_cursors();
				} else {
					document.getElementById(cursor_id + '_pos').value = document.getElementById('right_cursor_svg_pos').value;
					update_lr_cursor(cursor_id);
				}
			} else if (cursor_id == 'right_cursor_svg') {
				if (x > ".($this->dimensions['totalWidth'] - $this->dimensions['backgroundPadding']).") {
					document.getElementById(cursor_id + '_pos').value = get_time_to_display(duration);
					update_lr_cursor(cursor_id);
				} else if (x >= document.getElementById('left_cursor_svg').transform.baseVal.getItem(0).matrix.e) {
					cursor.transform.baseVal.getItem(0).setTranslate(x, cursor.transform.baseVal.getItem(0).matrix.f);
					cursor.setAttribute('title', document.getElementById(cursor_id + '_pos').value);
					cursor.setAttribute('time', sec);
					get_segments_between_lr_cursors();
				} else {
					document.getElementById(cursor_id + '_pos').value = document.getElementById('left_cursor_svg_pos').value;
					update_lr_cursor(cursor_id);
				}
			}
		}

		update_lr_cursor('right_cursor_svg');
		update_lr_cursor('left_cursor_svg');";

		// Fonction d'ouverture du popup
		$this->js .= "
		function openPopUpCall(id) {
			openPopUp('./select.php?what=auteur&callback=update_associate_author&caller=explnum_associate_speaker_' + id + '&param1=aut' + id + '_id&param2=aut' + id + '&deb_rech='+".pmb_escape()."(document.getElementById('aut' + id).value), 'selector');
		}";

		// Réinitialisation du formulaire
		$this->js .= "
		function clearAut(id) {
			document.getElementById('aut' + id).value='';
			document.getElementById('aut' + id + '_id').value='0';
			update_associate_author();
		}";

		// Validation du formulaire
		$this->js .= "
		function update_associate_author() {
			var id = document.getElementById('id_current_author_associate_form').value;
			if (document.getElementById('aut' + id).value != '') {
				document.getElementById('explnum_associate_author_libelle_' + id).innerHTML = document.getElementById('aut' + id).value;
			} else {
				document.getElementById('explnum_associate_author_libelle_' + id).innerHTML = '".$msg['explnum_associate_author']."';
			}
			document.getElementById('author_associate_form_' + id).style.display = 'none';

			var author_id = document.getElementById('aut' + id + '_id').value;

			var req = new http_request();
			req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=update_associate_author&speaker_id=' + id + '&author_id=' + author_id,0,'',1,'','');
		}";

		// Fermeture du formulaire
		$this->js .= "
		function close_author_associate_form(id) {
			document.getElementById('author_associate_form_' + id).style.display = 'none';
		}";

		// Création des div de selection d'autorité
		$this->js .= "
		for (var i in speakers) {
			if (!document.getElementById('author_associate_form_' + i)) {
				var form = document.createElement('form');
				form.id = 'author_associate_form_' + i;
				form.className = 'form-catalog';
				form.name = 'explnum_associate_speaker_' + i;
				var x = findPos(document.getElementById('speech_timeline'))[0] + ".$this->dimensions['speakerLeft'].";
				var y = findPos(document.getElementById('speech_timeline'))[1] - 10 + speakers[i]['posY']*1;
				form.style = 'position: absolute; top: ' + y + 'px; left: ' + x + 'px;';
				form.addEventListener('submit', function(e){
					e.preventDefault();
					e.stopPropagation();
				},false);

				var label = document.createElement('label');
				label.className = 'etiquette';
				label.for = 'aut' + i;
				label.innerHTML = '".$msg['234']."';

				var img = document.createElement('img');
				img.src = '".get_url_icon('close.png')."';
				img.alt = '".$msg['197']."';
				img.title = '".$msg['197']."';
				img.className = 'right';
				img.setAttribute('field_id', i);
				img.style.cursor = 'pointer';
				img.addEventListener('click', function(){
					close_author_associate_form(this.getAttribute('field_id'));
				}, false);

				var div = document.createElement('div');
				div.className = 'row';

				var span = document.createElement('span');

				var input1 = document.createElement('input');
				input1.type = 'text';
				input1.id = 'aut' + i;
				input1.className = 'saisie-20emr';
				input1.name = 'aut' + i;
				input1.setAttribute('autfield', 'aut' + i + '_id');
				input1.setAttribute('completion', 'authors');
				input1.setAttribute('autocompletion', 'on');
				input1.value = speakers[i].author_libelle;
				input1.setAttribute('callback', 'update_associate_author');

				var input2 = document.createElement('input');
				input2.type = 'button';
				input2.className = 'bouton';
				input2.value = '...';
				input2.setAttribute('field_id', i);
				input2.addEventListener('click', function(){
					openPopUpCall(this.getAttribute('field_id'));
				}, false);

				var input3 = document.createElement('input');
				input3.type = 'button';
				input3.className = 'bouton';
				input3.value = 'X';
				input3.setAttribute('field_id', i);
				input3.addEventListener('click', function(){
					clearAut(this.getAttribute('field_id'));
				}, false);

				var input4 = document.createElement('input');
				input4.type = 'hidden';
				input4.id = 'aut' + i + '_id';
				input4.name = 'aut' + i + '_id';
				input4.value = speakers[i].author;

				span.appendChild(input1);
				div.appendChild(span);
				div.appendChild(input2);
				div.appendChild(input3);
				div.appendChild(input4);
				form.appendChild(label);
				form.appendChild(img);
				form.appendChild(div);
				document.getElementById('att').appendChild(form);

				ajax_pack_element(document.getElementById('aut' + i));
				document.getElementById('author_associate_form_' + i).style.display = 'none';
			}
		}

		var input = document.createElement('input');
		input.type = 'hidden';
		input.id = 'id_current_author_associate_form';
		input.value = 0;

		document.getElementById('att').appendChild(input);
		";

		// Clic sur un auteur, on affiche le formulaire
		$this->js .= "
		function display_author_associate_form(event) {
			var current_id = document.getElementById('id_current_author_associate_form').value;
			if (current_id != 0) {
				document.getElementById('author_associate_form_' + current_id).style.display = 'none';
			}
			var id = event.currentTarget.id.replace('explnum_associate_author_libelle_','');
			document.getElementById('id_current_author_associate_form').value = id;
			document.getElementById('author_associate_form_' + id).style.display = 'block';
			document.getElementById('aut' + id).focus();
		}
		";

		// Drag des segments
		$this->js .= "
		var current_drag_segment;
		var last_pageY;
		var current_drag_speaker_id;

		function set_current_speaker(id, is_current) {
			var current_drag_speaker = document.getElementById(id);
			if (is_current) {
				current_drag_speaker.setAttribute('stroke', 'red');
			} else {
				current_drag_speaker.removeAttribute('stroke');
			}
		}

		function start_drag_segment(event) {
			current_drag_segment = event.currentTarget;
			last_pageY = event.pageY;

			var segment_id = current_drag_segment.id.replace('segment_svg_','');
			current_drag_speaker_id = 'speaker_svg_' + segments[segment_id]['speaker'];
			set_current_speaker(current_drag_speaker_id, true);

			document.addEventListener('mouseup', stop_drag_segment, false);
			document.addEventListener('mousemove', drag_segment, false);
			event.preventDefault();

			var clone = current_drag_segment.cloneNode(true);
			clone.id = clone.id + '_clone';
			clone.setAttribute('fill-opacity', 0.5);
			clone.setAttribute('stroke', 'red');
			clone.setAttribute('stroke-dasharray','5,5');
			document.getElementById('speech_timeline_svg').appendChild(clone);
		}

		function drag_segment(event) {
			var clone = document.getElementById(current_drag_segment.id + '_clone');
			clone.transform.baseVal.getItem(0).setTranslate(clone.transform.baseVal.getItem(0).matrix.e, clone.transform.baseVal.getItem(0).matrix.f + event.pageY - last_pageY);
			last_pageY = event.pageY;

			var mouse_posY = event.pageY - findPos(document.getElementById('speech_timeline'))[1];
			var speaker_id = current_drag_speaker_id.replace('speaker_svg_', '');
			if ((speakers[speaker_id].posY*1 > mouse_posY) || ((speakers[speaker_id].posY*1 + ".$this->dimensions['speakerHeight'].") < mouse_posY)) {
				set_current_speaker(current_drag_speaker_id, false);
				for (var i in speakers) {
					if ((speakers[i].posY*1 <= mouse_posY) && ((speakers[i].posY*1 + ".$this->dimensions['speakerHeight'].") >= mouse_posY)) {
						current_drag_speaker_id = 'speaker_svg_' + i;
						set_current_speaker(current_drag_speaker_id, true);
						break;
					}
				}
			}
		}

		function stop_drag_segment(event) {
			document.removeEventListener('mousemove', drag_segment, false);
			document.removeEventListener('mouseup', stop_drag_segment, false);

			var clone = document.getElementById(current_drag_segment.id + '_clone');
			clone.remove();

			set_current_speaker(current_drag_speaker_id, false);

			var speaker_id = current_drag_speaker_id.replace('speaker_svg_', '');
			var segment_id = current_drag_segment.id.replace('segment_svg_','');
			var move_allowed = true;

			for (var i in segments) {
				if ((i != segment_id) && (speaker_id == segments[i].speaker)) {
					if ((((segments[segment_id].start*1) > (segments[i].start*1)) && ((segments[segment_id].start*1) < (segments[i].end*1))) || (((segments[segment_id].end*1) > (segments[i].start*1)) && ((segments[segment_id].end*1) < (segments[i].end*1))) || (((segments[i].start*1) > (segments[segment_id].start*1)) && ((segments[i].start*1) < (segments[segment_id].end*1))) || (((segments[i].end*1) > (segments[segment_id].start*1)) && ((segments[i].end*1) < (segments[segment_id].end*1)))) {
						move_allowed = false;
						break;
					}
				}
			}

			if ((segments[segment_id].speaker != speaker_id) && move_allowed) {
				current_drag_segment.transform.baseVal.getItem(0).setTranslate(current_drag_segment.transform.baseVal.getItem(0).matrix.e, speakers[speaker_id].posY*1);
				segments[segment_id].speaker = speaker_id;

				var req = new http_request();
				req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=update_associate_speaker&segment_id=' + segments[segment_id].db_id + '&speaker_id=' + speaker_id,0,'',1,'','');
			} else if (!move_allowed) {
				alert('".$msg['explnum_associate_segments_move_forbidden']."');
			}
		}
		";

		// Ajout d'un locuteur
		$this->js .= "
		function add_speaker(event) {
			var req = new http_request();
			req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=add_new_speaker&explnum_id=".$this->explnum_id."',0,'',1,get_explnum_associate_ajax,'');
		}";

		// Suppression d'un locuteur
		$this->js .= "
		function del_speaker(event) {
			var speaker_id = event.currentTarget.id.replace('explnum_del_associate_speaker_', '');
			hasSegment = false;

			for (var i in segments) {
				if (segments[i].speaker == speaker_id) {
					hasSegment = true;
					break;
				}
			}

			if (hasSegment) {
				alert('".$msg['explnum_del_associate_speaker_forbidden']."');
			} else if (confirm('".$msg['explnum_del_associate_speaker_confirm']."')) {
				var req = new http_request();
				req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=delete_associate_speaker&speaker_id=' + speaker_id,0,'',1,get_explnum_associate_ajax,'');
			}
		}";

		// Drag des taquets
		global $explnum_associate_left_cursor_svg_width;

		$this->js .= "
		var current_drag_lr_cursor;
		var last_pageX;
		var lr_cursor_pos;

		function start_drag_lr_cursor(event) {
			current_drag_lr_cursor = event.currentTarget;
			last_pageX = event.pageX;

			document.addEventListener('mouseup', stop_drag_lr_cursor, false);
			document.addEventListener('mousemove', drag_lr_cursor, false);
			event.preventDefault();
		}

		function drag_lr_cursor(event) {
			var cond;

			if (current_drag_lr_cursor.id == 'left_cursor_svg') {
				lr_cursor_pos = current_drag_lr_cursor.transform.baseVal.getItem(0).matrix.e + event.pageX - last_pageX + ".($explnum_associate_left_cursor_svg_width - ($this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + $this->dimensions['backgroundPadding'])).";
				cond = (((current_drag_lr_cursor.transform.baseVal.getItem(0).matrix.e + event.pageX - last_pageX + ".$explnum_associate_left_cursor_svg_width.") <= (document.getElementById('right_cursor_svg').transform.baseVal.getItem(0).matrix.e)) && (lr_cursor_pos >= 0));
			} else if (current_drag_lr_cursor.id == 'right_cursor_svg') {
				lr_cursor_pos = current_drag_lr_cursor.transform.baseVal.getItem(0).matrix.e + event.pageX - last_pageX - ".($this->dimensions['speakerLeft'] + $this->dimensions['speakerWidth'] + $this->dimensions['speakerMarginRight'] + $this->dimensions['backgroundPadding']).";
				cond = (((current_drag_lr_cursor.transform.baseVal.getItem(0).matrix.e + event.pageX - last_pageX) >= (document.getElementById('left_cursor_svg').transform.baseVal.getItem(0).matrix.e + ".$explnum_associate_left_cursor_svg_width.")) && ((current_drag_lr_cursor.transform.baseVal.getItem(0).matrix.e + event.pageX - last_pageX) <= ".($this->dimensions['totalWidth'] - $this->dimensions['backgroundPadding'])."));
			}

			if (cond) {
				current_drag_lr_cursor.transform.baseVal.getItem(0).setTranslate(current_drag_lr_cursor.transform.baseVal.getItem(0).matrix.e + event.pageX - last_pageX, current_drag_lr_cursor.transform.baseVal.getItem(0).matrix.f);
				document.getElementById(current_drag_lr_cursor.id + '_pos').value = get_time_to_display(lr_cursor_pos / ratio);
				current_drag_lr_cursor.setAttribute('title', get_time_to_display(lr_cursor_pos / ratio));
				current_drag_lr_cursor.setAttribute('time', lr_cursor_pos / ratio);

				last_pageX = event.pageX;

				get_segments_between_lr_cursors();
			}
		}

		function stop_drag_lr_cursor(event) {
			document.removeEventListener('mousemove', drag_lr_cursor, false);
			document.removeEventListener('mouseup', stop_drag_lr_cursor, false);
		}";

		// Trouver les segments présents entre les taquets
		$this->js .= "
		function get_segments_between_lr_cursors() {
			var left_cursor_time = document.getElementById('left_cursor_svg').getAttribute('time');
			var right_cursor_time = document.getElementById('right_cursor_svg').getAttribute('time');
			var current_segment;

			segments_between_lr_cursors = [];

			for (var i in segments) {
				current_segment = document.getElementById('segment_svg_' + i);
				if (((segments[i].start/100) <= right_cursor_time) && ((segments[i].end/100) >= left_cursor_time)) {
					is_between_lr_cursors(current_segment, true);
					segments_between_lr_cursors[segments_between_lr_cursors.length] = segments[i];
				} else {
					is_between_lr_cursors(current_segment, false);
				}
			}
		}

		function is_between_lr_cursors(segment, is_between) {
			if (is_between) {
				segment.setAttribute('stroke', 'green');
				segment.setAttribute('stroke-width', '2px');
			} else {
				segment.removeAttribute('stroke');
				segment.removeAttribute('stroke-width');
			}
		}";

		// Ajout d'un segment
		$this->js .= "
		function add_new_segment() {
			var left_cursor_time = Math.round(document.getElementById('left_cursor_svg').getAttribute('time')*100);
			var right_cursor_time = Math.round(document.getElementById('right_cursor_svg').getAttribute('time')*100);

			if (left_cursor_time != right_cursor_time) {
				var speakers_with_segment = new Array();
				var speaker_available = true;
				var speaker_id = 0;

				for (var i in segments_between_lr_cursors) {
					if (speakers_with_segment.indexOf(segments_between_lr_cursors[i].speaker) == -1) {
						speakers_with_segment[speakers_with_segment.length] = segments_between_lr_cursors[i].speaker;
					}
					if (speakers_with_segment.length == speakers.length) {
						speaker_available = false;
						break;
					}
				}

				if (speaker_available) {
					for (var i in speakers) {
						if (speakers_with_segment.indexOf(i) == -1) {
							speaker_id = i;
							break;
						}
					}
				}

				var req = new http_request();
				req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=add_new_segment&explnum_id=".$this->explnum_id."&speaker_id=' + speaker_id + '&start=' + left_cursor_time + '&end=' + right_cursor_time,0,'',1,get_explnum_associate_ajax,'');
			}
		}";

		// Fonction de recherche des segments entre les taquets pour un locuteur donné
		$this->js .= "
		function get_speaker_segments(speaker_id) {
			var segments = new Array();

			if (speaker_id) {
				for (var i in segments_between_lr_cursors) {
					if (segments_between_lr_cursors[i].speaker == speaker_id) {
						segments[segments.length] = segments_between_lr_cursors[i];
					}
				}
			}

			return segments;
		}";

		// Suppression de segments
		$this->js .= "
		function del_segments(segments) {
			if (confirm('".$msg['explnum_associate_del_segments_confirm']."')) {
				var segments_ids = new Array();
				for (var i in segments) {
					segments_ids[segments_ids.length] = segments[i].db_id;
				}

				var ids_list = segments_ids.join(',');

				var req = new http_request();
				req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=delete_segments&segments_ids=' + ids_list,0,'',1,get_explnum_associate_ajax,'');
			}
		}";

		// Fusion de segments
		$this->js .= "
		function join_segments(segments) {
			if (confirm('".$msg['explnum_associate_join_segments_confirm']."')) {
				var segments_ids = new Array();
				var start = 0;
				var end = 0;
				var speaker_id = segments[0].speaker;

				for (var i in segments) {
					segments_ids[segments_ids.length] = segments[i].db_id;
					if ((!start) || (start > (segments[i].start*1))) {
						start = segments[i].start*1;
					}
					if (end < (segments[i].end*1)) {
						end = segments[i].end*1;
					}
				}

				var ids_list = segments_ids.join(',');

				var req = new http_request();
				req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=delete_segments&segments_ids=' + ids_list,0,'',0,'','');

				var req = new http_request();
				req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=add_new_segment&explnum_id=".$this->explnum_id."&speaker_id=' + speaker_id + '&start=' + start + '&end=' + end,0,'',1,get_explnum_associate_ajax,'');
			}
		}";

		// Scission de segments
		$this->js .= "
		function cut_segments(segments) {
			if (confirm('".$msg['explnum_associate_cut_segments_confirm']."')) {
				var speaker_id = segments[0].speaker;
				var left_cursor_time = Math.round(document.getElementById('left_cursor_svg').getAttribute('time')*100);
				var right_cursor_time = Math.round(document.getElementById('right_cursor_svg').getAttribute('time')*100);
				var start;
				var end;
				var exit = 0

				for (var i in segments) {
					start = 0;
					end = 0;
					if ((segments[i].start*1 < left_cursor_time) && (left_cursor_time < segments[i].end*1)) {
						start = left_cursor_time;
						var left_req = new http_request();
						left_req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=add_new_segment&explnum_id=".$this->explnum_id."&speaker_id=' + speaker_id + '&start=' + segments[i].start*1 + '&end=' + left_cursor_time,0,'',0,'','');
						exit = exit+1;
					}

					if ((segments[i].start*1 < right_cursor_time) && (right_cursor_time < segments[i].end*1)) {
						end = right_cursor_time;
						var right_req = new http_request();
						right_req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=add_new_segment&explnum_id=".$this->explnum_id."&speaker_id=' + speaker_id + '&start=' + right_cursor_time + '&end=' + segments[i].end*1,0,'',0,'','');
						exit = exit+1;
					}

					if (start || end) {
						var req = new http_request();
						req.request('$base_path/ajax.php?module=catalog&categ=explnum&quoifaire=update_segment_time&segment_id=' + segments[i].db_id + '&start=' + start + '&end=' + end,0,'',0,'','');
					}

					if (exit == 2) break;
				}
				get_explnum_associate_ajax();
			}
		}";

		// Affichage du menu contextuel d'édition des segments
		$this->js .= "
		function display_edit_menu(event) {
			event.preventDefault();

			var clickPosX = event.pageX - findPos(document.getElementById('speech_timeline'))[0];
			var clickPosY = event.pageY - findPos(document.getElementById('speech_timeline'))[1];
			var selectedSpeakerId = 0;

			for (var i in speakers) {
				if ((clickPosY >= speakers[i].posY*1) && (clickPosY <= (speakers[i].posY*1 + ".$this->dimensions['speakerHeight']."))) {
					selectedSpeakerId = i;
					break;
				}
			}
			var selectedSpeakerSegments = get_speaker_segments(selectedSpeakerId);

			if ((clickPosX > (document.getElementById('left_cursor_svg').transform.baseVal.getItem(0).matrix.e + ".$explnum_associate_left_cursor_svg_width.")) && (clickPosX < document.getElementById('right_cursor_svg').transform.baseVal.getItem(0).matrix.e)) {
				document.addEventListener('mousedown', remove_edit_menu, false);

				var div = document.createElement('div');
				div.id = 'speech_timeline_edit_menu';
				div.style.position = 'absolute';
				div.style.top = event.pageY + 'px';
				div.style.left = event.pageX + 'px';

				var ul = document.createElement('ul');

				var li = document.createElement('li');
				li.innerHTML = '".$msg['explnum_associate_add_segment']."';
				li.addEventListener('mousedown', add_new_segment, false);
				ul.appendChild(li);

				if (selectedSpeakerSegments.length) {
					var li = document.createElement('li');
					li.innerHTML = '".$msg['explnum_associate_del_segments']."';
					li.addEventListener('mousedown', function(){
						del_segments(selectedSpeakerSegments);
					}, false);
					ul.appendChild(li);

					var li = document.createElement('li');
					li.innerHTML = '".$msg['explnum_associate_cut_segments']."';
					li.addEventListener('mousedown', function(){
						cut_segments(selectedSpeakerSegments);
					}, false);
					ul.appendChild(li);
				}
				if (selectedSpeakerSegments.length > 1) {
					var li = document.createElement('li');
					li.innerHTML = '".$msg['explnum_associate_join_segments']."';
					li.addEventListener('mousedown', function(){
						join_segments(selectedSpeakerSegments);
					}, false);
					ul.appendChild(li);
				}

				div.appendChild(ul);
				document.getElementById('speech_timeline').appendChild(div);
			}
		}

		function remove_edit_menu(event) {
			document.removeEventListener('mousedown', remove_edit_menu, false);
			if (document.getElementById('speech_timeline_edit_menu')) {
				document.getElementById('speech_timeline').removeChild(document.getElementById('speech_timeline_edit_menu'));
			}
		}";

		// Fonction de déplacement d'un taquet sur le cursor
		global $explnum_associate_cursor_svg_width;
		$this->js .= "
		function move_lr_cursor_on_cursor(cursor_id) {
			var cursor_pos = document.getElementById('cursor_svg').transform.baseVal.getItem(0).matrix.e*1 + ".($explnum_associate_cursor_svg_width / 2).";
			var allowed = true;

			if (cursor_id == 'left_cursor_svg') {
				cursor_pos = cursor_pos - ".$explnum_associate_left_cursor_svg_width.";
				var cursor_to_move = document.getElementById('left_cursor_svg');
				var other_cursor = document.getElementById('right_cursor_svg');
				var other_cursor_pos = document.getElementById('right_cursor_svg').transform.baseVal.getItem(0).matrix.e*1 - ".$explnum_associate_left_cursor_svg_width.";

				if (cursor_pos > other_cursor_pos) allowed = false;
			} else {
				var cursor_to_move = document.getElementById('right_cursor_svg');
				var other_cursor = document.getElementById('left_cursor_svg');
				var other_cursor_pos = document.getElementById('left_cursor_svg').transform.baseVal.getItem(0).matrix.e*1;

				if (cursor_pos < other_cursor_pos) allowed = false;
			}

			if (allowed) {
				cursor_to_move.transform.baseVal.getItem(0).setTranslate(cursor_pos, cursor_to_move.transform.baseVal.getItem(0).matrix.f);
				var time = Math.round(player.currentTime());
				cursor_to_move.setAttribute('time', time);
				time = get_time_to_display(time);
				document.getElementById(cursor_id + '_pos').value = time;
				cursor_to_move.setAttribute('title', time);
			} else {
				cursor_to_move.transform.baseVal.getItem(0).setTranslate(other_cursor_pos, cursor_to_move.transform.baseVal.getItem(0).matrix.f);
				document.getElementById(cursor_id + '_pos').value = other_cursor.getAttribute('title');
				cursor_to_move.setAttribute('title', other_cursor.getAttribute('title'));
				cursor_to_move.setAttribute('time', other_cursor.getAttribute('time'));
			}

			get_segments_between_lr_cursors();
		}";

		// Ajout du listener sur un segment
		$this->js .= "
		for (var i in segments) {
			document.getElementById('segment_svg_' + i).addEventListener('mousedown', start_drag_segment, false);
		}";

		// Ajout des listeners sur les locuteurs (Ouverture formulaire et suppression)
		$this->js .= "
		for (var i in speakers) {
			document.getElementById('explnum_associate_author_libelle_' + i).addEventListener('click', display_author_associate_form, false);

			document.getElementById('explnum_del_associate_speaker_' + i).addEventListener('click', del_speaker, false);
		}";

		// Ajout du listener sur le bouton d'ajout d'un locuteur
		$this->js .= "
		document.getElementById('explnum_associate_add_speaker').addEventListener('click', add_speaker, false);";

		// Ajout des listeners sur les taquets
		$this->js .= "
		document.getElementById('left_cursor_svg').addEventListener('mousedown', start_drag_lr_cursor, false);
		document.getElementById('right_cursor_svg').addEventListener('mousedown', start_drag_lr_cursor, false);";

		// Ajout des listeners sur les champs position des taquets
		$this->js .= "
		document.getElementById('left_cursor_svg_pos').addEventListener('change', function(event) {
			update_lr_cursor('left_cursor_svg');
		}, false);
		document.getElementById('right_cursor_svg_pos').addEventListener('change', function(event) {
			update_lr_cursor('right_cursor_svg');
		}, false);";

		// Ajout du listener du clic droit
		$this->js .= "
		document.getElementById('speech_timeline').addEventListener('contextmenu', display_edit_menu, true);";

		// Ajout des listener pour déplacer un taquet sur le curseur
		$this->js .= "
		document.getElementById('left_cursor_svg_to_cursor').addEventListener('click', function(event) {
			move_lr_cursor_on_cursor('left_cursor_svg');
		}, false);
		document.getElementById('right_cursor_svg_to_cursor').addEventListener('click', function(event) {
			move_lr_cursor_on_cursor('right_cursor_svg');
		}, false);";
	}

	private function utf8_normalize($array) {
		global $charset;
		$rarray=array();
		foreach ($array as $key=>$val) {
			if (is_array($val)) {
				$rarray[$key]=$this->utf8_normalize($val);
			} else $rarray[$key]=($charset!="utf-8"?utf8_encode($val):$val);
		}
		return $rarray;
	}
}
?>
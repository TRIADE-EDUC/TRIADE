<?php
/**
* Fournit une API pour parser les playlists XML
* Extremement basique... Mais on fera mieux la prochaine fois
* @author	 m_i_h_k_e_l_AT_w_w_DOT_e_e sur fr.php.net/xml_parse
*/

class XmlC
{
  var $xml_data;
  var $obj_data;
  var $pointer;

  function XmlC()
  {
  }

  function Set_xml_data( &$xml_data )
  {
   $this->index = 0;
   $this->pointer[] = &$this->obj_data;

   $this->xml_data = $xml_data;
   $this->xml_parser = xml_parser_create( "ISO-8859-1" );

   xml_parser_set_option( $this->xml_parser, XML_OPTION_CASE_FOLDING, false );
   xml_set_object( $this->xml_parser, $this );
   xml_set_element_handler( $this->xml_parser, "_startElement", "_endElement");
   xml_set_character_data_handler( $this->xml_parser, "_cData" );

   xml_parse( $this->xml_parser, $this->xml_data, true );
   xml_parser_free( $this->xml_parser );
  }

  function _startElement( $parser, $tag, $attributeList )
  {
   $object = "";
   foreach( $attributeList as $name => $value )
   {
     $value = $this->_cleanString( $value );
     $object->$name = $value;
   }

   eval( "\$this->pointer[\$this->index]->" . $tag . "[] = \$object;" );
   eval( "\$size = sizeof( \$this->pointer[\$this->index]->" . $tag . " );" );
   eval( "\$this->pointer[] = &\$this->pointer[\$this->index]->" . $tag . "[\$size-1];" );
   
   $this->index++;
  }

  function _endElement( $parser, $tag )
  {
   array_pop( $this->pointer );
   $this->index--;
  }

	function _cData( $parser, $data )
	  {
	   if( trim( $data ) != '')
	   {
		 if (empty($this->pointer[$this->index]))
		   $this->pointer[$this->index] = trim($data) ;
		 else
		   $this->pointer[$this->index] .= $data."\n" ;
	   }
	  } 

  function _cleanString( $string )
  {
   return utf8_decode( trim( $string ) );
  }


}

class PlaylistInfo
{
	var $playlist;
	var $pdefault;
	  
	  function PlaylistInfo($data) {
		$this->playlist = $data->obj_data->playlist[0];
		$this->pdefault = $this->playlist->default[0];
	  }
	
	  function getPrefixValue($prefixname, $i=0) {
		$defaultprefix = $this->pdefault->$prefixname; $defaultprefix = $defaultprefix[0];
		$soundprefix = $this->playlist->sound[$i]->$prefixname; $soundprefix = $soundprefix[0];
		return ($soundprefix!="") ? $soundprefix : $defaultprefix;  
	  }
	  
	  function getSoundValue($name, $i=0) {
	  	$val = $this->playlist->sound[$i];
		$val = $val->$name;
		$val = $val[0];
		return $val;
	  }
	  
	  function getValue($name, $i=0) {
	  	$prefix = $this->getPrefixValue($name."prefix");
		$value = $this->getSoundValue($name, $i);
		return strip_tags($prefix.$value);
	  }
	  
	  function getUseUrlForDownload($name, $i=0) {
	  	$prefix = $this->getPrefixValue($name);
		$value = $this->getSoundValue($name, $i);
		if ($value!="") { 
			return $value;
		} elseif ($prefix!="")  {
			return $prefix;
		} else {
			return false;
		}
		
		return $prefix.$value;
	  }
	  
	  function getSound($i=0) {
	  	$s=array();
		$s['url'] = $this->getValue("url", $i);
		$s['title'] = $this->getValue("title", $i);
		$s['artist'] = $this->getValue("artist", $i);
		$s['website'] = $this->getValue("website", $i);
		$s['downloadurl'] = $this->getUseUrlForDownload("downloadurl", $i);
		$s['useurlfordownload'] = $this->getUseUrlForDownload("useurlfordownload", $i);
		$s['license'] = $this->getValue("license", $i);
		return $s;
	  }
}


class DisplayPlaylistInfo extends PlaylistInfo {

	function stripAndFormat($chaine, $max=30, $abbr=1) {
		$chaine= strip_tags($chaine);
		$original = $chaine;
		if(strlen($chaine)>=$max){$chaine=substr($chaine,0,$max) . "..." ;} 
		if ($abbr==1) {
			$chaine = "<span class=\"abbr\" title=\"". ereg_replace('"', "'", $original) ."\">".$chaine."</span>";
		}
		return $chaine; 
	}	
	
	function fTitle($title, $n=false) {
		$title = $this->stripAndFormat($title);
		$title = (strlen($title)>0) ? $title : "&nbsp;";
		if (($title!="&nbsp;") && ($n!==false)) {
			$title = "<a href=\"javascript:void(0);\" onClick=\"LoadIndex('".$n."');\">".$title."</a>";
		}
		return $title;
	}

	function fArtist($artist, $website=false) {
		$artist = $this->stripAndFormat($artist);
		$artist = (strlen($artist)>0) ? $artist : "&nbsp;";// die("$artist"); 
		if ($website!=false) {
			$artist = "<a href=\"".$website."\" target=\"_blank\">$artist</a>";
		}
		return $artist;
	}	
	
	function fDownload($downloadurl, $useurlfordownload, $url, $item=">-<") {
		
		if (!empty($downloadurl)) {
			$out = "<a href=\"".$downloadurl."\" title=\"Clic-droit => Enregistrer sous...\">$item</a>";
		} else if ($useurlfordownload=="true") {
			$out = "<a href=\"".$url."\" title=\"Clic-droit => Enregistrer sous...\">$item</a>";
		} else {
			$out = "&nbsp;";
		}
		return $out;
	}

	function fLicense($license) {
		$license = $this->stripAndFormat($license);
		$license = (strlen($license)>0) ? $license : "&nbsp;";
		return $license;
	}
	
	function formatSound($i=0) {
		$sound = parent::getSound($i);
		$s['url'] = $this->stripAndFormat($sound['url']);
		$s['title'] = $this->fTitle($sound['title']);
		$s['titlelinked'] = $this->fTitle($sound['title'],$i);
		$s['artist'] = $this->fArtist($sound['artist'], $sound['website']);
		//$s['website'] = $this->stripAndFormat($sound['website']);
		$s['download'] = $this->fDownload($sound['downloadurl'], $sound['useurlfordownload'], $sound['url']);
		$s['license'] = $this->fLicense($sound['license']);
		return $s;
	}
	
	function displaySound() {
		$rsc = $this->playlist->sound;
		//print_r($rsc);
				
		$html = "";		
		if (count($rsc)>0) {	// il y a une ou plusieurs ressources
			for ($i=0; $i<count($rsc); $i++) {	// Pour chaque ressource...
			$num = ($i<10) ? "0".$i+1 : $i+1;
				$s = $this->formatSound($i);
				
				$html .= "  <tr id=\"file_".$i."\"> \n";
				$html .= "    <td>". $num ."</td> \n"; // num
				$html .= "    <td>". $s['titlelinked'] ."</td> \n"; // titre
				$html .= "    <td class=\"centered\">". $s['artist']."</td> \n"; // artiste
				//$html .= "    <td>". $s['wesite'] ."</td> \n"; // web
				$html .= "    <td class=\"centered\">". $s['download'] ."</td> \n"; // download
				$html .= "    <td class=\"centered\">". $s['license'] ."</td> \n"; // licence
				$html .= "  </tr> \n";
			}
		}
		echo $html;
	}

}

class xml_2_m3u extends PlaylistInfo {

	function M3UGeneration() {
		$rsc = $this->playlist->sound;
		//print_r($rsc);
		$out = "#EXTM3U\n";		
		if (count($rsc)>0) {	// il y a un ou plusieurs sons
			for ($i=0; $i<count($rsc); $i++) {	// pour chaque son...
				$sound = parent::getSound($i);
				$out .= "#EXTINF:-1,";
				$out .= $sound["artist"]." - ".$sound["title"]." - ".$sound["license"]."\n";
				$out .= $sound["url"]."\n";
			}
		}
		return $out;
	}	
}


?>
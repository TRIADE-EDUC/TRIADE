/************  Start XML functions *****************/
function LaunchPlaylist(xmlfile) {
	XML_var = new XML();
	XML_var.load(xmlfile);
	XML_var.onLoad = parseXML;
	if (playStatusWriteable==true) { playStatus = l_loadingXML; }
}

function parseXML() {
    mainTag = new XML;
    elementTag = new XML;
    soundList = new Array;
    elementList = new Array;
	found_xml = (this.loaded!=true) ? false : true;
    mainTag = this.firstChild.nextSibling;
    soundList = mainTag.childNodes;
	my_playlist = new Array;
	
	for(n=0;n<=allowedPrefixes.length;n++) {
		my_prefixes[allowedPrefixes[n]] = "";
	}
	var k = 0;	
		for(i=0;i<=soundList.length;i++) {
		my_soundAttr = new Array;
		elementList = soundList[i].childNodes;
			for(j=0;j<=elementList.length;j++) 	{
				elementTag = elementList[j];
				my_soundAttr[elementTag.nodeName] = elementTag.firstChild.nodeValue;
			}
			for(n=0;n<=allowedPrefixes.length;n++) {
				if (my_soundAttr[allowedPrefixes[n]]!=null) { my_prefixes[allowedPrefixes[n]] = my_soundAttr[allowedPrefixes[n]]; }
			}
			if (my_soundAttr["url"]!=null) { my_playlist[k++] = my_soundAttr; }
		}
		my_playlist.trace();
		my_prefixes.trace();
		my_playlistSoundsCount = k-1;
		my_trace("count:"+my_playlistSoundsCount);
		if (my_playlistSoundsCount==-1) { 
			if (found_xml) { 
				setTempText(l_xmlFileNotValid);
			} else {
				setTempText(l_xmlFileNotFound);
			}
				//stop();
				//onEnterFrame = null;	// stop tracking playstate
				//stopSnd();
		}
		if (startIndex!=false) {
			firstIndex=Number(startIndex);
			startIndex=false;
		} else if ((playRandom==true) && (startIndex==false)) {
			firstIndex = Math.round(Math.random()*my_playlistSoundsCount);
		} else {
			firstIndex = 0;
		}
		//firstIndex = (playRandom==false) ? soundIndex : Math.floor(Math.random()*my_playlistSoundsCount);
		//my_trace("firstindex="+firstIndex);
		if (my_playlistSoundsCount!=-1) {
			playFileFromXML(firstIndex);
		}
		
		SetPlaylist(my_prefixes, my_playlist);

		
}


function SetPlaylist(my_prefixes, my_playlist) {
//my_trace(my_playlist)
	liste.removeAll();
	for (i=0; i<=my_playlistSoundsCount; i++) {
		ob = my_playlist[i];
		playlist_content = i+1 add "-" add my_prefixes["artistPrefix"] add ob["artist"] add ":" add my_prefixes["titlePrefix"]add ob["title"];
		liste.addItem(playlist_content, ob["url"]);
	}
	liste.setSelectedIndex(0);
}

function playFileFromXML(index2play) {
	soundIndex=index2play;
	ob=my_playlist[index2play];
	my_obPrefixes= new Array;
	my_obPrefixes=my_prefixes;
	license = "";
	my_trace("index2play: "+index2play)
	for(n=0;n<=allowedPrefixes.length;n++) {
		if (ob[allowedPrefixes[n]]=="0") { my_obPrefixes[allowedPrefixes[n]] = ""; }
	}
	for(n=0;n<=allowedValues.length;n++) {
		if (ob[allowedValues[n]]==null) { ob[allowedValues[n]] = ""; }
	}	
	if ((ob["license"]!="") || (my_obPrefixes["licenseprefix"]!="")) {
		license = my_obPrefixes["licenseprefix"] add ob["license"];
	}
	if (license=="") { license="Inconnue"; }
	license = "Licence:" add license;
	moreInfos = license;
	soundTitle = displayTrackNb(index2play) add ob["title"]; 
	soundAuthor = my_obPrefixes["artistprefix"] add ob["artist"];
	my_bitrate = my_obPrefixes["bitrateprefix"] add ob["bitrate"];
	websiteUrl = my_obPrefixes["websiteprefix"] add ob["website"];
	url = my_obPrefixes["urlprefix"] add ob["url"]; 
	_root.liste.setSelectedIndex(soundIndex);
	if ((ob["useurlfordownload"]=="true") || ((my_obPrefixes["useurlfordownload"]=="true") && (ob["useurlfordownload"]=="")) ) {
		soundDownloadUrl = url;
	} else {
		soundDownloadUrl = my_obPrefixes["downloadurlprefix"] add ob["downloadurl"];
	}
	websiteBtn.enabled = (websiteUrl.length<1) ? false : true;
	DownloadBtn.enabled = (websiteBtn.length<1) ? false : true;
	LoadIndex = false;
}

function PlayNextSound() {
	my_trace("PlayRandom ============= " + playRandom);
	if (playRandom==true) {
		randomIndexTemp = randomIndex();
		my_trace("playNextSound:"+soundIndex+"->"+randomIndexTemp+"->"+my_playlistSoundsCount);
		playFileFromXML(randomIndexTemp);
	} else if ((soundIndex<my_playlistSoundsCount) && (playRandom!=true)) {
		soundIndex++;
		playFileFromXML(soundIndex);
	} else {
		playStatus = l_complete;
		onEnterFrame = null;
		stopSnd;
	}
}

function displayTrackNb(n) {
	TrackNb = (Number(n)+1) add "/" add (my_playlistSoundsCount+1) add "-";
	return TrackNb;
}

function updateIndex(n) {
	i=Number(n);
	var indexMax = my_playlistSoundsCount;
	var indexMin = 0;
	previousIndex = soundIndex;
	//my_trace("soundIndex0="+soundIndex);
	soundIndex += i;
	//my_trace("soundIndex1="+soundIndex);
	soundIndex = Math.max(indexMin, soundIndex);
	soundIndex = Math.min(indexMax, soundIndex);
	my_trace("previousIndex="+previousIndex+"-soundIndex="+soundIndex+"-"+my_playlist[soundIndex]["url"]);
	if (previousIndex!=soundIndex) { playFileFromXML(soundIndex); }
}

function randomIndex() {
	randomIndexNb = Math.round(Math.random()*(my_playlistSoundsCount));
	my_trace("randomIndex="+randomIndexNb);
	if (soundIndex==randomIndexNb) { randomIndex(); } // avoid to play a sound twice in succesion
	soundIndex = randomIndexNb;
	return soundIndex;
}
/************  End XML functions  *****************/

/*
function Playlist() {
	liste.removeAll();
	for (i=1; i<=Num_list; i++) {
		My_List_Num = new XML();
		My_List_Num = _root.read_XML.firstChild.firstChild;
		while (My_List_Num != null) {
			if (My_List_Num.attributes["id"] == Number(Rec_Debut)+i) {
				nom[i] = My_List_Num.attributes["nom"];
				fichier[i] = My_List_Num.attributes["fichier"];
				liste.addItem(nom[i], fichier[i]);
			}
			My_List_Num = My_List_Num.nextSibling;
		}
	}
	liste.setSelectedIndex(0);
}Playlist();
*/

function initVal() {
	// variables initialisations
	_soundbuftime = my_defaultSBT;
	isPlaying = false;
	isLoading = true;
	mcInfoWriteable = true;
	playNb=0; trace ("PlayNb=="+playNb);
	my_fileType = GetFileType();
	//trace ("ext2:"+my_fileType);
	// store playstate in global variable
	my_LoadedBarColor = new Color(mc2);
	my_LoadedBarColor.setRGB(my_color2);	// set Loaded bar (mc) color
	my_BufferBarColor = new Color(mc);
	my_BufferBarColor.setRGB(my_color4);	// set Buffer bar (mc2) color
	playBtn._visible = true;
	pauseBtn._visible = false;
	//options buttons display (postload)
	loopBtns._visible = SDV(loopBtnVisible, true);
	randomBtns._visible = SDV(randomBtnVisible, true);
	infosBtn._visible = SDV(infosBtnVisible, true);
	voteBtn._visible = SDV(voteBtnVisible, true);
	downloadBtn._visible = SDV(downloadBtnVisible, true);
	websiteBtn._visible = SDV(websiteBtnVisible, true);
	copyBtnBtn._visible = SDV(copyBtnVisible, false);
	
	if (mode!="playlist") {
		nextBtn._visible = false;
		previousBtn._visible = false;
	} else {
		nextBtn._visible = true;
		previousBtn._visible = true;		
	}
	// sound "copyrights"
	soundTitleText = SDV(soundTitle, "");
	soundAuthorText = SDV(soundAuthor, "");
	soundAuthorScrollee.SetText(soundAuthor);
	soundTitleScrollee.SetText(soundTitle);
	//soundDownloadUrl = SDV(soundDownloadUrl, "http://www.framasoft.net");
	// buttons display
	BandwithStatusButton._visible = false;
	BandwithStatusButtonColor.setRGB(0x000000);
	if ((soundDownloadUrl==false) || (soundDownloadUrl=="")) {
		downloadBtn.enabled = false;
		downloadBtn._alpha = 30;
	} else {
		downloadBtn.enabled = true;
		downloadBtn._alpha = 100;		
	}
	if ((websiteUrl==false) || (websiteUrl=="")) {
		websiteBtn.enabled = false;
		websiteBtn._alpha = 30;		
	} else {
		websiteBtn.enabled = true;
		websiteBtn._alpha = 100;		
	}
	
	//downloadBtn.enabled = ((soundDownloadUrl==false) || (soundDownloadUrl=="")) ? false : true;
	//websiteBtn.enabled = ((websiteUrl==false) || (websiteUrl=="")) ? false : true;
	stopSndExt = 0;
	//loopBtn._visible = (my_show_loop == true) ? true : false;
	my_loopColor.setRGB((my_loop == true) ? my_color9 : my_color4);
	//randomBtns._visible = (my_show_randomBtns == true) ? true : false;
	my_randomBtnColor.setRGB((playRandom == true) ? my_color9 : my_color4);
	OldGBL = 0;
	// old getBytesLoaded
	my_position = 0;
	isPaused = false;
	prevPos = 0;
	nbTestsBandwith = 0;
	nbTestsBandwithBTD = 0;
	NbTestsRealtime = 0;
	sumBandwith = 0;
	sumBandwithBTD = 0;
	my_position = 0;
	my_alreadyLoadedDuration = 0;
	my_alreadyLoadedBytes = 0;
	my_preloaded = 0;
	PlayStatus = "...";
	LOAD_TIME = 0;
	START_TIME = 0;
	START_DELAY = 0;
	my_size = 0;
	my_duration = 0;
	my_loaded = 0;
	my_buffer = 0;
	my_position2 = 0;
	// %age de position
	my_loaded2 = 0;
	my_loaded3 = 0;
	my_buffer_size = 0;
	my_duration2 = 0;
	// on determine la durée en fonction du poids et de l'encodage
	my_timer = 0;
	my_bufferTimer = 0;
	mc._xscale = my_position2;
	mc2._xscale = my_loaded2;
	//trace("mc2._xscale:"+mc2._xscale);
}
function createSoundObject() {
	// create sound object
	success = false;
	soundContainer = new Sound(this);
	// define sound object
	soundContainer.onSoundComplete = updateStatus;
	// change state at song end
	soundContainer.onLoad = function(success) {
		if (!success) {
			stopSnd();
			playStatus = l_notFound;
			// file not found 
		}
	};
	return;
}
function updateStatus() {
	// sound completely listened
	isPlaying = false;
	isLoading = false;
	clearInterval(my_test);
	ShowPlayBtn(1);
	if (stopSndExt==1) { StopSnd(); }
	if (my_loop == true) {
		DoLoop();
	} else if (mode=="playlist") {
		playStatus = l_complete;
		onEnterFrame = null;
		delete soundContainer;
		PlayNextSound();
	} else {
		playStatus = l_complete;
		onEnterFrame = null;
		if (ActivateFSCommand == true) {
			fscommand("afAMPeventSoundComplete", sndurl);
		}
	}
}
function DoLoop() {
	// sound completely listened
	playStatus = l_loop;
	if (START_DELAY == 0) {
		START_DELAY = new Date().getTime();
	}
	my_trace("START_DELAY="+ START_DELAY)
	tmpdelay=0;
    while ((new Date().getTime()-START_DELAY)<my_loop_delay) {
		playStatus = tmpdelay++;
	}
	my_trace("lance:"+autolaunch+"-url:"+sndurl+"-LoadIndex:"+soundIndex);
	autolaunch = (autolaunch == false) ? "disabled" : autolaunch;
	START_DELAY = 0;
	url = sndurl;
	if (mode=="playlist") {
		LoadIndex=soundIndex;
	}
}

function updateBandwithStatus(realtimekbps) {
	if (isNaN(realtimekbps)) {
		realtimekbps = 0;
	}	// Still in realtimeBandiwth computation
	if (my_loaded2>=99.9) {
		realtimekbps = 0;
	}
	if (BandwithStatusDisplayType == "color") {
		bufferStatus = "";
		BandwithStatusButton._visible = true;
		if ((realtimekbps<=my_bitrate) && (my_loaded2<98)) {
			BSColor = my_color5;	//bad scenario : bandwith insuficient (red)
		} else if ((realtimekbps>my_bitrate) && (realtimekbps<=my_bitrate+5) && (my_loaded2<98)) {
			BSColor = my_color6;	// bandwith near bitrate (orange)
		} else {
			BSColor = my_color7;	//kbps is sufficient : green
		}
		BandwithStatusButtonColor.setRGB(BSColor);
	} else if (BandwithStatusDisplayType == "value") {
		BandwithStatusButton._visible = false;
		if (realtimekbps == 0) {
			bufferStatus = "...";
		} else {
			bufferStatus = Number(Math.round(realtimekbps)) add "Kb/s";
		}
	} else {
		bufferStatus = "";
	}
}
function MinutesSeconds(s) {
	// convert seconds to human readable mm:ss
	minutes = Math.floor(s/60);
	seconds = Math.floor(s-(minutes*60));
	if (minutes<10) {
		minutes = "0" add minutes;
	}
	if (seconds<10) {
		seconds = "0" add seconds;
	}
	my_conv = minutes add ":" add seconds;
	return my_conv;
}
function showPlayState() {
	// evrythings that happens during loading or playing
	my_timer = Number((getTimer()-START_TIME)/1000);
	// mark start time
	var sndPos = Number(soundContainer.position);
	if (stopSndExt==1) { 
		playStatus = l_complete;
		stopSnd();
		
	}
	var fileLoaded = (Number(soundContainer.getBytesTotal()>0) && (Number(soundContainer.getBytesLoaded()) == Number(soundContainer.getBytesTotal())));
	if (sndPos<=prevPos && !fileLoaded) {
		// sound position had'nt change = we're loading
		playStatus = l_loading;
		if (isLoading == false) {
			// usefull to know if file is (re)loading after a buffer insuficience
			LOAD_TIME = getTimer();
			isLoading = true;
		} else {
			my_bufferTimer = Number((getTimer()-LOAD_TIME)/1000);
			// buffer timer must be based on time since last play=>load state
			if (nbTestsBandwith>MinimumTestsBandwith) {
				// take some time before displaying buffer waiting %
				bufferStatus = (showBufferStatus) ? ShowBufferLeft(_soundbuftime, my_bufferTimer) add "%" : "";
				p_bufferTimer = my_bufferTimer;
			} else {
				bufferStatus = (showBufferStatus) ? "..." : "";
				// during a few seconds, we can't count on this value
			}
		}
	} else {
		// playing
		playStatus = l_playing;
		isLoading = false;
		RealTimekbps = (RealTimeBandwith != "computing") ? kbps : RealTimeBandwith;
		updateBandwithStatus(RealTimekbps);
	}
	// variables actualisation
	my_position = Number(soundContainer.position/1000);	// position of playtime (in s)
	my_size = Math.round(Number(soundContainer.getBytesTotal())/1024);	// size of the file
	my_duration = Math.round((soundContainer.duration)/1000);	// length of file. Beware : we can't count on this value before the file is completely loaded !!
	my_loaded = (Number(soundContainer.getBytesLoaded()/1024)); 	// how many bytes are loaded ?
	my_duration2 = Number(soundContainer.getBytesTotal()/(my_bitrate/8)/1000);	// we determine sound length based on his bitrate and his size
	my_position2 = Number(((Math.min(my_position, my_duration2)/my_duration2)*100));	// position %age
	my_loaded2 = ((Number(soundContainer.getBytesLoaded())/Number(soundContainer.getBytesTotal()))*100); 	// loaded %age
	my_loaded3 = Math.ceil(Number(my_loaded/(my_bitrate/8))); 	// loaded size
	my_buffer = _soundbuftime;
	my_buffer_size = Math.ceil(my_buffer*(my_bitrate/8)); 	// ko to load
	soundContainer.setVolume(volume);	// keep volume between files played
	if ((my_duration2>0) && (my_position>=(my_duration2-0.01))) {
		updateStatus();
	}
	// we're at the end of the file, mark sound complete
	// important variables
	mc._xscale = my_position2;
	mc2._xscale = my_loaded2;
	volumeStatus = Math.round(volume) add "%";
	p_soundbuftime = "SoundBufTime = "+_soundbuftime;
	TimeStatus = MinutesSeconds(my_position) add "/" add MinutesSeconds(my_duration2); 	// display time position and total play time
	prevPos = sndPos;	// store for next loop
	moreInfos = license add " | Bitrate:" add my_bitrate add " | Size:" add my_size add "KB";

}
/************  Buttons Actions   *****************/
function ShowPlayBtn(stateBtn) {
	// Show or hide play/pause buttons
	if ((stateBtn == null) || (stateBtn == undefined)) {
		stateBtn = 0;
	}
	if (stateBtn == 0) {		// hide play & show Pause
		playBtn._visible = false;
		pauseBtn._visible = true;
	} else if (stateBtn == 1) {		// hide pause & show play
		playBtn._visible = true;
		pauseBtn._visible = false;
	}
}
function playSnd(url2play) {
	// check playstate
	if (my_once == false) {
		return;
	}
	if ((my_limit!=0) && (playNb>=my_limit)) { 
	  setTempText(l_limit+my_limit+l_limit2);
	  return; 
	}
	ShowPlayBtn(0);
	//my_debug(url);
	my_debug("sndurl:"+sndurl+" \n url:"+url+"\n url2play:"+url2play+"\n prevUrl:"+prevUrl+"\n relaunch:"+relaunch);
	// check if same or new sound
	if ((prevUrl != url2play)) {		// new sound
		soundContainer.loadSound(url2play, true);		// sound automatically plays after loading
		prevUrl = url2play;
		START_TIME = getTimer();
		LOAD_TIME = getTimer();
		my_test = setInterval(setBuffer, my_BTD);
		relaunch = false;
		if (ActivateFSCommand == true) {
			fscommand("afAMPeventSoundLaunch", sndurl);
		}
	} else {
		if ((Number(soundContainer.duration)>0) && (soundContainer.position>(soundContainer.duration-10))) {			// same sound
			my_debug("1");
			my_restartPosition = 0;			// replay from beginning
		}
		if (isPaused == true) {			// sound was paused
			my_debug("2");
			my_restartPosition = (soundContainer.position/1000);	// last paused
			isPaused == false;
		}
		if ((prevUrl == url2play) && (url == sndurl)) {
			my_debug("3"+soundContainer.position);
			stopSnd;
			playSnd;
		}
		soundContainer.start(my_restartPosition);
	}
	onEnterFrame = showPlayState;	// start tracking playstate
	if (autolaunch=="wait") { pauseSnd(); autolaunch=true; }
}
// stop both playback and download
function stopSnd() {
	if (my_once == false) {
		return;
	}
	soundContainer.stop();	// stop playback
	delete soundContainer;	// stop download
	createSoundObject();	// create a new sound object
	isPlaying = false;
	prevUrl = "";	// clear for next load
	onEnterFrame = null;	// stop tracking playstate
	mcInfo.onEnterFrame = null;
	playStatus = l_stopped;
	clearInterval(my_test);
	nbTestsBandwith = 0;
	ShowPlayBtn(1);	// show play btn & hide pause
	playNb++;
}
// pause sound
function pauseSnd() {
	if (my_once == false) {
		return;
	}
	if (isLoading == true) {
		return;
	}
	isPaused = true;
	soundContainer.stop();
	isPlaying = false;
	onEnterFrame = null;	// stop tracking playstate
	playStatus = l_paused;
	ShowPlayBtn(1);	// hide play btn & show pause
}
function setVol(my_Vol) {
	volume = soundContainer.getVolume();
	volume = volume+my_Vol;
	if (volume>100) {
		volume = 100;
	}
	if (volume<1) {
		volume = 0;
	}
	soundContainer.setVolume(volume);
}

function openM3u() {
	if (sndUrl!=0) {
		to_open	= (mode=="playlist") ? lastPlaylist : sndUrl;
		m3u_script_url = thisPath add xml2m3u_scriptname add "?xmlloaded=" add to_open;
		trace(m3u_script_url);
		getURL(m3u_script_url, "_self");
	}
}
/************  End Buttons Actions   *****************/

function GetThisPath(myurl) {
//return path of an url (= all url minus what's next to the last "/")
	var thisPath = "";
	var myPathArray = myurl.split("/"); 
	//trace("La longueur est de " + myPathArray.length) 
	for(var i=0; i<myPathArray.length-1; i++){ 
		//trace("L'élément ["+i+"]=" + myPathArray[i]); 
		thisPath += myPathArray[i] add "/";
	} 
	//trace("thisPath ="+thisPath+");
	return thisPath;
}

/************  Start Buffer Actions  *****************/
function setBuffer() {
	if ((nbTestsBandwith == 0) && (soundContainer.getBytesLoaded()>=5000)) { //bad news : we're reloading, so we recalculate a new buffer
		my_trace("we're reloading:"+soundContainer.getBytesLoaded());
		my_timer = 0;
		my_bufferTimer = 0;
		RealTimeArray = new array();
		NbTestsRealtime = 0;
		if (my_alreadyLoadedBytes == 0) {
			my_alreadyLoadedBytes = soundContainer.getBytesLoaded();
			my_alreadyLoadedDuration = my_loaded3;
		}
	}
	BytesLoaded = soundContainer.getBytesLoaded()-my_alreadyLoadedBytes;
	trace("BytesLoaded:"+BytesLoaded);
	++nbTestsBandwith;
	// computation of avergage bandwith for the last my_BTD seconds
	if (nbTestsBandwith<=MinimumTestsBandwith) {		// New start, wait 3 BTD
		AverageBandwithBTD = 10000;	// be sure to this high engough to avoid the player takes it as a real average value
		p_OldGbl = "--";
	} else {
		if (nbTestsBandwith == MinimumTestsBandwith+1) {
			sumBandwithBTD = 0;
		}
		my_loadedInOneBTD = BytesLoaded-OldGbl;
		my_bandwithBTD = getkbps(my_BTD/1000, my_loadedInOneBTD);
		sumBandwithBTD += my_bandwithBTD;
		AverageBandwithBTD = (sumBandwithBTD/(nbTestsBandwith-MinimumTestsBandwith));
		RealTimeBandwith = Number(GetRealTimeBandwith(my_loadedInOneBTD));
		p_OldGbl = RealTimeBandwith+"kb/s for the last "+my_BTD*10+"ms";
	}
	OldGBL = BytesLoaded;
	if (fileLoaded == true) {		// file completely loaded
		clearInterval(my_test);		// no need to know bandwith anymore
	} else {		// still loading
		// computation of avergage bandwith since file launch 
		my_bandwith = getkbps(my_timer, BytesLoaded);
		p_actualBandwith = Math.round(my_bandwith, 2)+"kb/s in "+my_timer+"s";
		trace("sumBandwith:"+sumBandwith+"-my_bandwith:"+my_bandwith);
		sumBandwith = sumBandwith+Number(my_bandwith);
		AverageBandwith = Number(getAverageBandwith(nbTestsBandwith, sumBandwith));
		trace("AverageBandwith:"+AverageBandwith+"-nbTestsBandwith:"+nbTestsBandwith+"-sumBandwith:"+sumBandwith+"-my_timer:"+my_timer+"-BytesLoaded:"+BytesLoaded+"-my_bandwith:"+my_bandwith);
		p_averageBandwith = "BP : "+AverageBandwith;
	}
	setSoundBufTime(AverageBandwith, AverageBandwithBTD, RealTimeBandwith);	// determine buffer
}
function getAverageBandwith(nbTests, Bandwith) {
	// just compute an average
	if ((nbTests == 0) || (Bandwith == 0)) {
		return 0;
	}
	AverageC = (Bandwith/nbTests);
	return AverageC;
}
function GetRealTimeBandwith(bytes) {
	if (NbTestsRealtime == 10) {
		NbTestsRealtime = 0;
	}
	RealTimeArray[NbTestsRealtime] = bytes;
	if (RealTimeArray.length == 10) {
		sum = 0;
		for (i=0; i<10; i++) {
			sum += RealTimeArray[i];
		}
		averageRT = sum/10;
		NbTestsRealTime++;
		averageRTB = getkbps(my_BTD/1000, averageRT);
		return averageRTB;
	} else {
		NbTestsRealtime++;
		return "computing";
	}
}
function setSoundBufTime(average, averageBTD, RealTimeBandwith) {
	// determine the amount of buffer needed
	trace("average:"+average+"- averageBTD:"+averageBTD+"- RealTimeBandwith:"+RealTimeBandwith+"-");
	if (my_duration2 != 0) {
		if (nbTestsBandwith>MinimumTestsBandwith) {
			kbps = (RealTimeBandwith == "computing") ? Number(Math.min(average, averageBTD)) : Number(Math.min(Math.min(average, averageBTD), Math.min(averageBTD, RealTimeBandwith)));			// get the lower value of average, averageBTD and realTimeBandwith
			//my_trace("kps="+Number(Math.min(average, averageBTD)));
		} else {
			kbps = 1;
		}
		if (kbps<0) {
			kbps = 1;
		}
		p_kbps = Math.round(kbps, 2);
		my_soundbuftime = Math.ceil((1-(kbps/my_bitrate))*(my_duration2-my_alreadyLoadedDuration));
		trace("kbps="+kbps+"-my_bitrate="+my_bitrate+"-my_duration2="+my_duration2+"-my_alreadyLoadedDuration="+my_alreadyLoadedDuration+"-my_sbft="+my_soundbuftime+"");
		if (((kbps<=my_bitrate+5) || (kbps<=my_bitrate-5)) && (my_minDialUpSBT>=my_soundbuftime)) {
			//if the bandwith bitrate is too close from sound file bitrate... 
			my_soundbuftime = my_minDialUpSBT;
			// ... we set a minimum 
		}
		p_soundbuftime = "SoundBufTime = "+my_soundbuftime;
		if (nbTestsBandwith>=MinimumTestsBandwith) {
			my_soundbuftime = my_soundbuftime*my_security;
			// experienced showed me that we need a security margin
			my_soundbuftime = Math.min(my_soundbuftime, my_duration2);
			// no need to buffer more than the sound duration
		}
		trace("my_soundbuftime:"+my_soundbuftime);
		if (my_soundbuftime<1) {
			my_soundbuftime = 1;
		}
		// if bandwith is very higher than bitrate no need to buffer
		if ((my_loaded2>99) && (kbps<1)) {
			my_soundbuftime = my_minDialUpSBT;
		}
	} else {
		// sometimes my_duration2 is incorrectly set to 0, so we set a minimum sbt
		my_soundbuftime = my_minDialUpSBT;
	}
	_soundbuftime = Math.ceil(my_soundbuftime);
	trace(_soundbuftime+"\n ----------------- \n");
	p_soundbuftime = "SoundBufTime = "+_soundbuftime;
}
function getkbps(seconds, bytes) {
	// determine bandwith
	if ((seconds == 0) || (bytes == 0)) {
		return 0;
	}
	var totalBits = bytes*8;
	// convert to bits
	var totalKBits = totalBits/1024;
	// convert to kbits
	var kbps = Math.round(totalKBits/seconds);
	// kbps
	return (kbps);
}
function ShowBufferLeft(sbf, pos) {
	// return percentage of buffer loaded
	percentBuf = Math.ceil((pos/sbf)*100);
	if (percentBuf>100) {
		percentBuf = 100;
	}
	if (percentBuf<0) {
		percentBuf = 0;
	}
	return percentBuf;
}
/************  End Buffer Actions  *****************/


/*********  Start Loading sounds Actions  **********/
function GetFileType() {
	// get file type (.mp3 or .xml) and set it to my_fileType
	my_trace(url+"-length:"+url.length);
	if (url.length>3) {
		my_ext = url.substr(-3);
		my_trace("my_ext:"+my_ext);
		if (my_ext=="mp3") { return "mp3"; }
		if (my_ext=="xml") { return "xml"; }
	}
	return false;
}
function setTempText(texte) {
	texte = "[" + texte + "]";
	if (mcInfo.soundAuhtorText!=texte) { soundAuthorScrollee.SetText(texte);}
	mcInfoWriteable = false;
}

function freePlayStatus() {
	freeText = (soundAuthor!="") ? soundAuthor : "";
	soundAuthorScrollee.SetText(freeText);
	mcInfoWriteable = true;
}

function getOptionStatus(option) {
	if ((option==true) || (option==1)) {
		return " (" add l_now add l_disabled add ")";
	} else {
		return " (" add l_now add l_enabled add ")";
	}
}

function switchLoop() {
	if (my_loop == true) {
		my_loop = false; //no more loop
		my_loopColor.setRGB(my_color4);	// reset button (disabled)
	} else {
		my_loop = true;	//no more loop
		my_loopColor.setRGB(my_color3);	// button enabled	
	}
}

function switchRandom() {
	if (playRandom == true) {
		playRandom = false; //no more loop
		my_randomBtnColor.setRGB(my_color4);	// reset button (disabled)
	} else {
		playRandom = true;	//no more loop
		my_randomBtnColor.setRGB(my_color3);	// button enabled	
	}
}

function LoadSnd(snd) {
	// just for fullplayer: load a sound when a flash button is pressed
	url = snd;
}
function my_debug(mytext) {
	// to debug on web page instead of trace function
	my_debugtxt = my_debugtxt add "\n" add mytext;
}
function launch() {
	// executed every my_launchDelay ms to check if user wants to play another sound
	if (url != 0) {
		// on a une url différente, il faut jouer le son
		my_once = true;
		stopSnd();
		onEnterFrame = null;		// stop tracking playstate
		createSoundObject();		// create sound object
		initVal();		// initialize variables
		sndurl = url;		// transfer sound to play to sndurl
		if ((autolaunch != false) && (autolaunch!="wait") && (my_fileType=="mp3")) {
			playSnd(sndurl);
		} else if ((autolaunch != false) && (my_fileType=="xml")) {
			mode = "playlist";
			lastPlaylist=sndurl;
			soundIndex = 0;
			LaunchPlaylist(sndurl);
		}
		url = 0;		// reset url to 0
		autolaunch = (autolaunch == "disabled") ? false : autolaunch;
	}
	if (LoadIndex!==false) {
		playFileFromXML(Number(LoadIndex));
		
	}
}
my_launch = setInterval(launch, my_launchDelay); // check if a new sound was required by user
/*********  End Loading sounds Actions  **********/
// assign button events
playBtn.onRelease = function() {
	playSnd(sndurl);
};
pauseBtn.onRelease = pauseSnd;
stopBtn.onRelease = stopSnd;

loopBtns.onRelease = switchLoop;
loopBtns.onRollOver = function() {  setTempText(l_loop add getOptionStatus(my_loop)) }
loopBtns.onRollOut = function() { freePlayStatus(); }

randomBtns.onRelease = switchRandom;
randomBtns.onRollOver = function() {  setTempText(l_random add getOptionStatus(playRandom)); }
randomBtns.onRollOut = function() { freePlayStatus(); }

nextBtn.onRelease = function() { updateIndex(1); }
nextBtn.onRollOver = function() {  setTempText(l_nextTrack); }
nextBtn.onRollOut = function() { freePlayStatus(); }

previousBtn.onRelease = function() { updateIndex(-1); }
previousBtn.onRollOver = function() {  setTempText(l_previousTrack); }
previousBtn.onRollOut = function() { freePlayStatus(); }

volUpBtn.onRelease = function() { setVol(my_volStep); };
volDownBtn.onRelease = function() { setVol(-my_volStep); };

copyBtn.onRelease = function() { getURL("http://pyg.keonox.com/flashmp3player/", "_blank", "POST"); }

voteBtn.onRelease = function() { getURL("http://pyg.keonox.com/flashmp3player/afampvote/", "_blank", "POST"); }
voteBtn.onRollOver = function() {  setTempText(l_vote); }
voteBtn.onRollOut = function() { freePlayStatus(); }

downloadBtn.onRelease = function() { getURL(soundDownloadUrl, "_blank"); };
downloadBtn.onRollOver = function() {  setTempText(l_download); }
downloadBtn.onRollOut = function() { freePlayStatus(); }

m3uBtn.onRelease = function() { OpenM3u(); };
m3uBtn.onRollOver = function() {  setTempText(l_m3u); }
m3uBtn.onRollOut = function() { freePlayStatus(); }

infosBtn.onRelease = function() {  setTempText(moreInfos); }
infosBtn.onRollOver = function() {  setTempText(l_license); }
infosBtn.onRollOut = function() { freePlayStatus(); }

websiteBtn.onRelease = function() { getURL(websiteUrl, "_blank"); };
websiteBtn.onRollOver = function() {  setTempText(l_website); }
websiteBtn.onRollOut = function() { freePlayStatus(); }

soundClip.onRollOver = function() {  setTempText(l_sound); }
soundClip.onRollOut = function() { freePlayStatus(); }

stop();// prevent timeline looping
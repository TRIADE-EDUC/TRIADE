/****************************************************************
afAMP : Another Flash MP3 player
version 2.4 (FramaPlayer) - 20/04/2005
******************************************************************
Author: Pierre-Yves Gosset
Usage, contact, credits & license: http://framaplayer.keonox.com/
****************************************************************/
function SDV(varName, defaultValue) {
	// set a variable default value. Usefull if webmaster want to override value using <param> tag
	if (varName == "true") {
		varName = true;
	}
	if (varName == "false") {
		varName = false;
	}
	return (varName != null) ? varName : defaultValue;
}

// Player Version
FramaPlayerVersion = SDV(FramaPlayerVersion, "2.3.2");
// default bitrate of .mp3 file (may be overriden by parameter)
my_bitrate = SDV(my_bitrate, 128);
// minimum sound buffer time (except if file already fully loaded)
// must be high enough to give time and get realistic average bandwith
// but it must be low enough to not penalize broadband users that don't need buffering
my_defaultSBT = SDV(my_defaultSBT, 5);
// minimum Dial up connexion buffer time
my_minDialUpSBT = SDV(my_minDialUpSBT, 8);
// Buffer Testing delay in ms
my_BTD = SDV(my_BTD, 500);
// Launch delay in ms (= how often with must check if a new url is given)
my_launchDelay = SDV(my_launchDelay, 500);
// How many bandwith tests shall we do before displaying the buffer
MinimumTestsBandwith = SDV(MinimumTestsBandwith, 6);
// buffer security factor. 1=no security; 1.4 = 40% security
my_security = SDV(my_security, 1.4);
// sound init (you may want to replace it with a default sound loaded by default)
file = SDV(file, 0);
url = file;
// start in playlist or singleFile mode 
mode = SDV(mode, "playlist");
// sound "copyrights"
soundTitleText = SDV(soundTitle, "Titre");
soundAuthorText = SDV(soundAuthor, "Auteur");
m3uUrl = SDV(m3uUrl, "xml2m3u.php?xmlloaded=+url");
soundDownloadUrl = SDV(soundDownloadUrl, false);
websiteUrl = SDV(websiteUrl, false);
// Volume step
my_volStep = SDV(my_volStep, 15);
// flag usefull to know if a sound has been launched
my_once = false;
// default volume percentage
volume = SDV(volume, 60);
// some colors 
my_color1 = SDV(my_color1, 0xF5F5FF);
my_color2 = SDV(my_color2, 0xC4C4FF);
my_color3 = SDV(my_color3, 0xFFFFFF);
my_color4 = SDV(my_color4, 0x707092);
my_color5 = SDV(my_color5, 0xD03524); //red
my_color6 = SDV(my_color6, 0xDFAC42); //orange
my_color7 = SDV(my_color7, 0x25CF8F); //green
my_color8 = SDV(my_color8, 0xBBBBBB); //grey
my_color9 = SDV(my_color9, 0xEAEAFF); //light blue
// allow Background color changing
ChangeBackgroundColor = SDV(ChangeBackgroundColor, true);
my_BackgroundColor = SDV(my_BackgroundColor, my_color1);
my_SetBackgroundColor = new Color(myBackground);
my_SetBackgroundColor.setRGB(my_BackgroundColor);
// create an array for reatime bandwith check
RealTimeArray = new array();
// Bandwith informations displayed next to playing status : "value"=display realtimeBandwith in kb, "color"=display a colored circle, "none"=nothing diplayed
BandwithStatusDisplayType = SDV(BandwithStatusDisplayType, "value");
// Do we want to show buffer % loading ?
showBufferStatus = SDV(showBufferStatus, true);

BandwithStatusButton._visible = SDV(BandwithStatusButtonVisible, false);// hide clip at loading
BandwithStatusButtonColor = new Color(BandwithStatusButton);// create color instance
// loop ability
my_loop = SDV(my_loop, false);// do we want to loop ?
//my_show_loop = SDV(my_show_loop, true);//display loop button ?
my_loop_delay = SDV(my_loop_delay, 1000);// delay to wait for between 2 loops (in miliseconds)
//loopBtn._visible = SDV(loopBtnVisible, my_show_loop);// hide loop button by default
my_loopColor = new Color(loopBtns.loopBtn);
my_loopColor.setRGB((my_loop == true) ? my_color9 : my_color4);
//limit the number of plays ? 0=false
my_limit = SDV(my_limit, 0);
//how many times the file was played ? (init)
playNb=SDV(playNb, 0);

// shuffle playlist ?
playRandom = SDV(playRandom, false); // play tracks in random order
//my_show_randomBtns = SDV(my_show_randomBtns, true);//display shuffle button ?
//randomBtns._visible = SDV(randomBtnsVisible, my_show_randomBtns);// hide shuffle button by default
my_randomBtnColor = new Color(randomBtns.randomBtn);
my_randomBtnColor.setRGB((playRandom == true) ? my_color9 : my_color4);

my_prefixes = new Array;
var allowedPrefixes = ["urlprefix", "artistprefix", "bitrateprefix", "downloadurlprefix", "websiteprefix", "licenseprefix", "useurlfordownload"];
var allowedValues = ["url", "artist", "bitrate", "downloadurl", "website", "license", "useurlfordownload"];
license = SDV(license, "");
moreInfos = SDV(moreInfos, license);
var my_size = 0;
var sndurl;
var lastPlaylist;
startIndex = false;
soundIndex = SDV(soundIndex, false);
stopSndExt=0;
thisUrl = _url;
thisPath = GetThisPath(thisUrl);
xml2m3u_scriptname = SDV(xml2m3u_scriptname, "xml2m3u.php");
var LoadIndex = SDV(LoadIndex, false);

//options buttons display (preload)
loopBtnVisible = SDV(loopBtnVisible, true);
loopBtns._visible = loopBtnVisible;
randomBtnVisible = SDV(randomBtnVisible, true);
randomBtns._visible = randomBtnVisible;
infosBtnVisible = SDV(infosBtnVisible, true);
infosBtn._visible = infosBtnVisible;
voteBtnVisible = SDV(voteBtnVisible, false);
voteBtn._visible = voteBtnVisible;
downloadBtnVisible = SDV(downloadBtnVisible, true);
downloadBtn._visible = downloadBtnVisible;
m3uBtnVisible = SDV(m3uBtnVisible, true);
m3uBtn._visible = m3uBtnVisible;
websiteBtnVisible = SDV(websiteBtnVisible, true);
websiteBtn._visible = websiteBtnVisible;
copyBtnVisible = SDV(copyBtnVisible, false);
copyBtnBtn._visible = copyBtnVisible;

//activate fscommand properties (send launch and start event to JS)
ActivateFSCommand = SDV(ActivateFSCommand, false);
//advanced users only
// Launch sound as soon as loaded
autolaunch = SDV(autolaunch, true);
/**** language ****/
l_loadingXML = SDV(l_loadingXML, "XML...");
l_loading = SDV(l_loading, "Charg.");
l_notFound = SDV(l_notFound, "Introuvable");
l_playing = SDV(l_playing, "Lecture");
l_complete = SDV(l_complete, "Terminé");
l_paused = SDV(l_paused, "Pause");
l_stopped = SDV(l_stopped, "Stop");
l_loop = SDV(l_loop, "Lire la piste en boucle");
l_nextTrack = SDV(l_nextTrack, "Piste suivante");
l_previousTrack = SDV(l_previousTrack, "Piste précédente");
l_random = SDV(l_random, "Lecture aléatoire");
l_download = SDV(l_download, "Télécharger la piste");
l_m3u = SDV(l_m3u, "Générer la playlist au format .m3u (ouverture dans votre lecteur .mp3 habituel)");
l_website = SDV(l_website, "Site web");
l_license = SDV(l_license, "Affiche la licence et autres infos - {FramaPlayer "+ FramaPlayerVersion +" - http://framaplayer.keonox.com/ }");
l_vote = SDV(l_vote, "Voter pour cette piste");
l_now = SDV(l_now, "cliquez pour ");
l_sound = SDV(l_sound, "Contrôle du volume");
l_enabled = SDV(l_enabled, "activer");
l_disabled = SDV(l_disabled, "désactiver");
l_limit = SDV(l_limit, "Le fichier a déjà été joué ");
l_limit2 = SDV(l_limit2, " fois.");
l_xmlFileNotFound = SDV(l_xmlFileNotFound, "Fichier XML introuvable à l'adresse spécifiée (mauvaise addresse ou serveur protégé)");
l_xmlFileNotValid = SDV(l_xmlFileNotValid, "Le fichier XML ne semble pas valide.");
/******************/

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: proxy.js,v 1.8.4.1 2025/04/25 08:25:12 qvarin Exp $

var f_empr_client;
var f_expl_client;
var f_ack_write;
var f_ack_erase;
var f_ack_detect;
var f_ack_write_empr;
var f_ack_antivol_all;
var f_ack_antivol;
var f_ack_read_uid;
var flag_semaphore_rfid = 0;
var flag_semaphore_rfid_read = 0;
var flag_rfid_active = 1;
var rfid_focus_active = 1;
var get_uid_from_cb = new Array();
var pmb_rfid_driver3m;
var bibliotheca;

function afficheErreur() {
	console.error(arguments);
}

function init_rfid_read_cb(empr_client, expl_client) {
	f_empr_client = empr_client;
	f_expl_client = expl_client;

	bibliotheca = new Bibliotheca(url_serveur_rfid);
	if (bibliotheca.isReturnExplPage()) {
		// On a deriver la function pour bibliotheca
		f_expl_client = f_expl_bibli;
	}

	setInterval(read_cb, 1500);
}

function f_expl_bibli(cb_expl, cb_index, cb_count, cb_secur, cb_valid) {

	// Verifier les parties
	var nb_part = 0;
	var real_count_cb = [];
	var error = new Array();

	for (let i = 0; i < cb_expl.length; i++) {
		if (!real_count_cb.includes(cb_expl[i])) {
			nb_part += cb_count[i];
			real_count_cb.push(cb_expl[i])
		}

		if (cb_valid && !cb_valid[cb_expl[i]]) {
			error.push(cb_expl[i]);
		}
	}

	// On calcule le nombre de doc traiter
	var count = 0;
	for (let i = 0; i < memo_cb_rfid_js.length; i++) {
		const index = cb_expl.findIndex(cb => cb == memo_cb_rfid_js[i]);
		if (index != -1) {
			count++;
		}
	}

	// On affiche le nombre de doc traiter
	const sum = cb_expl.length - nb_part;
	if (0 === sum) {
		var node = document.getElementById('indicateur_nb_doc');
		if (node) {
			node.innerHTML = `(${count} / ${real_count_cb.length})`;
		}
	}

	for (let i = 0; i < cb_expl.length; i++) {

		if (memo_cb_rfid_js.includes(cb_expl[i])) {
			// document deja retourner
			continue;
		}

		if (error.includes(cb_expl[i])) {
			alert(formatString("Des &eacute;l&eacute;ments sont manquants pour le code exemplaire :" + cb_expl[i]))
			continue;
		}

		document.getElementById('form_cb_expl').value = cb_expl[i];

		// si post_rfid == , on soumet le premier formulaire
		// ensuite c'est l'utilisateur qui doit soumettre le formulaire
		if (post_rfid) {
			// On retourne le document
			document.saisie_cb_ex.submit();
		}
		break;
	}
}

function timeout() {
	if (!flag_rfid_active_test) {
		flag_rfid_active = 0;
	}
}

function read_cb() {
	if (!rfid_focus_active || (flag_semaphore_rfid || flag_semaphore_rfid_read)) {
		// Aucun focus dans le champs on fait rien
		return false;
	}

	if (flag_disable_antivol) {
		return false;
	}

	flag_rfid_active_test = 0;
	flag_semaphore_rfid_read = 1;

	var multiple = false;
	if (bibliotheca.isReturnExplPage() || bibliotheca.isRFIDReadPage() || bibliotheca.isEditExplPage()) {
		// On est sur la page de retour de document
		// Ou on est sur la page de lecture rfid
		// Ou on est sur la page d'edition ou de creation d'un exemplaire
		multiple = true;
	}

	bibliotheca.getItems(multiple).then(result_read_cb);
}

function result_read_cb(result) {
	if (result && 0 !== Object.keys(result).length) {

		var cb_expl = new Array();
		var cb_index = new Array();
		var cb_count = new Array();
		var cb_secur = new Array();
		var cb_valid = new Array();

		flag_rfid_active_test = 1;
		flag_rfid_active = 1;

		if (f_empr_client) {
			f_empr_client(result.empr);
		}

		const tab_expl = result.expl;
		if (f_expl_client) {
			for (let i = 0; i < tab_expl.length; i++) {

				cb_expl[i] = tab_expl[i].cb;
				cb_index[i] = tab_expl[i].part;
				cb_count[i] = tab_expl[i].nbPart;
				cb_valid[tab_expl[i].cb] = tab_expl[i].IsValid;

				cb_secur[i] = (tab_expl[i].IsSecured ? "Activé": "Désactivé");
			}

			f_expl_client(cb_expl, cb_index, cb_count, cb_secur, cb_valid);
		}
	}

	flag_semaphore_rfid_read = 0;
}


function mode1_init_rfid_read_cb(empr_client, expl_client) {
	f_empr_client = empr_client;
	f_expl_client = expl_client;

	// RFID init
	bibliotheca = new Bibliotheca(url_serveur_rfid);

	setInterval(mode1_read_cb, 1500, true);
}

// Pour le pret a la chaine mode1
function mode1_read_cb(fromInterval) {
	if (!fromInterval && mode1_timeout_read) {
		// Cas spécifique du mode 1, on a un setTimout de fait,
		// on le laisse faire pour éviter de spam
		return false;
	}

	flag_semaphore_rfid_read = 1;

	if (!rfid_focus_active) {
		return false;
	}

	// On passe true a getItems car nous sommes en mode1,
	// pour avoir une selection multiple
	bibliotheca.getItems(true).then(mode1_result_read_cb);
}

function mode1_result_read_cb(result) {

	flag_semaphore_rfid_read = 0;

	if (result && 0 !== Object.keys(result).length) {

		var cb_expl = new Array();
		var cb_index = new Array();
		var cb_count = new Array();
		var cb_antivol = new Array();
		var cb_uid = new Array();
		var cb_valid = {};

		flag_rfid_active_test = 1;
		flag_rfid_active = 1;

		if (typeof result.empr[0] !== 'undefined' && 1 == result.empr.length) {
			f_empr_client(result.empr);
		}

		const tab_expl = result.expl ?? [];
		if (typeof tab_expl !== 'undefined' && 0 !== tab_expl.length) {
			for (let i = 0; i < tab_expl.length; i++) {


				cb_expl[i] = tab_expl[i].cb;
				cb_index[i] = tab_expl[i].part;
				cb_count[i] = tab_expl[i].nbPart;
				cb_valid[tab_expl[i].cb] = tab_expl[i].IsValid;
				cb_uid[i] = tab_expl[i].uid;
			}
			f_expl_client(cb_expl, cb_index, cb_count, cb_antivol, cb_uid, cb_valid);
		}
	}

	flag_semaphore_rfid_read = 0;
}

async function read_uid(f_ack) {
	flag_rfid_active_test = 0;
	flag_semaphore_rfid_read = 1;
	f_ack_read_uid = f_ack;

	const result = await bibliotheca.getItems(true);
	const items = [...result.empr, ...result.expl];

	result_read_uid(items);
}

function result_read_uid(resultItems) {
	flag_rfid_active_test = 1;
	flag_rfid_active = 1;
	flag_semaphore_rfid_read = 0;

	if (f_ack_read_uid) {
		f_ack_read_uid(resultItems);
	}
}

// Detect presence d'element rfid
function init_rfid_detect(ack_detect) {
	if (!flag_rfid_active) {
		return;
	}
	f_ack_detect = ack_detect;

	bibliotheca = new Bibliotheca(url_serveur_rfid);
	bibliotheca.getItems(true).then(result_detect).catch(afficheErreur);
}


function result_detect(result) {
	const items = [...result.empr, ...result.expl];
	var flag = items.length > 0 ? items.length : 'false';
	if (f_ack_detect) f_ack_detect(flag);
}

// Efface tout !!!
function init_rfid_erase(ack_erase) {
	f_ack_erase = ack_erase;
	if (!flag_rfid_active) {
		return false;
	}
	read_uid(rfid_erase_suite);
}

async function rfid_erase_suite(resultItems) {
	if (!flag_rfid_active) {
		return false;
	}

	var success = 1;
	for (let i = 0; i < resultItems.length; i++) {
		const item = resultItems[i];
		success &= await bibliotheca.clearTag(item.tagId);
	}

	if (f_ack_erase) {
		f_ack_erase(success);
	}
}


var write_etiquette_data = new Array();

// Programme une etiquette
function init_rfid_write_etiquette(cb, nbtags, ack_write) {
	f_ack_write = ack_write;

	if (!flag_rfid_active) return false;
	write_etiquette_data.ack_write = ack_write;

	bibliotheca.writeExpl(cb, nbtags).then(result_write);
}

function result_write(success) {
	if (success && f_ack_write) {
		f_ack_write(success);
	}
}

// Programme une carte lecteur
var write_patron_data = new Array();
function init_rfid_write_empr(cb, ack_write) {
	f_ack_write_empr = ack_write;

	if (!flag_rfid_active)  {
		return false;
	}

	write_patron_data.ack_write = ack_write;
	bibliotheca.writeEmpr(cb).then(result_write_empr);
}

function result_write_empr(success) {
	if (success && f_ack_write_empr) {
		f_ack_write_empr(success);
	}
}

// Active / desactive un antivol
// cb | code barre du livre
// level | boolean (ativation ou non de l'antivol)
// ack_antivol | callback
async function init_rfid_antivol(cb, level, ack_antivol) {
	if (typeof memo_cb_rfid_js != "undefined" && memo_cb_rfid_js.length > 0) {
		// petit hack car cb n'est pas replace
		cb = memo_cb_rfid_js[memo_cb_rfid_js.length - 1];
	}

	if ("!!expl_cb!!" == cb && typeof memo_cb_rfid_js != "undefined" && memo_cb_rfid_js.length == 0) {
		return false;
	}

	var callback_name = typeof ack_antivol == "function" ?  ack_antivol.name : "";
	if (!flag_rfid_active || callback_name.includes("mode1_ack_antivol_pret")) {
		return false;
	}

	f_ack_antivol = ack_antivol;
	if (!bibliotheca) {
		bibliotheca = new Bibliotheca(url_serveur_rfid);
	}

	var afi = rfid_afi_security_off;
	if (level) {
		afi = rfid_afi_security_active;
	}

	var result = {};
	if (!get_uid_from_cb[cb]) {
		result = await bibliotheca.setTagSecurity(afi, cb);

		param_antivol_level = level;
		param_antivol_cb = cb;

		if (!callback_name.includes("ack_antivol_pret")) {
			// petit hack pour les pret de doc
			f_ack_antivol();
		}
	} else {
		var list = get_uid_from_cb[cb];
		if (!list) f_ack_antivol(0);
		for (var i = 0; i < list.length; i++) {
			uidlist[i] = new Array();
			uidlist[i]['uid'] = list[i];
		}
		result = await bibliotheca.setTagSecurity(afi);
	}

	return result;
}

function rfid_antivol_suite_1(retVal) {
	for (i = 0; i < retVal.length; i++) {
		var uid = retVal[i].uid;
		if (!retVal[i].error) {
			if (!retVal[i].type) {
				if (!get_uid_from_cb[retVal[i].cb]) get_uid_from_cb[retVal[i].cb] = new Array();
				get_uid_from_cb[retVal[i].cb][get_uid_from_cb[retVal[i].cb].length] = uid;
			}
		}
	}

	var level = param_antivol_level;
	var cb = param_antivol_cb;

	var afi = rfid_afi_security_off;
	if (level) {
		afi = rfid_afi_security_active;
	}
	var list = get_uid_from_cb[cb];
	if (!list) {
		f_ack_antivol(0);
	}
	bibliotheca.setTagSecurity(afi);
}


// Active / desactive tous les antivols
var rfid_antivol_all_data = new Array();

function init_rfid_antivol_all(level, ack_antivol) {
	f_ack_antivol = ack_antivol;
	bibliotheca = new Bibliotheca(url_serveur_rfid);
	//pour enlever l'antivol
	rfid_antivol_level = level;
	bibliotheca.getItems(true).then(result_rfid_antivol_1);
}

async function result_rfid_antivol_1(result) {
	var afi = rfid_antivol_level ? rfid_afi_security_active : rfid_afi_security_off;

	var success = 1;
	const items = [...result.empr, ...result.expl];
	for (let index in items) {
		success &= await bibliotheca.setTagSecurity(afi, items[index].cb);
	}
	result_rfid_antivol(success);
}

function result_rfid_antivol(success) {
	f_ack_antivol(1);
}

function effacer_ligne_tableau(array, valueOrIndex) {
	var output = [];
	var j = 0;
	for (var i in array) {
		if (i != valueOrIndex) {
			output[j] = array[i];
			j++;
		}
	}
	return output;
}

function formatString(encodedStr) {
	var parser = new DOMParser();
	// convertie les "&eacute;" en "é", etc.
	var dom = parser.parseFromString(encodedStr, 'text/html');
	// remplace les multiples espaces en 1 seul
	var str = dom.body.textContent.replace(/(\s){2,}/gm, ' ');
	return str.trim();
}
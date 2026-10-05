// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: driver.js,v 1.7.8.2 2025/04/29 09:16:47 qvarin Exp $

/*
Information pour utiliser le driver bibliotheca
rfid_afi_security_codes		true,false
rfid_driver					bibliotheca
rfid_serveur_url			http://localhost:21645/
*/

class WebService {
	constructor(url_serveur_rfid) {
		this.url_serveur_rfid = url_serveur_rfid + "TagService/Web/";
	}

	get HTTP_POST() {
		return "POST";
	}

	get HTTP_GET() {
		return "GET";
	}

	async fetch(http_method, fetch_url, data) {

		if (!this.url_serveur_rfid) {
			throw new Error("[Webserivce] URL server not defined !");
		}

		let url = this.url_serveur_rfid + fetch_url;
		let init = {
			method: http_method == this.HTTP_POST ? this.HTTP_POST : this.HTTP_GET,
			cache: 'no-cache'
		};

		if (http_method == this.HTTP_POST) {
			var headers = new Headers();
			headers.append("Content-Type", "application/json");
			init["headers"] = headers;
			init['body'] = JSON.stringify(data);
		} else {
			url += '?';
			for (let prop in data) {
				url += '&' + prop + '=' + data[prop];
			}
		}

		return fetch(url, init)
	}

	async getItems() {
		try {
			const response = await this.fetch(this.HTTP_GET, 'GetItems');
			const result = await response.json();
			return result.GetItemsResult ?? [];
		} catch (e) {
			console.error(`[WebService - getItems] ${e}`);
			return [];
		}
	}

	async writeTag(tag) {
		try {
			const response = await this.fetch(this.HTTP_POST, 'WriteTag', { "tag": tag });
			return response.ok ? true : false;
		} catch (e) {
			console.error(`[WebService - writeTag] ${e}`);
			return false;
		}
	}

	async setTagSecurity(idTag, IsSecured) {
		try {
			const response = await this.fetch(this.HTTP_POST, 'SetTagSecurity', { "tagId": idTag, "isSecured": IsSecured });
			return response.ok ? true : false;
		} catch (e) {
			console.error(`[WebService - setTagSecurity] ${e}`);
			return false;
		}
	}

	async isOnline() {
		try {
			const response = await this.fetch(this.HTTP_GET, 'IsOnline');
			const result = await response.json();
			return result.IsOnlineResult ?? false;
		} catch (e) {
			console.error(`[WebService - isOnline] ${e}`);
			return false;
		}
	}

	async isConnected() {
		try {
			const response = await this.fetch(this.HTTP_GET, 'IsConnected');
			const result = await response.json();
			return result.IsConnectedResult ?? false;
		} catch (e) {
			console.error(`[WebService - isConnected] ${e}`);
			return false;
		}
	}
}

class Bibliotheca {

	constructor(url_serveur_rfid) {
		this.ws = new WebService(url_serveur_rfid);
		this.isBibliotheca = true;
	}

	// Permet de savoir si on est sur la page des retour de document
	isReturnExplPage() {
		return window.location.href.toLowerCase().includes('categ=retour') ? true : false;
	}

	// Permet de savoir si on est sur la page de lecture rfid
	isRFIDReadPage() {
		return window.location.href.toLowerCase().includes('categ=rfid_read') ? true : false;
	}

	// Permet de savoir si on est sur la page d'edition ou de creation d'un exemplaire
	isEditExplPage() {
		return (
			window.location.href.toLowerCase().includes('categ=edit_expl') ||
			window.location.href.toLowerCase().includes('categ=expl_create') ||
			window.location.href.toLowerCase().includes('action=expl_form')
		) ? true : false;
	}

	getFormatedValue(data, multiple) {

		var items = {
			"empr": [],
			"expl": [],
		};

		if (data.length > 0) {
			var length = (multiple) ? data.length : 1;
			for (let i = 0; i < length; i++) {
				if (Tag.EMPR_TYPE == data[i].Type) {
					items.empr.push(data[i].Id);
				} else {
					for (let j = 0; j < data[i].Tags.length; j++) {

						let part = 1;
						let nbPart = 1;

						const tag = data[i].Tags[j];
						const indexNumber = tag.Fields.findIndex(field => field.Name == "SetNumber");
						if (indexNumber !== -1) {
							part = parseInt(tag.Fields[indexNumber].Value);
						}
						const indexSize = tag.Fields.findIndex(field => field.Name == "SetSize");
						if (indexSize !== -1) {
							nbPart = parseInt(tag.Fields[indexSize].Value);
						}

						items.expl.push({
							"cb": data[i].Id,
							"tagId": tag.Id,
							"IsSecured":tag.IsTagSecured,
							"IsValid": data[i].IsValid,
							"part": part,
							"nbPart": nbPart
						});
					}
				}
			}
		}
		return items;
	}

	async getItems(multiple = false, formated = true) {
		try {
			const items = await this.ws.getItems();
			if (formated) {
				return this.getFormatedValue(items, multiple);
			} else {
				var length = (multiple) ? items.length : 1;
				return items.slice(0, length);
			}
		} catch (e) {
			console.error(`[Bibliotheca - getItems] ${e}`);
			return this.getFormatedValue([], multiple);
		}
	}

	async setTagSecurity(security, cb = "") {
		try {
			var items = await this.ws.getItems();
			items = items.filter(item => item.Type == Tag.EXPL_TYPE);

			if (cb && cb != "") {
				const item = items.find(item => item.Id == cb);
				items = item ? [item] : [];
			}

			var result = {};
			for (let i = 0; i < items.length; i++) {
				var valid = true;
				for (let j = 0; j < items[i].Tags.length; j++) {
					var valide = await this.ws.setTagSecurity(items[i].Tags[j].Id, security);
					valid &= valide;
				}
				result[items[i].Id] = Boolean(valid);
			}
			return result;
		} catch (e) {
			console.error(`[Bibliotheca - setTagSecurity] ${e}`);
			return false;
		}
	}

	async clearTag(tagId) {

		if (!tagId) {
			console.error('[Bibliotheca - clearTag] Id tag not found !');
			return false;
		}

		const tag = new Tag({
			Type: Tag.EMPTY_TYPE,
			TagFormat: Tag.FormatGenericBlank,
			IsTagSecured: Tag.SecuredDisabled
		});

		const writeTag = tag.computed(tagId);
		const response = await this.ws.writeTag(writeTag);
		if (!response) {
			alert("Une erreur est survenue à l'écriture de l'étiquette");
		}
		return response;
	}

	async writeEmpr(cb) {

		if (!cb) {
			console.error('[Bibliotheca - writeEmpr] Wrong cb !');
			return false;
		}

		const items = await this.ws.getItems();
		if (items.length != 1) {
			alert("Le nombre d'étiquette ne correspond pas !");
			console.error('[Bibliotheca - writeEmpr] Wrong part number !');
			return false;
		}

		const tag = new Tag({
			Type: Tag.EMPR_TYPE,
			TagFormat: Tag.FormatISO28560,
			IsTagSecured: Tag.SecuredDisabled
		});

		const writeTag = tag.computed(items[0].Tags[0].Id, cb);
		return this.writeTag(writeTag);
	}

	async writeExpl(cb, nbPart = 1) {

		if (!cb) {
			console.error('[Bibliotheca - writeExpl] Wrong cb !');
			return false;
		}

		const items = await this.getItems(true);
		if (items.expl.length != nbPart) {
			alert("Le nombre d'étiquette ne correspond pas !");
			console.error('[Bibliotheca - writeExpl] Wrong part number !');
			return false;
		}

		var valid = true;
		for (let i = 0; i < nbPart; i++) {
			const tag = new Tag({
				Type: Tag.EXPL_TYPE,
				TagFormat: Tag.FormatISO28560,
				IsTagSecured: Tag.SecuredActived
			});

			if (nbPart > 1) {
				tag.setSize(nbPart);
				tag.setNumber(i+1);
			}

			const writeTag = tag.computed(items.expl[i].tagId, cb);
			valid &= await this.writeTag(writeTag);
		}

		if (!valid) {
			alert("Une erreur est survenue à l'écriture de l'étiquette");
		}
		return valid;
	}

	async writeTag(writeTag) {

		if (![Tag.SecuredActived, Tag.SecuredDisabled].includes(writeTag.IsTagSecured)) {
			console.error('[Bibliotheca - writeTag] Wrong IsTagSecured !');
			return false;
		}

		if (!writeTag.Id) {
			console.error('[Bibliotheca - writeTag] Wrong Id !');
			return false;
		}

		if (!writeTag.TagFormat) {
			console.error('[Bibliotheca - writeTag] Wrong TagFormat !');
			return false;
		}
		return this.ws.writeTag(writeTag);
	}
}


class Tag {

	static get EMPTY_TYPE() {
		return 0;
	}

	static get EXPL_TYPE() {
		return 1;
	}

	static get EMPR_TYPE() {
		return 3;
	}

	static get SecuredActived() {
		return true;
	}

	static get SecuredDisabled() {
		return false;
	}

	static get FormatFrenchAlphaItem() {
		return 513;
	}

	static get FormatISO28560() {
		return 768;
	}

	static get FormatGenericBlank() {
		return 86;
	}

	get RequiredFields() {
		return {
			86: [
				{
					Name: "SIDType",
					Type: 1,
					Value: 2
				}
			],
			768: [
				{
					Name: "ItemID",
					Type: 2,
					Value: null
				},
				{
					Name: "SetNumber",
					Type: 1,
					Value: 1
				},
				{
					Name: "SetSize",
					Type: 1,
					Value: 1
				}
			],
			513: [
				{
					Name: "ItemID",
					Type: 2,
					Value: null
				},
				{
					Name: "SIDType",
					Type: 1,
					Value: 2
				},
				{
					Name: "SetNumber",
					Type: 1,
					Value: 1
				},
				{
					Name: "SetSize",
					Type: 1,
					Value: 1
				}
			]
		}
	}

	get DefaultFields() {
		return this.RequiredFields[this.settings.TagFormat] ?? [];
	}

	_settings = {}

	_fields = []

	get settings() {
		return this._settings;
	}

	set settings(settings) {
		this._settings = settings;
	}

	get fields() {
		return this._fields;
	}

	set fields(fields) {
		this._fields = fields;
	}

	constructor(settings) {
		this.settings = settings;
		this.fields = this.DefaultFields;
	}

	setSize(number) {
		const index = this.fields.findIndex(field => field.Name == "SetSize");
		if (index === -1) {
			throw new Error(`[Tag - setSize] Field setSize undefined`);
		}
		this.fields[index].Value = parseInt(number);
	}

	setNumber(number) {
		const index = this.fields.findIndex(field => field.Name == "SetNumber");
		if (index === -1) {
			this.fields.push({
				Name: "SetNumber",
				Type: 1,
				Value: number
			});
		} else {
			this.fields[index].Value = number
		}
	}

	computed(tagId, code = "") {

		if (![Tag.SecuredActived, Tag.SecuredDisabled].includes(this.settings['IsTagSecured'])) {
			this.settings['IsTagSecured'] = Tag.SecuredActived;
		}


		var tag = {
			'Id': tagId ?? false,
			'Type': this.settings['Type'] ?? false,
			'TagFormat': this.settings['TagFormat'] ?? false,
			'IsSecutirySupported': true,
			'IsTagSecured': this.settings['IsTagSecured'],
		};

		if (false === tag.Id ||false === tag.Type || false === tag.TagFormat) {
			throw new Error(`[Tag - computed] Wrong data`);
		}

		if (code != "") {
			const index = this.fields.findIndex(field => field.Name == "ItemID");
			if (index !== -1) {
				this.fields[index].Value = code;
			}
		}

		tag['Fields'] = this.fields;
		return tag;
	}

}

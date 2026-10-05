import WebService from './WebService.js'

class CmsModel {

	constructor(url_base, version_num) {
		this.ws = new WebService(url_base, version_num);
		this.cache = {}; 
	}

	getMessage(group, code) {
		return pmbDojo.messages.getMessage(group, code) ?? '';
	}

	empty(value) {

		if (typeof (value) == "undefined") {
			return true;
		}

		if (typeof value == "string") {
			var value_clean = value.trim();
			if (value_clean && value_clean.length == 0) {
				return true;
			}

			if (value == false) {
				return true;
			}
		}
		return false;
	}

	clearCache() {
		this.cache = {};
	}

	removeCache(key) {
		if (!this.cache || !this.cache[key]) {
			return false;
		}
		this.cache[key] = undefined;
		return true;
	}

	setCache(key, item) {
		this.cache[key] = {
			time: Date.now(),
			item: item
		};
	}

	getCache(key) {
		if (this.cache[key]) {
			let minutes = ((Date.now() - this.cache[key].time) / 1000) / 60;
			if (minutes < 1) {
				return this.cache[key].item ?? false;
			}
		}
		return false;
	}

	updatePage(id, data) {
		if (!id) {
			return { error: true, errorMessage: "id page need for update" };
		}

		let result = this.checkPage(data);
		if (result === true) {
			this.clearCache();
			return this.ws.updatePage(id, data);
		} else {
			return result;
		}
	}

	createPage(data) {
		if (!data || Object.values(data).length == 0) {
			return { error: true, errorMessage: this.getMessage('portal', 'page_no_data_set') };
		}

		let result = this.checkPage(data);
		if (result === true) {
			return this.ws.createPage(data);
		} else {
			return result;
		}
	}

	async getPagesUsingGabarit(id) {
		let key_cache = `pagesUsingGabarit_${id}`;
		if (this.getCache(key_cache)) {
			return this.getCache(key_cache);
		}
		let result = await this.ws.getPagesUsingGabarit(id);
		// Pour éviter le spam on met le résultat en cache
		this.setCache(key_cache, result);
		return result;
	}

	updateGabarit(id, data) {
		if (!id) {
			return { error: true, errorMessage: "id gabarit need for update" };
		}

		let result = this.checkGabarit(data.gabarit);
		if (result === true) {
			this.clearCache();
			return this.ws.updateGabarit(id, data);
		} else {
			return result;
		}
	}

	createGabarit(data) {
		if (!data || Object.values(data).length == 0 || !data.gabarit) {
			return { error: true, errorMessage: this.getMessage('portal', 'gabarit_no_data_set') };
		}

		let result = this.checkGabarit(data.gabarit);
		if (result === true) {
			return this.ws.createGabarit(data);
		} else {
			return result;
		}
	}

	checkGabarit(gabarit) {
		if (this.empty(gabarit.name)) {
			return { error: true, errorMessage: this.getMessage('portal', 'gabarit_no_name_set') };
		}
		return true;
	}

	checkPage(data) {
		if (this.empty(data.name)) {
			return { error: true, errorMessage: this.getMessage('portal', 'page_no_name_set') };
		}
		if (this.empty(data.type)) {
			return { error: true, errorMessage: this.getMessage('portal', 'page_no_pagetype_set') };
		}
		if (this.empty(data.sub_type)) {
			return { error: true, errorMessage: this.getMessage('portal', 'page_no_pagesubtype_set') };
		}
		return true;
	}
	
	async getOpacViews() {
		let key_cache = `opac_views`;
		if (this.getCache(key_cache)) {
			return this.getCache(key_cache);
		}
		
		let result = await this.ws.getOpacViews();
		this.setCache(key_cache, result);
		return result;
	}
}

export default CmsModel;
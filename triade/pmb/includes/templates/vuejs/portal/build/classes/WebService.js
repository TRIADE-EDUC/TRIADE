class WebService {

	constructor(url_base, version_num) {
		this.url = url_base;
		this.setVersion(version_num);
	}

	get HTTP_POST() {
		return "POST";
	}

	get HTTP_GET() {
		return "GET";
	}

	getVersion() {
		return sessionStorage.getItem('version_num');
	}
	
	setVersion(version) {
		sessionStorage.setItem('version_num', version.toString());
	}

	async fetch(http_method, fetch_url, data) {

		if (!this.getVersion()) {
			throw new Error("[Webservice] Version not set !");
		}

		let url = this.url + fetch_url;
		let init = {
			method: this.HTTP_GET,
			cache: 'no-cache'
		};

		if (http_method == this.HTTP_POST) {
			let post = new URLSearchParams();
			post.append("version_num", this.getVersion());
			for (let prop in data) {
				post.append(prop, JSON.stringify(data[prop]));
			}
			init['method'] = this.HTTP_POST;
			init['body'] = post;
		} else {
			url += '?';
			for (let prop in data) {
				url += '&' + prop + '=' + data[prop];
			}
		}

		try {
			let response = await fetch(url, init);
			let result = await response.json();
			if (result.error) {
				throw result.errorMessage;
			}
			
			let new_version_num;
			if (typeof result == "object") {
				if (result.version_num) {
					new_version_num = parseInt(result.version_num);
				}
			} else {
				new_version_num = parseInt(result);
			}
			if (new_version_num && this.getVersion() < new_version_num) {
				this.setVersion(new_version_num);
			}
			
			
			return result;
		} catch (e) {
			return {
				error: true,
				errorMessage: e
			};
		}
	}

	/*
	 * List Methods
	 */
	getPageList() {
		return this.fetch(this.HTTP_POST, 'page/list');
	}
	getGabaritList() {
		return this.fetch(this.HTTP_POST, 'gabarit/list');
	}
	getFrameList() {
		return this.fetch(this.HTTP_POST, 'frame/list');
	}

	/*
	 * Create/Remove/Update d'une Page/Modèle
	 */
	createPage(data) {
		return this.fetch(this.HTTP_POST, 'page/create', { data: data });
	}
	createGabarit(data) {
		return this.fetch(this.HTTP_POST, 'gabarit/create', { data: data });
	}
	removePage(id, data) {
		return this.fetch(this.HTTP_POST, `page/remove/${id}`);
	}
	removeGabarit(id, data) {
		return this.fetch(this.HTTP_POST, `gabarit/remove/${id}`);
	}
	updatePage(id, data) {
		return this.fetch(this.HTTP_POST, `page/update/${id}`, { data: data });
	}
	updateGabarit(id, data) {
		return this.fetch(this.HTTP_POST, `gabarit/update/${id}`, { data: data });
	}

	/*
     * Update de l'arbre
     */
	updateTreePage(id, data) {
		return this.fetch(this.HTTP_POST,  `page/${id}/update/tree`, { data: data });
	}
	updateTreeGabarit(id, data) {
		return this.fetch(this.HTTP_POST,  `gabarit/${id}/update/tree`, { data: data });
	}
	
	/*
     *  Masquer une Zone/Cadre
     */
	hideElementPageLayout(id, data) {
		return this.fetch(this.HTTP_POST,  `page/${id}/hide/element`, { data: data });
	}
	hideElementGabaritLayout(id, data) {
		return this.fetch(this.HTTP_POST,  `gabarit/${id}/hide/element`, { data: data });
	}
	
	/*
     *  Suppression de Zone/Cadre
     */
	removeElementPageLayout(id, data) {
		return this.fetch(this.HTTP_POST,  `page/${id}/remove/element`, { data: data });
	}
	removeZone(id, data) {
		return this.fetch(this.HTTP_POST, `zone/remove/${id}`, { data: data });
	}
	
	/*
     * Semantique
     */
	updateTagElementPageLayout(id, data) {
		return this.fetch(this.HTTP_POST,  `page/${id}/update/tag/element`, { data: data });
	}
	updateTagElementGabaritLayout(id, data) {
		return this.fetch(this.HTTP_POST,  `gabarit/${id}/update/tag/element`, { data: data });
	}
	
	/*
     * Classes CSS
     */
	addElementPageLayout(id, data) {
		return this.fetch(this.HTTP_POST,  `page/${id}/create/element`, { data: data });
	}
	addElementGabaritLayout(id, data) {
		return this.fetch(this.HTTP_POST,  `gabarit/${id}/create/element`, { data: data });
	}

	/*
	 * Récupération d'un liste
	 */
	getFramesInGabarit(id) {
		return this.fetch(this.HTTP_POST, `gabarit/${id}/frame/list`);
	}
	getFramesInPage(id) {
		return this.fetch(this.HTTP_POST, `page/${id}/frame/list`);
	}
	getZonesInPage(id) {
		return this.fetch(this.HTTP_POST, `page/${id}/zone/list`);
	}
	getZonesInGabarit(id) {
		return this.fetch(this.HTTP_POST, `gabarit/${id}/zone/list`);
	}
	getPagesUsingFrame(data) {
		return this.fetch(this.HTTP_POST, `frame/page/list`, { data: data });
	}
	getGabaritsUsingFrame(data) {
		return this.fetch(this.HTTP_POST, `frame/gabarit/list`, { data: data });
	}
	
	/*
	 * Cache
	 */
	clearCacheInPage(id) {
		return this.fetch(this.HTTP_POST, `page/${id}/clear/cache`);
	}
	clearCacheFrame(id) {
		return this.fetch(this.HTTP_POST, `frame/${id}/clear/cache`);
	}
	clearCache() {
		return this.fetch(this.HTTP_POST, `portal/clear/cache`);
	}
	
	/*
	 * Classement
	 */
	editFrameClassement(frame) {
		return this.fetch(this.HTTP_POST, `frame/classement`, {data: frame});
	}
	editGabaritClassement(gabarit) {
		return this.fetch(this.HTTP_POST, `gabarit/classement`, {data: gabarit});
	}

	/*
	 * Classes CSS
	 */
	addZoneClasses(id, data) {
		return this.fetch(this.HTTP_POST, `zone/classes/${id}`, {data: data});
	}
	addFrameClasses(id, data) {
		return this.fetch(this.HTTP_POST, `frame/classes/${id}`, {data: data});
	}
	/*
	 * attributs
	 */
	addZoneAttributes(id, data) {
		return this.fetch(this.HTTP_POST, `zone/attributes/${id}`, {data: data});
	}
	addFrameAttributes(id, data) {
		return this.fetch(this.HTTP_POST, `frame/attributes/${id}`, {data: data});
	}
	
	gabaritRemoveFrame(idGabarit, idTag) {
		return this.fetch(this.HTTP_POST, `gabarit/${idGabarit}/remove/frame`, {data: {idTag: idTag}});		
	}

	gabaritRemoveZone(idGabarit, idTag) {
		return this.fetch(this.HTTP_POST, `gabarit/${idGabarit}/remove/zone`, {data: {idTag: idTag}});		
	}

	pageRemoveFrame(idPage, idTagFrame) {
		return this.fetch(this.HTTP_POST, `page/${idPage}/remove/frame`, {data: {idTag: idTagFrame}});				
	}
	
	pageSaveContext(id, data) {
		return this.fetch(this.HTTP_POST, `page/save/context/${id}`, {data: data});						
	}
	
	pageEditContext(id, data) {
		return this.fetch(this.HTTP_POST, `page/edit/context/${id}`, {data: data});						
	}
	
	pageRemoveContext(id, data) {
		return this.fetch(this.HTTP_POST, `page/remove/context/${id}`, {data: data});						
	}
	
	getOpacViews() {
		return this.fetch(this.HTTP_GET, `opac_views`, {});	 
	}
	
	pageBookmarkContext(id, data) {
		return this.fetch(this.HTTP_POST, `page/bookmark/context/${id}`, {data: data});						
	}
	
	/*
	 * Duplication
	 */
	duplicateGabarit(idGabarit) {
		return this.fetch(this.HTTP_POST, `gabarit/duplicate/${idGabarit}`);		
	}

	removeLayout(data) {
		return this.fetch(this.HTTP_POST, `gabarit/${data.gabarit}/remove/layout`, {data: {layout: data.layout}});		
	}

	removePageLayout(data) {
		return this.fetch(this.HTTP_POST, `page/${data.page}/remove/layout`, {data: {layout: data.layout}});		
	}
	
	fecthLayout(item) {
		return this.fetch(this.HTTP_POST, `fecth/layout`, {data: item});	
	}
	
	shareLayout(item, zone) {
		return this.fetch(this.HTTP_POST, `share/layout`, {data: {item, zone}});	
	}

	frameRemove(id) {
		return this.fetch(this.HTTP_POST, `frame/remove/${id}`);
	}

	/*
	 * Versions
	 */
	switchVersion(id_portal, id_version) {
		return this.fetch(this.HTTP_POST, `portal/${id_portal}/switch/version/${id_version}`);		
	}

	renameVersion(id_version, name) {
		return this.fetch(this.HTTP_POST, `portal/rename/version`, {data: {id_version, name}});		
	}

	getVersions() {
		return this.fetch(this.HTTP_GET, `portal/versions`, {});		
	}

	cleanVersions() {
		return this.fetch(this.HTTP_GET, `portal/versions/clean`, {});		
	}
}

export default WebService;
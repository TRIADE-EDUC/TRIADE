import CmsModel from "./CmsModel.js";

const proxyHandler = {
	get(cms, prop, receiver) {
		if (typeof cms.store.$data[prop] != "undefined") {
			return Reflect.get(cms.store.$data, prop, receiver);
		}
		if (typeof cms[prop] != "undefined") {
			return Reflect.get(...arguments);
		}
		return undefined;
	},
	set(cms, prop, value) {
		if (typeof cms.store.$data[prop] != "undefined") {
			return Reflect.set(cms.store.$data, prop, value);
		}
		return Reflect.set(...arguments);
	},
}

class Cms {

	constructor(Vue, options) {

		// Définir ici toutes les propriétés
		var properties = {
			container: {
				title: "",
				component: "",
				data: {
					type: "",
					item: {
						contexts: []
					},
					component: ""
				},
			},
			itemNavActive: -1,
			itemsNav: [],
			framesClassements: [],
			gabaritsClassements: [],
			zoneList: [],
			refreshFrameList: false,
			gabaritsDisplayType : 0,
			showUnusedGabarit : 1
		};

		// On récupère les paramètres donnés au plugin
		for (const prop in options) {
			properties[prop] = this.cloneObject(options[prop]);
		}

		this.store = new Vue({
			data: () => {
				return properties;
			}
		});

		// Cela nous permet d'avoir un plugin réactif :
		// getter et setter "magique"
		this.proxy = new Proxy(this, proxyHandler);
		for (const prop in properties) {
			this.__defineGetter__([prop], () => { return this.proxy[prop] });
			this.__defineSetter__([prop], (value) => { return this.proxy[prop] = value; });
		}
		return this.proxy;
	}

	init() {
		var model = new CmsModel(this.url_base, this.portal.version_num);
		this.model = new Proxy(model, {
			get(model, prop, receiver) {
				var result = undefined;
				if (typeof model[prop] != "undefined") {
					result = Reflect.get(...arguments);
				}
				if (typeof model.ws[prop] != "undefined") {
					result = Reflect.get(model.ws, prop, receiver);
				}
				if (result.error) {
					throw result.errorMessage;
				}
				return result;
			}
		});
	}

	cloneObject(obj) {
		if (obj instanceof Array) {
			var clone = new Array();
			for (let index in obj) {
				clone[index] = this.cloneObject(obj[index]);
			}
			return clone;
		} else if (obj instanceof Object) {
			// On clone object on fait en sorte d'avoir les getter/setter
			var clone = Object.create(Object.getPrototypeOf(obj));
			var descriptors = Object.getOwnPropertyDescriptors(obj);
			Object.defineProperties(clone, descriptors);
			return clone;
		}
		return obj;
	}

	getMessage(code) {
		return pmbDojo.messages.getMessage('portal', code) ?? '';
	}

	getImage(image) {
		const imgName = image.replace(/\.[a-zA-Z]+/, "");
		if (this.img[imgName]) {
			return this.img[imgName] ?? '';
		}
		return pmbDojo.images.getImage(image) ?? '';
	}

	getTypeFromSubType(sub_type) {
		return parseInt(sub_type / 100);
	}

	get itemsAccordion() {
		return [
			{
				title: this.getMessage('banner_title_pages'),
				children: this.formatPagesAccordion(),
				component: "accordionItem",
				add: () => { this.addPage() }
			},
			{
				title: this.getMessage('banner_title_frame'),
				children: this.formatFramesAccordion(),
				component: "accordionFrameItem",
				add: () => { this.addFrame() }
			},
			{
				title: this.getMessage('banner_title_gabarit'),
				children: this.formatGabaritsAccordion(this.gabaritsDisplayType),
				component: "accordionGabaritItem",
				add: () => { this.addGabarit() }
			},
			{
				title: this.getMessage('banner_title_version'),
				children: this.portals,
				component: "accordionVersionItem",
			}
		];
	}

	getFormatedChildren(gabaritId) {
		var children = [];
		var gabaritInheritance = this.getGabaritInheritanceOf(gabaritId);
		if (this.showUnusedGabarit == 0) {
			gabaritInheritance = gabaritInheritance.filter(gabarit => this.gabaritIsUsed(gabarit.id));
		}
		gabaritInheritance.forEach((gabarit) => {
			var isEdited = false;
			if (gabarit.legacy_layout != null && gabarit.children.length > 0) {
				isEdited = true;
			}

			children.push({
				title : gabarit.name,
				isTag : false,
				isEdited : isEdited,
				children : this.getFormatedChildren(gabarit.id),
				data: {
					type: "gabarit",
					name: gabarit.name,
					item: gabarit
				},
			});
		})
		return children;
	}

	getGabaritInheritanceOf(gabaritId) {
		return this.gabarits.filter(gabarit => {
			return (gabarit.legacy_layout != null && gabarit.legacy_layout.id == gabaritId);
		}) || [];
	}

	gabaritIsUsed(gabarit_id) {
		const gabaritInheritance = this.getGabaritInheritanceOf(gabarit_id);
		for (let index in gabaritInheritance) {
			if (this.gabaritIsUsed(gabaritInheritance[index].id)) {
				return true;
			}
		}
		return this.getPagesUsingGabarit(gabarit_id).length > 0;
	}

	/**
	 * tree_view = 1 : vue en arbre
	 * tree_view = 0 : vue en classement
	 */
	formatGabaritsAccordion(tree_view=false) {
		const default_classement = this.getMessage('default_classement');
		let gabarits = {};


		var gabaritList = this.gabarits
		if (this.showUnusedGabarit == 0) {
			var gabaritList = this.gabarits.filter(gabarit => this.gabaritIsUsed(gabarit.id));
		}

		gabaritList.forEach((gabarit) => {
			if (gabarit.legacy_layout && tree_view > 0){
				return;
			}
			var classement = (gabarit.classement && gabarit.classement != "") ? gabarit.classement : default_classement;

			if(tree_view > 0){
				classement = this.getMessage('tree_classement');
			}
			if (!gabarits[classement]) {
				gabarits[classement] = {
					title: classement,
					isTag: true,
					children: []
				}
			}
			var isEdited = false;
			if (gabarit.legacy_layout != null) {
				const index = gabarit.legacy_layout.class + "_" + gabarit.legacy_layout.id;
				if (gabarit.layouts[index] && gabarit.layouts[index].length > 0) {
					isEdited = true;
				}
			}

			gabarits[classement].children.push({
				title: gabarit.name,
				isTag: false,
				isEdited: isEdited,
				classement: classement,
				data: {
					type: "gabarit",
					name: gabarit.name,
					item: gabarit
				},
				children: tree_view > 0 ? this.getFormatedChildren(gabarit.id) : []
			})
		});

		// On récupère tout les classements définis
		this.gabaritsClassements = Object.keys(gabarits);
		return Object.values(gabarits);
	}

	formatPagesAccordion() {
		let formatedPages = {};

		this.portal.types.forEach((type) => {
			formatedPages[type.value] = {
				title: type.label,
				isTag: true,
				children: [],
				data: {
					type: "pageType",
					name: type.label ?? `Page ${type.value}`,
					item: {
						type: type.value
					}
				}
			};
		})
		this.portal.sub_types.forEach((sub_type) => {
			if (!sub_type.label) {
				sub_type.label = `Page ${sub_type.value}`
			}

			let type_code = this.getTypeFromSubType(sub_type.value);
			let type = formatedPages[type_code] !== undefined ? formatedPages[type_code] : null;
			if (type) {

				var page = null;
				var children = [];
				var pages = this.getPages(type_code, sub_type.value);
				if (pages.length > 0) {
					const indexPage = pages.findIndex(page => page.conditions.length == 0);
					if (0 <= indexPage && indexPage <= pages.length) {
						page = pages[indexPage] ?? null;
						pages.splice(indexPage, 1);
					}
					pages.forEach((page) => {

						// Page de portail
						var name = page.name;
						if (type_code == 25) {
							var id = parseInt(page.sub_type.toString().substr(2), 10);
							if (id) {
								name += ` id: ${id}`;
							}
						}

						children.push({
							title: page.name,
							isTag: false,
							isEdited: this.pageIsEdited(page),
							data: {
								type: "page",
								name: name,
								item: page
							},
							children: []
						})
					});
				}


				if (!page) {
					var dataName = sub_type.label;
					page = {
						"class": "Pmb\\CMS\\Models\\PageModel",
						id: 0,
						name: sub_type.label,
						conditions: [],
						gabarit_layout: {},
						parent_page: {},
						page_layout: {
							layouts: {}
						},
						type: type_code,
						sub_type: sub_type.value,
					}
				} else {
					if (!page.name) {
						page.name = sub_type.label;
					}
					var dataName = page.name;
				}

				// Page de portail
				if (type_code == 25) {
					var id = parseInt(page.sub_type.toString().substr(2), 10);
					if (id) {
						dataName += ` id: ${id}`;
					}
				}

				var sub_page = {
					title: page.name,
					isTag: false,
					isEdited: this.pageIsEdited(page),
					data: {
						type: "page",
						name: dataName ?? sub_type.label,
						item: page
					},
					children: children
				};
				type.children.push(sub_page);
			}
		})
		return Object.values(formatedPages);
	}

	formatFramesAccordion(opac = true) {
		const default_classement = this.getMessage('default_classement');

		let frames = {};
		for (const index in this.frames) {
			const frame = this.frames[index];
			if (!opac && frame && frame.class.includes('FrameOpac')) {
				continue;
			}

			const classement = (frame.classement && frame.classement != "") ? frame.classement : default_classement;
			if (!frames[classement]) {
				frames[classement] = {
					title: classement,
					isTag: true,
					children: []
				}
			}
			frames[classement].children.push({
				title: frame.name,
				isTag: false,
				isEdited: false,
				classement: classement,
				data: {
					type: "frame",
					name: frame.name,
					item: frame
				},
				children: []
			});
		}

		// On récupère tout les classements définis
		this.framesClassements = Object.keys(frames);
		return Object.values(frames);
	}

	addPage() {
		this.resetNav();
		this.itemsNav.push({
			title: this.getMessage("nav_prop_page"),
			component: 'prop-page',
			data: {
				type: "page",
				name: "",
			}
		});
	}

	addFrame() {
		this.resetNav();
		this.itemsNav.push({
			title: this.getMessage("nav_prop_frame"),
			data: {
				type: "frame",
				name: ""
			},
			component: 'prop-frame'
		})
	}

	addGabarit() {
		this.resetNav();
		this.itemsNav.push({
			title: this.getMessage("nav_prop_gabarit"),
			component: 'prop-gabarit',
			data: {
				type: "gabarit",
				name: ""
			}
		});
	}

	openPage(page, subNav = false) {
		this.pageOpen = page;
		this.pageOpen.subNav = subNav;
		if(!subNav) {
			this.closeGabarit();
		}
		this.closePageType();
	}

	closePage() {
		this.pageOpen = undefined;
	}

	openGabarit(gabarit) {
		this.gabaritOpen = gabarit;
		this.closePage();
		this.closePageType();
	}

	closeGabarit() {
		this.gabaritOpen = undefined;
	}

	openFrame(frame) {
		this.frameOpen = frame;
		this.closePageType();
	}

	closeFrame() {
		this.frameOpen = undefined;
	}

	openPageType(pageType) {
		this.pageTypeOpen = pageType;
		this.closePage();
		this.closeGabarit();
	}

	closePageType() {
		this.pageTypeOpen = undefined;
	}

	openItem(data, fromBanner = false) {
		if (fromBanner) {
			this.resetNav();
			this.clearContainer();
		}
		switch (data['type']) {
			case "pageType":
				this.openPageType(data);
				break;
			case "page":
				this.openPage(data);
				break;
			case "frame":
				this.openFrame(data);
				break;
			case "gabarit":
				this.openGabarit(data);
				break;
			case "pageSubNav":
				this.openPage(data, true);
				break;
		}
		this.generateItemsNav()
	}

    refreshItemOpen() {
        if (this.pageOpen) {
            this.pageOpen.item = this.pages.find(page => page.id == this.pageOpen.item.id);
        }
        if (this.gabaritOpen) {
            this.gabaritOpen.item = this.gabarits.find(gabarit => gabarit.id == this.gabaritOpen.item.id);
        }
        if (this.frameOpen) {
            this.frameOpen.item = this.frames.find(frame => frame.id == this.frameOpen.item.id);
        }
        this.generateItemsNav();
    }

	generateItemsNav() {
		var itemsNav = [];

		if (this.pageTypeOpen) {
			itemsNav.push({
				title: this.getMessage("nav_prop_page_type"),
				data: this.pageTypeOpen,
				component: 'prop-page-type'
			});
		} else {
			if (this.gabaritOpen) {
				itemsNav.push({
					title: this.getMessage("nav_prop_gabarit"),
					data: this.gabaritOpen,
					component: 'prop-gabarit'
				});
				if (this.gabaritOpen.item.id > 0) {
					itemsNav.push({
						title: this.getMessage("nav_layout"),
						data: this.gabaritOpen,
						component: 'layout'
					});
				}
			}

			var pageNav = [];
			if(this.pageOpen) {
				pageNav.push({
					title: this.getMessage("nav_prop_page"),
					data: this.pageOpen,
					component: 'prop-page'
				});

				if (this.pageOpen.item.id > 0) {
					pageNav.push({
						title: this.getMessage("nav_layout"),
						data: this.pageOpen,
						component: 'layout'
					});
					pageNav.push({
						title: this.getMessage("nav_prev"),
						data: this.pageOpen,
						component: 'prev'
					});
				}
			}


			if(this.pageOpen && this.pageOpen.subNav) {
				itemsNav.push({
					title: this.getMessage("gabarit_page_label"),
					data: this.pageOpen,
					component: 'prop-page',
					subNav: pageNav
				});
			} else {
				itemsNav.push(... pageNav);
			}
		}

		if (this.frameOpen) {
			itemsNav.push({
				title: this.getMessage("nav_prop_frame"),
				data: this.frameOpen,
				component: 'prop-frame'
			});
		}

		this.itemsNav = itemsNav;
	}

	resetNav() {
		this.itemNavActive = -1;
		this.itemsNav = [];
		this.closePage();
		this.closeFrame();
		this.closeGabarit();
	}

	resetVue() {
		this.resetNav();
		this.clearContainer();
		this.generateItemsNav();
	}

	getPages(type, sub_type) {
		return this.pages.filter(page => (page.type == type && page.sub_type == sub_type));
	}

	getPagesUsingGabarit(gabaritId) {
		return this.pages.filter(page => (page.gabarit_layout && page.gabarit_layout.id && page.gabarit_layout.id == gabaritId));
	}

	async updateGabarit(data) {
		this.showLoader();

		let result;
		if (data.gabarit.id) {
			result = await this.model.updateGabarit(data.gabarit.id, data);
		} else {
			result = await this.model.createGabarit(data);
		}

		if (!result.error) {
			this.pages = await this.model.getPageList();
			this.gabarits = await this.model.getGabaritList();

			let gabarit;
			if (data.gabarit.id) {
				gabarit = this.gabarits.find(gabarit => gabarit.id == data.gabarit.id);
			} else {
				gabarit = this.gabarits.find(gabarit => (gabarit.name == data.gabarit.name));
			}
			this.reloadVue({
				type: "gabarit",
				name: gabarit.name,
				item: gabarit
			});
		}
		this.hiddenLoader();
		return result;
	}

	reloadVue(item) {
		if (!item) {
			this.resetNav();
			this.clearContainer();
			this.generateItemsNav();
			return true;
		}

		// Backup
		var itemsNav = this.cloneObject(this.itemsNav);
		var itemNavActive = this.cloneObject(this.itemNavActive);
		if (this.pageTypeOpen) {
			var pageTypeOpen = this.cloneObject(this.pageTypeOpen);
		}

		if (this.pageOpen) {
			var pageOpen = this.cloneObject(this.pageOpen);
			pageOpen.item = this.pages.find(page => page.id == pageOpen.item.id);
		}

		if (this.gabaritOpen) {
			var gabaritOpen = this.cloneObject(this.gabaritOpen);
			gabaritOpen.item = this.gabarits.find(gabarit => gabarit.id == gabaritOpen.item.id);
		}

		if (this.frameOpen) {
			var frameOpen = this.cloneObject(this.frameOpen);
			frameOpen.item = this.frames.find(frame => frame.id == frameOpen.item.id);
		}

		this.resetNav();
		var isOpen = [];

		for (let i = 0; i < itemsNav.length; i++) {
			const itemNav = itemsNav[i];

			if (isOpen.includes(itemNav.data.type)) {
				continue;
			}

			var useItem = false;
			if (item.type == itemNav.data.type) {
				useItem = true;
			}

			switch (itemNav.data.type) {
				case "pageType":
					this.openPageType(useItem ? item : pageTypeOpen);
					break;
				case "page":
					const page = useItem ? item : pageOpen;
					this.openPage(page, page.subNav);
					break;
				case "frame":
					this.openFrame(useItem ? item : frameOpen);
					break;
				case "gabarit":
					this.openGabarit(useItem ? item : gabaritOpen);
					break;
				case "pageSubNav":
					this.openPage(useItem ? item : pageOpen, true);
					break;
			}
			isOpen.push(itemNav.data.type)
		}

		this.itemNavActive = itemNavActive;
		this.generateItemsNav();
		return true;
	}

	async removeGabarit(id) {
		this.showLoader();
		let result = await this.model.removeGabarit(id);
		if (!result.error) {
			this.gabarits = await this.model.getGabaritList();
			this.model.getPageList().then(pages => this.pages = pages);
			this.resetVue();
		}
		this.hiddenLoader();
		return result;
	}

	async updatePage(data, view = "page") {
		this.showLoader();

		if (data.id) {
			var result = await this.model.updatePage(data.id, data);
		} else {
			var result = await this.model.createPage(data);
		}

		if (!result.error) {
			this.pages = await this.model.getPageList();

			if (data.id) {
				var page = this.pages.find(page => page.id == data.id);
			} else {
				var page = this.pages.find(page => (page.name == data.name && page.type == data.type && page.sub_type == data.sub_type));
			}

			if (!page) {
				this.hiddenLoader();
				return this.clearContainer();
			}

			// Page de portail
			var name = page.name;
			if (page.type == 25) {
				var id = parseInt(page.sub_type.toString().substr(2), 10);
				if (id) {
					name += ` id: ${id}`;
				}
			}

			if (view == "page") {
				this.reloadVue({
					type: view,
					name: name,
					item: page
				});
			} else if (view == "layout") {
				this.updateContainer({
					type: view,
					name: name,
					item: page
				});
			}
		}

		this.hiddenLoader();
		return result;
	}

	async removePage(id) {
		this.showLoader();
		let result = await this.model.removePage(id);
		if (!result.error) {
			this.pages = await this.model.getPageList();
			this.resetVue();
		}
		this.hiddenLoader();
		return result;
	}

	async updateFrame() {
		this.showLoader();
		this.frames = await this.model.getFrameList();

		for	(let index in this.pages) {
		  this.pages[index].tree = [];
		}

		for	(let index in this.gabarits) {
		  this.gabarits[index].tree = [];
		}

		this.hiddenLoader();
		return this.frames;
	}

	async updateParent(data) {
		this.showLoader();
		if (data.page_id) {
			var result = await this.model.updateTreePage(data.page_id, data);
			if (!result.error) {
				this.pages = await this.model.getPageList();
				var page = this.pages.find(page => page.id == data.page_id);
				this.updateContainer({
					type: 'layout',
					name: page.name,
					item: page
				});
			}
		} else {
			var result = await this.model.updateTreeGabarit(data.gabarit_id, data);
			if (!result.error) {
				this.gabarits = await this.model.getGabaritList();
				var gabarit = this.gabarits.find(gabarit => gabarit.id == data.gabarit_id);
				this.updateContainer({
					type: 'layout',
					name: gabarit.name,
					item: gabarit
				});
			}
		}
		this.hiddenLoader();
	}

	async hideElementLayout(data) {
		this.showLoader();
		if (data.page_id) {
			var result = await this.model.hideElementPageLayout(data.page_id, data);
			if (!result.error) {
				this.pages = await this.model.getPageList();
				var page = this.pages.find(page => page.id == data.page_id);
				this.updateContainer({
					type: 'layout',
					name: page.name,
					item: page
				});
			}
		} else {
			var result = await this.model.hideElementGabaritLayout(data.gabarit_id, data);
			if (!result.error) {
				this.pages = await this.model.getPageList();
				this.gabarits = await this.model.getGabaritList();
				var gabarit = this.gabarits.find(gabarit => gabarit.id == data.gabarit_id);
				this.updateContainer({
					type: 'layout',
					name: gabarit.name,
					item: gabarit
				});
			}
		}

		this.hiddenLoader();
	}

	async removeElementLayout(data) {
		this.showLoader();
		if (data.page_id) {
			var result = await this.model.removeElementPageLayout(data.page_id, data);
			if (!result.error) {
				this.pages = await this.model.getPageList();
				var page = this.pages.find(page => page.id == data.page_id);
				this.updateContainer({
					type: this.container.data.type,
					name: page.name,
					item: page
				});
			}
		} else {
			if (data.item.class.toLocaleLowerCase().includes("zone")) {
				var result = await this.model.gabaritRemoveZone(data.gabarit_id, data.item.semantic.id_tag);
			} else {
				var result = await this.model.gabaritRemoveFrame(data.gabarit_id, data.item.semantic.id_tag);
			}
			if (!result.error) {
				this.gabarits = await this.model.getGabaritList();
				var gabarit = this.gabarits.find(gabarit => gabarit.id == data.gabarit_id);
				var pageFound = this.pages.find(page => page.gabarit_layout.id == gabarit.id);
				if (pageFound != undefined) {
					// le gabarit utilise des page on les mets à jours
					this.model.getPageList().then(pages => {
						this.pages = pages;
					});
				}
				this.updateContainer({
					type: this.container.data.type,
					name: gabarit.name,
					item: gabarit
				});
			}
		}
		this.hiddenLoader();
	}

	async updateTagElementLayout(data) {
		this.showLoader();

		if (data.page_id) {
			var result = await this.model.updateTagElementPageLayout(data.page_id, data);
			if (!result.error) {
				this.pages = await this.model.getPageList();
				var page = this.pages.find(page => page.id == data.page_id);
				this.updateContainer({
					type: 'layout',
					name: page.name,
					item: page
				});
			}
		} else {
			var result = await this.model.updateTagElementGabaritLayout(data.gabarit_id, data);
			if (!result.error) {
				this.gabarits = await this.model.getGabaritList();
				var gabarit = this.gabarits.find(gabarit => gabarit.id == data.gabarit_id);
				this.updateContainer({
					type: 'layout',
					name: gabarit.name,
					item: gabarit
				});
			}
		}

		this.hiddenLoader();
	}

	async refreshZones(data) {
		if (data.class.includes("PagePortalModel")) {
			var result = await this.model.getZonesInPage(data.id);
		} else {
			var result = await this.model.getZonesInGabarit(data.id);
		}
		this.zoneList = result;
		return result;
	}

	clearContainer() {
		this.container = {
			title: "",
			component: "",
			data: {},
		};
	}

	updateContainer(container) {
		this.refreshItemOpen();
		this.container.data = container;
	}

	showLoader() {
		if (typeof showLoader == "function") {
			showLoader();
		}
	}

	hiddenLoader() {
		if (typeof hiddenLoader == "function") {
			hiddenLoader();
		}
	}

	editFrameClassement(accordionframe) {
		const default_classement = this.getMessage('default_classement');
		if (!accordionframe.classement) {
			accordionframe.classement = default_classement;
		}

		let promise = this.model.editFrameClassement(accordionframe);
		promise.then(response => {
			if (response && !response.error) {
				var frame = this.frames.find(frame => frame.id == accordionframe.data.item.id)
				frame.classement = accordionframe.classement
				this.refreshFrameList = true;
			} else {
				throw response.errorMessage;
			}
		})
	}

	editGabaritClassement(accordionGabarit) {
		const default_classement = this.getMessage('default_classement');
		if (!accordionGabarit.classement) {
			accordionGabarit.classement = default_classement;
		}

		let promise = this.model.editGabaritClassement(accordionGabarit);
		promise.then(response => {
			if (response && !response.error) {
				var gabarit = this.gabarits.find(gabarit => gabarit.id == accordionGabarit.data.item.id)
				gabarit.classement = accordionGabarit.classement
			} else {
				throw response.errorMessage;
			}
		})
	}

	addZoneClasses(zone, classes) {
		let promise = this.model.addZoneClasses(zone.semantic.id_tag, {classes: classes, item: {
			"class": this.container.data.item.class,
			id: this.container.data.item.id
		}});
		promise.then(async (response) => {
			this.pages = await this.model.getPageList();
			this.gabarits = await this.model.getGabaritList();
		})
	}

	addFrameClasses(frame, classes) {
		let promise = this.model.addFrameClasses(frame.semantic.id_tag, {classes: classes, item: {
			"class": this.container.data.item.class,
			id: this.container.data.item.id
		}});
		promise.then(async (response) => {
			this.pages = await this.model.getPageList();
			this.gabarits = await this.model.getGabaritList();
		})
	}

	addFrameAttributes(frame, attributes) {
		let promise = this.model.addFrameAttributes(frame.semantic.id_tag, {attributes: attributes, item: {
			"class": this.container.data.item.class,
			id: this.container.data.item.id
		}});
		promise.then(async (response) => {
			this.pages = await this.model.getPageList();
			this.gabarits = await this.model.getGabaritList();
		})
	}

	addZoneAttributes(zone, attributes) {
		let promise = this.model.addZoneAttributes(zone.semantic.id_tag, {attributes: attributes, item: {
			"class": this.container.data.item.class,
			id: this.container.data.item.id
		}});
		promise.then(async (response) => {
			this.pages = await this.model.getPageList();
			this.gabarits = await this.model.getGabaritList();
		})
	}

	async addElementlayout(data, view = '') {
		this.showLoader();
		if (data.page_id) {
			var result = await this.model.addElementPageLayout(data.page_id, data);
			if (!result.error) {
				this.pages = await this.model.getPageList();
				var page = this.pages.find(page => page.id == data.page_id);
				if (view == "page") {
					this.reloadVue({
						type: view,
						name: page.name,
						item: page
					});
				} else if (view == "layout") {
					this.updateContainer({
						type: view,
						name: page.name,
						item: page
					});
				}
			}
		} else {
			var result = await this.model.addElementGabaritLayout(data.gabarit_id, data);
			if (!result.error) {
				this.gabarits = await this.model.getGabaritList();
				var gabarit = this.gabarits.find(gabarit => gabarit.id == data.gabarit_id);

				var pageFound = this.pages.find(page => page.gabarit_layout.id == gabarit.id);
				if (pageFound != undefined) {
					// le gabarit utilise des page on les mets à jours
					this.model.getPageList().then(pages => {
						this.pages = pages;
					});
				}

				if (view != "") {
					this.updateContainer({
						type: view,
						name: gabarit.name,
						item: gabarit
					});
				}
			}
		}
		this.hiddenLoader();
		return result;
	}

	getDefaultGabarit() {
		return this.gabarits.find(gabarit => gabarit.default == 1) ?? null;
	}

	async updatePages(pages) {
		this.showLoader();

		var valid = true;
		var success = {};
		for (var i = 0; i < pages.length; i++) {
			const page = pages[i];
			const result = await this.model.updatePage(page.id, page);
			success[page.id] = result.error ? false : true;
			valid &= result.error ? false : true;
		}

		if (valid) {
			this.pages = await this.model.getPageList();
		}

		this.hiddenLoader();
		return success;
	}

	async gabaritRemoveFrame(idGabarit, idTagFrame) {
		const response = await this.model.gabaritRemoveFrame(idGabarit, idTagFrame);
		if (!response.error) {
			this.gabarits = await this.model.getGabaritList();
		}
		return response;
	}

	async pageRemoveFrame(idPage, idTagFrame) {
		const response = await this.model.pageRemoveFrame(idPage, idTagFrame);
		if (!response.error) {
			this.pages = await this.model.getPageList();
		}
		return response;
	}

	haveElementOpac(element) {
		if(element.class.includes('Frame')) {
			if(element.class.includes('Opac')) {
				return true;
			}
		} else {
			for(let child of element.children) {
				if(child.class.includes('Opac')) {
					return true;
				}
				if(child.class.includes('Zone') && this.haveElementOpac(child)) {
					return true;
				}
			}
		}

		return false;
	}

	async duplicateGabarit(idGabarit) {
		this.showLoader();

		const find = this.gabarits.find(gabarit => gabarit.id == idGabarit);
		if (find == undefined) {
			return false;
		}

		const result = await this.model.duplicateGabarit(idGabarit);
		if (result.error) {
			this.hiddenLoader();
			return false;
		}

		this.gabarits = await this.model.getGabaritList();
		this.hiddenLoader();
		return true;
	}

	get properties () {
        return this.store.$data || {};
    }

    set properties (value) {
        throw "[CMS] don't do this !";
    }

    async removeLayout(element) {
		this.showLoader();

		await this.model.removeLayout(element);

		this.pages = await this.model.getPageList();
		this.gabarits = await this.model.getGabaritList();

		let gabarit = this.gabarits.find(gabarit => gabarit.id == element.gabarit);
		this.reloadVue({
			type: "gabarit",
			name: gabarit.name,
			item: gabarit
		});

		this.hiddenLoader();
	}

    async removePageLayout(element) {
		this.showLoader();

		await this.model.removePageLayout(element);

		this.pages = await this.model.getPageList();
		let page = this.pages.find(page => page.id == element.page);
		this.reloadVue({
			type: "page",
			name: page.name,
			item: page
		});

		this.hiddenLoader();
	}

	pageIsEdited (page) {
		var layouts = page.page_layout.layouts || {};
		if (page.parent_page != null) {
			var index = page.parent_page.class + "_" + page.parent_page.id
		} else {
			var index = page.gabarit_layout.class + "_" + page.gabarit_layout.id
		}
		return (layouts[index] && layouts[index].length > 0) ? true : false;
	}

	async fecthLayout(item) {
		this.showLoader();
		const layout = await this.model.fecthLayout(item);

		if (layout.error) {
			console.error(layout.errorMessage);
			this.hiddenLoader();
			return [];
		}

		if (item.class.toLowerCase().includes("page")) {
			const index = this.pages.findIndex(page => page.id == item.id);
			if (index != -1) this.pages[index].tree = layout;
		} else {
			const index = this.gabarits.findIndex(gabarit => gabarit.id == item.id);
			if (index != -1) this.gabarits[index].tree = layout;
		}

		this.hiddenLoader();
		return layout;
	}

	async shareLayout(item, zone) {

		if (!zone.class.toLocaleLowerCase().includes("zone")) {
			console.log(zone.class)
			return false;
		}

		const className = item.class.toLocaleLowerCase();
		if (!className.includes("page") && !className.includes("gabarit")) {
			console.log(className)
			return false;
		}

		this.showLoader();
		const result =  await this.model.shareLayout(item, zone);
		this.hiddenLoader();
		return result;
	}

	async frameRemove(frame) {
		this.showLoader();
		const result =  await this.model.frameRemove(frame.semantic.id_tag);
		this.pages = await this.model.getPageList();
		this.gabarits = await this.model.getGabaritList();
		this.hiddenLoader();
		return result;
	}

	async switchVersion(id_portal, id_version) {
		this.showLoader();
		const result = await this.model.switchVersion(id_portal, id_version);
		this.hiddenLoader();
		return result;
	}

	async renameVersion(id_version, name) {
		this.showLoader();
		const result = await this.model.renameVersion(id_version, name);
		this.hiddenLoader();
		return result;
	}

	async fetchVersions() {
		this.showLoader();
		const result = await this.model.getVersions();
		this.hiddenLoader();
		return result;
	}

	async cleanVersions() {
		this.showLoader();
		const result = await this.model.cleanVersions();
		this.hiddenLoader();
		return result;
	}
}

const CMS_PLUGIN = {
	install(Vue, options) {
		var cms = new Cms(Vue, options);
		cms.init();
		Vue.prototype.$cms = cms;
	}
};
export default CMS_PLUGIN;


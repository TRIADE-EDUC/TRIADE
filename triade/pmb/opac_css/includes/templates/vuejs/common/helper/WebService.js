/**
 * !WARNING! ici on met le contenu du POST dans une propriété
 * opacData car la globale $data existe déjà en OPAC
 */
class WebService extends EventTarget {

	constructor(url_base) {
		super();
		this.url = url_base;
	}

	get HTTP_POST() {
		return "POST";
	}

	get HTTP_GET() {
		return "GET";
	}

	async fetch(http_method, fetch_url, data) {

		this.dispatchFetch({
			http_method,
			fetch_url,
			data
		});

		if (!this.url) {
			const error = {
				error: true,
				errorMessage: "[Webservice] url not set !"
			};

			this.dispatchError(error);
			throw new Error(error.errorMessage);
		}

		let url = this.url + fetch_url;
		let init = {
			method: this.HTTP_GET,
			cache: 'no-cache'
		};

		if (http_method == this.HTTP_POST) {
			let post = new URLSearchParams();
			for (let prop in data) {
				if (typeof data[prop] == "boolean") {
					data[prop] = data[prop] ? 1 : 0;
				}
			}
			post.append("opacData", JSON.stringify(data));
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

			this.dispatchResponse({
				response_code: response.ok
			});
			return result;
		} catch (e) {
			const error = {
				error: true,
				errorMessage: e
			};

			this.dispatchError(error)
			return error;
		}
	}

	unloadOff() {
		window.onbeforeunload = '';
	}

	unloadOn() {
		window.onbeforeunload = null;
	}

	dispatchError(error) {
		this.unloadOff();
		this.dispatchEvent(new CustomEvent("error", {
			detail: error
		}));
	}

	dispatchFetch(options) {
		this.unloadOn();
		this.dispatchEvent(new CustomEvent("fetch", {
			detail: options
		}));
	}

	dispatchResponse(response) {
		this.unloadOff();
		this.dispatchEvent(new CustomEvent("response", {
			detail: response
		}));
	}

	post(route, action, data) {
		return this.fetch(this.HTTP_POST, `${route}/${action}`, data);
	}

	get(route, action, data) {
		return this.fetch(this.HTTP_GET, `${route}/${action}`, data);
	}
}

export default WebService;
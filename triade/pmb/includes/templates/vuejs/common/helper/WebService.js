import Cache from "./Cache.js";

class WebService extends EventTarget {
  constructor(url_base, cacheOptions) {
    super();
    this.url = url_base;
    if (cacheOptions) {
      this.cache = new Cache(cacheOptions);
    }
  }

  get HTTP_POST() {
    return "POST";
  }

  get HTTP_GET() {
    return "GET";
  }

  get cacheActive() {
    return this.cache ? true : false;
  }

  async fetch(http_method, fetch_url, data, isFileUpload = false) {
    this.dispatchFetch({
      http_method,
      fetch_url,
      data,
    });

    if (!this.url) {
      const error = {
        error: true,
        errorMessage: "[Webservice] url not set !",
      };

      this.dispatchError(error);
      throw new Error(error.errorMessage);
    }

    let url = this.url + fetch_url;
    let init = {
      method: this.HTTP_GET,
      cache: "no-cache",
    };

    if (http_method == this.HTTP_POST) {
      let post;
      if(!isFileUpload) {
        post = new URLSearchParams();
        for (let prop in data) {
          if (typeof data[prop] == "boolean") {
            data[prop] = data[prop] ? 1 : 0;
          }
        }
        post.append("data", JSON.stringify(data));
      } else {
        post = new FormData();
        for (let prop in data) {
          if (data[prop] instanceof File) {
            post.append(prop, data[prop]);
          }
        }
      }

      init["body"] = post;
      init["method"] = this.HTTP_POST;
    } else {
      url += "?";
      for (let prop in data) {
        url += "&" + prop + "=" + data[prop];
      }
    }

    try {
      let response = await fetch(url, init);
      let result = await response.json();
      if (result.error) {
        throw result.errorMessage;
      }

      if (!response.ok && response.status == 404) {
        throw "[Webservice] url not found !";
      }

      this.dispatchResponse({
        response_code: response.ok,
      });
      return result;
    } catch (e) {
      const error = {
        error: true,
        errorMessage: e,
      };

      this.dispatchError(error);
      return error;
    }
  }

  unloadOff() {
    window.onbeforeunload = "";
  }

  unloadOn() {
    window.onbeforeunload = function(e) {
      return true;
    };
  }

  dispatchError(error) {
    this.unloadOff();
    this.dispatchEvent(
      new CustomEvent("error", {
        detail: error,
      })
    );
  }

  dispatchFetch(options) {
    this.unloadOn();
    this.dispatchEvent(
      new CustomEvent("fetch", {
        detail: options,
      })
    );
  }

  dispatchResponse(response) {
    this.unloadOff();
    this.dispatchEvent(
      new CustomEvent("response", {
        detail: response,
      })
    );
  }

  _generateCacheKey() {
    return this.cacheActive ? JSON.stringify(arguments) : false;
  }

  post(route, action, data, isFileUpload = false) {
    return this.fetch(this.HTTP_POST, `${route}/${action}`, data, isFileUpload);
  }

  get(route, action, data) {
    const requestKey = this._generateCacheKey(
      this.HTTP_GET,
      `${route}/${action}`,
      data
    );
    if (requestKey && this.cache.hasItem(requestKey)) {
      return this.cache.getItem(requestKey);
    }

    const response = this.fetch(this.HTTP_GET, `${route}/${action}`, data);
    if (requestKey) {
      this.cache.add(requestKey, response);
    }
    return response;
  }
}

export default WebService;

import CacheItem from "./CacheItem.js";

class Cache extends EventTarget {

    constructor(options) {
        super();
        this._store = [];

        this._lifetime = options.lifetime;
        this._interval = setInterval(
            this._checkExpirationDate.bind(this),
            this._lifetime
        );
	}

    getItem(key) {
        const cache = this._store.find(item => item.key == key);
        return cache?.item?.get() ?? undefined;
    }

    getItems(keys) {
        return keys.map(key => this.getItem(key)) || [];
    }

    hasItem(key) {
        const cache = this._store.find(item => item.key == key);
        return cache?.item ? true : false;
    }

    delete(key) {
        const index = this._store.findIndex(item => item.key == key);
        if (index >= 0) {
            this._store.splice(index, 1);
            this._dispatchDelete(key);
        }
    }

    add(key, value) {
        const now = new Date();
        const item = new CacheItem();

        item.set(value);
        item.expiresAt(now.getTime() + this._lifetime);

        this._store.push({key, item});
    }

    _checkExpirationDate() {
        this._store.forEach((cache) => {
            if (cache.item.isExpired()) {
                this.delete(cache.key);
            }
        });
    }

    _dispatchDelete(key) {
		this.dispatchEvent(new CustomEvent("delete", {
			detail: { key: key }
		}));
	}

    clear() {
        this._store = [];
    }
}

export default Cache;
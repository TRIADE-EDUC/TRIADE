class CacheItem {

    constructor()
    {
        this._value = undefined;
        this._expiresAt = undefined;
    }

    get() {
        return this._value;
    }

    set(value) {
        this._value = value;
    }

    expiresAt(expiration) {
        this._expiresAt = expiration;
    }

    isExpired() {
        const now = new Date();
        return this._expiresAt < now.getTime();
    }
}

export default CacheItem;
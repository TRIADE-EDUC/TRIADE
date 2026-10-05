class Utils {
    static isEmptyObj(obj) {
        return Object.keys(obj).length === 0;
    }
    static capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1)
    }
}

export default Utils;
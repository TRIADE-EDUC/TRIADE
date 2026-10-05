export default class Const {

    /**
     * Classe de gestion de constantes
     * Ajouter un dossier "const" dans le module et un fichier "main.json"
     *
     * @param {string} moduleName - Nom du module Vuejs
     * @param {Array} additionalFiles - Liste des noms de fichiers additionnels dans le repertoire "const" du module
     * @return {void}
     */
    constructor(moduleName, additionalFiles = []) {
        let json = require('@/' + moduleName + '/const/main.json');
        Object.assign(this, json);

        this.additionalFiles = additionalFiles;

        for (let additionalFile of this.additionalFiles) {
            let additionalJson = require('@/' + moduleName + '/const/' + additionalFile + '.json');
            this[additionalFile] = { ...additionalJson };
        }
    }

    /**
     * Ajoute une constante a la classe
     * @param {string} key - Nom de la constante
     * @param {*} value - Valeur de la constante
     */
    add(key, value) {
        let splitedKey = key.split('.');
        if (splitedKey.length > 1) {
            let elt = this;
            for (let i = 0; i < splitedKey.length - 1; i++) {
                if (!this[splitedKey[i]]) {
                    this[splitedKey[i]] = {};
                }
                elt = this[splitedKey[i]];
            }
            if (!elt[splitedKey[splitedKey.length - 1]]) {
                elt[splitedKey[splitedKey.length - 1]] = value;
            }
        } else if (!this[key]) {
            this[key] = value;
        }
    }

    set(key, value) {
        return;
    }
}
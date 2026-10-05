import Messages from "./Messages.js";
import Images from "./Images.js";

class TranslationManager {
    constructor(tableName) {
        this.tableName = tableName;
        this.languages = null;
        this.translations = null;
        this.getLanguages();
        // this.messages = new Messages();
    }

    async getLanguages() {
        try {
            const response = await fetch('./ajax.php?module=ajax&categ=translations&action=get_languages');
            this.languages = await response.json();
        } catch (error) {
            console.error('Error fetching languages:', error);
        }
    }

    async getTranslations(num_field, table_name, field_name) {
        try {
            const response = await fetch('./ajax.php?module=ajax&categ=translations&action=get_translations&num_field='+num_field+'&table_name='+table_name+'field_name='+field_name);
            this.translations = await response.json();
            console.log(this.translations);
        } catch (error) {
            console.error('Error fetching translations:', error);
        }
    }

    loadTranslations(domNodeId) {
        this.getTranslations();
        this.buildFields(domNodeId);
    }

    buildFields(domNodeId) {
        if (!this.languages) {
            this.getLanguages();
        }
        if (this.languages.length) {
            document.getElementById(domNodeId).querySelectorAll("[data-translation-fieldname]").forEach(node => {
                const icon = node.insertAdjacentElement('afterend', this.createIcon(node));
                icon.insertAdjacentElement('afterend', this.createTranslationsContainer(node));
            });
        }
    }

    createButton(node) {
        const button = document.createElement('input');
        button.type = 'button';
        button.className = 'bouton';
        button.value = Messages.get('translation', 'translations');
        button.addEventListener('click', () => this.displayTranslations(node));
        return button;
    }

    createIcon(node) {
        const icon = document.createElement('img');
        icon.src = Images.get('translate.png'); // Remplace pmbDojo.images
        icon.title = Messages.get('translation', 'translations');
        icon.alt = Messages.get('translation', 'translations');
        icon.addEventListener('click', () => this.displayTranslations(node));
        return icon;
    }

    createTranslationLabel(node, language) {
        const label = document.createElement('label');
        label.innerHTML = language.label;
        label.className = 'etiquette';
        label.htmlFor = `${language.code}_${node.id}`;
        return label;
    }

    createTranslationField(node, lang) {
        const field = node.cloneNode(true);
        console.log(field);
        field.id = `${lang}_${node.id}`;
        field.name = `${lang}_${node.name}`;
        if (this.translations && this.translations[node.dataset.translationFieldname]?.[lang]) {
            field.value = this.translations[node.dataset.translationFieldname][lang];
        } else {
            field.value = '';
        }
        field.removeAttribute('data-translation-fieldname');
        field.removeAttribute('required');
        const wrapper = document.createElement('div');
        wrapper.className = 'row';
        wrapper.appendChild(field);
        return wrapper;
    }

    hasDisplayTranslation(node, language) {
        if (language.is_current_lang && this.translations) {
            const translatedValue = this.translations[node.dataset.translationFieldname]?.[language.code];
            if (translatedValue && node.value === translatedValue) {
                return false;
            }
        }
        return true;
    }

    createTranslationsContainer(node) {
        const translations = document.createElement('div');
        translations.id = `translations_${node.id}`;
        translations.className = 'row translations';
        translations.style.display = 'none';
        
        this.languages.forEach(language => {
            if (this.hasDisplayTranslation(node, language)) {
                translations.appendChild(this.createTranslationLabel(node, language));
                translations.appendChild(this.createTranslationField(node, language.code));
            }
        });
        return translations;
    }

    displayTranslations(node) {
        const translationsNode = document.getElementById(`translations_${node.id}`);
        translationsNode.style.display = translationsNode.style.display === 'block' ? 'none' : 'block';
    }
}

export default TranslationManager;
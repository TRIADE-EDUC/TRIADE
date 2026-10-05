<template>
	<div class="treeview">
        <nav class="treeview-navigation-nav" :aria-label="name">
            <ul class="treeview-navigation" role="tree" ref="treeNode">
                <treeview-item
                    v-for="(item, index) in tree"
                    :key="index" :index="index" :item="item"
                    @clickItem="onClickItem"
                    @keydownItem.stop="keydownItem">
                </treeview-item>
            </ul>
        </nav>
        <section class="treeview-content" ref="content"><slot name="content"></slot></section>
    </div>
</template>

<script>
    import VueComponent from 'vue'; // import utilise pour JSDoc
    import treeviewItem from './treeviewItem.vue';

	export default {
        name: 'treeview',
        components: { treeviewItem },
        props: {
            name: {
                type: String,
                default: () => '',
            },
            tree: {
                type: Array,
                default: () => [],
            },
        },
        computed: {
            /**
             * Retourne le dernier item visible
             *
             * @returns {VueComponent} Instance de treeview-item
             */
            lastChildrenVisible: function () {
                let treeviewItem = this.$children[this.tree.length - 1];
                while (treeviewItem.item.children && treeviewItem.expanded) {
                    treeviewItem = treeviewItem.$children[item.children.length - 1];
                }

                return treeviewItem;
            },

            /**
             * Retourne l'index de l'item courant
             *
             * @returns {Number|null} Index de l'item courant ou null
             */
            currentIndexItem : function () {
                for (const index in this.tree) {
                    if (this.tree[index].current) {
                        return index;
                    }
                }
                return null;
            }
        },
        methods: {
            /**
             * Envoi un evenement de clickItem et met le focus sur le contenu
             *
             * @param {Object} event Object contenant l'item clique
             * @returns {void}
             */
            onClickItem: function (event) {
                this.$refs.content.focus();
                this.$emit('clickItem', event);
            },

            /**
             * Permet d'ouvrir tous les enfants (non recursivement)
             *
             * @returns {void}
             */
            expandAllChildren: function () {
                for (const index in this.$children) {
                    this.$children[index].expand();
                }
            },

            /**
             * Permet de gerer les evenements clavier
             *
             * @param {Event} event Evenement clavier
             * @returns {void}
             */
            keydownItem: function (event) {
                if (event.altKey || event.ctrlKey || event.metaKey || !event.treeviewItem ) {
                    return;
                }

                this.keydownManager(event);
            },

            /**
             * Envoi un evenement de clickItem
             *
             * @param {Object} item Item a envoyer
             * @returns {void}
             */
            dispatchItemClick: function (item) {
                this.$emit('clickItem', { item: item });
            },

            /**
             * Permet de savoir si la chaine est un caractere imprimable
             *
             * @param {String} str Chaine a tester
             * @returns {void}
             */
            isPrintableCharacter: function(str) {
               return str.length === 1 && str.match(/\S/);
            },

            /**
             * Permet de trouver le premier item par son premier caractere
             *
             * @see https://www.w3.org/WAI/ARIA/apg/patterns/treeview/examples/treeview-navigation/#kbd_label
             * @param {VueComponent} treeviewItem Instance de treeview-item
             * @param {String} char Caractere a rechercher
             * @param {Boolean} firstSearch (optional, default: false) Permet de savoir qu'on est sur la premiere recherche
             * @returns {void}
             */
            searchFirstItemByFirstCharacter: function (treeviewItem, char, firstSearch = false) {
                if (!['treeview', 'treeview-item'].includes(treeviewItem.$options.name)) {
                    return null;
                }

                if (treeviewItem.$children.length > 0) {
                    for (const index in treeviewItem.$children) {
                        const childTreeviewItem = treeviewItem.$children[index];
                        if (char === childTreeviewItem.item.name[0].trim().toLowerCase()) {
                            return childTreeviewItem;
                        }

                        const result = this.searchFirstItemByFirstCharacter(childTreeviewItem, char);
                        if (result) {
                            return result;
                        }
                    }
                }

                if (firstSearch) {
                    for (const index in treeviewItem.$parent.$children) {
                        if (index <= treeviewItem.index) {
                            continue;
                        }

                        const childTreeviewItem = treeviewItem.$parent.$children[index];
                        if (char === childTreeviewItem.item.name[0].trim().toLowerCase()) {
                            return childTreeviewItem;
                        }

                        const result = this.searchFirstItemByFirstCharacter(childTreeviewItem, char);
                        if (result) {
                            return result;
                        }
                    }

                    for (const index in this.$children) {
                        const childTreeviewItem = this.$children[index];
                        if (char === childTreeviewItem.item.name[0].trim().toLowerCase()) {
                            return childTreeviewItem;
                        }

                        const result = this.searchFirstItemByFirstCharacter(childTreeviewItem, char);
                        if (result) {
                            return result;
                        }
                    }
                }
                return null;
            },

            /**
             * Gestion des evenements clavier (Pour le RGAA)
             *
             * @see https://www.w3.org/WAI/ARIA/apg/patterns/treeview/examples/treeview-navigation/#kbd_label
             * @param {Event} event Evenement clavier
             * @returns {void}
             */
            keydownManager: function (event) {
                const treeviewItem = event.treeviewItem;
                const key = event.key;
                let flag = false;

                switch (key) {
                    case "Up":
                    case "ArrowUp":
                        // On place le focus sur l'item precedent
                        // S'il n'y en a pas, on fait rien
                        treeviewItem.instance.previousSibling?.focus();
                        flag = true;
                        break;

                    case "Down":
                    case "ArrowDown":
                        // On place le focus sur l'item suivant
                        // S'il n'y en a pas, on fait rien
                        treeviewItem.instance.nextSibling?.focus();
                        flag = true;
                        break;

                    case "Right":
                    case "ArrowRight":
                        // Si le noeud focus est ferme, on l'ouvre
                        // Si le noeud focus est ouvert, on met le focus sur l'item suivant (le primier enfant)
                        // Si on est focus sur le dernier enfant, on fait rien
                        if (treeviewItem.item.children) {
                            if (treeviewItem.instance.expanded) {
                                treeviewItem.instance.nextSibling?.focus();
                            } else {
                                treeviewItem.instance.expand();
                            }
                        }
                        flag = true;
                        break;

                    case "Left":
                    case "ArrowLeft":
                        // Si le noeud focus est ouvert, on le ferme
                        // Si le noeud focus est un enfant, on met le focus sur le parent
                        // Si on est focus sur un enfant a la racine, on fait rien
                        if (treeviewItem.item.children && treeviewItem.instance.expanded) {
                            treeviewItem.instance.collapse();
                            flag = true;
                        } else if (
                            treeviewItem.instance.$parent.$options.name === 'treeview-item' &&
                            !treeviewItem.item.children
                        ) {
                            treeviewItem.instance.$parent.focus();
                            flag = true;
                        }
                        break;

                    case "Home":
                        // On place le focus sur le premier item
                        this.$children[0].focus();
                        flag = true;
                        break;

                    case "End":
                        // On place le focus sur le dernier item
                        this.lastChildrenVisible.focus();
                        flag = true;
                        break;

                    default:
                        if (this.isPrintableCharacter(key)) {
                            if (key == "*") {
                                // * (asterisk) On ouvre tous les enfants du meme niveau que l'item focus
                                // Sans déplacer le focus
                                treeviewItem.instance.$parent.expandAllChildren();
                                flag = true;
                            } else {
                                // a-z, A-Z
                                // On deplace le focus sur l'item dont le nom commence par le caractere saisi.
                                const treeviewItemFound = this.searchFirstItemByFirstCharacter(treeviewItem.instance, key, true);
                                if (treeviewItemFound) {
                                    treeviewItemFound.focus();
                                    flag = true;
                                }
                            }
                        }
                        break;
                }

                if (flag) {
                    event.stopPropagation();
                    event.preventDefault();
                }
            }
        }
    }
</script>
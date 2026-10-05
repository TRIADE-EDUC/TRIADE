<template>
    <li role="none">
        <a
            role="treeitem"
            ref="treeitem"
            v-bind="attrs"
            @click.stop="onLinkClick"
            @keydown="onKeydown">
            <span class="label">
                <span class="icon" v-if="item.children">
                    <span :class="['fa', expanded ? 'fa-chevron-down' : 'fa-chevron-right']" aria-hidden="true" @click.stop="onIconClick"></span>
                </span>
                {{ item.name }}
            </span>
        </a>
        <ul :id="owns" role="group" :aria-label="item.name"  v-if="item.children" :style="{ display: expanded ? 'block' : 'none' }">
            <treeview-item
                v-for="(child, index) in item.children"
                :key="index" :index="index" :item="child"
                @clickItem="$emit('clickItem', $event)"
                @keydownItem="$emit('keydownItem', $event)">
            </treeview-item>
        </ul>
    </li>
</template>

<script>
    import VueComponent from 'vue'; // import utilise pour JSDoc

	export default {
        name: 'treeview-item',
        props: {
            index: {
                type: Number,
                required: true
            },
            item: {
                type: Object,
                required: true
            }
        },
        data: function () {
            return {
                expanded: false
            }
        },
        computed: {
            /**
             * Retourne l'item precedant
             *
             * @returns {VueComponent} Instance de treeview-item
             */
            previousSibling: function () {
                let previousSibling = this.$parent.$children[this.index - 1] ?? null;
                if (previousSibling) {
                    while (previousSibling?.item?.children && previousSibling.expanded) {
                        // On cherche le premier item precedent visible
                        previousSibling = previousSibling.$children[previousSibling.$children.length - 1];
                    }
                } else if (this.$parent.$options.name === 'treeview-item') {
                    // Aucun item precedant visible, si mon parent est un treeview-item
                    // On passe sur le parent
                    previousSibling = this.$parent;
                }
                return previousSibling;
            },

            /**
             * Retourne l'item suivant
             *
             * @returns {VueComponent} Instance de treeview-item
             */
            nextSibling: function () {
                if (this.item.children && this.expanded) {
                    // Treeview-item ouvert, l'item suivant est le premier enfant
                    return this.$children[0];
                }

                let nextSibling = this.$parent.$children[this.index + 1] ?? null;
                if (nextSibling) {
                    // On prend l'item suivant en fonction du parent
                    return nextSibling;
                }

                // On a aucun enfant et le parent n'a pas d'enfant suivant
                // On va chercher le premier item suivant
                let child = this;
                let parent = this.$parent;
                while (parent.$children[child.index + 1] === undefined) {
                    child = parent;
                    parent = parent.$parent;

                    if (parent.$options.name === 'treeview') {
                        // On va trop loin, on sort
                        break;
                    }
                }

                return parent.$children[child.index + 1];
            },

            /**
             * Retourne la valeur de l'attribut aria-owns
             *
             * @returns {String}
             */
            owns: function () {
                let name = this.item.name;
                name = name.toLowerCase();
                name = name.replace(/[^a-zA-Z0-9]/g, '-');

                return `id-${name}-subtree`;
            },

            /**
             * Retourne les attributs pour l'element [role="treeitem"]
             *
             * @returns {Object}
             */
            attrs: function () {
                let attrs = {
                    href: this.item.link ?? '#'
                };

                if (this.item.current) {
                    attrs['aria-current'] = 'page';
                    attrs['tabindex'] = '0';
                } else {
                    attrs['tabindex'] = '-1';
                }

                if (this.item.children) {
                    attrs['aria-owns'] = this.owns;
                    attrs['aria-expanded'] = this.expanded;
                }

                return attrs;
            }
        },
        methods: {
            /**
             * Permet de mettre le focus sur l'element [role="treeitem"]
             *
             * @returns {void}
             */
            focus: function() {
                this.$refs.treeitem.focus();
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
             * Permet d'ouvrir l'item
             *
             * @returns {void}
             */
            expand: function () {
                this.expanded = true;
            },

            /**
             * Permet de fermer l'item
             *
             * @returns {void}
             */
            collapse: function () {
                this.expanded = false;
            },

            /**
             * Envoi un evenement de clickItem
             *
             * @returns {void}
             */
            dispatchItemClick: function () {
                this.$emit('clickItem', { item: this.item });
            },

            /**
             * Gestion du click sur l'icone
             *
             * @returns {void}
             */
            onIconClick: function () {
                if (this.expanded) {
                    this.collapse();
                } else {
                    this.expand();
                }
            },

            /**
             * Gestion du click sur le lien
             *
             * @returns {void}
             */
            onLinkClick: function () {
                this.dispatchItemClick();
            },

            /**
             * Permet de gerer les evenements clavier
             *
             * @param {Event} event Evenement clavier
             * @returns {void}
             */
            onKeydown: function (event) {
                if (event.altKey || event.ctrlKey || event.metaKey) {
                    return;
                }

                if (
                    (event.key === 'Enter' || event.keyCode === 13) ||
                    (event.key === ' ' || event.keyCode === 32)
                ) {
                    this.dispatchItemClick();

                    event.stopPropagation();
                    event.preventDefault();
                    return;
                }

                // On ajout dans l'evenement l'instance de treeviewItem
                const INSTANCE_VUE_JS = this;
                event.treeviewItem = {
                    item: this.item,
                    get instance() { return INSTANCE_VUE_JS; },
                    set instance(_) { throw new Error("instance is read-only"); }
                };

                this.$emit('keydownItem', event);
            },
        }
    }
</script>
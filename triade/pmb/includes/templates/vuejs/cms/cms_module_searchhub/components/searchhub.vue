<template>
    <div class="search-hub">
        <treeview :tree="tree" @clickItem="itemClick">
            <template v-slot:content>
                <profileform v-if="currentProfile" :profile="currentProfile"></profileform>
            </template>
        </treeview>
    </div>
</template>

<script>
    import treeview from '@/common/components/treeview.vue';
    import profileform from './profileform.vue';

	export default {
        name: 'search-hub',
        props: ['profiles'],
        components: { treeview, profileform },
        data: function () {
            return {
                currentProfile: null,
                emptyProfile: { id: 0, name: '', searches: [] }
            }
        },
        mounted: function() {
            this.loadCurrentProfile();
            window.addEventListener('hashchange', this.loadCurrentProfile.bind(this));
        },
		updated: function() {
			if (typeof domUpdated === "function") {
			    domUpdated();
			}
		},
        computed: {
            /**
             * Arbre des profils
             *
             * @return {Object[]} Arbre des profils
             */
            tree: function () {
                let tree = [];

                if (this.profiles) {
                    for (const index in this.profiles) {
                        const profile = this.profiles[index];
                        tree.push({
                            profile: this.clone(this.profiles[index]),
                            name: profile.name,
                            link: `#profile-${profile.id}`,
                            current: this.currentProfile?.id === this.profiles[index].id
                        })
                    }
                }

                tree.push({
                    profile: this.clone(this.emptyProfile),
                    link: `#profile-new`,
                    name: this.cmsMessages['cms_module_searchhub_add_profile'],
                    current: this.currentProfile?.id === 0
                });

                return tree;
            }
        },
        methods: {
            /**
             * Clone un objet
             *
             * @param {Object} obj
             */
            clone: function(obj) {
                return JSON.parse(JSON.stringify(obj));
            },

            /**
             * Handler du clic sur un item du treeview
             *
             * Met  a jour le profil courant
             *
             * @param {Object} event - evenement du clic
             */
            itemClick: function (event) {
                this.currentProfile = event.item.profile;
            },

            /**
             * Charge le profil courant depuis l'url
             *
             * @returns {void}
             */
            loadCurrentProfile: function () {
                const hash = window.location.hash;
                if (hash && hash.startsWith('#profile-')) {
                    const id = hash.replace('#profile-', '');
                    if (id === 'new') {
                        this.currentProfile = this.clone(this.emptyProfile);
                    } else {
                        const profil = this.profiles.find(p => p.id === parseInt(hash.replace('#profile-', '')));
                        if (profil && this.currentProfile?.id !== profil.id) {
                            this.currentProfile = this.clone(profil);
                        }
                    }
                }
            }
        }
    }
</script>
<template>
    <div>
        <div class="row mb-s">
            <div class="colonne3">
                <label class="etiquette" for="search-settings-cms-page">
                    {{ cmsMessages['cms_module_searchhub_profile_search_cms_page'] }}
                </label>
            </div>
            <div class="colonne-suite">
                <select id="search-settings-cms-page" v-model="search.settings.page" name="page" class="saisie-80em" required>
                    <option value="">{{ cmsMessages['cms_module_searchhub_profile_search_cms_page_none'] }}</option>
                    <option v-for="page in cmsPages" :key="page.id" :value="page.id">{{ page.label }}</option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["search"],
        data: function() {
            return {
                cmsPages: [],
                selectedPage: '',
            }
        },
		updated: function() {
		},
        mounted: function() {
            this.$set(this.search.settings, 'page', this.search.settings.page || '');
            this.initCmsPages();
        },
        computed: {
        },
        methods: {
            /**
             * Recupere les pages de portail
             *
             * @return {void}
             */
             initCmsPages: function() {
                fetch(this.urlWebservice+"&do=get_cms_pages", {
                    method: 'GET',
                }).then((response)=> {
                    if (response.ok) {
                        response.json().then((jsonContent) => {
                            this.cmsPages = jsonContent;
                        })
                    } else {
                        console.log("error from response : " + response);
                    }
                    this.hiddenLoader();
                }).catch((error) => {
                    console.log("error during fetch : " + error.message);
                    this.hiddenLoader();
                });
            },
        }
    }

</script>
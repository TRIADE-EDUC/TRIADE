<template>
    <div class="dsi-form-selector">
        <div class="dsi-form-group">
            <label for="sectionList">{{ messages.get('dsi', 'source_form_label_articles_by_parent_section') }}</label>
            <div class="dsi-form-group-content">
                <select name="sectionList" v-model.number="data.sectionId">
                    <option value="0" disabled>
                        {{ messages.get('dsi', 'source_form_selector_articles_by_parent_section') }}
                    </option>
                    <option v-for="(section, index) in sectionList"
                        :key="index"
                        :value="section.value"
                        v-html="section.label">
                    </option>
                </select>
            </div>
        </div>
        <div v-show="data.sectionId > 0">
            <slot name="trySearch"></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        data: {
            type: Object,
            default: () => {
                return {
					sectionId: 0
                }
            }
        }
    },
    data: function () {
        return {
            sectionList: [],
        }
    },
    created() {
        if (!this.data.sectionId) {
            this.$set(this.data, "sectionId", 0);
        }

        this.getSectionList();
    },
    methods: {
        getSectionList: async function () {
            let response = await this.ws.get('items', 'getSectionList');
            if (response.error) {
                this.notif.error(this.messages.get('dsi', response.errorMessage));
            } else {
                this.sectionList = response;
            }
        }
    }
}
</script>


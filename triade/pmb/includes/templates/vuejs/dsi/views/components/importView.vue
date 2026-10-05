<template>
    <form>
        <select v-model="selectedView">
            <option value="" disabled>{{ messages.get('dsi', 'view_wysiwyg_select_view') }}</option>
            <option v-for="(view, index) in filteredList" :key="index" :value="view">{{ view.name }}</option>
        </select>
        <button class="dsi-button bouton" type="button" @click="importView">{{ messages.get('dsi', 'view_wysiwyg_import_view') }}</button>
    </form>
</template>

<script>
export default {
    props : ["parentViewId"],
    data : function() {
        return {
            list : [],
            selectedView : "",
            view : {}
        }
    },
    created : async function() {
        this.list = await this.ws.get("views", "getModels");
    },
    computed : {
        filteredList: function() {
            return this.list.filter(v => v.type == 2);
        }
    },
    methods : {
        importView : async function() {
            let clonedView = JSON.parse(JSON.stringify(this.selectedView));

            clonedView.id = 0;
            clonedView.name = "";
            clonedView.model = 0;
            clonedView.numParent = this.parentViewId;
            // clonedView.numModel = this.selectedView.id;

            this.$set(this, "view", clonedView);

            let response = await this.ws.post("views", "save", this.view);
            if(! response.error) {
                this.$emit("addView", response);
            }
        }
        
    }
}
</script>
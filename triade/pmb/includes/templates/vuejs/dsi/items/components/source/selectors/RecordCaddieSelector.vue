<template>
    <div class="dsi-form-group">
        <label>{{ msg.select_caddie }}</label>
        <div class="dsi-form-group-content">
            <select v-model="data.caddieId">
                <option value="" disabled>{{ msg.select_caddie_default }}</option>
                <option v-for="(caddie, i) in recordCaddies" :value="caddie.idcaddie" :key="i">{{ caddie.name }}</option>
            </select>
        </div>
    </div>
</template>

<script>
export default {
    name : "RecordCaddieSelector",
    props : ["data", "msg"],
    data : function() {
        return {
            recordCaddies : []
        }
    },
    created : async function() {
        this.recordCaddies = await this.ws.get("items", "record/caddies");
    }
}
</script>
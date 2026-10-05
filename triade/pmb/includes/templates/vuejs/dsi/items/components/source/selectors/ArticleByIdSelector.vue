<template>
    <div class="dsi-form-selector">
        <div class="dsi-form-group">
            <label class="etiquette" for="view-name" v-if="moduleMsg.ArticleByIdSelector">{{ moduleMsg.ArticleByIdSelector.selectorByIdDesc }}</label>
            <div class="dsi-form-group-content">
                <input v-model="data.articleId" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props : ["data", "moduleMsg"]
}
</script>
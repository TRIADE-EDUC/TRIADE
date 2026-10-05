<template>
    <div>
        <div class="dsi-form-group dsi-form-wysiwyg">
            <label class="etiquette" for="wysiwyg-patterns">{{ messages.get('dsi', 'label_pattern') }}</label>
            <div class="dsi-form-group-content">
                <div class="dsi-form-group-line">
                    <select name="wysiwyg-patterns" id="wysiwyg-patterns" class="dsi-patterns" v-model="pattern">
                        <option value="">{{ messages.get('common', 'common_default_select') }}</option>
                        <optgroup :label="group" v-for="(patterns, group) in filteredPatterns" :key="group">
                            <option v-for="(label, pattern) in patterns" :value="pattern" :key="pattern">
                                {{ label }}
                            </option>
                        </optgroup>
                    </select>
                    <input type="button" class="bouton" :value="messages.get('common', 'common_insert_pattern')" @click="addPattern"/>
                </div>
            </div>
        </div>

        <editor
            :id="uniqueId"
            v-model="tempContent" 
            :init="configEditor">
        </editor>
    </div>
</template>

<script>
    import tinymce from 'tinymce';

    import 'tinymce/icons/default';
    import 'tinymce/themes/silver';
    import 'tinymce/skins/ui/oxide/skin.css';
    import 'tinymce/models/dom';

    import 'tinymce/plugins/advlist';
    import 'tinymce/plugins/code';
    import 'tinymce/plugins/link';
    import 'tinymce/plugins/lists';
    import 'tinymce/plugins/table';
    import 'tinymce/plugins/insertdatetime';
    import 'tinymce/plugins/image';
    import 'tinymce/plugins/media';
    import 'tinymce/plugins/fullscreen';
    import 'tinymce/plugins/link';
    import 'tinymce/plugins/nonbreaking';
    import 'tinymce/plugins/preview';
    import 'tinymce/plugins/searchreplace';
    import 'tinymce/plugins/directionality';
    import 'tinymce/plugins/visualchars';
    import 'tinymce/plugins/anchor';
    import 'tinymce/plugins/charmap';

    import editor from "@tinymce/tinymce-vue";

    export default {
        props: ["content", "patterns"],
        components: {
            editor
        },
        data: function() {
            return {
                uniqueId: this.helper.generateRandomString(10),
                tempContent: this.content,
			    pattern: "",
			    dynamicValue : "",
                configEditor: {
                    height: 500,
                    entity_encoding : 'raw',
                    convert_urls : false,
                    language: 'fr_FR',
                    language_url : './javascript/tinymce/fr_FR.js',
                    toolbar: ['undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify outdent indent | bullist numlist | forecolor backcolor | fontsizeselect | fontselect',
                            'link image | anchor | fullscreen preview code'],
                    plugins: "table insertdatetime image media link nonbreaking preview searchreplace directionality visualchars code anchor fullscreen charmap lists advlist",
                    browser_spellcheck : false,
                    removed_menuitems: 'newdocument',
                    skin: false,
                    content_css: false,
                    formats: {
                        p: { block: 'p', styles: { 'margin': '0' }, exact: true },
                    },
                    promotion: false,
                    fullscreen_native: true
                }
            }
        },
        watch: {
            tempContent: function() {
                this.$emit("changeContent", this.tempContent);
            }
        },

        computed: {
            filteredPatterns: function() {
                if(! Object.keys(this.patterns).length) {
                    return [];
                }
                let result = {};
                for(let pattern in this.patterns) {
                    if(pattern != this.Const.views.dynamicGroupsKey) {
                        result[pattern] = this.patterns[pattern];
                    }
                }
                return result;
            }
        },
        methods: {
            addPattern: function() {
                if(this.patterns["dynamicGroups"][this.pattern] && this.dynamicValue) {
                    this.tempContent += this.pattern.substring(0, this.pattern.length-2) + `_${this.dynamicValue}!!`;
                } else {
                    this.tempContent += this.pattern;
                }

                this.pattern = "";
            }
        }
    }
</script>
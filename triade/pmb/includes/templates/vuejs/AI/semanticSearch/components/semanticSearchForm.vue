<template>
    <div id="searchSemanticForm" class="searchSemanticForm">
        <form class="form-admin" action="" method="POST">
            <h3 v-if="'add' == action">
                {{ messages.get("ai_search_semantic", "admin_ai_search_semantic_add") }}
            </h3>
            <h3 v-else>
                {{ messages.get("ai_search_semantic", "admin_ai_search_semantic_modifify") }}
            </h3>

            <div class='form-contenu'>
                <div class='row'>
                    <div class="colonne4">
                        <label for="name">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_name") }}* :</label>
                    </div>
                    <div class="colonne_suite">
                        <input id="name" class="saisie-80em" type="text" name="name" v-model="data.settings.name" />
                    </div>
                </div>
                <div class='row'>
                    <div class="colonne4">
                        <label for="caddieDetail">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_caddies_list") }}* :</label>
                    </div>
                    <div class="colonne_suite">
                        <select id="caddieDetail" name="caddieDetail" class="saisie-80em" v-model="data.settings.caddie_id">
                            <option value="0">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_caddies_list_choice") }}</option>
                            <option v-for="(caddie, index) in caddieslist" :key="index"
                                :value="caddie.idcaddie">{{ caddie.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class='row'>
                    <div class="colonne4">
                        <div class="row">
                            <label for="ws_server_python">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_python_server") }}* :</label>
                        </div>
                        <div class="row">
                            <span>{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_python_server_comment") }}</span>
                        </div>
                    </div>
                    <div class="colonne_suite">
                        <input class="saisie-80em" id="ws_server_python" type="text" name="ws_server_python" v-model.trim="data.settings.url_server_python" />
                    </div>
                </div>
                <div class='row'>
                    <div class="colonne4">
                        <label for="token">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_python_token") }}* :</label>
                    </div>
                    <div class="colonne_suite">
                        <input class="saisie-80em" id="token" type="text" name="token" v-model.trim="data.settings.token" />
                    </div>
                    <div class="colonne_suite">
                        <input @click="checkToken" class="bouton" type="button"
                            :value="messages.get('ai_search_semantic', 'admin_ai_search_semantic_check_token')" />
                    </div>
                </div>
                <div class='row'>
                    <div class="colonne4">
                        <label for="prompt_system">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_system_python_server") }} * :</label>
                    </div>
                </div>
                <div class="row">
                    <div class="colonne4" id="django_tree_system"></div>
                    <textarea id="prompt_system" name="prompt_system" :value="data.settings.prompt_system"></textarea>
                </div>
                <div class='row'>
                    <div class="colonne4">
                        <label for="prompt_user">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_user_python_server") }} * :</label>
                    </div>
                </div>
                <div class="row">
                    <div class="colonne4" id="django_tree_user"></div>
                    <textarea id="prompt_user" name="prompt_user" :value="data.settings.prompt_user" />
                </div>
                <div class='row'>
                    <div class="colonne4">
                        <label for="prompt_system_tips">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_system_tips") }} * :</label>
                    </div>
                </div>
                <div class="row">
                    <div class="colonne4" id="django_tree_system_tips"></div>
                    <textarea id="prompt_system_tips" name="prompt_system_tips" :value="data.settings.prompt_system_tips"></textarea>
                </div>
                <div class='row'>
                    <div class="colonne4">
                        <label for="min_score">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_slider") }} * :</label>
                    </div>
                    <div class="colonne_suite">
                        <input type="range" id="min_score" name="min_score" min="1" max="100" v-model.number="data.settings.min_score" class="ai_slider">
                    </div>
                    <div class="colonne_suite">
                        <span class="right">{{ data.settings.min_score }} %</span>
                    </div>
                </div>
                <div class='row'>
                    <div class="colonne4">
                        <label>{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_choice_indexation") }} * :</label>
                    </div>
                    <div class="colonne_suite">
                        <label for="indexation_choice_docnum">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_choice_indexation_label_docnum") }}</label>
                        <input type="checkbox" id="indexation_choice_docnum" name="indexation_choice_docnum" v-model="data.settings.indexation_choice.docnum" value="1" />
                        <label for="indexation_choice_summary">{{ messages.get("ai_search_semantic", "admin_ai_search_semantic_choice_indexation_label_notice") }}</label>
                        <input type="checkbox" id="indexation_choice_summary" name="indexation_choice_summary" v-model="data.settings.indexation_choice.summary" value="1" />
                    </div>
                </div>
                <div class='row'>
                    <br />
                    <div class="left">
                        <input @click="cancel" class="bouton" type="button"
                            :value="messages.get('ai_search_semantic', 'admin_ai_search_semantic_cancel')" />
                        <input @click="submit" class="bouton" type="button"
                            :value="messages.get('ai_search_semantic', 'admin_ai_search_semantic_save')" />
                        </div>
                        <div class="right" v-if="data.id">
                            <input @click="emptyContainer" class="bouton" type="button"
                                :value="messages.get('ai_search_semantic', 'admin_ai_search_semantic_empty_container')" />
                            <input @click="deleteSemanticSearch" class="bouton" type="button"
                                :value="messages.get('ai_search_semantic', 'admin_ai_search_semantic_delete')" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    props: ["action", "semanticsearch", "caddieslist"],
    data: function () {
        return {
            data: {
                id: null,
                settings: {
                    name: "",
                    caddie_id: 0,
                    min_score: 50,
                    indexation_choice: {
                        docnum: false,
                        summary: false,
                    },
                    url_server_python: "",
                    prompt_system: "",
                    prompt_system_tips: "",
                    prompt_user: "",
                    token: "",
                },
            },
            searchMethod: [],
        };
    },
    created() {
        if (typeof this.semanticsearch.id_ai_setting !== "undefined" && 0 !== this.semanticsearch.id_ai_setting) {
            this.data.id = parseInt(this.semanticsearch.id_ai_setting);
            this.data.settings = JSON.parse(this.semanticsearch.settings_ai_settings);
        } else {
            this.data.settings.prompt_system = this.messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_system_text");
            this.data.settings.prompt_system_tips = this.messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_system_tips_text");
            this.data.settings.prompt_user = this.messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_user_question_text");
        }
    },
    mounted() {
        this.$nextTick(function () {
            pmbDojo.aceManager.initEditor("prompt_user");
            pmbDojo.aceManager.initEditor("prompt_system");
            pmbDojo.aceManager.initEditor("prompt_system_tips");

            const systemEditor = pmbDojo.aceManager.getEditor("prompt_system");
            if (systemEditor) {
                systemEditor.session.on('change', (_, systemEditor) => {
                    this.$set(this.data.settings, "prompt_system", systemEditor.getValue());
                });
            }

            const systemTipsEditor = pmbDojo.aceManager.getEditor("prompt_system_tips");
            if (systemTipsEditor) {
                systemTipsEditor.session.on('change', (_, systemTipsEditor) => {
                    this.$set(this.data.settings, "prompt_system_tips", systemTipsEditor.getValue());
                });
            }

            const userEditor = pmbDojo.aceManager.getEditor("prompt_user");
            if (userEditor) {
                userEditor.session.on('change', (_, userEditor) => {
                    this.$set(this.data.settings, "prompt_user", userEditor.getValue());
                });
            }

            window.dispatchEvent(new CustomEvent("startTree"));
        });
    },
    methods: {
        cancel: function () {
            document.location = './admin.php?categ=ai&sub=semantic_search';
        },

        deleteSemanticSearch: async function () {
            if (this.data.id && !confirm(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_delete_confirm"))) {
                return false;
            }

            let response = await fetch('./admin.php?categ=ai&sub=semantic_search&action=delete&id=' + this.data.id, {});

            if (response) {
                document.location = './admin.php?categ=ai&sub=semantic_search';
            } else {
                console.log("Error");
            }
        },

        emptyContainer: async function () {
            if (!this.checkForm("emptyContainer") || !confirm(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_empty_store_confirm"))) {
                return false;
            }

            const response = await this.ws.post('container', 'clean', {
                id: this.data.id,
            });
            if (response.success) {
                alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_empty_store_success"));
            }
        },

        checkToken: async function () {
            if (!this.checkForm("url_server_python")) {
                return false;
            }

            if (!this.checkForm("checkToken")) {
                return false;
            }

            const response = await this.ws.post('check_token', '', {
                token: this.data.settings.token,
                url_server_python: this.data.settings.url_server_python
            });
            if (response.is_valid) {
                alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_check_token_success"));
            } else {
                alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_check_token_failed"));
            }
        },

        submit: async function () {
            if (!this.checkForm()) {
                return false;
            }

            let formData = new FormData();
            formData.append('data', JSON.stringify(this.data));

            let response = await fetch('./admin.php?categ=ai&sub=semantic_search&action=save', {
                method: 'POST',
                body: formData,
            });

            if (response) {
                document.location = './admin.php?categ=ai&sub=semantic_search';
            } else {
                console.log("Error");
            }
        },

        checkForm: function (type = "form") {
            switch (true) {
                case !this.data.id && type == "emptyContainer":
                    return false;
                case !this.data.settings.name && type == "form":
                    alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_name_error"));
                    return false;
                case !this.data.settings.caddie_id && type == "form":
                    alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_caddies_list_error"));
                    return false;
                case !this.data.settings.url_server_python:
                    alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_python_server_error"));
                    return false;
                case !this.data.settings.token:
                    alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_token_error"));
                    return false;
                case !this.data.settings.prompt_system && type == "form":
                    alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_system_python_server_error"));
                    return false;
                case !this.data.settings.prompt_system_tips && type == "form":
                    alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_system_tips_error"));
                    return false;
                case !this.data.settings.prompt_user && type == "form":
                    alert(this.messages.get("ai_search_semantic", "admin_ai_search_semantic_prompt_user_python_server_error"));
                    return false;
            }
            return true;
        },
    },
};
</script>

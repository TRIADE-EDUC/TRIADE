<template>
    <div class="harvest-list">
        <table class="harvest-list-table">
            <tr>
                <th>{{ caption }}</th>
            </tr>
            <tr v-for="profil in list" :key="profil.id" @click="selectProfil(profil.id)" style="cursor:pointer;">
                <td>{{ profil.name }}</td>
            </tr>
        </table>
        <button class="bouton" @click="addProfil">{{ messages.get('common', 'more_label')}}</button>
    </div>
</template>

<script>
export default {
    name : "list",
    props : ["list", "caption", "url"],
    methods : {
        selectProfil : function(id) {
            document.location = this.url + "&action=modif&id=" + id;
        },
        addProfil : function() {
            document.location = this.url + "&action=add";
        }
    }
}
</script>
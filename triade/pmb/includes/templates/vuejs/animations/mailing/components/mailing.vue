<template>
	<div>
	<h1 class="section-title">{{ pmb.getMessage("animation", "animation_registred_action") }}</h1>
		<table>
			<tr>
				<th>{{ pmb.getMessage("animation", "admin_animation_mailing_label") }}</th>
				<th>{{ pmb.getMessage("animation", "admin_animation_mailing_action") }}</th>
				<th>{{ pmb.getMessage("animation", "admin_animation_mailing_time") }}</th>
			</tr>
			<tr v-for="(mailingtype, indexRegis) in mailingstypes" 
				:key='indexRegis' style='cursor: pointer' 
				@click="update(mailingtype.id, 'inscriptions')" 
				@mouseover="hover = indexRegis" 
				@mouseout="hover = -1 " 
				:class="[ indexRegis%2 == 0 ? 'odd' : 'even', indexRegis == hover ? 'surbrillance' : '' ]"
				v-if="mailingtype.periodicity > 2"
			>
				<td>{{ mailingtype.name }}</td>
				<td v-if="mailingtype.periodicity == 3">{{ pmb.getMessage("animation", "animation_mailing_registration") }}</td>
				<td v-else-if="mailingtype.periodicity == 4">{{ pmb.getMessage("animation", "animation_mailing_confirmation") }}</td>
				<td v-else-if="mailingtype.periodicity == 5">{{ pmb.getMessage("animation", "animation_mailing_annulation") }}</td>
				<td v-else>{{ pmb.getMessage("animation", "animation_mailing_sendtobibli") }}</td>
				<td v-if="mailingtype.delay > 0">{{ mailingtype.delay }}</td>
				<td v-else>{{ pmb.getMessage("animation", "animation_mailing_delay_now") }}</td>
			</tr>
		</table>
		<div class='row'>
			<template v-if="this.typecomisset.registration > 0 && this.typecomisset.confirmation > 0 && this.typecomisset.annulation > 0">
		   		<input class="bouton disabled" disabled="disabled" :title="pmb.getMessage('animation', 'animation_type_com_full')" type="button" :value="pmb.getMessage('animation', 'admin_animations_mailing_add')"/>
			</template>
			<template v-else>
		   		<input @click="newType('inscriptions')" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_animations_mailing_add')"/>
			</template>
		</div>
	<br><hr>
	<h1 class=section-title>{{ pmb.getMessage("animation", "campaign_type_animation") }}</h1>
		<table>
			<tr>
				<th>{{ pmb.getMessage("animation", "admin_animation_mailing_label") }}</th>
				<th>{{ pmb.getMessage("animation", "admin_animation_mailing_action") }}</th>
				<th>{{ pmb.getMessage("animation", "admin_animation_mailing_time") }}</th>
			</tr>
			<tr v-for="(mailingtype, index) in mailingstypes" 
				:key='index' 
				style='cursor: pointer' 
				@click="update(mailingtype.id)" 
				@mouseover="hover = index" 
				@mouseout="hover = -1 " 
				:class="[ index%2 == 0 ? 'odd' : 'even', index == hover ? 'surbrillance' : '' ]"
				v-if="mailingtype.periodicity == 1 || mailingtype.periodicity == 2"
			>
				<td>{{ mailingtype.name }}</td>
				<td v-if="mailingtype.periodicity == 1">{{ pmb.getMessage("animation", "animation_mailing_beforeAnim") }}</td>
				<td v-else>{{ pmb.getMessage("animation", "animation_mailing_afterAnim") }}</td>
				<td v-if="mailingtype.delay > 0">{{ mailingtype.delay }}</td>
				<td v-else>{{ pmb.getMessage("animation", "animation_mailing_delay_now") }}</td>
			</tr>
		</table>
		<div class='row'>
	   		<input @click="newType" class="bouton" type="button" :value="pmb.getMessage('animation', 'admin_animations_mailing_add')"/>
		</div>
	</div>
	
</template>


<script>
	export default {
		props : ["pmb", "mailingstypes", "typecomisset"],
		data:function(){
			return {
				hover:-1
			}
		},
		methods:{
			newType : function(type = '') {
				var extend = '';
				if (type == 'inscriptions'){
					extend = '&type=inscriptions';
				} 
				document.location = './admin.php?categ=animations&sub=mailing&action=add' + extend;
			},
			update : function(id, type = '') {
				var extend = '';
				if (type == 'inscriptions'){
					extend = '&type=inscriptions';
				} 
				document.location = './admin.php?categ=animations&sub=mailing&action=edit&id=' + id + extend;
			}
		}
	}
</script>
<template>
	<div>
		<table>
			<tr>
				<th>{{ pmb.getMessage("animation", "mailing_list_date_send_mail") }}</th>
				<th>{{ pmb.getMessage("animation", "mailing_object_mail") }}</th>
				<th>{{ pmb.getMessage("animation", "mailing_author_send") }}</th>
				<th>{{ pmb.getMessage("animation", "mailing_list_auto_send") }}</th>
				<th>{{ pmb.getMessage("animation", "mailing_fail_mail") }}</th>
				<th>{{ pmb.getMessage("animation", "mailing_succes_mail") }}</th>
				<th></th>
			</tr>
			<template v-if="mailingsendlist.length">
				<tr v-for="(mailing, index) in mailingsendlist" 
					:key='index' 
					style='cursor: pointer' 
					@mouseover="hover = index "
					@mouseout="hover = -1 "
					:class="[ index%2 == 0 ? 'odd' : 'even', index == hover ? 'surbrillance' : '' ]"
				>
					<td @click="viewMailing(mailing.id_mailing_list, mailing.num_animation)">{{ mailing.send_at}}</td>
					<td @click="viewMailing(mailing.id_mailing_list, mailing.num_animation)">{{ mailing.mailing_content.mailtplObjet}}</td>
					<td @click="viewMailing(mailing.id_mailing_list, mailing.num_animation)">{{ mailing.user_name}}</td>
					<td @click="viewMailing(mailing.id_mailing_list, mailing.num_animation)" v-if="mailing.auto_send">{{ pmb.getMessage("animation", "mailing_list_automatique_send") }}</td>
					<td @click="viewMailing(mailing.id_mailing_list, mailing.num_animation)" v-else>{{ pmb.getMessage("animation", "mailing_list_manuel_send") }}</td>
					<td @click="viewMailing(mailing.id_mailing_list, mailing.num_animation)">{{ mailing.nb_error_mails}}</td>
					<td @click="viewMailing(mailing.id_mailing_list, mailing.num_animation)">{{ mailing.nb_success_mails}}</td>
					<td v-if="mailing.num_campaign" @click="viewCampaign(mailing.num_campaign)">
						<i class="fa fa-bar-chart" :title="pmb.getMessage('animation', 'animation_go_campaign')"></i>
					</td>
					<td v-else @click="viewMailing(mailing.id_mailing_list, mailing.num_animation)"></td>
				</tr>
			</template>
			<template v-else>
				<tr>
					<td colspan="10" style='text-align: center'>{{ pmb.getMessage("animation", "mailing_no_send_mail") }}</td>
				</tr>
			</template>
		</table>
	</div>
</template>


<script>
	export default {
		props : ["mailingsendlist", "pmb"],
		data:function(){
			return {
				hover:-1
			}
		},
		methods:{
			viewMailing :function(idMailingList, idAnimation){
				document.location = './animations.php?categ=animations&action=mailing&id=' + idAnimation + '&idMailingList=' + idMailingList;
			},
			viewCampaign :function(idCampaign){
				document.location = './edit.php?categ=opac&sub=campaigns&action=view&id=' + idCampaign;
			}
		}
	}
</script>
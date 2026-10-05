<template>
	<div id="registrationList" class="row">
		<h3>{{ pmb.getMessage("animation", "animation_empr_registration_title") }}</h3>
		<div class="registrationListContainer">
			<table v-if="registrations.length">
				<caption class='visually-hidden'>{{ pmb.getMessage("animation", "animation_empr_registration_caption") }}</caption>
				<tbody>
					<tr>
						<th>{{ pmb.getMessage("animation", "animation_empr_registration_animation_start") }}</th>
						<th>{{ pmb.getMessage("animation", "animation_empr_registration_animation_title") }}</th>
						<th>{{ pmb.getMessage("animation", "animation_empr_registration_nb_registred") }}</th>
						<th>{{ pmb.getMessage("animation", "animation_empr_registration_list_registred") }}</th>
						<th>{{ pmb.getMessage("animation", "animation_empr_registration_status") }}</th>
					</tr>
					<tr v-for="(registration, index) in registrations" :class="index % 2 ? 'even' : 'odd'">
						<td>
							{{ registration.animation.event.startDate }}
							<template v-if="registration.animation.event.startHour != '00:00'"> - {{ registration.animation.event.startHour }}</template>
						</td>
						<td><a :href="'./index.php?lvl=animation_see&id=' + registration.animation.id">{{ registration.animation.name }}</a></td>
						<td class="registrationNbPerson">{{ registration.nbRegisteredPersons }}</td>
						<td>
							<ul>
								<li class="registrationListPerson" v-for="person, indexPerson in registration.registrationListPerson">
									{{ person.name }}
									<a v-if='person.unsubscribeLink'
										role='button'
										class="unsubscribeLink"
										:href="person.unsubscribeLink"
										:title="person.is_contact ? pmb.getMessage('animation', 'animation_empr_main_user_title_delete_registration') : pmb.getMessage('animation', 'animation_empr_other_user_title_delete_registration')"
										:onclick="person.is_contact ? `return confirm('${pmb.getMessage('animation', 'animation_empr_main_user_confirm_delete_registration')}')` : `return confirm('${pmb.getMessage('animation', 'animation_empr_other_user_confirm_delete_registration')}')`">
											<span aria-hidden="true" class="fa fa-times"></span>
									</a>
								</li>
							</ul>
						</td>
						<td>{{ registration.registrationStatus.name }}</td>
					</tr>
				</tbody>
			</table>
			<p v-else>{{ pmb.getMessage("animation", "animation_empr_no_registration") }}</p>
		</div>
	</div>
</template>

<script>
	export default {
		props : ["pmb", "registrations"],
	}
</script>
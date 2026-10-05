<template>
    <div class="wysiwyg-actions">
        <div class="wysiwyg-section-label" @click.prevent="$root.$emit('editBlock', block)">
            <span>{{ block.name ? block.name : blockLabels[block.type] }}</span>
        </div>
        <div v-if="block" class="wysiwyg-add-section-actions">
            <button class="wysiwyg-actions-dropdown-toggle" :title="messages.get('dsi', 'view_wysiwyg_see_actions')" @click.prevent.stop="toggleDropdown">
                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
            </button>
            <div v-if="dropdownOpen" class="wysiwyg-actions-dropdown-menu" ref="dropdown">
                <button v-if="showUpArrow" type="button" :title="upOrLeftMessage"
                    @click.prevent.stop="$root.$emit('moveBlock', { block: block, direction: 'up', parent: parentView })">
                    <i :class="upOrLeft" aria-hidden="true"></i>
                </button>
                <button v-if="showDownArrow" type="button" :title="downOrRightMessage"
                    @click.prevent.stop="$root.$emit('moveBlock', { block: block, direction: 'down', parent: parentView })">
                    <i :class="downOrRight" aria-hidden="true"></i>
                </button>
                <button type="button" :title="editMessage" @click.prevent.stop="$root.$emit('editBlock', block); dropdownOpen = false">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </button>
                <button v-if="blockTypes[block.type] === 'block' && isDuplicating" @click.prevent.stop="duplicateBlock"
                    :title="messages.get('dsi', 'view_wysiwyg_paste_element')">
                    <i class="fa fa-clipboard" aria-hidden="true"></i>
                </button>
                <button v-if="blockTypes[block.type] === 'block' && !isDuplicating" @click.prevent.stop="initDuplication(block)"
                    :title="messages.get('dsi', 'view_wysiwyg_copy_element')">
                    <i class="fa fa-clone" aria-hidden="true"></i>
                </button>
                <button v-if="blockTypes[block.type] === 'block' && isCut" @click.prevent.stop="pasteBlock(block)"
                    :title="messages.get('dsi', 'view_wysiwyg_move_here_element')">
					<i class="fa fa-arrows" aria-hidden="true"></i>
                </button>
                <button v-if="!isCut" @click.prevent.stop="initCut(block)"
                    :title="messages.get('dsi', 'view_wysiwyg_move_element')">
                    <i class="fa fa-arrows-alt" aria-hidden="true"></i>
                </button>
                <button v-if="!this.root" type="button" :title="deleteMessage"
                    @click.prevent.stop="$root.$emit('removeBlock', { block : block, parent : parentView })">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <button v-if="blockTypes[block.type] === 'block'" type="button" @click.prevent.stop="$emit('showAddBlock')"
                    :title="messages.get('dsi', 'view_wysiwyg_add_element')">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
            </div>
        </div>
	</div>
</template>

<script>
	export default {
        name: "actionsBlock",
		props: ["block", "parentBlock", "blockLabels", "blockTypes", "root", "parentView"],
		data: function () {
			return {
				isDuplicating: false,
				isCut: false,
				dropdownOpen: false
			}
		},
		mounted() {
            addEventListener("endDuplicateBlock", e => this.isDuplicating = false);
            addEventListener("duplicateBlock", e => this.isDuplicating = true);

            addEventListener("endCutBlock", e => this.isCut = false);
            addEventListener("initCutBlock", e => this.isCut = true);
            
            document.addEventListener('click', this.handleClickOutside);
		},
		beforeDestroy() {
			removeEventListener('click', this.handleClickOutside);
		},
		computed: {
			downOrRightMessage: function() {
				let message = "";
				if (this.parentBlock?.style?.flexDirection == 'column') {
					message = this.messages.get('dsi', 'view_wysiwyg_move_down');
				} else {
					message = this.messages.get('dsi', 'view_wysiwyg_move_right');
				}
				return message.replace('%s', this.blockLabels[this.block.type].toLowerCase());
			},
			upOrLeftMessage: function() {
				let message = "";
				if (this.parentBlock?.style?.flexDirection == 'column') {
					message = this.messages.get('dsi', 'view_wysiwyg_move_up');
				} else {
					message = this.messages.get('dsi', 'view_wysiwyg_move_left');
				}
				return message.replace('%s', this.blockLabels[this.block.type].toLowerCase());
			},
			editMessage: function() {
				return this.messages.get('dsi', 'view_wysiwyg_edit_element')
					.replace('%s', this.blockLabels[this.block.type].toLowerCase())
			},
			deleteMessage: function() {
				return this.messages.get('dsi', 'view_wysiwyg_delete_element')
					.replace('%s', this.blockLabels[this.block.type].toLowerCase())
			},
			showUpArrow: function() {
				return this.indexInParent == 0 ? false : true;
			},
			showDownArrow: function() {
				return !this.root && this.parentBlock && this.indexInParent != (this.parentBlock.blocks.length-1) ? true : false;
			},
			upOrLeft: function() {
				return !this.root && this.parentBlock && this.parentBlock.style.flexDirection == 'column' ? 'fa fa-arrow-up' : 'fa fa-arrow-left'
			},
			downOrRight: function() {
				return !this.root && this.parentBlock && this.parentBlock.style.flexDirection == 'column' ? 'fa fa-arrow-down' : 'fa fa-arrow-right'
			},
            indexInParent: function() {
                return !this.root && this.parentBlock && this.parentBlock.blocks.indexOf(this.block);
            }
		},
		methods: {
			initDuplication : function(block) {
				//On ajoute le block dans la session
				sessionStorage.setItem("duplicateBlock", JSON.stringify(block));
				//On envoie un event pour indiquer une duplication en cours
				let event = new CustomEvent("duplicateBlock", { bubbles : true });
				window.dispatchEvent(event);
			},
			duplicateBlock : function() {
				//On recupere le block et on lui donne un nouvel id avant de l'ajouter aux blocks
				let block = JSON.parse(sessionStorage.getItem("duplicateBlock"));
				this.changeBlockId(block);

				this.$set(this.block.blocks, this.block.blocks.length, block);

				//On supprime le block de la session
				sessionStorage.removeItem('duplicateBlock');

				//On envoie un event pour indiquer la fin de la duplication
				let event = new CustomEvent("endDuplicateBlock", { bubbles : true });
				window.dispatchEvent(event);
			},
            initCut: function(block) {
				sessionStorage.setItem("cutBlock", JSON.stringify(block));

				addEventListener("cutBlock_" + block.id, e => this.cutBlock());

				let event = new CustomEvent("initCutBlock", { bubbles : true });
				window.dispatchEvent(event);
			},
			cutBlock: function() {
				let block = JSON.parse(sessionStorage.getItem("cutBlock"));

				let index = -1;
				for(let b in this.parentBlock.blocks) {
					if(this.parentBlock.blocks[b].id == block.id) {
						index = b;
						break;
					}
				}

				if(index > -1) {
					let event = new CustomEvent("endCutBlock", { bubbles : true });
					window.dispatchEvent(event);

					sessionStorage.removeItem('cutBlock');

					this.parentBlock.blocks.splice(index, 1);
				}
			},
			pasteBlock: function() {
				let block = JSON.parse(sessionStorage.getItem("cutBlock"));

				this.$set(this.block.blocks, this.block.blocks.length, block);

				let event = new CustomEvent("cutBlock_" + block.id, { bubbles : true });
				window.dispatchEvent(event);
			},
			changeBlockId: function(block) {
				block.id = Date.now().toString(36) + Math.random().toString(36).substring(2);
				for(let i in block.blocks) {
					this.changeBlockId(block.blocks[i]);
				}
				return block;
			},
			getBlockById: function(id) {
				for(let block in this.parentBlock.blocks) {
					
				}	
			},
			toggleDropdown() {
				this.dropdownOpen = !this.dropdownOpen;

                // On dispatch un click sur contenu pour palier le problème de fermeture du dropdown, c'est pas beau mais ça fonctionne...
                var event = new MouseEvent('click', {
                    view: window,
                    bubbles: true,
                    cancelable: true
                });
                document.getElementById("contenu").dispatchEvent(event);
			},
			handleClickOutside(event) {
				if (this.dropdownOpen && this.$refs.dropdown) {
					this.dropdownOpen = false;
				}
			}
		}
	}
</script>
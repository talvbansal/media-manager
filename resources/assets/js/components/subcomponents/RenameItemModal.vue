<template>
  <media-modal

    v-if="show"
    :size="size"
    :show="show"
    @media-modal-close="close()">
    <div>
      <div class="modal-header">
        <h4 class="modal-title">Rename item</h4>
        <button 
          class="close float-right" 
          type="button" 
          @click="close()">×</button>
      </div>

      <div 
        v-if="loading" 
        class="text-center">
        <span class="spinner icon-spinner2"/>Loading...
      </div>

      <div v-else>
        <div class="modal-body">
          <div class="form-group">
            <label>Current name</label>
            <p class="form-control-static">{{ getItemName(currentFile) }}</p>
          </div>

          <div class="form-group fg-line">
            <label>New name</label>
            <input
              ref="newItemName"
              v-model="newItemName"
              type="text"
              class="form-control"
              @keyup.enter="renameItem()">
          </div>

          <media-errors :errors="errors"/>

        </div>

        <div class="modal-footer">
          <button 
            class="btn btn-primary" 
            type="button" 
            @click="renameItem()">
            Apply
          </button>
          <button 
            class="btn btn-default" 
            type="button" 
            @click="close()">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </media-modal>
</template>

<script>

import axios from "axios";
import fileManagerMixin from "./../../mixins/file-manager-mixin";
import MediaErrors from "./MediaErrors";

export default{
	components: {
		"media-errors": MediaErrors,
	},

	mixins: [fileManagerMixin],

	props:{
		currentPath:{
		    default: "",
			type: String
		},

		currentFile:{
			default: function(){
				return {};
			},
			type: [Object, Boolean]
		},

		/**
         * Default route prefix
         */
		prefix: {
			default : "/admin/",
			type: String
		},

		show:{
			default : false,
			type: Boolean
		}
	},

	data(){
		return {
			errors: [],
			loading: false,
			newItemName: null,
			size: "modal-md"
		};
	},

	watch: {
		show: function (val) {
			if (val) {
				window.Vue.nextTick(() => {
					this.$refs.newItemName.focus();
				});
			}
		}
	},

	mounted(){
		document.addEventListener("keydown", (e) => {
			if (this.show && e.keyCode === 13) {
				this.renameItem();
			}
		});
	},

	methods: {
		close(){
			this.errors = [];
			this.newItemName = null;
			this.loading = false;
			this.$emit("media-modal-close");
		},

		renameItem(){

			if (!this.newItemName) {
				this.errors = ["Please provide a new name for this item"];
			} else {
				this.loading = true;
				const original = this.getItemName(this.currentFile);

				const data = {
					"path": this.currentPath,
					"original": original,
					"newName": this.newItemName,
					"type": (this.isFolder(this.currentFile)) ? "Folder" : "File"
				};

				axios.post(`${this.prefix}browser/rename`, data).then(
					(response) => {
						this.$emit("reload-folder");
						this.mediaManagerNotify(response.data.success);
						this.close();
					}).catch((error) => {
					this.errors = (error.response.data.error) ? error.response.data.error : error.response.statusText;
					this.loading = false;
				});
			}
		}
	}

};
</script>

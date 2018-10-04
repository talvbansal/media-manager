<template>
  <media-modal
    v-if="show"
    :size="size" 
    :show="show"
    @media-modal-close="close()" >
    <div>
      <div class="modal-header">
        <button 
          class="close" 
          type="button" 
          @click="close()">×</button>
        <h4 class="modal-title">New folder</h4>
      </div>

      <div 
        v-if="loading" 
        class="text-center">
        <span class="spinner icon-spinner2"/>Loading...
      </div>

      <div v-else>
        <div class="modal-body">
          <div class="form-group fg-line">
            <label>Folder name</label>
            <input
              ref="folderName"
              v-model="newFolderName"
              type="text"
              class="form-control"
              @keyup.enter="createFolder()"
            >
          </div>

          <media-errors :errors="errors"/>

        </div>

        <div class="modal-footer">
          <button 
            class="btn btn-primary" 
            type="button" 
            @click="createFolder()">
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
			newFolderName: null,
			size: "modal-md"
		};
	},

	watch: {
		show: function (val) {
			if (val) {
				window.Vue.nextTick(() => {
					this.$refs.folderName.focus();
				});
			}
		}
	},


	mounted(){
		document.addEventListener("keydown", (e) => {
			if (this.show && e.keyCode === 13) {
				this.createFolder();
			}
		});
	},

	methods: {
		close(){
			this.newFolderName = null;
			this.loading = false;
			this.errors = [];
			this.$emit("media-modal-close");
		},

		createFolder(){

			if (!this.newFolderName) {
				this.errors = ["Please provide a name for the new folder"];
				return;
			}

			const data = {
				"folder": this.currentPath,
				"new_folder": this.newFolderName
			};

			this.loading = true;
			axios.post(`${this.prefix}browser/folder`, data).then(
				(response) => {
					this.mediaManagerNotify(response.data.success);
					this.$emit("reload-folder");
					this.close();
				},
				(errors) => {
					this.errors = (errors.response.data.error) ? errors.response.data.error : errors.response.statusText;
					this.loading = false;
				}
			);
		}
	}
};
</script>

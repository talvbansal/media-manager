<template>
  <media-modal
    v-if="show"
    :size="size" 
    :show="show"
    @media-modal-close="close()">
    <div>
      <div class="modal-header">
        <h4 class="modal-title">Delete item</h4>
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
            <label>Are you sure you want to delete the following item?</label>
            <p class="form-control-static">{{ getItemName(currentFile) }}</p>
          </div>

        </div>

        <div class="modal-footer">
          <button 
            class="btn btn-primary" 
            type="button" 
            @click="deleteItem()">
            Delete
          </button>
          <button 
            class="btn btn-default" 
            type="button" 
            @click="close">
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

export default{
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

	data: () => {
		return {
			loading: false,
			newItemName: null,
			size: "modal-md"
		};
	},

	mounted() {
		document.addEventListener("keydown", (e) => {
			if (this.show && e.keyCode === 13) {
				this.deleteItem();
			}
		});
	},

	methods: {
		close(){
			this.newItemName = null;
			this.loading = false;
			this.$emit("media-modal-close");
		},

		deleteItem(route, data){
			if (this.isFolder(this.currentFile)) {
				return this.deleteFolder(route, data);
			}
			return this.deleteFile(route, data);
		},

		deleteFile(){
			if (this.currentFile) {
				const data = {
					"path": this.currentFile.fullPath
				};
				this.rm(`${this.prefix}browser/file`, data);
			}
		},

		deleteFolder(){
			if (this.isFolder(this.currentFile)) {
				const data = {
					"path" : this.currentFile.fullPath
				};
				this.rm(`${this.prefix}browser/folder`, data);
			}
		},

		rm(route, payload){
			this.loading = true;
			axios.delete(route, {params: payload}).then(
				(response) => {
					this.$emit("reload-folder");
					this.mediaManagerNotify(response.data.success);
					this.close();
				},
				(errors) => {
					const error = (errors.response.data.error) ? errors.response.data.error : errors.response.statusText;
					this.mediaManagerNotify(error, "danger");
					this.close();
				}
			);
		}
	}
};
</script>
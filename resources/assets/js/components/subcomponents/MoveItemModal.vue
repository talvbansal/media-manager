<template>
  <media-modal
    v-if="show"
    :size="size"
    :show="show"
    @media-modal-close="close()"
  >
    <div>
      <div class="modal-header">
        <h4 class="modal-title">Move item</h4>
        <button
          class="close float-right"
          type="button"
          @click="close()">×
        </button>
      </div>

      <div
        v-if="loading"
        class="text-center">
        <span class="spinner icon-spinner2"/>Loading...
      </div>

      <div v-else>
        <div class="modal-body">
          <div class="form-group">
            <label>Item name</label>
            <p class="static">{{ getItemName(currentFile) }}</p>
          </div>

          <div class="form-group">
            <label>Move to</label>
            <select
              id="newFolderLocation"
              ref="newFolderLocation"
              v-model="newFolderLocation"
              class="form-control"
              name="newFolderLocation"
              @keyup.enter="moveItem()">
              <option
                v-for="(name, path) in allDirectories"
                :key="path"
                :value="path"
                v-html="name"/>
            </select>
          </div>

          <media-errors :errors="errors"/>
        </div>

        <div class="modal-footer">
          <button
            class="btn btn-primary"
            type="button"
            @click="moveItem()">
            Apply
          </button>
          <button
            class="btn btn-outline-secondary"
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

export default {
	components: {
		"media-errors": MediaErrors,
	},

	mixins: [fileManagerMixin],

	props: {
		currentPath: {
			default: "",
			type: String
		},

		currentFile: {
			default: function () {
				return {};
			},
			type: [Object, Boolean]
		},

		/**
             * Default route prefix
             */
		prefix: {
			default: "/admin/",
			type: String
		},

		show: {
			default: false,
			type: Boolean
		}
	},

	data() {
		return {
			allDirectories: {},
			errors: [],
			newFolderLocation: null,
			loading: false,
			size: "modal-md"
		};
	},

	watch: {
		show(open) {
			if (open) {
				this.open();
			}
		}
	},

	mounted() {
		document.addEventListener("keydown", (e) => {
			if (this.show && e.keyCode === 13) {
				this.moveItem();
			}
		});
	},

	methods: {
		close() {
			this.newFolderName = null;
			this.loading = false;
			this.$emit("media-modal-close");
		},

		open() {
			axios.get(`${this.prefix}browser/directories`).then(
				(response) => {
					this.newFolderLocation = this.currentPath;
					this.allDirectories = response.data;
				},
				(response) => {
					const error = (response.data.error) ? response.data.error : response.statusText;
					this.mediaManagerNotify(error, "danger");

				}
			);

			window.Vue.nextTick(() => {
				this.$refs.newFolderLocation.focus();
			});
		},

		moveItem() {

			const data = {
				"path": this.currentPath,
				"currentItem": this.getItemName(this.currentFile),
				"newPath": this.newFolderLocation,
				"type": (this.isFolder(this.currentFile)) ? "Folder" : "File"
			};

			this.loading = true;
			axios.post(`${this.prefix}browser/move`, data).then(
				(response) => {
					this.$emit("reload-folder");
					window.eventHub.$emit("media-manager-notification", response.data.success);
					this.close();
				}).catch((error) => {
				this.errors = (error.response.data.error) ? error.response.data.error : error.response.statusText;
				this.loading = false;
			});
		}
	}
};
</script>

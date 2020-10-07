<template>
  <transition name="modal">
    <div id="media-manager-file-picker">
      <div class="modal-header">
        <top-toolbar
          :current-file="currentFile"
          :current-path="currentPath"
          @reload-folder="loadFolder( currentPath )"
          @open-modal-create-folder="showCreateFolderModal = true"
          @open-modal-delete-item="showDeleteItemModal = true"
          @open-modal-move-item="showMoveItemModal = true"
          @open-modal-rename-item="showRenameItemModal = true"
          @upload-file="uploadFile"
        />
        <!-- Close button for modal windows -->
        <button 
          v-if="isModal" 
          type="button" 
          class="close" 
          @click="close()">×</button>
      </div>

      <div 
        id="mediaManagerDropZone" 
        class="dropzone">
        <div 
          v-if="loading" 
          class="media-manager-alternative-content loading">
          <p>
            <span class="spinner icon-spinner2"/>Loading...
          </p>

          <h4 v-if="uploadProgress > 0">{{ uploadProgress }} %</h4>
        </div>

        <div v-else>

          <div class="media-manager-file-browser">
            <div class="row">
              <div class="col-sm-12">
                <ol class="breadcrumb border-bottom-0">

                  <li 
                    v-for="(name, key) in breadCrumbs" 
                    :key="name">
                    <a 
                      href="javascript:void(0);" 
                      @click="loadFolder(key)">{{ name }}</a>
                  </li>

                  <li class="active">
                    {{ folderName }}
                  </li>
                </ol>
              </div>
            </div>

            <div 
              v-if="isFolderEmpty" 
              class="media-manager-alternative-content">
              <h4>This folder is empty.</h4>
              <p>
                Drag and drop files onto this window to upload files.
              </p>
            </div>

            <div 
              v-else 
              class="row">
              <div 
                :class="{ 'col-sm-12' : !isFile(currentFile) || isFolder(currentFile), 'col-sm-9' : isFile(currentFile) && ! isFolder(currentFile) }">

                <div class="table-responsive media-manager-file-picker-list">

                  <table class="table table-condensed table-vmiddle">
                    <thead>
                      <tr>
                        <th><a 
                          href="javascript:void(0);" 
                          @click="orderBy('name')">Name</a></th>
                        <th><a 
                          href="javascript:void(0);" 
                          @click="orderBy('mimeType')">Type</a></th>
                        <th><a 
                          href="javascript:void(0);" 
                          @click="orderBy('modified.date')">Date</a></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr 
                        v-for="folder in folders"
                        :key="folder.name"
                        :class="[ (folder == currentFile) ? 'bg-primary' : '' ]"
                      >
                        <td>
                          <i class="icon-folder"/>
                          <a
                            class="word-wrappable"
                            href="javascript:void(0);"
                            @click="previewFile(folder)"
                            @dblclick="loadFolder(folder.fullPath)"
                            @keyup.enter="loadFolder(folder.fullPath)"
                          >
                            {{ folder.name }}
                          </a>
                        </td>
                        <td>folder</td>
                        <td>{{ folder.modified | formatDate('DD/MM/YYYY') }}</td>
                      </tr>

                      <tr 
                        v-for="file in files"
                        :key="file.name"
                        :class="[ (file == currentFile) ? 'bg-primary' : '' ]">
                        <td>
                          <i 
                            v-if="isImage(file)" 
                            class="icon-image"/>
                          <i 
                            v-else 
                            class="icon-file-text2"/>
                          <a 
                            class="word-wrappable" 
                            href="javascript:void(0);"
                            @click="previewFile(file)"
                            @keyup.enter="selectFile(file)"
                            @dblclick="selectFile(file)"
                          >
                            {{ file.name }}
                          </a>

                        </td>
                        <td>{{ file.mimeType }}</td>
                        <td>{{ file.modified | formatDate('DD/MM/YYYY') }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <preview-sidebar :current-file="currentFile"/>
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex align-items-center">
          <div class="item-count">
            {{ itemsCount }} Items
          </div>

          <!-- Buttons to be rendered if the media-manager is in a modal window-->
          <div 
            v-if="isModal" 
            class="buttons text-right">
            <button
              v-show="currentFile && !isFolder(currentFile)"
              type="button"
              class="btn btn-primary"
              @click="selectFile()">
              Select File
            </button>
            <button 
              type="button" 
              class="btn btn-outline-secondary"
              @click="close()">
              Close
            </button>
          </div>
        </div>
      </div>

      <modal-create-folder
        :current-path="currentPath"
        :prefix="prefix"
        :show="showCreateFolderModal"
        @media-modal-close="showCreateFolderModal = false"
        @reload-folder="loadFolder( currentPath )"
      />

      <modal-delete-item
        :current-path="currentPath"
        :current-file="currentFile"
        :prefix="prefix"
        :show="showDeleteItemModal"
        @media-modal-close="showDeleteItemModal = false"
        @reload-folder="loadFolder( currentPath )"
      />

      <modal-move-item
        :current-path="currentPath"
        :current-file="currentFile"
        :prefix="prefix"
        :show="showMoveItemModal"
        @media-modal-close="showMoveItemModal = false"
        @reload-folder="loadFolder( currentPath )"

      />

      <modal-rename-item
        :current-path="currentPath"
        :current-file="currentFile"
        :prefix="prefix"
        :show="showRenameItemModal"
        @media-modal-close="showRenameItemModal = false"
        @reload-folder="loadFolder( currentPath )"

      />

    </div>
  </transition>
</template>

<script>
import axios from "axios";
import {orderBy} from "lodash";
import FileManagerMixin from "./../mixins/file-manager-mixin";

import RenameItemModal from "./subcomponents/RenameItemModal";
import CreateFolderModal from "./subcomponents/CreateFolderModal";
import ConfirmDeleteModal from "./subcomponents/ConfirmDeleteModal";
import MoveItemModal from "./subcomponents/MoveItemModal";
import PreviewSideBar from "./subcomponents/PreviewSideBar";
import TopToolBar from "./subcomponents/TopToolBar";
import dayjs from "dayjs";

export default{
  name: 'MediaManager',

	components:{
		"modal-rename-item": RenameItemModal,
		"modal-create-folder": CreateFolderModal,
		"modal-delete-item": ConfirmDeleteModal,
		"modal-move-item": MoveItemModal,
		"preview-sidebar": PreviewSideBar,
		"top-toolbar": TopToolBar,
	},

	mixins: [FileManagerMixin],

  filters: {
    formatDate: (date, format = 'DD/MM/YYYY HH:mm:ss A') => {
      if (!date) return null;

      return dayjs(date).format(format);
    }
  },

	props: {
		/**
     * Is this instance of the media manager a modal window.
     * If so then this property is used to show the close
     * buttons at the top and bottom of the screen.
     */
		isModal: {
			default: false,
			type: Boolean
		},

		/**
     * Default route prefix
     */
		prefix: {
			default : "/admin/",
			type: String
		},

		/**m
     * The event to be fired when selectItem() is called.
     * The actual event name emitted is prefixed w/
     * "media-manager-selected-" so to avoid
     * clashes w/ other events.
     */
		selectedEventName: {
			default: "",
			type: String
		},

		/**
     * If this instance is a modal window then this
     * property is used to show or hide the
     * modal window.
     */
		show: {
			default: false,
			type: Boolean
		}
	},

	data: function () {

		return {

			/**
       * breadCrumbs for the current path that are used to go
       * backwards through the directory tree.
       */
			breadCrumbs: {},

			/**
       * The currently highlighted file
       */
			currentFile: {},

			/**
       * The current path that the media manager is displaying
       */
			currentPath: "",

			/**
       * All of the files in the current path
       */
			files: [],

			/**
       * The current path's folder name
       */
			folderName: null,

			/**
       * All of the sub folders in the current path
       */
			folders: [],

			/**
       * Property to show the loading indicator
       */
			loading: true,

			/**
       * Property to show upload progress
       */
			uploadProgress: 0,

			/**
       * Total files and folder count
       */
			itemsCount: 0,

			/**
       * properties to show and hide internal modal windows
       */
			showCreateFolderModal: false,
			showDeleteItemModal: false,
			showMoveItemModal: false,
			showRenameItemModal: false,

			/**
       * property to hold direction of column sorting
       */
			sortDirection: false
		};
	},

	computed:{
		isFolderEmpty(){
			return ((this.files.length + this.folders.length ) === 0);
		}
	},

	mounted: function () {
		this.loadFolder();
		this.dragUpload();

		if( ! this.prefix.endsWith("/") )
		{
			this.prefix = `${this.prefix}/`;
		}
	},

	methods: {
		/**
     * sort files and folders...
     * @param column
     */
		orderBy(column)
		{
			this.sortDirection = !this.sortDirection;
			const order = (this.sortDirection)? "desc" : "asc";
			this.files = orderBy(this.files, [column], [order]);
			this.folders = orderBy(this.folders, [column], [order]);
		},

		close() {
			this.breadCrumbs = {};
			this.currentFile = {};
			this.currentPath = "";
			this.files = {};
			this.folderName = "";
			this.folders = {};
			this.itemsCount = 0;
			this.$emit("media-modal-close");
		},

		loadFolder(path){
			this.uploadProgress = 0;
			if (!path) {
				path = ( this.currentPath ) ? this.currentPath : "";
			}

			this.loading = true;
			this.currentFile = false;

			axios.get(`${this.prefix}browser/index?path=${path}`).then(
				(response) => {
					this.breadCrumbs = response.data.breadCrumbs;
					this.currentFile = {};
					this.currentPath = response.data.folder;
					this.loading = false;
					this.files = response.data.files;
					this.folderName = response.data.folderName;
					this.folders = response.data.subFolders;
					this.itemsCount = response.data.itemsCount;
				},
				(response) => {
					if (response.data.error) {
						this.mediaManagerNotify(response.data.error, "danger");
					}

					this.loading = false;
					this.currentFile = {};
				}
			);
		},

		previewFile(file){
			this.currentFile = file;
		},

		uploadFile(payload){

			let fieldName = payload.name;
			let fileList = payload.files;

			/**
       * Create a new form request object.
       * Gather all of the files to be uploaded and append them to it.
       * Attach the current path so the server knows where to upload the files to.
       * Send a post request to the server...
       */
			const form = new FormData();
			Array
				.from(Array(fileList.length).keys())
				.map(x => {
					form.append(fieldName, fileList[x], fileList[x].name);
				});
                
			form.append("folder", this.currentPath);

			this.loading = true;
			axios.post(`${this.prefix}browser/file`, form, {
				progress(e){
					if (e.lengthComputable) {
						this.uploadProgress = parseFloat( Math.round(e.loaded / e.total * 100) ).toFixed(2);
					}
				}
			}
			).then(
				(response) => {
					this.mediaManagerNotify(response.data.success);
					this.loadFolder(this.currentPath);
				},
				(response) => {
					const error = (response.data.error) ? response.data.error : response.statusText;
					// when uploading we might have some files uploaded and others fail
					if (response.data.notices) this.mediaManagerNotify(response.data.notices);
					this.mediaManagerNotify(error, "danger", 5000);
					this.loadFolder(this.currentPath);
				}
			);

		},

		selectFile(){
			/**
             * Only dispatch an event if a custom event has been defined
             */
			if (this.selectedEventName) {
				window.eventHub.$emit("media-manager-selected-" + this.selectedEventName, this.currentFile);
			}
		},

		dragUpload(){
      let Dropzone = require('dropzone'); //eslint-disable-line
			Dropzone.autoDiscover = false;
			this.dropzone = new Dropzone("div#mediaManagerDropZone", {
				clickable: false,
				createImageThumbnails: false,
				dictDefaultMessage: "",
				enqueueForUpload: true,
				paramName: "files",
				previewsContainer: null,
				previewTemplate: "<span class=\"d-none\"></span>",
				hiddenInputContainer: true,
				uploadMultiple: true,
				url: `${this.prefix}browser/file`,
				headers: {
					"X-CSRF-TOKEN": window.axios.defaults.headers.common["X-CSRF-TOKEN"]
				},

				sending: (file, xhr, form) => {
					this.loading = true;
					form.append("folder", this.currentPath);
				},

				completemultiple: () => {
					this.loading = false;
					this.loadFolder(this.currentPath);
				},

				errormultiple: (files, response) => {
					this.mediaManagerNotify(response.error);
				},

				successmultiple: (files, response) => {
					this.mediaManagerNotify(response.success);
				},

				totaluploadprogress: (uploadProgress) => {
					this.uploadProgress = parseFloat(Math.round(uploadProgress * 100) / 100).toFixed(2);
					if (this.uploadProgress < 100) {
						this.loading = true;
					} else {
						this.uploadProgress = 0;
						this.loading = false;
					}
				}
			});
		}
	}
};
</script>
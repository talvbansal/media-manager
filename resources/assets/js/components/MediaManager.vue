<template>
  <transition name="modal">
    <div id="easel-file-picker">
      <div class="modal-header">
        <!-- Close button for modal windows -->
        <button 
          v-if="isModal" 
          type="button" 
          class="close" 
          @click="close()">×</button>

        <top-toolbar
          :current-file="currentFile"
          :current-path="currentPath"
          @open-modal-create-folder="showCreateFolderModal = true"
          @open-modal-delete-item="showDeleteItemModal = true"
          @open-modal-move-item="showMoveItemModal = true"
          @open-modal-rename-item="showRenameItemModal = true"
          @upload-file="uploadFile"
        />

      </div>

      <div 
        id="mediaManagerDropZone" 
        class="dropzone">
        <div 
          v-if="loading" 
          class="easel-alternative-content loading">
          <p>
            <span class="spinner icon-spinner2"/>Loading...
          </p>

          <h4 v-if="uploadProgress > 0">{{ uploadProgress }} %</h4>
        </div>

        <div v-else>

          <div class="easel-file-browser">
            <div class="row">
              <div class="col-xs-12">
                <ol class="breadcrumb">

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
              class="easel-alternative-content">
              <h4>This folder is empty.</h4>
              <p>
                Drag and drop files onto this window to upload files.
              </p>
            </div>

            <div 
              v-else 
              class="row">
              <div 
                :class="{ 'col-sm-12' : !isFile(currentFile) || isFolder(currentFile), 'col-sm-9' : isFile(currentFile) && ! isFolder(currentFile) }"
                class="col-xs-12">

                <div class="table-responsive easel-file-picker-list">

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
                        <td>{{ folder.modified.date | moment('DD/MM/YYYY') }}</td>
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
                        <td> {{ file.mimeType }}</td>
                        <td> {{ file.modified.date | moment('DD/MM/YYYY') }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div 
                v-if="isFile(currentFile) && !isFolder(currentFile)"
                class="easel-file-picker-sidebar hidden-xs col-sm-3">

                <img 
                  v-if="isImage(currentFile)"
                  id="easel-preview-image"
                  :src="currentFile.webPath"
                  class="img-responsive center-block"
                  style="max-height: 200px"
                >

                <div 
                  v-else 
                  class="text-center">
                  <i 
                    class="icon-file-text2" 
                    style="font-size: 15rem"/>
                </div>

                <table class="table-responsive table-condensed table-vmiddle easel-file-picker-preview-table">
                  <tbody>
                    <tr>
                      <td class="description">Name</td>
                      <td class="file-value">{{ currentFile.name }}</td>
                    </tr>
                    <tr>
                      <td class="description">Size</td>
                      <td class="file-value">{{ currentFile.size | humanFileSize }}</td>
                    </tr>
                    <tr>
                      <td class="description">URL</td>
                      <td class="file-value">
                        <a 
                          :href="currentFile.webPath" 
                          target="_blank" 
                          rel="noopener">{{ currentFile.relativePath }}</a>
                      </td>
                    </tr>
                    <tr>
                      <td class="description">Uploaded On</td>
                      <td class="file-value">{{ currentFile.modified.date | moment }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer vertical-center">
          <div class="item-count">
            {{ itemsCount }} Items
          </div>

          <!-- Buttons to be rendered if the media-manager is in a modal window-->
          <div 
            v-if="isModal" 
            class="buttons">
            <button
              v-show="currentFile && !isFolder(currentFile)"
              type="button"
              class="btn btn-primary"
              @click="selectFile()">
              Select File
            </button>
            <button 
              type="button" 
              class="btn btn-default" 
              @click="close()">
              Close
            </button>
          </div>
        </div>
      </div>

      <media-create-folder-modal
        :current-path="currentPath"
        :prefix="prefix"
        :show="showCreateFolderModal"
        @media-modal-close="showCreateFolderModal = false"
        @media-manager-reload-folder="loadFolder( currentPath )"
      />

      <media-delete-item-modal
        :current-path="currentPath"
        :current-file="currentFile"
        :prefix="prefix"
        :show="showDeleteItemModal"
        @media-modal-close="showDeleteItemModal = false"
        @media-manager-reload-folder="loadFolder( currentPath )"
      />

      <media-move-item-modal
        :current-path="currentPath"
        :current-file="currentFile"
        :prefix="prefix"
        :show="showMoveItemModal"
        @media-modal-close="showMoveItemModal = false"
        @media-manager-reload-folder="loadFolder( currentPath )"

      />

      <media-rename-item-modal
        :current-path="currentPath"
        :current-file="currentFile"
        :prefix="prefix"
        :show="showRenameItemModal"
        @media-modal-close="showRenameItemModal = false"
        @media-manager-reload-folder="loadFolder( currentPath )"

      />

    </div>
  </transition>
</template>

<script>
import axios from "axios";
import {orderBy, isEmpty} from "lodash";

export default {

	components:{
		"top-toolbar": require("./subcomponents/TopToolBar.vue"),

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

		/**
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

	created: function () {
		window.eventHub.$on("media-manager-reload-folder", this.loadFolder);
	},

	beforeDestroy: function () {
		// It's good to clean up event listeners before a component is destroyed.
		window.eventHub.$off("media-manager-reload-folder", this.loadFolder);
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

	    isFile(){
	       return !isEmpty(this.currentFile);
		},

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
				previewTemplate: "<span class=\"hidden\"></span>",
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
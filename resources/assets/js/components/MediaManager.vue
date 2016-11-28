<template>
    <transition name="modal">
        <div id="easel-file-picker">
            <div class="modal-header">
                <!-- Close button for modal windows -->
                <button v-if="isModal" type="button" class="close" @click="close()">×</button>

                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group offset-right">

                        <!-- File input wont get triggered if this is a button so use a label instead -->
                        <label class="btn btn-primary btn-icon-text btn-file" title="Upload">
                            <i class="icon-upload"></i>
                            <span class="hidden-xs">Upload</span>
                            <input type="file" class="hidden" @change="uploadFile" name="files[]" multiple="multiple"/>
                        </label>

                        <button class="btn btn-primary btn-icon-text" type="button" title="Add Folder" @click="showCreateFolderModal = true">
                            <i class="icon-folder-plus"></i>
                            <span class="hidden-xs">Add folder</span>
                        </button>

                    </div>

                    <div class="btn-group offset-right">
                        <button class="btn btn-default btn-icon-text" type="button" @click="loadFolder(currentPath)" title="Refresh">
                            <i class="icon-loop2"></i>
                            <span class="hidden-xs">Refresh</span>
                        </button>
                    </div>

                    <div class="btn-group offset-right">
                        <button class="btn btn-default btn-icon-text" type="button" :disabled="!currentFile" @click="showMoveItemModal = true" title="Move">
                            <i class="icon-arrow-right"></i>
                            <span class="hidden-xs">Move</span>
                        </button>

                        <button class="btn btn-default btn-icon-text" type="button" :disabled="!currentFile" @click="showDeleteItemModal = true" title="Delete">
                            <i class="icon-bin"></i>
                            <span class="hidden-xs">Delete</span>
                        </button>

                        <button class="btn btn-default btn-icon-text" type="button" :disabled="!currentFile" title="Rename" @click="showRenameItemModal = true">
                            <i class="icon-pencil"></i>
                            <span class="hidden-xs">Rename</span>
                        </button>
                    </div>

                </div>

            </div>

            <div>
                <div v-if="loading" class="text-center">
                    <span class="spinner icon-spinner2"></span>Loading...
                </div>

                <div v-else>
                    <div class="easel-file-browser">
                        <div class="row">
                            <div class="col-xs-12">
                                <ol class="breadcrumb">

                                    <li v-for="(name, key) in breadCrumbs">
                                        <a href="javascript:void(0);" @click=loadFolder(key)>{{ name }}</a>
                                    </li>

                                    <li class="active">
                                        {{ folderName }}
                                    </li>
                                </ol>
                            </div>
                        </div>

                        <div class="row">
                            <div :class="{ 'col-sm-12' : !currentFile || isFolder(currentFile), 'col-sm-9' : currentFile && ! isFolder(currentFile) }" class="col-xs-12">

                                <div class="table-responsive easel-file-picker-list">
                                    <table class="table table-condensed table-vmiddle">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(folder, path) in folders" :class="[ (folder == currentFile) ? 'active' : '' ]">
                                            <td>
                                                <i class="icon-folder"></i>
                                                <a href="javascript:void(0);"
                                                   @click="previewFile(folder)"
                                                   @dblclick="loadFolder(path)"
                                                   @keyup.enter="loadFolder(path)"
                                                   class="word-wrappable">
                                                    {{ folder }}
                                                </a>
                                            </td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>

                                        <tr v-for="file in files" :class="[ (file == currentFile) ? 'active' : '' ]">
                                            <td>
                                                <i v-if="isImage(file)" class="icon-image"></i>
                                                <i v-else class="icon-file-text2"></i>
                                                <a href="javascript:void(0);"
                                                   @click="previewFile(file)"
                                                   @keyup.enter="selectFile(file)"
                                                   @dblclick="selectFile(file)"
                                                   class="word-wrappable">
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
                            <div v-if="currentFile && !isFolder(currentFile)" class="easel-file-picker-sidebar hidden-xs col-sm-3">

                                <img v-if="isImage(currentFile)"
                                     class="img-responsive center-block"
                                     id="easel-preview-image"
                                     :src="currentFile.webPath"
                                     style="max-height: 200px"
                                />

                                <div v-else class="text-center">
                                    <i class="icon-file-text2" style="font-size: 15rem"></i>
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
                                            <a :href="currentFile.webPath" target="_blank" rel="noopener">{{ currentFile.relativePath }}</a>
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
                    <div v-if="isModal" class="buttons">
                        <button type="button" class="btn btn-primary" v-show="currentFile && !isFolder(currentFile)" @click="selectFile()">
                            Select File
                        </button>
                        <button type="button" class="btn btn-default" @click="close()">
                            Close
                        </button>
                    </div>
                </div>
            </div>

            <media-create-folder-modal
                    @close="showCreateFolderModal = false"
                    @media-manager-reload-folder="loadFolder( currentPath )"
                    :current-path="currentPath"
                    :show="showCreateFolderModal"
            >
            </media-create-folder-modal>

            <media-delete-item-modal
                    @close="showDeleteItemModal = false"
                    @media-manager-reload-folder="loadFolder( currentPath )"
                    :current-path="currentPath"
                    :current-file="currentFile"
                    :show="showDeleteItemModal"
            >
            </media-delete-item-modal>

            <media-move-item-modal
                    @close="showMoveItemModal = false"
                    @media-manager-reload-folder="loadFolder( currentPath )"
                    :current-path="currentPath"
                    :current-file="currentFile"
                    :show="showMoveItemModal"
            >
            </media-move-item-modal>

            <media-rename-item-modal
                    @close="showRenameItemModal = false"
                    @media-manager-reload-folder="loadFolder( currentPath )"
                    :current-path="currentPath"
                    :current-file="currentFile"
                    :show="showRenameItemModal"
            >
            </media-rename-item-modal>

        </div>
    </transition>
</template>

<script>
    export default {

        props: {
            /**
             * Is this instance of the media manager a modal window.
             * If so then this property is used to show the close
             * buttons at the top and bottom of the screen.
             */
            isModal: {
                default: false
            },

            /**
             * The event to be fired when selectItem() is called.
             * The actual event name emitted is prefixed w/
             * "media-manager-selected-" so to avoid
             * clashes w/ other events.
             */
            selectedEventName: {
                default: false
            },

            /**
             * If this instance is a modal window then this
             * property is used to show or hide the
             * modal window.
             */
            show: {
                default : false
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
                currentFile: null,

                /**
                 * The current path that the media manager is displaying
                 */
                currentPath: null,

                /**
                 * All of the files in the current path
                 */
                files: {},

                /**
                 * The current path's folder name
                 */
                folderName: null,

                /**
                 * All of the sub folders in the current path
                 */
                folders: {},

                /**
                 * Property to show the loading indicator
                 */
                loading: true,

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
                showRenameItemModal: false
            }
        },

        created: function () {
            window.eventHub.$on('media-manager-reload-folder', this.loadFolder);
        },
        // It's good to clean up event listeners before a component is destroyed.
        beforeDestroy: function () {
            window.eventHub.$off('media-manager-reload-folder', this.loadFolder);
        },

        mounted: function () {
            /**
             * If this instance is modal we only want to load the root file structure when
             * the modal window is show, otherwise a request to get the root path would
             * be made un-necessarily. However if this isn't a modal window instance
             * then we need to automatically load the root file contents so that
             * some file data is displayed to the user upon component render.
             */
            //if (!this.isModal) {
                /**
                 * I have no idea why this time out is needed but calling loadFolder()
                 * when the component is ready doesn't work unless there is a short
                 * delay.
                 */
                //setTimeout(function () {
                    this.loadFolder();
                //}.bind(this), 500);
            //}
        },

        methods: {

            close: function () {
                this.breadCrumbs = {};
                this.currentFile = null;
                this.currentPath = null;
                this.files = {};
                this.folderName = null;
                this.folders = {};
                this.itemsCount = 0;
                this.$emit('close');
            },

            loadFolder: function (path) {
                if (!path) {
                    path = ( this.currentPath ) ? this.currentPath : '';
                }

                this.loading = true;
                this.currentFile = false;

                this.$http.get('/admin/browser/index?path=' + path).then(
                        function (response) {
                            this.breadCrumbs = response.data.breadCrumbs;
                            this.currentFile = null;
                            this.currentPath = response.data.folder;
                            this.loading = false;
                            this.files = response.data.files;
                            this.folderName = response.data.folderName;
                            this.folders = response.data.subFolders;
                            this.itemsCount = response.data.itemsCount;
                        },
                        function (response) {
                            if (response.data.error) {
                                this.mediaManagerNotify(response.data.error, 'danger');
                            }

                            this.loading = false;
                            this.currentFile = null;
                        }
                );
            },

            previewFile: function (file) {
                this.currentFile = file;
            },

            uploadFile: function (event) {
                event.preventDefault();

                /**
                 * Create a new form request object.
                 * Gather all of the files to be uploaded and append them to it.
                 * Attach the current path so the server knows where to upload the files to.
                 * Send a post request to the server...
                 */
                var form = new FormData();
                var files = event.target.files || event.dataTransfer.files;

                for (var key in files) {
                    form.append('files[' + key + ']', files[key]);
                }
                form.append('folder', this.currentPath);

                this.loading = true;
                this.$http.post('/admin/browser/file', form).then(
                        function (response) {
                            this.mediaManagerNotify(response.data.success);
                            this.loadFolder(this.currentPath);
                        },
                        function (response) {
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            // when uploading we might have some files uploaded and others fail
                            if (response.data.notices) this.mediaManagerNotify(response.data.notices);
                            this.mediaManagerNotify(error, 'danger', 5000);
                            this.loadFolder(this.currentPath);
                        }
                );

            },

            selectFile: function () {
                /**
                 * Only dispatch an event if a custom event has been defined
                 */
                if (this.selectedEventName) {
                    window.eventHub.$emit('media-manager-selected-' + this.selectedEventName, this.currentFile);
                }
            }
        }
    }
</script>
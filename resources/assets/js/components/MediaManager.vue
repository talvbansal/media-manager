<template>
    <div id="easel-file-picker">
        <div class="modal-header">
            <button v-if="isModal" type="button" class="close" @click="close">×</button>

            <div class="btn-toolbar" role="toolbar" role="toolbar">
                <div class="btn-group offset-right">

                    <!-- File input wont get triggered if this is a button so use a label instead -->
                    <label class="btn btn-primary btn-icon-text btn-file" title="Upload">
                        <i class="icon-upload"></i>
                        <span class="hidden-xs">Upload</span>
                        <input type="file" class="hidden" @change="uploadFile" name="files[]" multiple="multiple"/>
                    </label>

                    <button class="btn btn-primary btn-icon-text" type="button" title="Add Folder" @click="showCreateFolder = true">
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

                    <button class="btn btn-default btn-icon-text" type="button" :disabled="!currentFile" @click="deleteItem()" title="Delete">
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

        <div class="easel-file-browser">
            <div class="row">
                <div class="col-xs-12">
                    <ol class="breadcrumb">

                        <li v-for="(path, name) in breadCrumbs">
                            <a href="javascript:void(0);" @click=loadFolder(path)>{{ name }}</a>
                        </li>

                        <li class="active">
                            {{ folderName }}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="row">

                <div :class="{ 'col-sm-12' : !currentFile || isFolder(currentFile), 'col-sm-9  col-lg-10' : currentFile && ! isFolder(currentFile) }" class="col-xs-12">

                    <div v-show="loading" transition="fade" class="text-center">
                        <div class="spinner icon-spinner2"></div>
                    </div>

                    <div v-else class="table-responsive easel-file-picker-list" transition="fade">
                        <table class="table table-condensed table-vmiddle">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Date</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(path, folder) in folders" :class="[ (folder == currentFile) ? 'active' : '' ]">
                                <td>
                                    <i class="icon-folder"></i>
                                    <a href="javascript:void(0);" @click="previewFile(folder)" @dblclick="loadFolder(path)" v-touch:doubletap="loadFolder(path)" class="word-wrappable">{{
                                        folder }}</a>
                                </td>
                                <td>-</td>
                                <td>-</td>
                            </tr>

                            <tr v-for="file in files" :class="[ (file == currentFile) ? 'active' : '' ]">
                                <td>
                                    <i v-if="isImage(file)" class="icon-image"></i>
                                    <i v-else class="icon-file-text2"></i>
                                    <a href="javascript:void(0);" @click="previewFile(file)" v-touch:doubletap="selectFile(file)" class="word-wrappable">{{
                                        file.name }}</a>

                                </td>
                                <td> {{ file.mimeType }}</td>
                                <td> {{ file.modified.date | moment 'L' }}</td>
                            </tr>

                            </tbody>
                        </table>

                    </div>
                </div>

                <div v-if="currentFile && !isFolder(currentFile)" class="easel-file-picker-sidebar hidden-xs col-sm-3 col-lg-2">

                    <img v-if="isImage(currentFile)" id="easel-preview-image" class="img-responsive center-block" :src="currentFile.webPath" style="max-height: 200px" transition="fade"/>

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
                            <td class="description">Public URL</td>
                            <td class="file-value"><a :href="currentFile.webPath" target="_blank" rel="noopener">Click Here</a></td>
                        </tr>
                        <tr>
                            <td class="description">Date</td>
                            <td class="file-value">{{ currentFile.modified.date | moment 'L LT' }}</td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>

        <div class="modal-footer vertical-center">
            <div class="item-count">
                {{ itemsCount }} Items
            </div>

            <div class="buttons">
                <button type="button" class="btn btn-primary" v-show="currentFile && !isFolder(currentFile) && isModal" @click="selectFile()">
                    Select File
                </button>
                <button type="button" class="btn btn-default" v-if="isModal" @click="close">
                    Close
                </button>
            </div>
        </div>

        <media-create-folder-modal
                :show.sync="showCreateFolderModal"
                :current-path.sync="currentPath"
        >
        </media-create-folder-modal>

        <media-move-item-modal
                :show.sync="showMoveItemModal"
                :current-path.sync="currentPath"
                :current-file.sync="currentFile"
        >
        </media-move-item-modal>

        <media-rename-item-modal
                :show.sync="showRenameItemModal"
                :current-path.sync="currentPath"
                :current-file.sync="currentFile"
        >
        </media-rename-item-modal>

    </div>

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
                required: false,
                type: Boolean
            },

            /**
             * If this instance is a modal windows then this property
             * is used to show or hide the modal window.
             */
            show: {
                required: false,
                type: Boolean
            },

            /**
             * The event to be fired when selectItem() is called.
             * The actual event dispatched is prefixed with
             * "media-manager-selected-" so to avoid
             * clashes with other events.
             */
            selectedEventName: {
                required: false
            }
        },

        data: function () {

            return {
                /**
                 * Breadcrumbs for the current path that are used to go
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
                 * All of the files within the current path
                 */
                files: {},

                /**
                 * The current path's folder name
                 */
                folderName: null,

                /**
                 * All of the subfolders within the current path
                 */
                folders: {},

                /**
                 * Property to show the loading indicator
                 */
                loading: true,


                /**
                 * properties to show and hide internal modal windows
                 */
                showCreateFolderModal: false,
                showMoveItemModal: false,
                showRenameItemModal: false

            }
        },

        watch: {
            show: function (open) {
                /**
                 * When opening the media manager
                 */
                if (open) {
                    this.loadFolder();
                }
            }
        },

        events: {
            'media-manager-reload-folder': function () {
                this.loadFolder();
            }
        },

        computed: {
            itemsCount: function () {
                return this.files.length + Object.keys(this.folders).length;
            }
        },

        ready: function()
        {
            /**
             * If this instance is modal we only want to load the root file structure when
             * the modal window is show, otherwise a request to get the root path would
             * be made un-necessarily. However if this isn't a modal window instance
             * then we need to automatically load the root file contents so that
             * some file data is displayed to the user upon component render.
             */
            if( ! this.isModal )
            {
                /**
                 * I have no idea why this time out is needed but calling loadFolder()
                 * when the component is ready doesn't work unless there is a short
                 * delay.
                 */
                setTimeout(function() {
                    this.loadFolder();
                }.bind(this), 500);
            }
        },

        methods: {

            close: function () {
                this.show = false;
                this.breadCrumbs = {};
                this.currentFile = null;
                this.currentPath = null;
                this.files = {};
                this.folderName = null;
                this.folders = {};
            },

            loadFolder: function (path) {
                if (!path) {
                    path = ( this.currentPath ) ? this.currentPath : '';
                }

                this.loading = true;
                this.currentFile = false;

                this.$http.get('/admin/browser/index?path=' + path).then(
                        function (response) {
                            this.$set('breadCrumbs', response.data.breadcrumbs);
                            this.$set('currentFile', null);
                            this.$set('currentPath', response.data.folder);
                            this.$set('loading', false);
                            this.$set('files', response.data.files);
                            this.$set('folderName', response.data.folderName);
                            this.$set('folders', response.data.subfolders);
                        },
                        function (response) {
                            if (response.data.error) {
                                this.notify(response.data.error, 'danger');
                            }

                            this.$set('loading', false);
                            this.$set('currentFile', null);
                        }
                );
            },

            previewFile: function (file) {
                this.currentFile = file;
            },

            deleteItem: function () {

                if (this.isFolder(this.currentFile)) {
                    return this.deleteFolder();
                }
                return this.deleteFile();
            },

            deleteFile: function () {
                if (this.currentFile) {
                    var data = {
                        'path': this.currentFile.fullPath
                    };
                    this.delete('/admin/browser/delete', data);
                }
            },

            deleteFolder: function () {
                if (this.isFolder(this.currentFile)) {
                    var data = {
                        'folder': this.currentPath,
                        'del_folder': this.currentFile
                    };
                    this.delete('/admin/browser/folder', data);
                }
            },

            delete: function (route, payload) {
                this.loading = true;
                this.$http.delete(route, {body: payload}).then(
                        function (response) {
                            this.notify(response.data.success);
                            this.loadFolder(this.currentPath);
                        },
                        function (response) {
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            this.notify(error, 'danger');
                            if (response.data.notices) this.notify(response.data.notices);
                            this.loadFolder(this.currentPath);
                        }
                );
            },

            uploadFile: function (event) {
                event.preventDefault();

                /**
                 * Create a new form request object.
                 * Gather all of the files to be uploaded and append them to it.
                 * Attach the current path so the server knows where to upload the files to.
                 * Send a post request to the server with the payload...
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
                            this.notify(response.data.success);
                            this.loadFolder(this.currentPath);
                        },
                        function (response) {
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            // when uploading we might have some files uploaded and others fail
                            if (response.data.notices) this.notify(response.data.notices);
                            this.notify(error, 'danger', 5000);
                            this.loadFolder(this.currentPath);
                        }
                );

            },

            selectFile: function () {
                /**
                 * Only dispatch an event if a custom event has been defined
                 */
                if (this.selectedEventName) {
                    this.$dispatch('media-manager-selected-' + this.selectedEventName, this.currentFile);
                }
            }
        }
    }
</script>
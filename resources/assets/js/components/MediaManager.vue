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

                <div :class="{ 'col-sm-12' : !currentFile || isFolder(currentFile), 'col-sm-9' : currentFile && ! isFolder(currentFile) }" class="col-xs-12">

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
                                    <a href="javascript:void(0);" @click="previewFile(file)" @dblclick="selectFile(file)" v-touch:doubletap="selectFile(file)" class="word-wrappable">{{
                                        file.name }}</a>

                                </td>
                                <td> {{ file.mimeType }}</td>
                                <td> {{ file.modified.date | moment 'L' }}</td>
                            </tr>

                            </tbody>
                        </table>

                    </div>
                </div>

                <div v-if="currentFile && !isFolder(currentFile)" class="easel-file-picker-sidebar hidden-xs col-sm-3">

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
                <button type="button" class="btn btn-primary" v-show="currentFile && !isFolder(currentFile) && isModal" @click="selectFile(currentFile)">
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
            show: {
                required: false
            },

            isModal: {
                type: Boolean,
                required: false
            }
        },

        data: function () {

            return {
                currentFile: null,
                selectedFile: null,
                currentPath: null,
                folderName: null,
                folders: {},
                files: {},
                breadCrumbs: {},
                loading: true,
                insertIntoEditor: false,
                allDirectories: {},

                showCreateFolderModal: false,
                showMoveItemModal: false,
                showRenameItemModal: false

            }
        },

        watch: {
            show: function (open) {
                if (open) this.loadFolder();
            }
        },

        events: {
            'reload-folder': function (message) {
                if (message) {
                    console.log(message);
                }
                this.loadFolder();
            }
        },

        ready: function () {

            // if not modal load root folder
            if (!this.isModal) {
                setTimeout(function () {
                    this.loadFolder();
                }.bind(this), 500);
            }
        },

        computed: {
            itemsCount: function () {
                return this.files.length + Object.keys(this.folders).length;
            }
        },

        methods: {
            open: function () {
                this.loadFolder();
            },

            close: function () {
                this.show = false;
            },

            reset: function () {
                this.currentFile = null;
                this.currentPath = null;
                this.folderName = null;
                this.folders = {};
                this.files = {};
                this.breadCrumbs = {};
            },

            responseError: function (response) {

                if (response.data.error) {
                    //systemNotification(response.data.error);
                }

                this.$set('loading', false);
                this.$set('currentFile', null);
                this.$set('selectedFile', null);
            },

            loadFolder: function (path) {
                if (!path) {
                    path = ( this.currentPath ) ? this.currentPath : '';
                }

                this.loading = true;
                this.currentFile = false;

                this.$http.get('/admin/browser/index?path=' + path).then(
                        function (response) {
                            this.$set('loading', false);
                            this.$set('folderName', response.data.folderName);
                            this.$set('folders', response.data.subfolders);
                            this.$set('files', response.data.files);
                            this.$set('breadCrumbs', response.data.breadcrumbs);
                            this.$set('currentFile', null);
                            this.$set('currentPath', response.data.folder);
                            this.$set('selectedFile', null);
                        },
                        function (response) {
                            this.responseError(response);
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
                    var data = {'path': this.currentFile.fullPath};
                    this.delete('/admin/browser/delete', data);
                }
            },

            deleteFolder: function () {
                if (this.isFolder(this.currentFile)) {
                    var data = {'folder': this.currentPath, 'del_folder': this.currentFile};
                    this.delete('/admin/browser/folder', data);
                }
            },

            uploadFile: function (event) {
                event.preventDefault();

                var form = new FormData();
                var files = event.target.files || event.dataTransfer.files;

                for (var key in files) {
                    form.append('files[' + key + ']', files[key]);
                }

                form.append('folder', this.currentPath);

                this.post('/admin/browser/file', form);
            },

            delete: function (route, payload, callback) {
                this.loading = true;
                this.$http.delete(route, {body: payload}).then(
                        function (response) {
                            //if (response.data.success) systemNotification(response.data.success);
                            this.loadFolder(this.currentPath);
                            if (typeof callback == 'function') callback();
                        }.bind(this),
                        function (response) {
                            this.loadFolder(this.currentPath);
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            console.log(error);
                            //this.notify(error, 'danger');
                            //if (response.data.notices) this.notify(response.data.notices);

                            this.$set('loading', false);
                        }
                );
            },

            post: function (route, payload, callback) {
                this.loading = true;
                this.$http.post(route, payload).then(
                        function (response) {
                            //if (response.data.success) systemNotification(response.data.success);
                            this.loadFolder(this.currentPath);
                            if (typeof callback == 'function') callback();

                        }.bind(this),
                        function (response) {
                            this.loadFolder(this.currentPath);
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            //this.notify(error, 'danger');
                            //if (response.data.notices) this.notify(response.data.notices);
                            console.log(error);
                            this.$set('loading', false);
                        }
                );

            },

            notify: function (notices, type) {
                if (typeof notices == 'object') {
                    for (var i = 0, len = notices.length; i < len; i++) {
                        systemNotification(notices[i], type);
                    }
                    return

                }
                systemNotification(notices, type);

            },

            selectFile: function () {

            }
        }
    }
</script>
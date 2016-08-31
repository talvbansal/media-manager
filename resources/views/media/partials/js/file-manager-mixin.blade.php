{{-- This mixin provides almost all of the core file browser functionality --}}
<script>
    var FileManagerMixin = {
        data: {
            isModal: false,
            currentFile: null,
            selectedFile: null,
            currentPath: null,
            folderName: null,
            folders: {},
            files: {},
            breadCrumbs: {},
            loading: true,
            insertIntoEditor: false,
            newFolderName: null,
            newItemName: null,
            allDirectories: {},
            newFolderLocation: null,
            isMobile : false
        },

        ready: function () {
            this.isMobile = ( $('html').hasClass('ismobile') );

            // Create Folder
            var createFolderModal = $('#easel-new-folder');
            createFolderModal.on('shown.bs.modal', function () {
                $('#newFolderName').focus();
            });

            createFolderModal.keypress(function (e) {
                if (e.which == 13) {
                    $('#btnCreateFolder').click();
                }
            });

            // Move Item
            var moveItemModal = $('#easel-move-item');
            moveItemModal.on('shown.bs.modal', function () {
                $('newFolderLocation').focus();
            });

            moveItemModal.keypress(function (e) {
                if (e.which == 13) {
                    $('#btnMoveItem').click();
                }
            });

            // Rename Item
            var renameItemModal = $('#easel-rename-item')
            renameItemModal.on('shown.bs.modal', function () {
                $('#newItemName').focus()
            });

            renameItemModal.keypress(function (e) {
                if (e.which == 13) {
                    $('#btnRename').click();
                }
            });

        },

        computed: {
            itemsCount: function () {
                return this.files.length + Object.keys(this.folders).length;
            }
        },

        methods: {

            reset: function () {
                this.currentFile = null;
                this.currentPath = null;
                this.folderName = null;
                this.folders = {};
                this.files = {};
                this.breadCrumbs = {};
                this.newFolderName = null;
                this.newItemName = null;
                this.newFolderLocation = null;
            },

            responseError: function (response) {

                if (response.data.error) {
                    systemNotification(response.data.error);
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
                            this.$set('newFolderName', null);
                            this.$set('newItemName', null);
                        },
                        function (response) {
                            this.responseError(response);
                        }
                );
            },

            isImage: function (file) {
                return file.mimeType.indexOf('image') != -1;
            },

            isFolder: function (file) {
                return (typeof file == 'string');
            },

            getItemName: function( item )
            {
                if(! item)
                {
                    return null;
                }

                return ( this.isFolder(item) ) ? item : item.name;
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

            createFolder: function () {
                if (this.newFolderName) {
                    var data = {
                        'folder': this.currentPath,
                        'new_folder': this.newFolderName
                    };
                    this.post('/admin/browser/folder', data, function () {
                        $('#easel-new-folder').modal('hide');
                    });
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

            renameItem: function () {
                var original = this.getItemName( this.currentFile);

                var data = {
                    'path': this.currentPath,
                    'original': original,
                    'newName': this.newItemName,
                    'type': (this.isFolder(this.currentFile)) ? 'Folder' : 'File'
                };

                this.post('/admin/browser/rename', data, function () {
                    $('#easel-rename-item').modal('hide');
                });
            },

            {{-- Don't use the bootstrap html attributes to open the modal since we need to populate the folders based on an up to date listing--}}
            openMoveModal: function () {

                this.$http.get('/admin/browser/directories').then(
                        function (response) {
                            $('#easel-move-item').modal('show');
                            this.newFolderLocation = this.currentPath;
                            this.allDirectories = response.data;
                        }.bind(this),
                        function (response) {
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            systemNotification(error, 'danger');
                        }
                );

            },

            moveItem: function () {
                var currentItem = this.getItemName( this.currentFile);

                var data = {
                    'path': this.currentPath,
                    'currentItem': currentItem,
                    'newPath': this.newFolderLocation,
                    'type': (this.isFolder(this.currentFile)) ? 'Folder' : 'File'
                };

                this.post('/admin/browser/move', data, function () {
                    $('#easel-move-item').modal('hide');
                });
            },

            delete: function (route, payload, callback) {
                this.loading = true;
                this.$http.delete(route, {body: payload}).then(
                        function (response) {
                            if (response.data.success) systemNotification(response.data.success);
                            this.loadFolder(this.currentPath);
                            if (typeof callback == 'function') callback();
                        }.bind(this),
                        function (response) {
                            this.loadFolder(this.currentPath);
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            this.notify(error, 'danger');
                            if (response.data.notices) this.notify(response.data.notices);

                            this.$set('loading', false);
                        }
                );
            },

            post: function (route, payload, callback) {
                this.loading = true;
                this.$http.post(route, payload).then(
                        function (response) {
                            if (response.data.success) systemNotification(response.data.success);
                            this.loadFolder(this.currentPath);
                            if (typeof callback == 'function') callback();

                        }.bind(this),
                        function (response) {
                            this.loadFolder(this.currentPath);
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            this.notify(error, 'danger');
                            if (response.data.notices) this.notify(response.data.notices);
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

            selectFile: function ( ) { }
        }
    };
</script>

<template>
    <media-modal :show.sync="show" :size="size">
        <div class="modal-header">
            <button class="close" type="button" @click="close">×</button>
            <h4 class="modal-title">Move item</h4>
        </div>

        <div v-show="loading" transition="fade" class="text-center">
            <div class="spinner icon-spinner2"></div>
        </div>

        <div v-else>
            <div class="modal-body">
                <div class="form-group">
                    <label>Item name</label>
                    <p class="static">{{ this.getItemName(this.currentFile) }}</p>
                </div>

                <div class="form-group">
                    <label>Move to</label>
                    <select class="form-control" v-model="newFolderLocation" id="newFolderLocation" name="newFolderLocation">
                        <option v-for="(path, name) in allDirectories" :value="path">{{{ name }}}</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" @click="moveItem()">
                    Apply
                </button>
                <button class="btn btn-default" type="button" @click="close">
                    Cancel
                </button>
            </div>
        </div>
    </media-modal>
</template>

<script>
    export default{
        props: ['show', 'currentPath', 'currentFile'],

        data: function () {
            return {
                allDirectories: {},
                newFolderLocation: null,
                loading: false,
                size: 'modal-medium'
            }
        },

        watch: {
            show: function (open) {
                if (open) {
                    this.open();
                }
            }
        },

        ready: function () {
            document.addEventListener("keydown", (e) => {
                if (this.show && e.keyCode == 13) {
                    this.moveItem();
                }
            });
        },

        methods: {
            close: function () {
                this.newFolderName = null;
                this.loading = false;
                this.show = false;
            },

            open: function () {
                this.loading = true;
                this.$http.get('/admin/browser/directories').then(
                        function (response) {
                            this.newFolderLocation = this.currentPath;
                            this.allDirectories = response.data;
                            this.loading = false;
                        },
                        function (response) {
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            this.$dispatch('media-manager-notification', error, 'danger');
                            this.loading = false;
                        }
                );
            },

            moveItem: function () {
                this.loading = true;
                var currentItem = this.getItemName(this.currentFile);

                var data = {
                    'path': this.currentPath,
                    'currentItem': currentItem,
                    'newPath': this.newFolderLocation,
                    'type': (this.isFolder(this.currentFile)) ? 'Folder' : 'File'
                };

                this.$http.post('/admin/browser/move', data).then(
                        function (response) {
                            this.$dispatch('media-manager-reload-folder');
                            this.$dispatch('media-manager-notification', response.data.success);
                            this.close();
                        },
                        function (response) {
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            this.$dispatch('reload-folder', response.data.success);
                            this.$dispatch('media-manager-notification', error, 'danger');
                            this.loading = false;
                        }
                );
            }
        }

    };
</script>

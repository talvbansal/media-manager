<template>
    <media-modal :show.sync="show" :size="size">
        <div class="modal-header">
            <button class="close" type="button" @click="close">×</button>
            <h4 class="modal-title">Delete item</h4>
        </div>

        <div v-show="loading" transition="fade" class="text-center">
            <span class="spinner icon-spinner2"></span>Loading...
        </div>

        <div v-else>
            <div class="modal-body">
                <div class="form-group">
                    <label>Are you sure you want to delete the following item?</label>
                    <p class="form-control-static">{{ this.getItemName(this.currentFile) }}</p>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" @click="deleteItem()">
                    Delete
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
                loading: false,
                newItemName: null,
                size: 'modal-small'
            }
        },

        ready: function () {
            document.addEventListener("keydown", (e) => {
                if (this.show && e.keyCode == 13) {
                    this.renameItem();
                }
            });
        },

        methods: {
            close: function () {
                this.newItemName = null;
                this.loading = false;
                this.show = false;
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
                            this.$dispatch('media-manager-reload-folder');
                            this.$dispatch('media-manager-notification', response.data.success);
                            this.close();
                        },
                        function (response) {
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            this.$dispatch('media-manager-reload-folder');
                            this.$dispatch('media-manager-notification', error, 'danger');
                            this.loading = false;
                        }
                );
            }
        }

    }
</script>
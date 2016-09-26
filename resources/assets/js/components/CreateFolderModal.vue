<template>
    <media-modal :show.sync="show" :size="size">
        <div class="modal-header">
            <button class="close" type="button" @click="close">×</button>
            <h4 class="modal-title">New folder</h4>
        </div>

        <div v-show="loading" transition="fade" class="text-center">
            <span class="spinner icon-spinner2"></span>Loading...
        </div>

        <div v-else>
            <div class="modal-body">
                <div class="form-group fg-line">
                    <label>Folder name</label>
                    <input type="text" v-model="newFolderName" class="form-control">
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" @click="createFolder()">
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
        props: ['show', 'currentPath'],

        data: function () {
            return {
                newFolderName: null,
                size: 'modal-medium',
                loading: false
            }
        },

        ready: function () {
            document.addEventListener("keydown", (e) => {
                if (this.show && e.keyCode == 13) {
                    this.createFolder();
                }
            });
        },

        methods: {
            close: function () {
                this.newFolderName = null;
                this.loading = false;
                this.show = false;
            },

            createFolder: function () {

                if (this.newFolderName) {
                    this.loading = true;

                    var data = {
                        'folder': this.currentPath,
                        'new_folder': this.newFolderName
                    };

                    this.$http.post('/admin/browser/folder', data).then(
                            function (response) {
                                this.$dispatch('media-manager-reload-folder');
                                this.$dispatch('media-manager-notification', response.data.success);
                                this.close();
                            },
                            function (response) {
                                var error = (response.data.error) ? response.data.error : response.statusText;
                                this.$dispatch('media-manager-notification', error, 'danger');
                                this.close();
                            }
                    );
                }
            }
        }
    }
</script>

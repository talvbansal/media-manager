<template>
    <media-modal :show.sync="show" :size="size">
        <div class="modal-header">
            <button class="close" type="button" @click="close">×</button>
            <h4 class="modal-title">New folder</h4>
        </div>

        <div v-show="loading" transition="fade" class="text-center">
            <div class="spinner icon-spinner2"></div>
        </div>

        <div v-else>
            <div class="modal-body">
                <div class="form-group">
                    <label>Folder name</label>
                    <input type="text" value="" v-model="newFolderName" class="form-control">
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
                size: 'medium',
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
                                console.log(response);
                                this.$dispatch('reload-folder', response.data.success);
                                this.close();
                            },
                            function (response) {
                                var error = (response.data.error) ? response.data.error : response.statusText;
                                console.log(error);
                                this.close();
                            }
                    );
                }
            }
        }
    }
</script>

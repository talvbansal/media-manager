<template>
    <media-modal @close="close()" :size="size" :show="show" v-if="show">
        <div>
            <div class="modal-header">
                <button class="close" type="button" @click="close()">×</button>
                <h4 class="modal-title">New folder</h4>
            </div>

            <div v-if="loading" class="text-center">
                <span class="spinner icon-spinner2"></span>Loading...
            </div>

            <div v-else>
                <div class="modal-body">
                    <div class="form-group fg-line">
                        <label>Folder name</label>
                        <input type="text" v-model="newFolderName" class="form-control" @keyup.enter="createFolder()">
                    </div>

                    <media-errors :errors="errors"></media-errors>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" @click="createFolder()">
                        Apply
                    </button>
                    <button class="btn btn-default" type="button" @click="close()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </media-modal>
</template>

<script>
    export default{
        props:{
            currentPath:{},
            show:{
                default : false
            }
        },

        data: function () {
            return {
                errors: [],
                loading: false,
                newFolderName: null,
                size: 'modal-md'
            }
        },

        mounted: function () {
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
                this.errors = [];
                this.$emit('close');
            },

            createFolder: function () {

                if (!this.newFolderName) {
                    this.errors = ['Please provide a name for the new folder'];
                    return;
                }

                var data = {
                    'folder': this.currentPath,
                    'new_folder': this.newFolderName
                };

                this.loading = true;
                this.$http.post('/admin/browser/folder', data).then(
                        function (response) {
                            this.mediaManagerNotify(response.data.success);
                            window.eventHub.$emit('media-manager-reload-folder');
                            this.close();
                        },
                        function (response) {
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            this.errors = error;
                            this.loading =false;
                        }
                );
            }
        }
    }
</script>

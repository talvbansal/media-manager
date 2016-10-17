<template>
    <media-modal @close="close()" :size="size" :show="show" v-if="show">
        <div>
            <div class="modal-header">
                <button class="close" type="button" @click="close()">×</button>
                <h4 class="modal-title">Move item</h4>
            </div>

            <div v-if="loading" class="text-center">
                <span class="spinner icon-spinner2"></span>Loading...
            </div>

            <div v-else>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Item name</label>
                        <p class="static">{{ this.getItemName(this.currentFile) }}</p>
                    </div>

                    <div class="form-group">
                        <label>Move to</label>
                        <select class="form-control" v-model="newFolderLocation" id="newFolderLocation" name="newFolderLocation" @keyup.enter="moveItem()">
                            <option v-for="(name, path) in allDirectories" :value="path" v-html="name"></option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" @click="moveItem()">
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
            currentFile:{},
            show:{
                default : false
            }
        },

        data: function () {
            return {
                allDirectories: {},
                newFolderLocation: null,
                loading: false,
                size: 'modal-md'
            }
        },

        watch: {
            show: function (open) {
                if (open) {
                    this.open();
                }
            }
        },

        mounted: function () {
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
                this.$emit('close');
            },

            open: function () {
                this.$http.get('/admin/browser/directories').then(
                        function (response) {
                            this.newFolderLocation = this.currentPath;
                            this.allDirectories = response.data;
                        },
                        function (response) {
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            this.mediaManagerNotify(error, 'danger');

                        }
                );
            },

            moveItem: function () {

                var data = {
                    'path': this.currentPath,
                    'currentItem': this.getItemName(this.currentFile),
                    'newPath': this.newFolderLocation,
                    'type': (this.isFolder(this.currentFile)) ? 'Folder' : 'File'
                };

                this.loading = true;
                this.$http.post('/admin/browser/move', data).then(
                        function (response) {
                            window.eventHub.$emit('media-manager-reload-folder');
                            window.eventHub.$emit('media-manager-notification', response.data.success);
                            this.close();
                        },
                        function (response) {
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            window.eventHub.$emit('reload-folder', response.data.success);
                            window.eventHub.$emit('media-manager-notification', error, 'danger');
                            this.loading = false;
                        }
                );
            }
        }
    };
</script>

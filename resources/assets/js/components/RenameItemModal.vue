<template>
    <media-modal @close="close()" :size="size" :show="show" v-if="show">
        <div>
            <div class="modal-header">
                <button class="close" type="button" @click="close()">×</button>
                <h4 class="modal-title">Rename item</h4>
            </div>

            <div v-if="loading" class="text-center">
                <span class="spinner icon-spinner2"></span>Loading...
            </div>

            <div v-else>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Current name</label>
                        <p class="form-control-static">{{ this.getItemName(this.currentFile) }}</p>
                    </div>

                    <div class="form-group fg-line">
                        <label>New name</label>
                        <input type="text" v-model="newItemName" class="form-control" @keyup.enter="renameItem()">
                    </div>

                    <media-errors :errors="errors"></media-errors>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" @click="renameItem()">
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
                errors: [],
                loading: false,
                newItemName: null,
                size: 'modal-md'
            }
        },

        mounted: function () {
            document.addEventListener("keydown", (e) => {
                if (this.show && e.keyCode == 13) {
                    this.renameItem();
                }
            });
        },

        methods: {
            close: function () {
                this.errors = [];
                this.newItemName = null;
                this.loading = false;
                this.$emit('close');
            },

            renameItem: function () {

                if (!this.newItemName) {
                    this.errors = ['Please provide a new name for this item'];
                } else {
                    this.loading = true;
                    var original = this.getItemName(this.currentFile);

                    var data = {
                        'path': this.currentPath,
                        'original': original,
                        'newName': this.newItemName,
                        'type': (this.isFolder(this.currentFile)) ? 'Folder' : 'File'
                    };

                    this.$http.post('/admin/browser/rename', data).then(
                            function (response) {
                                window.eventHub.$emit('media-manager-reload-folder');
                                this.mediaManagerNotify(response.data.success);
                                this.close();
                            },
                            function (response) {
                                var error = (response.data.error) ? response.data.error : response.statusText;
                                this.errors = [error];
                                this.loading = false;
                            }
                    );
                }
            }
        }

    }
</script>

<template>
    <media-modal :show.sync="show" :size="size">
        <div class="modal-header">
            <button class="close" type="button" @click="close">×</button>
            <h4 class="modal-title">Rename item</h4>
        </div>

        <div v-show="loading" transition="fade" class="text-center">
            <div class="spinner icon-spinner2"></div>
        </div>

        <div v-else>
            <div class="modal-body">
                <div class="form-group">
                    <label>Current name</label>
                    <p class="form-control-static">{{ this.getItemName(this.currentFile) }}</p>
                </div>

                <div class="form-group">
                    <label>New name</label>
                    <input type="text" v-model="newItemName" class="form-control">
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" @click="renameItem()">
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
                loading: false,
                newItemName: null,
                size: 'medium'
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

            renameItem: function () {

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
                            console.log(response);
                            this.$dispatch('reload-folder', response.data.success);
                            this.close();
                        },
                        function (response) {
                            var error = (response.data.error) ? response.data.error : response.statusText;
                            console.log(error);
                            this.loading = false;
                        }
                );
            }
        }

    }
</script>

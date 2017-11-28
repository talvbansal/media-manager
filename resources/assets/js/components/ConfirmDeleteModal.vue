<template>
    <media-modal @media-modal-close="close()" :size="size" :show="show" v-if="show">
        <div>
            <div class="modal-header">
                <button class="close" type="button" @click="close()">×</button>
                <h4 class="modal-title">Delete item</h4>
            </div>

            <div v-if="loading" class="text-center">
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
                    <button class="btn btn-primary" type="button" @click="deleteItem()">
                        Delete
                    </button>
                    <button class="btn btn-default" type="button" @click="close">
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

            /**
             * Default route prefix
             */
            prefix: {
                default : '/admin/'
            },

            show:{
                default : false
            }
        },

        data: () => {
            return {
                loading: false,
                newItemName: null,
                size: 'modal-md'
            }
        },

        mounted() {
            document.addEventListener("keydown", (e) => {
                if (this.show && e.keyCode === 13) {
                    this.deleteItem();
                }
            });
        },

        methods: {
            close(){
                this.newItemName = null;
                this.loading = false;
                this.$emit('media-modal-close');
            },

            deleteItem(route, data){
                if (this.isFolder(this.currentFile)) {
                    return this.deleteFolder(route, data);
                }
                return this.deleteFile(route, data);
            },

            deleteFile(){
                if (this.currentFile) {
                    const data = {
                        'path': this.currentFile.fullPath
                    };
                    this.byeBye(`${this.prefix}browser/file`, data);
                }
            },

            deleteFolder(){
                if (this.isFolder(this.currentFile)) {
                    const data = {
                        'path' : this.currentFile.fullPath
                    };
                    this.byeBye(`${this.prefix}browser/folder`, data);
                }
            },

            byeBye(route, payload){
                this.loading = true;
                axios.delete(route, {params: payload}).then(
                        (response) => {
                            window.eventHub.$emit('media-manager-reload-folder');
                            this.mediaManagerNotify(response.data.success);
                            this.close();
                        },
                        (response) => {
                            const error = (response.data.error) ? response.data.error : response.statusText;
                            this.mediaManagerNotify(error, 'danger');
                            this.close();
                        }
                );
            }
        }
    }
</script>
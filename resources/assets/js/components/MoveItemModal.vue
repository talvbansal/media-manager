<template>
    <media-modal @media-modal-close="close()" :size="size" :show="show" v-if="show">
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
                    <button class="btn btn-primary" type="button" @click="moveItem()">
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

        data(){
            return {
                allDirectories: {},
                newFolderLocation: null,
                loading: false,
                size: 'modal-md'
            }
        },

        watch: {
            show(open){
                if (open) {
                    this.open();
                }
            }
        },

        mounted(){
            document.addEventListener("keydown", (e) => {
                if (this.show && e.keyCode === 13) {
                    this.moveItem();
                }
            });
        },

        methods: {
            close(){
                this.newFolderName = null;
                this.loading = false;
                this.$emit('media-modal-close');
            },

            open(){
                axios.get(`${this.prefix}browser/directories`).then(
                        (response) => {
                            this.newFolderLocation = this.currentPath;
                            this.allDirectories = response.data;
                        },
                        (response) => {
                            const error = (response.data.error) ? response.data.error : response.statusText;
                            this.mediaManagerNotify(error, 'danger');

                        }
                );
            },

            moveItem(){

                const data = {
                    'path': this.currentPath,
                    'currentItem': this.getItemName(this.currentFile),
                    'newPath': this.newFolderLocation,
                    'type': (this.isFolder(this.currentFile)) ? 'Folder' : 'File'
                };

                this.loading = true;
                axios.post(`${this.prefix}browser/move`, data).then(
                        (response) => {
                            window.eventHub.$emit('media-manager-reload-folder');
                            window.eventHub.$emit('media-manager-notification', response.data.success);
                            this.close();
                        },
                        (response) => {
                            window.eventHub.$emit('reload-folder', response.data.success);
                            window.eventHub.$emit('media-manager-notification', (response.data.error) ? response.data.error : response.statusText, 'danger');
                            this.loading = false;
                        }
                );
            }
        }
    };
</script>

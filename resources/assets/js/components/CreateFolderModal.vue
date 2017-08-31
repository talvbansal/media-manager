<template>
    <media-modal @media-modal-close="close()" :size="size" :show="show" v-if="show">
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
                    <button class="btn btn-primary" type="button" @click="createFolder()">
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
                errors: [],
                loading: false,
                newFolderName: null,
                size: 'modal-md'
            }
        },

        mounted(){
            document.addEventListener("keydown", (e) => {
                if (this.show && e.keyCode === 13) {
                    this.createFolder();
                }
            });
        },

        methods: {
            close(){
                this.newFolderName = null;
                this.loading = false;
                this.errors = [];
                this.$emit('media-modal-close');
            },

            createFolder(){

                if (!this.newFolderName) {
                    this.errors = ['Please provide a name for the new folder'];
                    return;
                }

                const data = {
                    'folder': this.currentPath,
                    'new_folder': this.newFolderName
                };

                this.loading = true;
                axios.post(`${this.prefix}browser/folder`, data).then(
                        (response) => {
                            this.mediaManagerNotify(response.data.success);
                            window.eventHub.$emit('media-manager-reload-folder');
                            this.close();
                        },
                         (response) => {
                             this.errors = (response.data.error) ? response.data.error : response.statusText;
                            this.loading =false;
                        }
                );
            }
        }
    }
</script>

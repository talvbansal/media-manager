<template>
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group offset-right">
            <!-- File input wont get triggered if this is a button so use a label instead -->
            <label class="btn btn-primary btn-icon-text btn-file" title="Select files to be uploaded - or drag files into the main window pane">
                <i class="icon-upload"></i>
                <span class="hidden-xs">Upload</span>
                <input type="file" class="hidden" @change="uploadFile($event.target.name, $event.target.files)" name="files[]"/>
            </label>

            <button class="btn btn-primary btn-icon-text" type="button" title="Add Folder" @click="openModalCreateFolder()">
                <i class="icon-folder-plus"></i>
                <span class="hidden-xs">Add folder</span>
            </button>
        </div>

        <div class="btn-group offset-right">
            <button class="btn btn-default btn-icon-text" type="button" title="Refresh" @click="refresh()">
                <i class="icon-loop2"></i>
                <span class="hidden-xs">Refresh</span>
            </button>
        </div>

        <div class="btn-group offset-right">
            <button class="btn btn-default btn-icon-text" type="button" title="Move" @click="openModalMoveItem()" :disabled="!currentFile" >
                <i class="icon-arrow-right"></i>
                <span class="hidden-xs">Move</span>
            </button>

            <button class="btn btn-default btn-icon-text" type="button" title="Delete" @click="openModalDeleteItem()" :disabled="!currentFile" >
                <i class="icon-bin"></i>
                <span class="hidden-xs">Delete</span>
            </button>

            <button class="btn btn-default btn-icon-text" type="button" title="Rename" @click="openModalRenameItem()" :disabled="!currentFile" >
                <i class="icon-pencil"></i>
                <span class="hidden-xs">Rename</span>
            </button>
        </div>

    </div>
</template>

<script>
    export default{
        props:{
            currentFile : {},
            currentPath : {
                type: String
            }
        },

        methods:{
            refresh(){
                window.eventHub.$emit('media-manager-reload-folder');
            },

            uploadFile(fileName, fileList){
                this.$emit('upload-file', {
                    name : fileName,
                    files: fileList
                });
            },

            openModalCreateFolder(){
                this.$emit('open-modal-create-folder');
            },

            openModalMoveItem(){
                this.$emit('open-modal-move-item');
            },

            openModalDeleteItem(){
                this.$emit('open-modal-delete-item');

            },

            openModalRenameItem(){
                this.$emit('open-modal-rename-item');

            }
        }
    }
</script>
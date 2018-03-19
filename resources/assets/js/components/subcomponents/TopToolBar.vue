<template>
  <div 
    class="btn-toolbar" 
    role="toolbar">
    <div class="btn-group offset-right">
      <!-- File input wont get triggered if this is a button so use a label instead -->
      <label 
        class="btn btn-primary btn-icon-text btn-file" 
        title="Select files to be uploaded - or drag files into the main window pane">
        <i class="icon-upload"/>
        <span class="hidden-xs">Upload</span>
        <input
          class="hidden"
          name="files[]"
          type="file"
          @change="uploadFile($event.target.name, $event.target.files)" 
        >
      </label>

      <button 
        class="btn btn-primary btn-icon-text" 
        type="button" 
        title="Add Folder" 
        @click="openModalCreateFolder()">
        <i class="icon-folder-plus"/>
        <span class="hidden-xs">Add folder</span>
      </button>
    </div>

    <div class="btn-group offset-right">
      <button 
        class="btn btn-default btn-icon-text" 
        type="button" 
        title="Refresh" 
        @click="refresh()">
        <i class="icon-loop2"/>
        <span class="hidden-xs">Refresh</span>
      </button>
    </div>

    <div class="btn-group offset-right">
      <button
        :disabled="!isFile(currentFile)"
        class="btn btn-default btn-icon-text" 
        type="button" 
        title="Move"
        @click="openModalMoveItem()" >
        <i class="icon-arrow-right"/>
        <span class="hidden-xs">Move</span>
      </button>

      <button 
        :disabled="!isFile(currentFile)"
        class="btn btn-default btn-icon-text"
        type="button" 
        title="Delete"
        @click="openModalDeleteItem()" >
        <i class="icon-bin"/>
        <span class="hidden-xs">Delete</span>
      </button>

      <button
        :disabled="!isFile(currentFile)"
        class="btn btn-default btn-icon-text" 
        type="button" 
        title="Rename"
        @click="openModalRenameItem()" >
        <i class="icon-pencil"/>
        <span class="hidden-xs">Rename</span>
      </button>
    </div>

  </div>
</template>

<script>
import fileManagerMixin from "./../../mixins/file-manager-mixin";

export default{
	mixins: [fileManagerMixin],

	props:{
		currentFile : {
		    default: function(){
		        return {};
			},
			type: [Object, Boolean]
		},
		currentPath : {
			default: "",
			type: String
		}
	},

	methods:{
		refresh(){
			this.$emit("reload-folder");
		},

		uploadFile(fileName, fileList){
			this.$emit("upload-file", {
				name : fileName,
				files: fileList
			});
		},

		openModalCreateFolder(){
			this.$emit("open-modal-create-folder");
		},

		openModalMoveItem(){
			this.$emit("open-modal-move-item");
		},

		openModalDeleteItem(){
			this.$emit("open-modal-delete-item");

		},

		openModalRenameItem(){
			this.$emit("open-modal-rename-item");

		}
	}
};
</script>
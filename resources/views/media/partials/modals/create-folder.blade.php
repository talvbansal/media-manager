<div class="modal fade" id="easel-new-folder" tabIndex="-1" role="dialog">
    <div class="modal-dialog easel-adaptive-modal">
        <div class="modal-content">

            <div class="modal-header">
                <button data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">New folder</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Folder name</label>
                    <input type="text" value="" v-model="newFolderName" class="form-control" id="newFolderName">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" @click="createFolder()" id=btnCreateFolder>
                Apply
                </button>
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

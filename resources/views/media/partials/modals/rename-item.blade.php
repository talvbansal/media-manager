<div class="modal fade" id="easel-rename-item" tabIndex="-1" role="dialog">
    <div class="modal-dialog easel-adaptive-modal">
        <div class="modal-content">

            <div class="modal-header">
                <button data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">Rename item</h4>
            </div>
            <div class="modal-body">


                <div class="form-group">
                    <label>Current name</label>
                    <p class="form-control-static">@{{ this.getItemName(this.currentFile) }}</p>
                </div>

                <div class="form-group">
                    <label>New name</label>
                    <input type="text" value="" v-model="newItemName" class="form-control" id="newItemName">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" @click="renameItem()" id="btnRename">
                Apply
                </button>
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

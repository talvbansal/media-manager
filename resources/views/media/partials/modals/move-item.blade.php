<div class="modal fade" id="easel-move-item" tabIndex="-1" role="dialog">
    <div class="modal-dialog easel-adaptive-modal">
        <div class="modal-content">

            <div class="modal-header">
                <button data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">Move item</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Item name</label>
                    <p class="static">@{{ this.getItemName(this.currentFile) }}</p>
                </div>

                <div class="form-group">
                    <label>Move to</label>
                    <select class="form-control" v-model="newFolderLocation" id="newFolderLocation" name="newFolderLocation">
                        <option v-for="(path, name) in allDirectories" :value="path">@{{{ name }}}</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" @click="moveItem()" id=btnMoveItem>
                Apply
                </button>
                <button data-dismiss="modal" class="btn btn-default" type="button">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

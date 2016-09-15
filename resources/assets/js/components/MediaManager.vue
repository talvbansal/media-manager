<template>

</template>

<script>
    export default {

        data: function () {

            return {
                isModal: false,
                currentFile: null,
                selectedFile: null,
                currentPath: null,
                folderName: null,
                folders: {},
                files: {},
                breadCrumbs: {},
                loading: true,
                insertIntoEditor: false,
                newFolderName: null,
                newItemName: null,
                allDirectories: {},
                newFolderLocation: null
            }
        }

    }
</script>
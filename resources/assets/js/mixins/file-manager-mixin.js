export default {
    methods: {
        getItemName: function (item) {
            if (!item) {
                return null;
            }

            return ( this.isFolder(item) ) ? item : item.name;
        },

        isImage: function (file) {
            return file.mimeType.indexOf('image') != -1;
        },

        isFolder: function (file) {
            return (typeof file == 'string');
        }
    }
};

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
        },

        mediaManagerNotify: function (notices, type, time) {

            if (!type) type = 'inverse';
            if (!time) time = 4000;

            if (typeof notices == 'object') {
                for (var i = 0, len = notices.length; i < len; i++) {
                    window.eventHub.$emit('media-manager-notification', notices[i], type, time);
                }
                return;
            }
            window.eventHub.$emit('media-manager-notification', notices, type, time);
        },
    }
};

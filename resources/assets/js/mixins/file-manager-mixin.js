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

        notify: function (notices, type, time) {
            if (typeof notices == 'object') {
                for (var i = 0, len = notices.length; i < len; i++) {
                    this.$dispatch('media-manager-notification', notices, type, time);
                }
                return;
            }
            this.$dispatch('media-manager-notification', notices, type, time);
        },
    }
};

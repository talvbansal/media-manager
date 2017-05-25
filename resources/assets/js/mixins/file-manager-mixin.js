export default {
    methods: {
        getItemName: (item) => {
            if (!item) {
                return null;
            }

            return item.name;
        },

        isImage: (file) => {
            return file.mimeType.indexOf('image') != -1;
        },

        isFolder: (file) => {
            return (file.mimeType == 'folder');
        },

        mediaManagerNotify: (notices, type = 'inverse', time = 4000) => {

            if (typeof notices == 'object') {
                notices.forEach(function(notice) {
                    window.eventHub.$emit('media-manager-notification', notice, type, time);
                });
                return;
            }
            window.eventHub.$emit('media-manager-notification', notices, type, time);
        },
    }
};

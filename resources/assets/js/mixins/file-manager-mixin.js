import { isEmpty } from "lodash";

export default {
	methods: {
		getItemName: (file) => {
			if(isEmpty(file)){
				return false;
			}

			return file.name;
		},

		isImage: (file) => {

			if(isEmpty(file)){
				return false;
			}

			return file.mimeType.indexOf("image") !== -1;
		},

		isFolder: (file) => {

			if(isEmpty(file)){
				return false;
			}

			return (file.mimeType === "folder");
		},

		mediaManagerNotify: (notices, type = "inverse", time = 4000) => {

			if (typeof notices === "object") {
				notices.forEach(function(notice) {
					window.eventHub.$emit("media-manager-notification", notice, type, time);
				});
				return;
			}
			window.eventHub.$emit("media-manager-notification", notices, type, time);
		},
	}
};

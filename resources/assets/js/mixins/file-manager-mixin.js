import { isEmpty } from "lodash";
import dayjs from "dayjs";

export default {
	filters:{
		formatDate: (date, format = "DD/MM/YYYY hh:mm:ss A") => {
			if (!date) return null;

			return dayjs(date).format(format);
		}
	},

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


		isFile: (file) => {
			return !isEmpty(file);
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

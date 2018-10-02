import { isEmpty } from "lodash";
import moment from "moment";

export default {
    filters:{
        moment: (date, format) => {
            if (!date) return null;

            if (!format) format = "DD/MM/YYYY LTS";

            return moment(date).utc().format(format);
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

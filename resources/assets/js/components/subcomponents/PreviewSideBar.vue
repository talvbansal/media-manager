<template>
  <div
    v-if="isFile(currentFile) && !isFolder(currentFile)"
    class="media-manager-file-picker-sidebar d-none d-md-block col-sm-3">

    <img
      v-if="isImage(currentFile)"
      id="media-manager-preview-image"
      :src="currentFile.webPath"
      class="img-responsive text-center d-block mx-auto"
    >

    <div
      v-else
      class="text-center">
      <i
        class="icon-file-text2"/>
    </div>

    <dl class="my-4">
      <dt>Name</dt>
      <dd>{{ currentFile.name }}</dd>

      <dt>Size</dt>
      <dd>{{ currentFile.size | humanFileSize }}</dd>

      <dt>URL</dt>
      <dd>
        <a
            :href="currentFile.webPath"
            target="_blank"
            rel="noopener">{{ currentFile.relativePath }}</a>
      </dd>

      <dt>Uploaded on</dt>
      <dd>
        {{ currentFile.modified | formatDate('DD/MM/YYYY') }}<br/>
        {{ currentFile.modified | formatDate('HH:mm:ss A') }}<br/>
      </dd>
    </dl>
  </div>
</template>

<style scoped>
	.icon-file-text2{
		font-size: 15rem;
	}

	#media-manager-preview-image{
		max-height: 200px;
	}
</style>

<script>
import FileManagerMixin from "./../../mixins/file-manager-mixin";
import dayjs from "dayjs";

export default{

	filters: {
      // Take any integer of bytes and convert it into something more human readable...
      humanFileSize: function (size) {
        let i = Math.floor(Math.log(size) / Math.log(1024));
        return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + " " + ["B", "kB", "MB", "GB", "TB"][i];
      },
      formatDate: (date, format = 'DD/MM/YYYY HH:mm:ss A') => {
        if (!date) return null;

        return dayjs(date).format(format);
      }
    },

	mixins: [FileManagerMixin],

	props:{
		currentFile : {
			default: function(){
				return {};
			},
			type: [Object, Boolean]
		},
	}
};
</script>
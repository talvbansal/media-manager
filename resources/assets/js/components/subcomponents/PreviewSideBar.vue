<template>
  <div
    v-if="isFile(currentFile) && !isFolder(currentFile)"
    class="easel-file-picker-sidebar hidden-xs col-sm-3">

    <img
      v-if="isImage(currentFile)"
      id="easel-preview-image"
      :src="currentFile.webPath"
      class="img-responsive center-block"
    >

    <div
      v-else
      class="text-center">
      <i
        class="icon-file-text2"/>
    </div>

    <table class="table-responsive table-condensed table-vmiddle easel-file-picker-preview-table">
      <tbody>
        <tr>
          <td class="description">Name</td>
          <td class="file-value">{{ currentFile.name }}</td>
        </tr>
        <tr>
          <td class="description">Size</td>
          <td class="file-value">{{ currentFile.size | humanFileSize }}</td>
        </tr>
        <tr>
          <td class="description">URL</td>
          <td class="file-value">
            <a
              :href="currentFile.webPath"
              target="_blank"
              rel="noopener">{{ currentFile.relativePath }}</a>
          </td>
        </tr>
        <tr>
          <td class="description">Uploaded On</td>
          <td class="file-value">{{ currentFile.modified.date | moment }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
	.icon-file-text2{
		font-size: 15rem;
	}

	#easel-preview-image{
		max-height: 200px;
	}
</style>

<script>
import fileManagerMixin from "./../../mixins/file-manager-mixin";

export default{

	filters: {
		// Take any integer of bytes and convert it into something more human readable...
		humanFileSize: function (size) {
			let i = Math.floor(Math.log(size) / Math.log(1024));
			return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + " " + ["B", "kB", "MB", "GB", "TB"][i];
		}
	},

	mixins: [fileManagerMixin],

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
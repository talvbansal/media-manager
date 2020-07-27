<template>
  <transition name="modal">
    <div 
      class="modal media-modal modal-mask" 
      @click="$emit('media-modal-close')">
      <div
        :class="[size]"
        class="modal-dialog modal-dialog-centered"
        @click.stop>
        <div class="modal-content">
          <slot/>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {

	props: {
		"size" :{
			default : "modal-lg",
			type: String
		}
	},

	mounted(){
		document.addEventListener("keydown", (e) => {
			if (this.show && e.keyCode == 27) {
				this.close();
			}
		});
	}
};
</script>

<template>
    <div class="modal media-modal modal-mask" @click="close" v-show="show" transition="modal">
        <div class="modal-dialog" :class="[size]" @click.stop>
            <div class="modal-content">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            show: {
                required: false
            },
            onClose: {
                required: false
            },
            onOpen: {
                required: false
            },
            size: {
                default: 'modal-lg'
            }
        },
        methods: {
            open: function()
            {
                this.show = true;
            },

            close: function () {
                this.show = false;
            }
        },
        ready: function () {
            document.addEventListener("keydown", (e) => {
                if (this.show && e.keyCode == 27) {
                    this.close();
                }
            });
        }
    }
</script>

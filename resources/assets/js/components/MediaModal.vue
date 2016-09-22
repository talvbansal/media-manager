<template>
    <div class="modal-mask" @click="close" v-show="show" transition="modal">
        <div class="modal-container" @click.stop  :class="[size]">
            <slot></slot>
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
                default: null
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

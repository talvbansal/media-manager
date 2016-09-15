<template>
    <div class="modal-mask" @click="close" v-show="show" transition="modal">
        <div class="modal-container" @click.stop>
            <slot></slot>
        </div>
    </div>
</template>

<style>

    .modal-mask {
        position: fixed;
        z-index: 9998;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .5);
        transition: opacity .3s ease;
    }

    .modal-container {
        width: auto;
        margin: 3em;
        padding: 1.5em;
        background-color: #fff;
        border-radius: 2px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
        transition: all .3s ease;
        font-family: Helvetica, Arial, sans-serif;
    }

    .modal-header h3 {
        margin-top: 0;

    }

    .modal-body {
        margin: 20px 0;
        max-height: calc(100vh - 265px);
        overflow-y: auto;
    }

    .text-right {
        text-align: right;
    }

    .form-label {
        display: block;
        margin-bottom: 1em;
    }

    .form-label > .form-control {
        margin-top: 0.5em;
    }

    .form-control {
        display: block;
        width: 100%;
        padding: 0.5em 1em;
        line-height: 1.5;
        border: 1px solid #ddd;
    }

    .modal-enter, .modal-leave {
        opacity: 0;
    }

    .modal-enter .modal-container,
    .modal-leave .modal-container {
        -webkit-transform: scale(1.1);
        transform: scale(1.1);
    }
</style>
<script>
    export default {
        props: ['show', 'onClose', 'onOpen'],
        methods: {
            open: function()
            {
                this.show = true;
                this.onOpen();
            },

            close: function () {
                this.onClose();
            }
        },
        ready: function () {
            document.addEventListener("keydown", (e) => {
                if (this.show && e.keyCode == 27) {
                    this.onClose();
                }
            });
        }
    }
</script>

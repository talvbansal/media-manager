<template>
    <div v-if="errors.length > 0" class="modal-errors" transition="expand">
        <ul>
            <li v-for="error in errors">{{ error }}</li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: [
            'errors'
        ]
    }
</script>
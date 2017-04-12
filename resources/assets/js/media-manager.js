/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./base');

import FileManagerMixin from './mixins/file-manager-mixin';
Vue.mixin(FileManagerMixin);

/**
 * Register Vue components...
 */
Vue.component('media-modal', require('./components/MediaModal.vue'));
Vue.component('media-create-folder-modal', require('./components/CreateFolderModal.vue'));
Vue.component('media-delete-item-modal', require('./components/ConfirmDeleteModal.vue'));
Vue.component('media-errors', require('./components/Errors.vue'));
Vue.component('media-move-item-modal', require('./components/MoveItemModal.vue'));
Vue.component('media-rename-item-modal', require('./components/RenameItemModal.vue'));
Vue.component('media-manager', require('./components/MediaManager.vue'));


/**
 * Register Vue Filters
 */
// Take any integer of bytes and convert it into something more human readable...
Vue.filter('humanFileSize', function (size) {
    var i = Math.floor(Math.log(size) / Math.log(1024));
    return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
});

// Date formatting filter...
Vue.filter('moment', function (date, format) {

    if (!date) return null;

    if (!format) format = 'DD/MM/YYYY LTS';

    return moment(date).utc().format(format)
});

/**
 * If it doesn't already exist, register a separate empty vue instance that
 * is attached to the window, we can use it to listen out for and handle
 * any events that may emitted by components...
 */
if (!window.eventHub) {
    window.eventHub = new Vue();
}

/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

Vue.component('modal', require('./components/Modal.vue'));
Vue.component('media-manager', require('./components/MediaManager.vue'));


// Date Formatting Filter
Vue.filter('moment', function( date, format ){

    if( ! date ) return null;

    if( ! format ) format = 'DD/MM/YYYY LTS';

    return moment().utc(date).local().format(format)
});
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */
require("./base");

window.Vue = require("vue");

/**
 * Moment is a javascript library that we can use to format dates
 * It's similar to Carbon in PHP so we mostly use it to format
 * dates that are returned from our Laravel Eloquent models
 */
window.moment = require("moment");

/**
 * Register Vue components...
 */
window.Vue.component("media-modal", require("./components/MediaModal.vue"));
window.Vue.component("media-manager", require("./components/MediaManager.vue"));


/**
 * Register Vue Filters
 */
// Take any integer of bytes and convert it into something more human readable...
window.Vue.filter("humanFileSize", function (size) {
	var i = Math.floor(Math.log(size) / Math.log(1024));
	return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + " " + ["B", "kB", "MB", "GB", "TB"][i];
});

// Date formatting filter...
window.Vue.filter("moment", function (date, format) {

	if (!date) return null;

	if (!format) format = "DD/MM/YYYY LTS";

	return window.moment(date).utc().format(format);
});

/**
 * If it doesn't already exist, register a separate empty vue instance that
 * is attached to the window, we can use it to listen out for and handle
 * any events that may emitted by components...
 */
if (!window.eventHub) {
	window.eventHub = new window.Vue();
}

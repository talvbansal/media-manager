/* global Vue */
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */
require("./base");

window.Vue = require("vue");

/**
 * Register Vue components...
 */
import MediaModal from "./components/MediaModal.vue";
import MediaManager from "./components/MediaManager.vue";

Vue.component("media-modal", MediaModal);
Vue.component("media-manager", MediaManager);

/**
 * If it doesn't already exist, register a separate empty vue instance that
 * is attached to the window, we can use it to listen out for and handle
 * any events that may emitted by components...
 */
if (!window.eventHub) {
	window.eventHub = new Vue();
}

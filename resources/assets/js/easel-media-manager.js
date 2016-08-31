Vue.filter('moment', function(value, format) {
    return moment.utc(value).local().format(format);
});

Vue.filter('humanFileSize',  function (size) {
    var i = Math.floor(Math.log(size) / Math.log(1024));
    return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
});

Vue.use(VueTouch);
VueTouch.registerCustomEvent('doubletap', {
    type: 'tap',
    taps: 2
});
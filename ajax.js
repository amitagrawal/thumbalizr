var getThumbalizr, options;

options = {
    path: '/'
};
getThumbalizr = function (url, callback) {
    $.get(options.path + 'ajax.php?url=' + url, function (resp) {
        if ($.isFunction(callback)) {
            callback(resp);
        }
    });
};

$(function () {
    getThumbalizr('http://www.example.com', function (resp) {
        if (resp.status === 'ok' || resp.status === 'local') {
            // success.
        } else {
            // error.
            // alert(resp.message);
        }
    });
});
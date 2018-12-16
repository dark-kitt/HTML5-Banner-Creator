$(document).ready(function() {

    var unusedCSS = JSON.parse(localStorage.getItem('unusedCSS')),
        unusedCon = $('.unusedCSS-wrapper');

    if (unusedCSS.length === 0)
    {
        unusedCon.append('<ul class="clearfix">' +
            '<li class="text text-size-normal">Nothing found.</li>' +
        '</ul>');
    }
    else
    {
        $.each(unusedCSS, function(key, value) {
            $.each(value, function(k, v) {
                unusedCon.append('<ul class="clearfix">' +
                    '<li>' + v['identifier'] + '</li>' +
                    '<li>' + v['message'] + '</li>' +
                    '<li>row: ' + v['line'].join('') + '</li>' +
                    '<li>' + v['directory'] + '</li>'+
                '</ul>');
            });
        });
    }
});

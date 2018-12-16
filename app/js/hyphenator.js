$(document).ready(function() {

    var saved = $('select[name="saved-values"]'),
        source = $('textarea[name="source"]'),
        radio = $('input[type="radio"]'),
        char = $('input[name="char"]'),
        identifier = $('input[name="identifier"]'),
        directory = $('input[name="directory"]'),
        language = $('select[name="language"]'),
        exeptions = $('textarea[name="exeptions"]');

    var wlocation = window.location.origin;

    saved.on('change', function() {
        var $self = $(this);

        if ($self.val() !== '') {
            $.getJSON( wlocation + '/app/hyphenator-cache' + $self.val(), function( data ) {
                source.val(data.source);
                $('input[value="' + data.replace + '"]').prop('checked', 'true');
                char.val(data.char);
                identifier.val(data.identifier);
                directory.val(data.directory);
                language.val(data.language);
                exeptions.val(data.exeptions.join(','));
            });
        } else {
            clearFrom();
        }

    });

    $('#delete-saved-value').click(function() {

        if (saved.val() !== '') {

            var json = saved.val();

                if (confirm('Do you really wanna delete ' + json.substring(1) + '!')) {
                    $.ajax({
                        type: 'post',
                        url: wlocation + '/app/handle-posts.php',
                        data: {
                            delete_json: json
                        },
                        success: function() {
                            $('option[value="' + json + '"]').remove();
                            clearFrom();
                        }
                    });
                }
        }

    });

    $('#save-values').click(function() {

        var options = {};
            options.source = source.val(),
            options.replace = $('input[name="replace"]:checked').val(),
            options.char = char.val(),
            options.identifier = identifier.val(),
            options.directory = directory.val(),
            options.language = language.val(),
            options.exeptions = exeptions.val().replace(/\s+/g, '').split(',');

        var name = prompt('Please enter a file name.', '');

        if (name !== null && name !== '') {
            $.ajax({
                type: 'post',
                url: wlocation + '/app/handle-posts.php',
                data: {
                    save_options: JSON.stringify(options),
                    file_name: name
                }
            });
        }

    });

    $('textarea[name="source"], select[name="language"], input[type="text"]').on('invalid', function() {
        var $self = $(this);
        this.setCustomValidity('');

        if (!this.validity.valid && $self.attr('name') === 'source') {
            this.setCustomValidity('Please enter some text.');
        }

        if (!this.validity.valid && $self.attr('name') === 'language') {
            this.setCustomValidity('Please choose a language.');
        }

        if (!this.validity.valid && $self.attr('name') === 'identifier') {
            this.setCustomValidity('Please enter a identifier.');
        }

        if (!this.validity.valid && $self.attr('name') === 'directory') {
            this.setCustomValidity('Please enter a directory.');
        }
    });

    var clearFrom = function() {
        source.val('');
        radio.prop('checked', 'false');
        char.val('');
        identifier.val('');
        directory.val('');
        language.val('');
        exeptions.val('');
    }

    var escapeRegExp = function(str) {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&');
    }

    var disableBtn = function(elem) {
        elem.addClass('disabled');
        setTimeout(function () {
            elem.removeClass('disabled');
        }, 500);
    };

    $('.button-hyphenator').on('click', function(event) {
        var $self = $(this);

        if (!$self.hasClass('disabled')) {
            disableBtn($self);

            var cnt = $('textarea[name=source]').val(),
                rep = $('input[name=replace]:checked').val(),
                ch = $('input[name=char]').val(),
                id = $('input[name=identifier]').val(),
                dir = $('input[name=directory]').val(),
                lang = $('select[name=language]').val(),
                exep = $('textarea[name=exeptions]').val().replace(/\s+/g, '').split(',');

            if (rep === undefined) {
                rep = 'input';
            }

            if (ch === undefined || ch === '') {
                ch = '|';
            }

            $.each( exep, function(key, val) {
                Hyphenator.addExceptions( lang, val );
            });

            Hyphenator.config({
                hyphenchar : ch
            });

            var pattern = new RegExp(escapeRegExp(ch), 'g'),
                output = Hyphenator.hyphenate(cnt, lang).replace(pattern, '&shy;');

            if (rep === 'input') {
                source.val(output);
                return false;
            } else {

                $.ajax({
                    type: 'post',
                    url: wlocation + '/app/handle-posts.php',
                    data: {
                        dir: dir,
                        id: id,
                        file: rep,
                        output: output
                    },
                    success: function(response) {
                        if (typeof(response) === 'string' && response.length > 0) {
                            alert(response);
                        } else {
                            $('textarea').val('');
                            $('input').val('');
                            $('select').val('');
                            location.reload(true);
                        }
                    }
                });
            }
        }

    });

});

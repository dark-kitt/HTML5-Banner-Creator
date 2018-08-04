$(document).ready(function() {
    var disableCheck = false,
        enableTime = 1500;

    function getBannerPath(elem) {
        var pathArr = [];

        while (elem.parent().length) {
            elem = elem.parent();
            pathArr.push(elem.children('a').text());

            if (elem.attr('aria-level') === '1') {
                return pathArr;
            }
        }

        return null;
    }

    function flatten_arrays(arrays) {
        return [].concat.apply([], arrays);
    }

    /**
     *
     * jstree
     * ajax request to call the active banner
     *
     */
    $('#jstree').on('click', '.jstree-anchor', function(e) {

        setTimeout(function() {
            disableCheck = false;
        }, enableTime);
        if (disableCheck === false) {
            disableCheck = true;

            $('#jstree').jstree(true).toggle_node(e.target);

            if ($(this).parent().hasClass('jstree-open')) {
                $(this).children('i').css('transform', 'rotate(180deg)');
            } else {
                $(this).children('i').css('transform', 'rotate(90deg)');
            }

            $('.jstree-open').each(function() {
                $(this).children('a').children('i').css('transform', 'rotate(180deg)');
            });

            if ($(this).parent().hasClass('jstree-leaf')) {
                var bannerPath = getBannerPath($(this)).reverse(),
                    pathFiltered = bannerPath.filter(function(x) {
                        return (x !== (undefined || null || ''));
                    }),
                    resultPath = pathFiltered.join('/'),
                    result = '/projects/' + resultPath,
                    storedPath = JSON.parse(localStorage.getItem('current_project_path')).match(/\/projects\b\/.*/)[0];

                if (result.match(storedPath)) {
                    $.ajax({
                        type: 'post',
                        url: 'classes/banner.php',
                        data: {
                            banner: 'create_banner',
                            banner_path: result
                        },
                        success: function(response) {

                            var responseArr = JSON.parse(response);
                            $('#banner').empty();
                            $.each(responseArr[0], function(index, value) {
                                $('#banner').append(value);
                            });

                            $('#unusedCSS').text('unusedCSS: ( ' + flatten_arrays(responseArr[1]).length + ' )');
                            localStorage.setItem('unusedCSS', JSON.stringify(flatten_arrays(responseArr[1])));

                            $.ajax({
                                type: 'post',
                                url: 'classes/helper.php',
                                data: {
                                    banner_size: 'folderSize',
                                    banner_path: result
                                },
                                success: function(response) {
                                    $('#banner-size').text('size: ' + response);
                                    $.ajax({
                                        type: 'post',
                                        url: 'classes/banner.php',
                                        data: {
                                            banner: 'update_banners',
                                            project_path: result
                                        },
                                        success: function(response) {

                                        }
                                    });
                                    return false;
                                }
                            });
                            return false;
                        }
                    });
                    return false;
                } else {
                    var current_project_path = JSON.parse(localStorage.getItem('current_project_path')).match(/(.*)\/projects\b\//)[1] + result;

                    $.ajax({
                        type: 'post',
                        url: 'classes/banner.php',
                        data: {
                            banner: 'create_banner',
                            banner_path: result
                        },
                        success: function(response) {

                            var responseArr = JSON.parse(response);
                            $('#banner').empty();
                            $.each(responseArr[0], function(index, value) {
                                $('#banner').append(value);
                            });

                            $('#unusedCSS').text('unusedCSS: ( ' + flatten_arrays(responseArr[1]).length + ' )');
                            localStorage.setItem('unusedCSS', JSON.stringify(flatten_arrays(responseArr[1])));

                            $.ajax({
                                type: 'post',
                                url: 'classes/helper.php',
                                data: {
                                    banner_size: 'folderSize',
                                    banner_path: result
                                },
                                success: function(response) {
                                    $('#banner-size').text('size: ' + response);

                                    $.ajax({
                                        type: 'post',
                                        url: 'classes/export.php',
                                        data: {
                                            export: 'create_checkboxes',
                                            file_info: 'banner-files',
                                            project_path: current_project_path
                                        },
                                        success: function(response) {

                                            var responseArr = JSON.parse(response),
                                                current_project_path = JSON.stringify(responseArr[1][0]);
                                            $('[data-files=banner]').siblings('ul').children().remove();

                                            $.each(responseArr[0], function(index, value) {
                                                $('[data-files=banner]').siblings('ul').append(value);
                                            });
                                            localStorage.setItem('current_project_path', current_project_path);

                                            $.ajax({
                                                type: 'post',
                                                url: 'classes/banner.php',
                                                data: {
                                                    banner: 'update_banners',
                                                    project_path: result
                                                }
                                            });
                                            return false;
                                        }
                                    });
                                    return false;
                                }
                            });
                            return false;
                        }
                    });
                    return false;
                }
            }
        }
    }).jstree({
        plugins: ['state'],
        core: {
            dblclick_toggle: false,
            data: {
                type: 'POST',
                url: 'classes/jstree.php',
                dataType: 'json',
                data: {
                    jstree: 'create_folder_structure'
                },
                success: function(response) {
                    return {
                        id: response.id,
                    };
                }
            }
        }
    }).on('ready.jstree', function() {
        $('.jstree-open').each(function() {
            $(this).children('a').children('i').css('transform', 'rotate(180deg)');
        });
        $('.jstree-leaf').each(function() {
            $(this).children('a').children('i').css('background-image', 'assets/playbutton.svg');
        });
    });

    /**
     *
     * sidebar left/right
     * handling width and position with localstorage
     * update storage on resize
     *
     */
    var sidebarRightPosition = localStorage.getItem('sidebar-right');
    if (sidebarRightPosition !== null) {
        if (sidebarRightPosition.match(/open:.*/)[0].includes('true')) {
            $('.sidebar-right').addClass('sidebar-open').css({
                right: '0',
                width: parseInt(sidebarRightPosition.match(/\d+/)[0])
            });
            $('.nav-right').css('right', parseInt(sidebarRightPosition.match(/\d+/)[0]) + 'px');

            $('.sidebar-arrow-right').css('transform', 'rotate(90deg)');
        } else {
            $('.sidebar-right').css({
                right: (parseInt(sidebarRightPosition.match(/\d+/)[0]) * -1),
                width: parseInt(sidebarRightPosition.match(/\d+/)[0])
            });
            $('.nav-right').css('right', parseInt(sidebarRightPosition.match(/\d+/)[0]) + 'px');

            $('.sidebar-arrow-right').css('transform', 'rotate(-90deg)');
        }
    } else {
        $('.sidebar-right').addClass('sidebar-open');
    }

    var sidebarLeftPosition = localStorage.getItem('sidebar-left');
    if (sidebarLeftPosition !== null) {
        if (sidebarLeftPosition.match(/open:.*/)[0].includes('true')) {
            $('.sidebar-left').addClass('sidebar-open').css({
                left: '0',
                width: parseInt(sidebarLeftPosition.match(/\d+/)[0])
            });
            $('.nav-left').css('left', parseInt(sidebarLeftPosition.match(/\d+/)[0]) + 'px');

            $('.sidebar-arrow-left').css('transform', 'rotate(-90deg)');
        } else {
            $('.sidebar-left').css({
                left: (parseInt(sidebarLeftPosition.match(/\d+/)[0]) * -1),
                width: parseInt(sidebarLeftPosition.match(/\d+/)[0])
            });
            $('.nav-left').css('left', parseInt(sidebarLeftPosition.match(/\d+/)[0]) + 'px');

            $('.sidebar-arrow-left').css('transform', 'rotate(90deg)');
        }
    } else {
        $('.sidebar-left').addClass('sidebar-open');
    }

    $('.sidebar-arrow').click(function() {
        var currentWidth;
        if ($(this).hasClass('sidebar-arrow-right')) {
            if (!$('.sidebar-right').hasClass('sidebar-open')) {
                currentWidth = $('.sidebar-right').outerWidth();
                $('.sidebar-right').addClass('sidebar-open').css('right', '0');
                $(this).css('transform', 'rotate(90deg)');
                localStorage.setItem('sidebar-right', 'width: ' + currentWidth + ',' + 'open: true');
            } else {
                currentWidth = $('.sidebar-right').outerWidth();
                $('.sidebar-right').removeClass('sidebar-open').css('right', (currentWidth * -1) + 'px');
                $(this).css('transform', 'rotate(-90deg)');
                localStorage.setItem('sidebar-right', 'width: ' + currentWidth + ',' + 'open: false');
            }
        } else {
            if (!$('.sidebar-left').hasClass('sidebar-open')) {
                currentWidth = $('.sidebar-left').outerWidth();
                $('.sidebar-left').addClass('sidebar-open').css('left', '0');
                $(this).css('transform', 'rotate(-90deg)');
                localStorage.setItem('sidebar-left', 'width: ' + currentWidth + ',' + 'open: true');
            } else {
                currentWidth = $('.sidebar-left').outerWidth();
                $('.sidebar-left').removeClass('sidebar-open').css('left', (currentWidth * -1) + 'px');
                $(this).css('transform', 'rotate(90deg)');
                localStorage.setItem('sidebar-left', 'width: ' + currentWidth + ',' + 'open: false');
            }
        }
    });

    $(function() {
        $('.resizable-wrapper-right').resizable({
            handles: "w",
            minWidth: 300,
            resize: function(event, ui) {
                localStorage.setItem('sidebar-right', 'width: ' + ui.size.width + ',' + 'open: true');
                $('.sidebar-right').css('width', 'auto');
                $('.nav-right').css('right', ui.size.width + 'px');
            }
        });

        $('.resizable-wrapper-left').resizable({
            handles: "e",
            minWidth: 300,
            resize: function(event, ui) {
                localStorage.setItem('sidebar-left', 'width: ' + ui.size.width + ',' + 'open: true');
                $('.sidebar-left').css('width', 'auto');
                $('.nav-left').css('left', ui.size.width + 'px');
            }
        });
    });

    /**
     *
     * collapses
     * remind checked checkboxes
     * refresh button
     *
     */
    $('.collapse-body').each(function(key, value) {
        var collapseValues = localStorage.getItem(key + '-collapse-open');
        if (collapseValues !== null && collapseValues.match(/open:.*/)[0].includes('true')) {
            $(this).addClass('collapse-open');
            $(this).siblings('.collapse-head').children('.collapse-arrow').css('transform', 'rotate(0deg)');
            if ($(this).hasClass('scripts-collapse')) {
                $(this).addClass('collapse-open').css('max-height', 'calc((100vh / 3) - 75px)');
            }
            if ($(this).hasClass('banner-collapse')) {
                $(this).addClass('collapse-open').css('max-height', 'calc((100vh / 3) + 75px)');
            }
            if ($(this).hasClass('tree-collapse')) {
                $(this).addClass('collapse-open').css('max-height', 'calc(100vh - 53px)');
            }
        } else {
            $(this).children('.collapse-arrow').css('transform', 'rotate(180deg)');
            $(this).css('max-height', '0');
        }
    });

    $('.collapse-head').click(function() {
        var currentHeight;
        if (!$(this).siblings('.collapse-body').hasClass('collapse-open')) {
            currentHeight = $(this).siblings('.collapse-body').children('.collapse-body-content').outerHeight();
            $(this).children('.collapse-arrow').css('transform', 'rotate(0deg)');
            if ($(this).siblings('.collapse-body').hasClass('scripts-collapse')) {
                $(this).siblings('.collapse-body').addClass('collapse-open').css('max-height', 'calc((100vh / 3) - 75px)');
            }
            if ($(this).siblings('.collapse-body').hasClass('banner-collapse')) {
                $(this).siblings('.collapse-body').addClass('collapse-open').css('max-height', 'calc((100vh / 3) + 75px)');
            }
            if ($(this).siblings('.collapse-body').hasClass('tree-collapse')) {
                $(this).siblings('.collapse-body').addClass('collapse-open').css('max-height', 'calc(100vh - 53px)');
            }
        } else {
            $(this).children('.collapse-arrow').css('transform', 'rotate(180deg)');
            $(this).siblings('.collapse-body').removeClass('collapse-open').css('max-height', '0');
        }

        $('.collapse-body').each(function(key, value) {
            currentHeight = $(this).children('.collapse-body-content').outerHeight();
            localStorage.setItem(key + '-collapse-open', 'open: ' + $(this).hasClass('collapse-open'));
        });

    });

    var activeCheckbox = localStorage.getItem('checkbox-checked');
    if (activeCheckbox !== null) {
        activeCheckbox.split(', ');
        $('.checkbox input').each(function(key, value) {
            if (this.value === activeCheckbox.split(', ')[key]) {
                $(this).prop('checked', true);
            }
        });
    }

    $('.checkbox').click(function() {
        var activeCheckboxArr = [];
        $('.checkbox input').each(function() {
            if (this.checked) {
                activeCheckboxArr.push(this.value);
            } else {
                activeCheckboxArr.push('false');
            }
        });
        localStorage.setItem('checkbox-checked', activeCheckboxArr.join(', '));
    });

    $('.export-button').on('click', function(event) {

        setTimeout(function() {
            disableCheck = false;
        }, enableTime);
        if (disableCheck === false) {
            disableCheck = true;

            var headArr = [],
                bodyArr = [],
                bannerArr = [];
            if ($("input:checkbox[name=head-files]:checked")) {
                $("input:checkbox[name=head-files]:checked").each(function() {
                    headArr.push($(this).val());
                });
            }

            if ($("input:checkbox[name=body-files]:checked")) {
                $("input:checkbox[name=body-files]:checked").each(function() {
                    bodyArr.push($(this).val());
                });
            }

            if ($("input:checkbox[name=banner-files]:checked")) {
                $("input:checkbox[name=banner-files]:checked").each(function() {
                    bannerArr.push($(this).val());
                });
            }

            var exportInforamtion = [headArr, bodyArr, bannerArr],
                storedPath = JSON.parse(localStorage.getItem('current_project_path'));

            $.ajax({
                type: 'post',
                url: 'classes/export.php',
                data: {
                    export: 'create_zip_files',
                    checkbox_data: exportInforamtion,
                    project_path: storedPath
                },
                success: function(response) {
                    window.open(window.location.origin + response, '_self');
                }
            });
            return false;
        }
    });

    $('.refresh-body-content').on('click', function() {

        setTimeout(function() {
            disableCheck = false;
        }, enableTime);
        if (disableCheck === false) {
            disableCheck = true;
            if ($(this).data('files') === 'tree') {
                $('#jstree').on('refresh.jstree', function() {
                    $('.jstree-open').each(function() {
                        $(this).children('a').children('i').css('transform', 'rotate(180deg)');
                    });
                }).jstree(true).refresh(false, false);
            } else {
                var current_project_path = JSON.parse(localStorage.getItem('current_project_path'));

                $.ajax({
                    context: this,
                    type: 'post',
                    url: 'classes/export.php',
                    data: {
                        export: 'create_checkboxes',
                        file_info: $(this).data('files') + '-files',
                        project_path: current_project_path
                    },
                    success: function(response) {

                        var responseArr = JSON.parse(response);
                        $(this).siblings('ul').children().remove();

                        $this = $(this);
                        $.each(responseArr[0], function(index, value) {
                            $this.siblings('ul').append(value);
                        });
                        localStorage.setItem('current_project_path', JSON.stringify(responseArr[1][0]));
                    }
                });
                return false;
            }
        }
    });

    $('.iframe-button').on('click', function() {

        setTimeout(function() {
            disableCheck = false;
        }, enableTime);
        if (disableCheck === false) {
            disableCheck = true;
            var $iframe = $(this).siblings('#banner').children('iframe'),
                path = '/' + $iframe[0].getAttribute('src');

            $.ajax({
                type: 'post',
                url: 'classes/banner.php',
                data: {
                    banner: 'create_banner',
                    banner_path: path
                },
                success: function(response) {
                    var responseArr = JSON.parse(response);
                    $('#banner').empty();
                    $.each(responseArr[0], function(index, value) {
                        $('#banner').append(value);
                    });

                    $('#unusedCSS').text('unusedCSS: ( ' + flatten_arrays(responseArr[1]).length + ' )');
                    localStorage.setItem('unusedCSS', JSON.stringify(flatten_arrays(responseArr[1])));

                    $.ajax({
                        type: 'post',
                        url: 'classes/helper.php',
                        data: {
                            banner_size: 'folderSize',
                            banner_path: path
                        },
                        success: function(response) {
                            $('#banner-size').text('size: ' + response);
                        }
                    });
                    return false;
                }
            });
            return false;
        }
    });

    $(window).on('keypress', function(e) {

        setTimeout(function() {
            disableCheck = false;
        }, enableTime);
        if ((e.metaKey || e.ctrlKey) && e.keyCode == 83 && disableCheck === false) {
            var $iframe = $('#banner').children('iframe'),
                path = '/' + $iframe[0].getAttribute('src');
            disableCheck = true;
            $.ajax({
                type: 'post',
                url: 'classes/banner.php',
                data: {
                    banner: 'create_banner',
                    banner_path: path
                },
                success: function(response) {
                    var responseArr = JSON.parse(response);
                    $('#banner').empty();
                    $.each(responseArr[0], function(index, value) {
                        $('#banner').append(value);
                    });

                    $('#unusedCSS').text('unusedCSS: ( ' + flatten_arrays(responseArr[1]).length + ' )');
                    localStorage.setItem('unusedCSS', JSON.stringify(flatten_arrays(responseArr[1])));

                    $.ajax({
                        type: 'post',
                        url: 'classes/helper.php',
                        data: {
                            banner_size: 'folderSize',
                            banner_path: path
                        },
                        success: function(response) {
                            $('#banner-size').text('size: ' + response);
                        }
                    });
                    return false;
                }
            });
            e.preventDefault();
            return false;
        }
    });

    $('#callProject').on('click', function(event) {

        setTimeout(function() {
            disableCheck = false;
        }, enableTime);
        var path = '';

        if (disableCheck === false) {
            disableCheck = true;

            $.ajax({
                type: 'post',
                url: 'classes/banner.php',
                data: {
                    banner: 'create_banner',
                    call_project: 'call_project'
                },
                success: function(response) {
                    $('#jstree').on('refresh.jstree', function() {
                        $('.jstree-open').each(function() {
                            $(this).children('a').children('i').css('transform', 'rotate(180deg)');
                        });
                    }).jstree(true).refresh(false, false);
                }
            });
            return false;
        }
    });
});

$(document).ready(function() {

    var wlocation = window.location.origin;

    /**
     *
     * default app data
     *
     */
    var appData = {
        sidebar: {
            left: {
                state: 'open',
                width: 290
            },
            right: {
                state: 'open',
                width: 290
            }
        },
        collapsible: {
            jstree: {
                state: 'closed',
                height: 0
            },
            head: {
                state: 'closed',
                height: 0
            },
            body: {
                state: 'closed',
                height: 0
            },
            banner: {
                state: 'closed',
                height: 0
            }
        },
        checkbox: {}
    };

    if (localStorage.getItem('banner-creator') === null) {
        localStorage.setItem('banner-creator', JSON.stringify(appData));
    }
    appData = JSON.parse(localStorage.getItem('banner-creator'));


    /**
     *
     * handle sidebar app data
     *
     */
    var sidebarRight = $('.sidebar-right');
    $('.sidebar-right .resizable').css('width', appData.sidebar.right.width + 'px');

    if (appData.sidebar.right.state === 'closed') {
        var right = sidebarRight.outerWidth();
        sidebarRight.css('right', '-' + right + 'px');
        sidebarRight.addClass('closed');

        $('.sidebar-right .arrow-con').css('transform', 'scale(-1, 1)');
    } else {
        sidebarRight.css('right', '0px');
    }

    var sidebarLeft = $('.sidebar-left');
    $('.sidebar-left .resizable').css('width', appData.sidebar.left.width + 'px');

    if (appData.sidebar.left.state === 'closed') {
        var left = sidebarLeft.outerWidth();
        sidebarLeft.css('left', '-' + left + 'px');
        sidebarLeft.addClass('closed');

        $('.sidebar-left .arrow-con').css('transform', 'scale(1, -1)');
    } else {
        sidebarLeft.css('left', '0px');
    }


    /**
      *
      * handle collapsible app data
      *
      */
    $('.collapsible-head').each(function() {
        var data = $(this).data().files,
            $self = $(this);

        if (appData.collapsible[data].state === 'open') {
            $self.siblings('.collapsible-body').css('max-height', appData.collapsible[data].height);
            $self.children('.arrow').css('transform', 'rotate(90deg)');
            $self.addClass('open');
        }
    });


    /**
      *
      * handle checkbox app data
      *
      */
    if (Object.keys(appData.checkbox).length > 0 && appData.checkbox.constructor === String) {
        var data = JSON.parse(localStorage.getItem('banner-creator')),
            dataArr = data.checkbox.split(',');
        $('.collapsible-body [type="checkbox"]').each(function(key, value) {
            if (dataArr[key] === 'true') {
                this.checked = true;
            } else {
                this.checked = false;
            }
        });
    }


    /**
      *
      * build / handle jstree treeview
      *
      */
    var jsTree = $('#jstree'),
        jsTreeSearch = $('#jstree-search');

    jsTree.jstree({
        search: {
            'case_insensitive': false,
            'show_only_matches': true,
            'show_only_matches_children': true
        },
        state: {
            'opened': false,
            'disabled': false,
            'selected': true
        },
        plugins: [
            'state', 'search'
        ],
        core: {
            dblclick_toggle: false,
            data: {
                type: 'POST',
                url: 'app-classes/post-handler.php',
                dataType: 'json',
                data: {
                    jstree: 'get_folder_structure'
                },
                success: function(response) {
                    return {
                        id: response.id
                    };
                }
            }
        }
    }).on('click', '.jstree-anchor', function(e) {

        jsTree.jstree(true).toggle_node(e.target);

        $('.jstree-anchor').on('mouseover mouseleave', function(event) {
            if (event.type === 'mouseover') {
                $(this).siblings('i').css('opacity', '1');
            }
            if (event.type === 'mouseleave') {
                $(this).siblings('i').css('opacity', '0');
            }
        });

        if ($(this).parent().hasClass('jstree-leaf')) {
            var project = $('main').data().project;
            var banner = $(this).parent().data().directory;

            var pattern = new RegExp(project + '\\b');
            var changed = (banner.match(pattern)) ? false : true;

            $.ajax({
                type: 'post',
                url: wlocation + '/app-classes/post-handler.php',
                data: {
                    banner: banner,
                    changed: changed
                },
                success: function(response) {
                    var data = JSON.parse(response);

                    $('#banner-size').html('size: ' + data.banner_data.size);

                    updateUnusedCSS(data.banner_data.unusedCSS);
                    updateIframes(data.banner_data.iframes);

                    if (changed) {
                        $('.collapsible-head[data-files="banner"]').siblings('.collapsible-body').children('ul').html(data.checkbox);
                        checkboxReminder(appData);

                        $('main[data-project]').attr('data-project', data.banner_data.project_dir);
                    }
                }
            });

        }

        archive();

    }).bind('select_node.jstree', function () {

        $('.jstree-clicked').removeClass('jstree-clicked');

        var banner = $('.banner-wrapper iframe').data().banner;
        $('#jstree li[data-directory="/' + banner + '"]').children('a').addClass('jstree-clicked');

        $('.jstree-anchor').on('mouseover mouseleave', function(event) {
            if (event.type === 'mouseover') {
                $(this).siblings('i').css('opacity', '1');
            }
            if (event.type === 'mouseleave') {
                $(this).siblings('i').css('opacity', '0');
            }
        });

        archive();
 });

    var searchTime = false;
    jsTreeSearch.keyup(function() {

        if (searchTime) {
            clearTimeout(searchTime);
        }

        if (jsTreeSearch.val() === '') {

            $('.jstree-anchor').css('display', 'block');
            $('#jstree ul li').css({
                'padding-bottom': '5px',
                'margin-top': '5px'
            });
            $('[class*="jstree-"] li').css('margin-left', '5px');

            jsTree.jstree('refresh');

        } else {

            searchTime = setTimeout(function() {
                var val = jsTreeSearch.val();

                jsTree.jstree(true).search(val);
                $('.jstree-anchor').css('display', 'none');
                $('.jstree-search').css('display', 'block');
                $('#jstree ul li').css({
                    'padding-bottom': '0',
                    'margin-top': '0'
                });
                $('[class*="jstree-"] li').css('margin-left', '0');

                $('[role="group"]').children('li').each(function() {
                    $(this).attr('aria-expanded', false).removeClass('jstree-open').addClass('jstree-closed');
                });
            }, 150);
        }
    });


    /**
      *
      * handle collapsible click
      *
      */
    $('.collapsible-head').click(function() {
        var $self = $(this),
            dataName = $self.data().files;

        if (!$self.hasClass('disabled')) {
            disableBtn($self);

            if (!$self.hasClass('open')) {
                $self.addClass('open');

                appData.collapsible[dataName].state = 'open';
                if (dataName === 'jstree') {
                    appData.collapsible[dataName].height = 'calc(100vh - 60px)';
                    $(this).siblings('.collapsible-body').css('max-height', 'calc(100vh - 60px)');
                } else if (dataName === 'banner') {
                    appData.collapsible[dataName].height = 'calc(40vh - 75px)';
                    $(this).siblings('.collapsible-body').css('max-height', 'calc(40vh - 75px)');
                } else {
                    appData.collapsible[dataName].height = 'calc(30vh - 75px)';
                    $(this).siblings('.collapsible-body').css('max-height', 'calc(30vh - 75px)');
                }

                localStorage.setItem('banner-creator', JSON.stringify(appData));
                $self.children('.arrow').css('transform', 'rotate(90deg)');

            } else {
                $self.removeClass('open');

                appData.collapsible[dataName].state = 'closed';
                appData.collapsible[dataName].height = 0;
                $(this).siblings('.collapsible-body').css('max-height', '0');

                localStorage.setItem('banner-creator', JSON.stringify(appData));
                $self.children('.arrow').css('transform', 'rotate(-90deg)');
            }
        }

    });


    /**
      *
      * handle resize sidebar
      *
      */
    $('.sidebar-left .resizable').resizable({
        handles: 'e',
        minWidth: 300,
        resize: function(event, ui) {
            $(this).css('width', ui.size.width);

            appData.sidebar.left.width = ui.size.width;
            localStorage.setItem('banner-creator', JSON.stringify(appData));
        }
    });
    $('.sidebar-right .resizable').resizable({
        handles: 'w',
        minWidth: 300,
        resize: function(event, ui) {
            $(this).css('left', '0');
            $(this).css('width', ui.size.width);

            appData.sidebar.right.width = ui.size.width;
            localStorage.setItem('banner-creator', JSON.stringify(appData));
        }
    });


    /**
      *
      * open / close sidebars
      *
      */
    $('.arrow-con').click(function() {

        var parent = $(this).parent().parent(),
            currWidth = $(this).parent().parent().outerWidth(),
            sidebar = $(this).parent().parent().data().sidebar;

        if (!parent.hasClass('closed')) {
            parent.addClass('closed');

            if (parent.hasClass('sidebar-left')) {
                parent.css('left', '-' + currWidth + 'px');

                $(this).css('transform', 'scale(1, 1)');
            }

            if (parent.hasClass('sidebar-right')) {
                parent.css('right', '-' + currWidth + 'px');

                $(this).css('transform', 'scale(-1, 1)');
            }

            appData.sidebar[sidebar].state = 'closed';
            localStorage.setItem('banner-creator', JSON.stringify(appData));

        } else {
            parent.removeClass('closed');

            if (parent.hasClass('sidebar-left')) {
                parent.css('left', '0px');

                $(this).css('transform', 'scale(-1, 1)');
            }

            if (parent.hasClass('sidebar-right')) {
                parent.css('right', '0px');

                $(this).css('transform', 'scale(1, 1)');
            }

            appData.sidebar[sidebar].state = 'open';
            localStorage.setItem('banner-creator', JSON.stringify(appData));
        }

    });


    /**
      *
      * checkbox reminder
      *
      */
    $('.collapsible-body [type="checkbox"]').click(function() {
        checkboxReminder(appData);
    });


    /**
      *
      * refresh collapsible body
      *
      */
    $('.refresh').click(function() {
        var $self = $(this),
            directory = ($self.data().files === 'banner') ? $('main').data().project : 'empty';

        if (!$self.hasClass('disabled')) {
            disableBtn($self);

            if ($self.data().files === 'jstree') {
                jsTree.jstree('refresh');
            } else {
                $.ajax({
                    type: 'post',
                    url: wlocation + '/app-classes/post-handler.php',
                    data: {
                        refresh: $self.data().files,
                        dir: directory
                    },
                    success: function(response) {

                        var data = JSON.parse(response);
                        $self.siblings('ul').html(data);

                        checkboxReminder(appData);
                    }
                });
            }
        }

    });

    /**
      *
      * export data
      *
      */
    $('.button-export').click(function() {
        var $self = $(this);

        if (!$self.hasClass('disabled')) {
            disableBtn($self);

            var expData = {
                head: [],
                body: [],
                banner: [],
                project_dir: $('main').data().project
            }

            $('.sidebar-right input[type="checkbox"]').each(function() {

                if (this.checked) {
                    if (this.name === 'head') {
                        expData.head.push(this.value);
                    }
                    if (this.name === 'body') {
                        expData.body.push(this.value);
                    }
                    if (this.name === 'banner') {
                        expData.banner.push(this.value);
                    }
                }

            });

            $.ajax({
                type: 'post',
                url: wlocation + '/app-classes/post-handler.php',
                dataType: 'text',
                data: {
                    export: JSON.stringify(expData)
                },
                success: function(response) {
                    window.open(window.location.origin + response, '_self');
                }
            });
        }
    });


    /**
      *
      * refresh iframes
      *
      */
    $('.button-iframe').click(function () {
        var $self = $(this),
            iframes = $('.banner-wrapper iframe'),
            dir = [];

        iframes.each(function() {
            dir.push($(this).data().banner)
        });

        if (!$self.hasClass('disabled')) {
            disableBtn($self);

            $.ajax({
                type: 'post',
                url: wlocation + '/app-classes/post-handler.php',
                data: {
                    iframes: JSON.stringify(dir)
                },
                success: function(response) {
                    var data = JSON.parse(response);

                    $('#banner-size').html('size: ' + data.size);

                    updateIframes(data.iframes);
                    updateUnusedCSS(data.unusedCSS);
                }
            });
        }

    });


    /**
      *
      * call project config
      *
      */
    $('#call-project-config').click(function () {
        var $self = $(this);

        if (!$self.hasClass('disabled')) {
            disableBtn($self);

            $.ajax({
                type: 'post',
                url: wlocation + '/app-classes/post-handler.php',
                data: {
                    project: $('main').data().project
                },
                success: function(response) {
                    var data = JSON.parse(response);

                    updateIframes(data.banner_data.iframes);
                    $('#banner-size').html('size: ' + data.banner_data.size);

                    updateUnusedCSS(data.banner_data.unusedCSS);

                    jsTree.jstree('refresh');
                    $('.collapsible-head[data-files="banner"]').siblings('.collapsible-body').children('ul').html(data.checkbox);
                    checkboxReminder(appData);
                }
            });
        }

    });

    /**
      *
      * archive
      *
      */
      var archive = function() {
          $('.jstree-icon.jstree-ocl').click(function () {
              var $self = $(this),
                  dataDir = $self.parent().data().directory,
                  pattern = new RegExp('/', 'g'),
                  name = dataDir.substring(1).replace(pattern, '-');
                  console.log(name);

              if (!$self.hasClass('disabled')) {
                  disableBtn($self);

                  var archive = prompt('Please enter a file name.', name);
                  if (archive !== null && archive !== '') {
                      $.ajax({
                          type: 'post',
                          url: wlocation + '/app-classes/post-handler.php',
                          data: {
                              archive: archive,
                              dir: $self.parent().data().directory
                          },
                          success: function() {
                              jsTree.jstree('refresh');
                          }
                      });
                  }
              }

          });
      }

      $(window).on('keypress', function(event) {

          if (event.which == 115 && (event.ctrlKey||event.metaKey)|| (event.which == 19)) {

              var $self = $(this),
                  iframes = $('.banner-wrapper iframe'),
                  dir = [];

              iframes.each(function() {
                  dir.push($(this).data().banner)
              });

              if (!$self.hasClass('disabled')) {
                  disableBtn($self);

                  $.ajax({
                      type: 'post',
                      url: wlocation + '/app-classes/post-handler.php',
                      data: {
                          iframes: JSON.stringify(dir)
                      },
                      success: function(response) {
                          var data = JSON.parse(response);

                          $('#banner-size').html('size: ' + data.size);

                          updateIframes(data.iframes);
                          updateUnusedCSS(data.unusedCSS);
                      }
                  });
              }

              event.preventDefault();
              return false;
          }

      });

    var checkboxReminder = function(appData) {
        var arr = [];
        $('.collapsible-body [type="checkbox"]').each(function() {
            if (this.checked) {
                arr.push(this.checked);
            } else {
                arr.push('false');
            }
        });
        appData.checkbox = arr.join(',');
        localStorage.setItem('banner-creator', JSON.stringify(appData));
    }

    var disableBtn = function(elem) {
        elem.addClass('disabled');
        setTimeout(function () {
            elem.removeClass('disabled');
        }, 500);
    };

    var updateIframes = function(data) {
        $('.banner-wrapper iframe').remove();
        $.each(JSON.parse(data), function(key, value) {
            $('.banner-wrapper').append(value);
        });
    };

    var updateUnusedCSS = function(data) {
        localStorage.setItem('unusedCSS', data);
        var count = 0;
        $.each(JSON.parse(data), function(key, value) {
            count += value.length;
        });
        $('#unusedCSS').html('unusedCSS: (' + count + ')');
    };

});

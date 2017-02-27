$(function () {
    'use strict';

    App.debug = (typeof DEBUG !== 'undefined' && DEBUG) ? true : false;

    var
        //$loader  = $('#loader'),
        $content = $('#content'),
        $body    = $('body')
        ;

    $(window).on('resize', function () {
        App.log('resize');

        App.layoutFormatter();
    });

    $(document).on('app_init', function () {
        App.log('init');

        App.layoutFormatter();
        App.initPageTransitions();
        App.initSidebar();
        App.setupClickActionHandlers();
        App.setupFormFields();
        App.setupDataTable();
        App.setupAjaxPages();

        //$content.fadeIn();
        $body.removeClass('loading');

        if (App.debug) {
            App.dev();
        }
    });

    if ($content.length) {
        $(document).trigger('app_init');
    }

    $(document).on('app_page_request_init', function () {
        //$content.fadeOut('fast');
        //$content.css('display', 'none');//hide the plugin destruction
        //alert('init');
        //$loader.fadeIn('fast');
        $body.addClass('loading');

        App.destroyPlugins();
    });
    $(document).on('app_page_request_finish', function () {
        App.setupFormFields();
        App.setupDataTable();
        //$loader.fadeOut('fast');
        //$content.fadeIn();
        $body.removeClass('loading');
    });

});


var App = App || {};

App.log                      = function () {
    if (!this.debug) {
        return;
    }
    console.log('[app]', arguments);
}
// THEME ------------------------------------------------------------
App.layoutFormatter          = function () {
    this.log('layoutFormatter');

    setTimeout(function () {

        var windowH = $(window).height();

        var headerH = $('#page-header').height();
        var navH    = $('#page-nav').height();

        var sideH = windowH - headerH;
        var contH = windowH - headerH - navH;

        $('#page-sidebar').height(sideH);
        $('#page-sidebar-wrapper').height(sideH);
        $('#page-content').css('min-height', contH - 2);

    }, 499);

};
App.initPageTransitions      = function () {
    this.log('initPageTransitions');

    var transitions = ['.pt-page-moveFromLeft', 'pt-page-moveFromRight', 'pt-page-moveFromTop', 'pt-page-moveFromBottom', 'pt-page-fade', 'pt-page-moveFromLeftFade', 'pt-page-moveFromRightFade', 'pt-page-moveFromTopFade', 'pt-page-moveFromBottomFade', 'pt-page-scaleUp', 'pt-page-scaleUpCenter', 'pt-page-flipInLeft', 'pt-page-flipInRight', 'pt-page-flipInBottom', 'pt-page-flipInTop', 'pt-page-rotatePullRight', 'pt-page-rotatePullLeft', 'pt-page-rotatePullTop', 'pt-page-rotatePullBottom', 'pt-page-rotateUnfoldLeft', 'pt-page-rotateUnfoldRight', 'pt-page-rotateUnfoldTop', 'pt-page-rotateUnfoldBottom'];
    for (var i in transitions) {
        var transition_name = transitions[i];
        if ($('.add-transition').hasClass(transition_name)) {

            $('.add-transition').addClass(transition_name + '-init page-transition');

            setTimeout(function () {
                $('.add-transition').removeClass(transition_name + ' ' + transition_name + '-init page-transition');
            }, 1200);
            return;
        }
    }

};
App.initSidebar              = function () {
    this.log('initSidebar');

    var $page_sidebar_wrapper = $("#page-sidebar-wrapper"),
        $body                 = $('body')
        ;

    $page_sidebar_wrapper.niceScroll({
        horizrailenabled: false,
        cursorborder: "0",
        cursorwidth: "6px",
        cursorcolor: "#dde5ed",
        zindex: "5555",
        autohidemode: true,
        bouncescroll: true,
        mousescrollstep: '40',
        scrollspeed: '100',
        background: "#f5f7f9",
        cursoropacitymax: "0.6",
        cursorborderradius: "0"
    });

    $page_sidebar_wrapper.getNiceScroll().resize();

    /* Open responsive nav menu */
    $('#responsive-open-menu').click(function () {
        $('#page-sidebar').toggle();
    });

    $('#sidebar-menu > ul').superclick({
        animation: {height: 'show'},
        animationOut: {height: 'hide'}
    });

    /* Colapse sidebar */
    $('#collapse-sidebar').click(function () {
        $('#page-sidebar, #page-content-wrapper, #header-logo').removeClass('rm-transition');
        $body.toggleClass('sidebar-collapsed');
        $('.glyph-icon', this).toggleClass('icon-chevron-right').toggleClass('icon-chevron-left');

        if ($body.hasClass('sidebar-collapsed')) {
            Cookies.set('sidebar-collapsed', true, {expires: 7, path: '/'});
        } else {
            Cookies.set('sidebar-collapsed', false, {expires: 7, path: '/'});
        }
    });

};
App.setupClickActionHandlers = function () {
    this.log('setupClickActionHandlers');
    var
        $page_content = $("#page-content")
        ;

    // handles onekey validation ----------------------------------------------
    $page_content.on('click', '.action.validate', function (e) {
        e.preventDefault();

        var $el    = $(this),
            onekey = $el.data('onekey'),
            url    = $el.attr('href')
            ;

        // create dialog if not already exist
        var $dialog = $('#validate-dialog');
        if ($dialog.length === 0) {
            $('body').append('<div id="validate-dialog" style="margin-left: 10px"><label class="control-label required">Onekey<input class="form-control"></label></div>')
            $dialog = $('#validate-dialog');
        }

        //cache input
        var $dialog_input = $dialog.find('input');
        $dialog_input.removeClass('parsley-error');
        $dialog_input.val(onekey);

        //create ajax result dom
        var $result = $dialog_input.parent().find('li');
        if ($result.length === 0) {
            $dialog_input.after('<ul class="parsley-errors-list"><li class="parsley-required"></li></ul>');
            $result = $dialog_input.parent().find('li');
        }
        $result.html('');

        // open dialog
        $dialog.dialog({
            dialogClass: "validate-modal-dialog",
            closeText: "",
            modal: true,
            closeOnEscape: true,
            buttons: [{
                text: 'Valid',
                click: function (e) {
                    var $btn = $(e.currentTarget);

                    // avoid double click
                    if ($btn.hasClass('disabled')) {
                        return;
                    }

                    // disable for empty onekey
                    var onekey = $dialog.find('input').val();
                    if (!onekey.length) {
                        $dialog_input.addClass('parsley-error');
                        return;
                    }

                    $btn.addClass('disabled');
                    //$btn.html('...'); // to keep, save the current text for recover on failure
                    var data = {'onekey': onekey, 'onekeyValid': true};

                    App.makeRequest('post', url, data,
                        function () {
                            $dialog.dialog("close");
                            return;//let app reload
                        },
                        function (jqxhr) {
                            if (jqxhr.responseJSON.error.message) {
                                $dialog_input.addClass('parsley-error');
                                $result.html(jqxhr.responseJSON.error.message);
                                $btn.removeClass('disabled');

                            } else {
                                alert('An error happened, try again later!');
                                //window.location.reload(true);
                            }
                        });

                },
            }, {
                text: 'Not Valid',
                click: function (e) {
                    var $btn = $(e.currentTarget);
                    if ($btn.hasClass('disabled')) {
                        return;
                    }

                    var onekey = $dialog.find('input').val();

                    $btn.addClass('disabled');
                    $btn.html('...');
                    var data = {'onekey': onekey, 'onekeyValid': false}

                    App.makeRequest('post', url, data,
                        function () {
                            $dialog.dialog("close");
                            return;//let app reload
                        },
                        function (jqxhr) {
                            alert('An error happened, try again later!');
                        });
                }
            }, {
                text: 'Cancel',
                click: function () {
                    $dialog.dialog("close");
                }
            }]
        });
        // fix double close button
        $('.ui-dialog-titlebar-close').html('');
        // add overlay
        $('.ui-widget-overlay').addClass('bg-black opacity-60');
    });

    // IMPORT ----------------------------------------
    $page_content.on('click', '.action.import', function (e) {
        e.preventDefault();
        var
            $import_status = $('#import-status'),
            $input_import  = $('#import_file');

        $input_import.fileupload({
            url: $input_import.data('target-url'),
            dataType: 'html',
            done: function (e, data) {
                $('#content').html(data.result);
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $import_status.find('.bar').css('width', progress + '%');
                $import_status.find('.percent').html(progress + '%');

                if (100 == progress) {
                    $import_status.find('.bar').css('display', 'none');
                    $import_status.find('.percent').css('display', 'none');

                    $import_status.find('.loader').css('display', 'block');
                }
            }
        });

        $input_import.click();

    });
};
// FORMS ------------------------------------------------------------
App.setupFormFields          = function () {
    this.log('setupFormFields');

    $('input:checkbox').bootstrapSwitch();

    $(".chosen-select").chosen();

    $(".datepicker:not([readonly])").datepicker({
        dateFormat: "dd/mm/yy"
    });

    $('.chart-home').easyPieChart({
        barColor: 'rgba(255,255,255,0.5)',
        trackColor: 'rgba(255,255,255,0.1)',
        animate: 1000,
        scaleColor: 'rgba(255,255,255,0.3)',
        lineWidth: 3,
        size: 100,
        lineCap: 'cap'
        //onStep: function(value) {
        //    this.$el.find('span').text(~~value);
        //}
    });

    //$("select, input:checkbox, input:radio, input:file").uniform();
};


// LIST ------------------------------------------------------------
App.setupDataTable  = function () {
    this.log('setupDataTable');

    // avoid empty tables breaking js
    try {
        var $datatable = $('.dataTable');
        if (!$datatable.length || !$datatable.find('thead th').length) {
            this.dataTable = null;
            return;
        }
        var options    = $datatable.data('datatable-options');
        this.dataTable = $datatable.DataTable(options);
    } catch (e) {
        this.log('datatable error:', e.message);
    }

};
App.destroyPlugins  = function () {
    this.log('destroyPlugins');

    if (this.dataTable) {
        this.dataTable.destroy();
    }

    $(".datepicker:not([readonly])").datepicker("destroy");

    $(".chosen-select").chosen("destroy");

    $('input:checkbox').bootstrapSwitch('destroy', true);

};
App.makePageRequest = function (method, url, data) {
    console.group('--page request--');
    this.log('makePageRequest');

    if (this.current_page_request) {
        this.current_page_request.abort();
        this.current_page_request = null;
    }

    var
        $content   = $('#content'),
        header_key = 'x-app-ajax',
        headers    = {}
        ;

    $(document).trigger('app_page_request_init');

    // if empty current state, set it to the current content
    if (!history.state) {
        history.replaceState({'content': $content.html()}, "");
    }

    // build custom headers
    headers[header_key] = true;

    this.current_page_request = this.makeRequest(method, url, data,
        //success
        function (json, statusText, jqxhr) {
            console.log(arguments);
            if ("content" in json) {
                //if ("success" == statusText) {
                history.pushState({'content': json.content}, "", url);
                $content.html(json.content);
                //}
            }

            $(document).trigger('app_page_request_finish', {'response': json, 'statusText': statusText});

        },
        //error
        function (jqxhr, statusText) {
            if ("responseJSON" in jqxhr) {
                if ('abort' !== statusText) {

                    var $iframe = $('<iframe/>');
                    $iframe.attr({'width': '100%', 'style': 'border:0 none; display:none'});
                    $iframe.on('load', function () {
                        $iframe.contents().find('body').append(jqxhr.responseJSON.content);
                        $iframe.fadeIn('fast');
                        this.style.height = this.contentWindow.document.body.offsetHeight + 'px';
                    });

                    $content.html($iframe);
                    history.pushState({'content': jqxhr.responseJSON.content}, "", url);
                }
            } else {
                alert('An error happened. Try refreshing your browser');
            }

            $(document).trigger('app_page_request_finish', {'response': jqxhr, 'statusText': statusText});
        },
        //complete
        function (jqxhr, statusText) {
            console.groupEnd();
        }
    );
};
App.makeRequest     = function (method, url, data, success, error, complete) {
    console.log(method.toUpperCase() + ' ' + url + ' with data:', data);

    var
        header_key      = 'x-app-ajax',
        headers         = {}
        ;
    // build custom headers
    headers[header_key] = true;

    return $.ajax({
        url: url,
        type: method,
        data: data,
        headers: headers,
        dataType: 'json',
        success: function (response, statusText, jqxhr) {
            console.log('response ' + statusText);

            if ("location" in response) {
                window.location = response.location;
                return;
            }

            if (typeof success === 'function') {
                success(response, statusText, jqxhr);
            }
        },
        error: function (jqxhr, statusText) {
            console.log('response ' + statusText);

            if (typeof error === 'function') {
                error(jqxhr, statusText);
            }
        },
        complete: function (jqxhr, statusText) {
            if (typeof error === 'function') {
                complete(jqxhr, statusText);
            }
        }
    });
};

App.setupAjaxPages = function () {
    this.log('setupAjaxPages');

    var
        $page_content = $('#page-content'),
        $content      = $('#content')
        ;

    if (!$page_content.length) {
        return;
    }

    // onpopstate ------------------------------------------
    $(window).on('popstate', function (event) {
        var
            state = event.state || event.originalEvent.state
            ;

        if (state && state.hasOwnProperty('content')) {
            $content.html(state.content);
            App.setupFormFields();
            App.setupDataTable();
        }

    });

    // onload ------------------------------------------
    $(document).on('click', 'a:not(.action), button[type="submit"]', function (e) {

        var
            $el    = $(this),
            path   = $el.attr('href'),
            $form  = $el.parents('form'),
            method = 'get',
            data   = {};

        if ($form.length && !path && !$el.is('a')) {
            // there is a parent form,
            // there is no path in the element clicked,
            // the element is not a 'a' tag without link
            method = $form.attr('method');
            path   = $form.attr('action') || window.location.href;
            data   = $form.serializeArray();
            data.push({'name': this.name, 'value': this.value});
        } else {
            if ($el[0].hasAttribute('onclick')) {
                return;
            }
        }

        if (!path || path.lastIndexOf('#', 0) === 0) {
            return;
        }

        e.preventDefault();

        App.makePageRequest(method, path, data);

        //dont submit form
        return false;
    });
};

App.dev = function () {
    this.log('dev');

}
$(document).ready(function () {
    /*EXPORT NOTY*/
    $('#export_data').click(function () {
        var str ='';
        var info = json_option_export;
        for (var prop in info) {
            if(prop == 0){
                str += info[prop].rawdate + '<br>' + info[prop].resultraw + '<br>';
            } else{
                str +=  info[prop].resultraw + '<br>';
            }
        }
        new Noty({
            type: 'warning',
            layout: 'topleft',
            theme: 'mint',
            text: str,
            timeout: 5000,
            progressBar: true,
            closeWith: ['click', 'button'],
            animation: {
                open: 'noty_effects_open',
                close: 'noty_effects_close'
            },
            id: false,
            force: false,
            killer: false,
            queue: 'global',
            container: false,
            buttons: [],
            sounds: {
                sources: [],
                volume: 1,
                conditions: []
            },
            titleCount: {
                conditions: []
            },
            modal: false
        }).show()
    });

    /*IMPORT NOTY*/
    $('#import_data').click(function () {
        var str_import ='';
        var info_import = json_option_import;
        for (var p in info_import) {
            if(p == 0){
                str_import += info_import[p].rawdate + '<br>' + info_import[p].resultraw + '<br>';
            } else{
                str_import +=  info_import[p].resultraw + '<br>';
            }
        }
        new Noty({
            type: 'warning',
            layout: 'topleft',
            theme: 'mint',
            text: str_import,
            timeout: 5000,
            progressBar: true,
            closeWith: ['click', 'button'],
            animation: {
                open: 'noty_effects_open',
                close: 'noty_effects_close'
            },
            id: false,
            force: false,
            killer: false,
            queue: 'global',
            container: false,
            buttons: [],
            sounds: {
                sources: [],
                volume: 1,
                conditions: []
            },
            titleCount: {
                conditions: []
            },
            modal: false
        }).show()
    });

    /*SCROLL TO TOP*/
    $(window).scroll(function () {
        var scroller = $('#scroller');
        if ($(this).scrollTop() > 0) {
            scroller.fadeIn();
        } else {
            scroller.fadeOut();
        }
    });
    $('#scroller').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 400);
        return false;
    });

    /*CHECKBOX COUNTRY|REGION*/
    $('.multiselect1').css('display', 'none');
    $('#toggle_country_region').change(function () {
        var i = $(this).prop('checked');
        if (i == true) {
            $('.multiselect').css('display', 'block');
            $('.multiselect1').css('display', 'none');
            //$('.multiselect1').removeProp('value');
        } else {
            //$('.multiselect').removeProp('value');
            $('.multiselect').css('display', 'none');
            $('.multiselect1').css('display', 'block');
        }
    })
});
/*
 * Welcome to your app's main JavaScript file!
 *
 */


import $ from 'jquery';

import('bootstrap');
import('popper.js');
global.$ = global.jQuery = $;
import('mdbootstrap');
import {initCalendar} from './fullcalendar'


$(document).ready(function () {
    setTimeout(function () {
        $('#snackbar').addClass('show').click(function (e) {
            $('#snackbar').removeClass('show');
        })
    }, 500);

    initGroups();
    initCalendar();
});
$(window).on('load', function () {
    $('[data-toggle="popover"]').popover({html: true});
});
var counter = 0;
function initGroups(){

    $('.moreGroup').click(function (e) {
        e.preventDefault();
        var $form = $('#formSample').html();
        $('#groups').append($form.replaceAll("[x]","["+counter++ +"]"));
        $('.removeForm').click(function (e) {
            e.preventDefault();
            $(this).closest('.addedForm').remove();
            initAddBtn();
        })
        initAddBtn();
    })
}
function initAddBtn() {
    if($('.addedForm').length >= maxGroupSize){
        $('.moreGroup').addClass('d-none');
    }else {
        $('.moreGroup').removeClass('d-none');
    }
}
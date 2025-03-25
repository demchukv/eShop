"use strict";

$(document).ready(function () {
    $.get("https://ipapi.co/json", function (data) {
        $("#city").val(data.city);
        $("#zipcode").val(data.postal);
    });
});

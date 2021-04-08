$(document).ready(function() {
    $('#clear-filter-button').click(function () {
        window.location.href = $('#reset-url').val();
    });

    $('#data-grid tr').click(function () {
        window.open($('#document-url').val() + $(this).attr('data-key'));
    });
})
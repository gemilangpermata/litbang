$(document).ready(function() {
    $('#check-all').change(function() {
        $(this)
            .parents('table')
            .find('.grid-select')
            .prop('checked', $(this).is(':checked'))
            .trigger("change");
    });

    $('#delete-button').click(function () {
        if ($('.grid-select:checked').length > 0) {
            Swal.fire({
                icon: 'warning',
                text: 'Yakin ingin menghapus data yang dipilih?',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#delete-category-form').submit();
                }
            })
        }
    });

    $('.delete-button').click(function () {
        var button = this;
        Swal.fire({
            icon: 'warning',
            text: 'Yakin ingin menghapus data yang dipilih?',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            confirmButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#hidden-id').val($(button).attr('data-id'));
                $('#delete-category-form').submit();
            }
        })

        return false;
    });
})
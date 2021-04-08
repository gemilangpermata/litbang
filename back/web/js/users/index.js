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
                text: 'Yakin ingin menghapus pengguna yang dipilih?',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#delete-user-form').submit();
                }
            })
        }
    });

    $('.delete-button').click(function () {
        var button = this;
        Swal.fire({
            icon: 'warning',
            text: 'Yakin ingin menghapus pengguna yang dipilih?',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            confirmButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#hidden-id').val($(button).attr('data-id'));
                $('#delete-user-form').submit();
            }
        })

        return false;
    });
})
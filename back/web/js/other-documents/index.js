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
                text: 'Yakin ingin menghapus dokumen yang dipilih? Hanya dokumen yang diupload oleh anda yang bisa dihapus.',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    var checkeds = document.querySelectorAll('input.grid-select:checked');
                    var ids = [];
                    for (i = 0; i < checkeds.length; i++) {
                        ids.push(checkeds[i].value);
                    }
                    $('#delete-document-form').find('.hidden-id').val(ids.join(','))
                    $('#delete-document-form').submit();
                }
            })
        }
    });

    $('.delete-button').click(function () {
        var button = this;
        Swal.fire({
            icon: 'warning',
            text: 'Yakin ingin menghapus dokumen yang dipilih?',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            confirmButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-document-form').find('.hidden-id').val($(button).attr('data-id'));
                $('#delete-document-form').submit();
            }
        })

        return false;
    });

    $('#clear-filter-button').click(function () {
        window.location.href = $('#filter-form').attr('action');
    });

    $('.set-status-button').click(function () {
        var button = this;
        var command = $(button).attr('data-command');
        var code = $(button).attr('data-code');
        var isToApprove = parseInt($(button).attr('data-is-to-approve')) === 1;

        Swal.fire({
            icon: 'warning',
            text: 'Yakin ingin mengubah status dokumen menjadi "' + command + '"?' + (isToApprove ? ' Dokumen yang diupload oleh anda tidak dapat disetujui oleh anda sendiri.' : ''),
            showCancelButton: true,
            confirmButtonText: 'Sure',
            confirmButtonColor: '#ffc107',
        }).then((result) => {
            if (result.isConfirmed) {
                var checkeds = document.querySelectorAll('input.grid-select:checked');
                var ids = [];
                for (i = 0; i < checkeds.length; i++) {
                    ids.push(checkeds[i].value);
                }
                $('#set-status-document-form').find('.hidden-id').val(ids.join(','));
                $('#set-status-document-form').attr('action', $('#set-status-document-form').attr('action') + code);
                $('#set-status-document-form').submit();
            }
        })

        return false;
    });
})
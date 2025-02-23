$(function() {
    // Initialize Select2
    function initializeSelect2() {
        $('.form-select').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: function() {
                return $(this).closest('.modal').length ? $(this).closest('.modal') : null;
            }
        });

        $('#admin_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#assignAdminModal'),
            ajax: {
                url: '/settings/personal/schools/admins/available',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            placeholder: 'Admin axtarın...'
        });
    }

    // Select all checkbox handler
    $('#select-all').on('change', function() {
        $('.school-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Form submission handler
    $('#schoolForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#createModal').modal('hide');
                    form[0].reset();
                    window.location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Xəta baş verdi!');
                const errors = xhr.responseJSON?.errors || {};
                Object.values(errors).flat().forEach(error => toastr.error(error));
            },
            complete: function() {
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Initial setup
    initializeSelect2();

    // School data form submission
    $('#addDataForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#addDataModal').modal('hide');
                    form[0].reset();
                    window.location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Xəta baş verdi!');
                const errors = xhr.responseJSON?.errors || {};
                Object.values(errors).flat().forEach(error => toastr.error(error));
            },
            complete: function() {
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Show more content
    $('.show-more').on('click', function(e) {
        e.preventDefault();
        const content = $(this).data('content');
        const cell = $(this).closest('td');
        cell.html(content);
    });

    // Edit data
    $('.edit-data').on('click', function() {
        const id = $(this).data('id');
        const categoryId = $(this).data('category');
        const content = $(this).data('content');

        $('#edit_data_id').val(id);
        $('#edit_category_id').val(categoryId).trigger('change');
        $('#edit_content').val(content);
        $('#editDataModal').modal('show');
    });

    // Delete data
    $('.delete-data').on('click', function() {
        const id = $(this).data('id');
        if (confirm('Bu məlumatı silmək istədiyinizə əminsiniz?')) {
            $.ajax({
                url: `/settings/personal/schools/data/${id}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        window.location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Məlumatı silərkən xəta baş verdi!');
                }
            });
        }
    });
});

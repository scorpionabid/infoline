$(function() {
    // Initialize Select2
    $('.form-select').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: function() {
            return $(this).closest('.modal').length ? $(this).closest('.modal') : null;
        }
    });

    // Form submission handler
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
                    updateDataCompletion(response.data_completion);
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
                        updateDataCompletion(response.data_completion);
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

    // Update data completion percentage
    function updateDataCompletion(percentage) {
        const progressBar = $('.progress-bar');
        progressBar.css('width', `${percentage}%`);
        progressBar.text(`${percentage}%`);
        
        // Update color based on percentage
        progressBar.removeClass('bg-danger bg-warning bg-success');
        if (percentage < 50) {
            progressBar.addClass('bg-danger');
        } else if (percentage < 80) {
            progressBar.addClass('bg-warning');
        } else {
            progressBar.addClass('bg-success');
        }
    }
});

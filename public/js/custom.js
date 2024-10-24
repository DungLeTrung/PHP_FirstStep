
function showVanillaToast(content, type) {
    VanillaToasts.create({
        title: type === 'success' ? 'Success' : 'Error',
        text: content,
        type: type, // Available types: success, error, info, warning
        timeout: 3000 // Duration in milliseconds (optional)
    });
}

function formValidAjax(xhr, status = null, error = null) {
    if (xhr.status == 422) {
        let errors = xhr.responseJSON.errors;
        $.each(errors, function(key, value) {
            console.log(key, value);
            $('.valid_error-' + key).html(value[0]);
        });
    } else {
        showVanillaToast(xhr.responseJSON.message, 'error')
    }
}
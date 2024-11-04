
function showVanillaToast(content, type) {
    VanillaToasts.create({
        title: type === 'success' ? 'Success' : 'Error',
        text: content,
        type: type, // Available types: success, error, info, warning
        timeout: 3000 // Duration in milliseconds (optional)
    });
}

function formValidAjax(xhr) {
    const response = xhr.responseJSON;
    const message = response && response.message ? response.message : 'An error occurred. Please try again.';

    showVanillaToast(message, 'error');
    if (response && response.errors) {
        $.each(response.errors, function (field, errors) {
            const errorElement = $(`#${field}-error`);
            if (errorElement.length) {
                errorElement.show().text(errors[0]);
            } else {
                showVanillaToast(errors[0], 'error');
            }
        });
    }
}

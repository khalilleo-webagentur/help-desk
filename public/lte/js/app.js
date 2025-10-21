$(document).ready(function () {

    setTimeout(function () {
        $(".alert").alert('close').fadeOut();
    }, 12000);

    let selector = $('.selector');

    if (selector.length) {
        selector.on('click', function (e) {
            e.preventDefault();
            $('.ID').val($(this).attr('data-id'));
        });
    }

    // Copy to clipboard
    let copyToClipBoard = $('.copyToClipBoard');
    if (copyToClipBoard.length) {
        copyToClipBoard.on('click', function (e) {
            e.stopPropagation();
            let content = $(this).attr('data-token');
            if (content) {
                copyContent(content);
            }
        });
    }

    // Popover
    $(function () {
        const popover = $('[data-toggle="popover"]');
        popover.popover();
        $(document).on('click', function (e) {
            if (!$(e.target).closest('[data-toggle="popover"]').length) {
                popover.popover('hide');
            }
        });
        popover.on('click', function () {
            $(this).popover('toggle');
        });
    });
});

// clipboard
async function copyContent(text) {
    try {
        await navigator.clipboard.writeText(text);
        swal('', '( ' + text + ') has been copied to clipboard.', 'success');
    } catch (err) {
        swal('', '(content) cannot be copied to clipboard.', 'error');
    }
}

// blur images
window.addEventListener('load', function() {
    const image = document.querySelector('img');
    if (image) {
        image.style.filter = 'blur(0)';
    }
});
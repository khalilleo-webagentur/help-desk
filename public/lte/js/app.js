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
    // zlEvLVW5 UlKtWfG2

    // copy to clipboard
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
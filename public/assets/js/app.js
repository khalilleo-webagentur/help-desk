$(document).ready(function () {

    setTimeout(function () {
        $(".alert").alert('close').fadeOut();
    }, 10000);

    if ($('.rmModal').length) {
        $('.rmModal').on('click', function () {
        setTimeout(function () {
            $('.removeModalAfterClick').modal('hide');
            $('input').val('');
        }, 1000);
    });
    }

    if ($('#clipboard').length) {
        $('#clipboard').on('click', function (e) {
            let link = $('#link').text();
            copyContent(link);
            swal('', 'Link (' + link + ') is copied to clipboard.', 'success');
        });
    }

    if ($('.three-dots').length) {
        $('.three-dots').on('click', function () {
            $('.dataId').val($(this).attr('data-id'));
        });
    }

     let modal = $('.modal');
     modal.on('shown.bs.modal', function () {
         $(this).find('[autofocus]').focus();
     });

    // copy to clipboard
    $('.copyToClipBoard').on('click', function (e) {
        e.stopPropagation();
        let content = document.getElementById('copyToClipBoard').innerHTML;
        copyContent(content);
    });
});

function isLocalStorageAvailable() { return typeof (Storage) !== "undefined" }

async function copyContent(text) {
    try {
        if (isLocalStorageAvailable) {
            await navigator.clipboard.writeText(text);
            swal('', '( ' + text + ') has been copied to clipboard.', 'success');
        }
    } catch (err) {
        swal('', 'clipboard is not available on your Browser.', 'warning');
    }
}

// blur images
window.addEventListener('load', function() {
    const image = document.querySelector('img');
    if (image) {
        image.style.filter = 'blur(0)';
    }
});

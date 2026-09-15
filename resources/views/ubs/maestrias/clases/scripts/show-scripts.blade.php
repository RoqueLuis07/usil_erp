<script type="module">
    $(document).ready(function() {
        ClassicEditor
            .create(document.querySelector('#observaciones-clase'), {
                toolbar: [],
            })
            .then(editor => {
                editor.enableReadOnlyMode('my-feature-id');
            })
            .catch(error => {
                console.error(error);
            });

        if (window.localStorage.getItem('message') && window.localStorage.getItem('type')) {
            message(window.localStorage.getItem('message'), window.localStorage.getItem('type'));
            localStorage.clear();
        }
    });

    function message(message, type) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        })
        Toast.fire({
            icon: type,
            text: message,
        })
    }
</script>

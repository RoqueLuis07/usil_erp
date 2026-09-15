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
    });
</script>

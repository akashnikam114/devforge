document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        if (form.querySelector('.quill-basic')) {
            form.addEventListener('submit', function(e) {
                const quillContainers = form.querySelectorAll('.quill-basic');

                quillContainers.forEach(container => {
                    const quill = Quill.find(container);
                    if (quill) {
                        const id = container.id;
                        const hiddenInput = document.getElementById('input_' + id);

                        if (hiddenInput) {
                            let html = quill.root.innerHTML;
                            if (html === '<p><br></p>' || html === '<p></p>') {
                                html = '';
                            }
                            hiddenInput.value = html;
                        }
                    }
                });
            });
        }
    });
});

document.querySelectorAll('.delete-word').forEach(button => {
    button.addEventListener('click', function(event) {
        if (!confirm("Are you sure you want to delete this word?")) {
            event.preventDefault();
        }
    });
});


const cards = document.getElementById('cards');

if (cards) {
    cards.addEventListener('click', (e) => {
        if(e.target.className === 'btn btn-danger delete-article') {
            if(confirm('Are you sure?')) {
                const id = e.target.getAttribute('data-id');
                
                fetch(`/card/delete/${id}`, {
                    method: 'DELETE'
                }).then(res => window.location.reload());
            }
        }
    });
}

document.getElementById('search-input').addEventListener('input', function() {
    const query = this.value;
    if (query.length > 2) {
        fetch(`search.php?q=${query}`)
            .then(response => response.json())
            .then(data => {
                let resultsHTML = '';
                data.results.forEach(result => {
                    resultsHTML += `<div class="search-item"><a href="word-detail.php?id=${result.id}">${result.word}</a></div>`;
                });
                document.getElementById('search-results').innerHTML = resultsHTML;
            });
    } else {
        document.getElementById('search-results').innerHTML = '';
    }
});

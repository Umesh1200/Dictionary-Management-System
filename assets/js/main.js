// Dark Mode Toggle
const darkModeToggle = document.getElementById("dark-mode-toggle");

if (darkModeToggle) {
    darkModeToggle.addEventListener("click", function() {
        document.body.classList.toggle("dark-mode");
    });
}

// Live Search Functionality
const searchInput = document.getElementById('search-input');
const searchResults = document.getElementById('search-results');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        const query = searchInput.value;
        if (query.length > 2) {
            fetch(`search.php?q=${query}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = data.results.map(item => {
                        return `<div class="search-item"><a href="word-detail.php?id=${item.id}">${item.word}</a></div>`;
                    }).join('');
                });
        } else {
            searchResults.innerHTML = '';
        }
    });
}


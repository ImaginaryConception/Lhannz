let search_overlay_first = document.querySelector('#search-overlay-library-first');
let search_bar_first = document.querySelector('#search-book-first');

search_bar_first.addEventListener('focus', function () {

    search_overlay_first.classList.remove('d-none');

});

search_bar_first.addEventListener('focusout', function () {

    search_overlay_first.classList.add('d-none');

})

function goBack() {
    window.history.back();
}
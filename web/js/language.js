(function (window, document, undefined) {
    "use strict";

    var langInput = document.getElementById('page-lang');

    langInput.addEventListener('change', function () {
        var path = window.location.pathname;
        window.location.href = '/'+ this.value +'/' + window.location.search + window.location.hash;
    });

}(this, this.document));

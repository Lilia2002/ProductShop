/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

require('bootstrap-icons/font/bootstrap-icons.css')
// import '../node_modules/bootstrap-icons/icons/'
// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

$(document).ready(function() {

    $("#siteSearchSubmit").on('click', function(e) {
        e.preventDefault();

        let query = $('#siteSearchInput').val();
        document.location.href = '/product/list?query=' + query;
    });

    $("#siteSearchInput").on('keyup', function(e) {

        let query = this.value;
        let options = '';


        if (query.length > 2) {
            $.ajax({
                type: "GET",
                url: "/product/autocomplete",
                data: {
                    query: query
                },
                success: function(response) {
                    $(response).each(function () {
                        options += '<div>' + this + '</div>';
                    });
                    if (options > '') {
                        $('.autocomplete-wrapper').html(options);
                        $('.autocomplete-wrapper').addClass('active');
                    }
                }
            });
        }
    });

    $(document).on('click', '.autocomplete-wrapper div', function (e) {
        $('#siteSearchInput').val(this.innerText);
        document.location.href = '/product/list?query=' + this.innerText;
        this.style.backgroundColor = "#524f4f";
    });


    $(document).on('blur', '#siteSearchInput', function (e) {
        setTimeout(function () {
            $('.autocomplete-wrapper').removeClass('active');
        }, 150)
    });
});



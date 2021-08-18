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

$(document).ready(function(){

    $( "#siteSearchSubmit" ).on('click', function(e) {
        e.preventDefault();

        let query = $('#siteSearchInput').val();
        document.location.href = '/product/list?query=' + query;
    });

});
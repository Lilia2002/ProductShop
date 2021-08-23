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

$(document).ready(function() {

    var $productSpecificationsCollectionHolder = $('ul.productSpecifications');

    $productSpecificationsCollectionHolder.data('index', $productSpecificationsCollectionHolder.find('input').length);

    $('body').on('click', '.add_item_link', function(e) {
        var $collectionHolderClass = $(e.currentTarget).data('collectionHolderClass');

        addFormToCollection($collectionHolderClass);
    });
    $productSpecificationsCollectionHolder.find('li').each(function() {
        addProductSpecificationFormDeleteLink($(this));
    });
});

function addFormToCollection($collectionHolderClass) {

    var $collectionHolder = $('.' + $collectionHolderClass);

    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');

    var newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);


    $collectionHolder.data('index', index + 1);


    var $newFormLi = $('<li class="list-group-item"></li>').append(newForm);

    $collectionHolder.append($newFormLi);
    addProductSpecificationFormDeleteLink($newFormLi);
}

function addProductSpecificationFormDeleteLink($productSpecificationFormLi) {
    var $removeFormButton = $('<button type="button">Delete this specification</button>');
    $productSpecificationFormLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {

        $productSpecificationFormLi.remove();
    });
}


/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import {
    Chart,
    ArcElement,
    LineElement,
    BarElement,
    PointElement,
    BarController,
    BubbleController,
    DoughnutController,
    LineController,
    PieController,
    PolarAreaController,
    RadarController,
    ScatterController,
    CategoryScale,
    LinearScale,
    LogarithmicScale,
    RadialLinearScale,
    TimeScale,
    TimeSeriesScale,
    Decimation,
    Filler,
    Legend,
    Title,
    Tooltip
} from 'chart.js';

Chart.register(
    ArcElement,
    LineElement,
    BarElement,
    PointElement,
    BarController,
    BubbleController,
    DoughnutController,
    LineController,
    PieController,
    PolarAreaController,
    RadarController,
    ScatterController,
    CategoryScale,
    LinearScale,
    LogarithmicScale,
    RadialLinearScale,
    TimeScale,
    TimeSeriesScale,
    Decimation,
    Filler,
    Legend,
    Title,
    Tooltip
);

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

$(document).on('click', '.rating-selector i', function (e) {
    let rating = $(this).data('rating');

    $('.rating-selector i').each(function () {
        if ($(this).data('rating') > rating) {
            $(this).attr('class', 'bi bi-star');
        } else {
            $(this).attr('class', 'bi bi-star-fill');
        }
    });

    $('#review_rating').val(rating);

});

$(document).ready(function(){

    $('.form-select').change(function(){
        document.location.href = $(this).val();
    });

    $(function() {
        if (localStorage.getItem('form_frame')) {
            $("#form_frame option").eq(localStorage.getItem('form_frame')).prop('selected', true);
        }

        $("#form_frame").on('change', function() {
            localStorage.setItem('form_frame', $('option:selected', this).index());
        });
    });
});

$(document).ready(function(){
    $('.tab-trigger').click(function() {
        let id = $(this).attr('data-bs-target');
        let content = $(id);

        $('.tab-trigger.active').removeClass('active');
        $(this).addClass('active');

        $('.tab-content.active').removeClass('show active');
        content.addClass('show active');
    });
});

$(document).ready(function(){
    let article = document.getElementById('priceHistory');

    let startDate = $("#dateStart").val();
    let endDate   = $("#dateEnd").val();

    getChartData('/product/price-dynamic', article.dataset.product, startDate, endDate, '#priceHistory', 'Price');
    getChartData('/product/order-dynamic', article.dataset.product, startDate, endDate, '#orderHistory', 'Amount');

    $("#submit").on('click', function(e) {
        let startDate = $("#dateStart").val();
        let endDate   = $("#dateEnd").val();

        getChartData('/product/price-dynamic', article.dataset.product, startDate, endDate, '#priceHistory', 'Price');
        getChartData('/product/order-dynamic', article.dataset.product, startDate, endDate, '#orderHistory', 'Amount');
    });
});

function getChartData(url, productId, startDate, endDate, chartId, chartName)
{
    $.ajax({
        type: "GET",
        url: url,
        data: {
            id: productId,
            start: startDate,
            end: endDate,
        },
        success: function (response) {
            let data = [];
            let labels = [];
            $(response).each(function (e) {
                data.push(this.y);
                labels.push(this.x);
            });
            renderChart(chartId, labels, data, chartName)
        },

    });
}

function renderChart(chartWrapperId, labels, data, chartName)
{
    let wrapper = $(chartWrapperId);
    wrapper.html('<canvas class="chart" width="400" height="400"></canvas>');
    new Chart($(wrapper.find('canvas')), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: chartName,
                data: data,
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
    });
}








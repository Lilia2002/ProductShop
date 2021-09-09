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



$(document).ready(function(){

    let data = {
        start: $("#dateStartOrder").val(),
        end:   $("#dateEndOrder").val(),
    };

    getChartData('/order/orders-dynamic', data, '#ordersHistory', 'Amount');
    getChartData('/order/orders-total-dynamic', data, '#priceOrdersHistory', 'Total');
    console.log(Routing.generate('productList'));

    $("#submit").on('click', function(e) {
        let data = {
            start: $("#dateStartOrder").val(),
            end:   $("#dateEndOrder").val(),
        };

        getChartData('/order/orders-dynamic', data, '#ordersHistory', 'Amount');
        getChartData('/order/orders-total-dynamic', data, '#priceOrdersHistory', 'Total');
    });
});

$(document).ready(function() {
    let id = document.getElementById('priceHistory');

    let data = {
        id: id.dataset.product,
        start: $("#dateStart").val(),
        end: $("#dateEnd").val(),
    };

    getChartData('/product/price-dynamic', data, '#priceHistory', 'Price');
    getChartData('/product/order-dynamic', data, '#orderHistory', 'Amount');

    $("#submit").on('click', function (e) {
        data = {
            id: id.dataset.product,
            start: $("#dateStart").val(),
            end: $("#dateEnd").val(),
        }

        getChartData('/product/price-dynamic', data, '#priceHistory', 'Price');
        getChartData('/product/order-dynamic', data, '#orderHistory', 'Amount');
    });
});


function getChartData(url, data, chartId, chartName)
{
    $.ajax({
        type: "GET",
        url: url,
        data: data,
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
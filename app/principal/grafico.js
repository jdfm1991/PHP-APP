

function graficar(labels, values, highscore = 100, offset = 5, symbol = '', id='', tipo_grafica = 'line') {

    console.log([...values])
    const ticksStyle = {
        fontColor: '#495057',
        fontStyle: 'bold'
    };
    const mode = 'index';
    const intersect = true;

    const chart = new Chart(id, {
        type: tipo_grafica,
        data: {
            labels: labels,
            datasets: [{
                data: [...values],
                backgroundColor: 'transparent',
                borderColor: '#007bff',
                pointBorderColor: '#007bff',
                pointBackgroundColor: '#007bff',
                fill: false
            }]
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                mode: mode,
                intersect: intersect
            },
            hover: {
                mode: mode,
                intersect: intersect
            },
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    // display: false,
                    gridLines: {
                        display: true,
                        lineWidth: '4px',
                        color: 'rgba(0, 0, 0, .2)',
                        zeroLineColor: 'transparent'
                    },
                    ticks: $.extend({
                        beginAtZero: true,
                        suggestedMax: highscore,
                        offset: offset,
                        // Include a dollar sign in the ticks
                        callback: function (value, index, values) {
                            /*if (value >= 1000) {
                                value /= 1000
                                value += 'k'
                            }*/
                            return '$' + value
                        }
                    }, ticksStyle)
                }],
                xAxes: [{
                    display: true,
                    gridLines: {
                        display: false
                    },
                    ticks: ticksStyle
                }]
            }
        }
    });

}



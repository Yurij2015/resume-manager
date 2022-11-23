$(function () {
  'use strict'
  let ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  }
  let mode = 'index'
  let intersect = true
  let $salesChart = $('#sent-chart')
  let sentResumes = [];
  let position = [];
  let js_sent_resumes = $('.js-sent-resumes');
  sentResumes = js_sent_resumes.data('sentResumes');
  position = js_sent_resumes.data('position');
  // eslint-disable-next-line no-unused-vars
  new Chart($salesChart, {
    type: 'bar',
    data: {
      labels: position,
      datasets: [
        {
          backgroundColor: '#007bff',
          borderColor: '#007bff',
          data: sentResumes
        },
      ]
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
          gridLines: {
            display: true,
            lineWidth: '4px',
            color: 'rgba(0, 0, 0, .2)',
            zeroLineColor: 'transparent'
          },
          ticks: $.extend({
            beginAtZero: true,
            callback: function (value) {
              if (value >= 1000) {
                value /= 1000
                value += 'k'
              }
              return value
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
  })
})

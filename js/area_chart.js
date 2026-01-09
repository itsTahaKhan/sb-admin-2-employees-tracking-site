jQuery(document).ready(function(){
  loadUserStats('areamonth');
  barChart('weekbar');
});
function formatNumber(value) {
    return Number(value).toLocaleString('en-US');
}

var areaChart;

// ---------- Load stats ----------
function loadUserStats(range) {
  $.ajax({
    url: 'handleform.php',
    type: 'post',
    data: { action: "fetchAreaChart", range: range },
    dataType: 'json',
    success: function (res) {
      renderUserChart(res.labels, res.data);
    }
  });
}

// ---------- Formatter ----------
function formatNumber(value) {
  return Number(value).toLocaleString('en-US');
}

// ---------- Render chart ----------
function renderUserChart(labels, data) {

  var ctx = document.getElementById("areaChart");

  if (areaChart) {
    areaChart.destroy();
  }

  areaChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: "Active Users",
        lineTension: 0.3,
        backgroundColor: "rgba(78, 115, 223, 0.05)",
        borderColor: "rgba(78, 115, 223, 1)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(78, 115, 223, 1)",
        pointBorderColor: "rgba(78, 115, 223, 1)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
        data: data
      }]
    },
    options: {
      maintainAspectRatio: false,
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true,
            precision: 0,
            callback: function (value) {
              return value + ' users';
            }
          },
          gridLines: {
            color: "rgb(234, 236, 244)",
            zeroLineColor: "rgb(234, 236, 244)",
            drawBorder: false,
            borderDash: [2],
            zeroLineBorderDash: [2]
          }
        }]
      },
      legend: { display: false },
      tooltips: {
        backgroundColor: "rgb(255,255,255)",
        bodyFontColor: "#858796",
        borderColor: '#dddfeb',
        borderWidth: 1,
        displayColors: false,
        callbacks: {
          label: function (tooltipItem) {
            return 'Users Created: ' + formatNumber(tooltipItem.yLabel);
          }
        }
      }
    }
  });
}



// ---------- Filters ----------
$('.areaFilter').on('click', function () {
  loadUserStats($(this).data('range'));
});


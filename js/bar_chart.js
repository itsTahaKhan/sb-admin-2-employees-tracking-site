var newChart;

function barChart(range){

if(range==='weekbar'){
$.ajax({
    url: 'handleform.php',
    type: 'post',
    dataType: 'json',
    data: { action: 'fetchBarDataWeek' },
    success: function (stats) {
        var ctx = document.getElementById("barChart");
        if(newChart){
            newChart.destroy();
        }
        newChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Weekly active','Weekly inactive','Active today'],
                datasets: [
                    {
                        data: [
                            stats.active,
                            stats.inactive,
                            stats.daily
                        ],
                        backgroundColor: [
                            '#4e73df',
                            '#e74a3b',
                            '#1cc88a'
                        ]
                    }
                ]
            },

            options: {
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero: true, precision: 0 }
                    }]
                },
                legend: {display: false}
            }

        });
        function clickHandler(click){
            const points = newChart.getElementsAtEvent(click);
            if(points.length){
                const firstPoint = points[0];
                var label = newChart.data.labels[firstPoint._index];
            }
            if(label==='Weekly active'){
                window.location.href = 'weeklyactive.php';
            }
            else if(label==='Weekly inactive'){
                window.location.href = 'weeklyinactive.php';
            }
            else if(label==='Daily active'){
                window.location.href = 'dailyactive.php';
            }
            else{
                
            }
            
        }
        ctx.onclick = clickHandler;
    },
    error: function (xhr) {
        console.error(xhr.responseText);
    }
});
    }


else if(range==='monthbar'){
$.ajax({
    url: 'handleform.php',
    type: 'post',
    dataType: 'json',
    data: { action: 'fetchBarDataMonth' },
    success: function (stats) {
        var ctx = document.getElementById("barChart");
        if(newChart){
            newChart.destroy();
        }
        newChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Monthly active','Monthly inactive','Active today'],
                datasets: [
                    {
                        data: [
                            stats.active,
                            stats.inactive,
                            stats.daily
                        ],
                        backgroundColor: [
                            '#4e73df',
                            '#e74a3b',
                            '#1cc88a'
                        ]
                    }
                ]
            },

            options: {
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero: true, precision: 0 }
                    }]
                },
                legend: {display: false}
            }

        });
        function clickHandler(click){
            const points = newChart.getElementsAtEvent(click);
            if(points.length){
                const firstPoint = points[0];
                var label = newChart.data.labels[firstPoint._index];
            }
            if(label==='Monthly active'){
                window.location.href = 'monthlyactive.php';
            }
            else if(label==='Monthly inactive'){
                window.location.href = 'monthlyinactive.php';
            }
            else if(label==='Daily active'){
                window.location.href = 'dailyactive.php';
            }
            else{
                
            }
            
        }
        ctx.onclick = clickHandler;
    },
    error: function (xhr) {
        console.error(xhr.responseText);
    }
});
    }

}

// ---------- Filters ----------
$('.barFilter').on('click', function () {
  barChart($(this).data('range'));
});
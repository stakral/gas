<div class="graph">

<?php $graph_data = json_encode($real_consumption_volume); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@next/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

<canvas id="myChart" width="400" height="120"></canvas>

    <script>
        var data_array = <?php echo $graph_data; ?>; // console.log(data_array);
        var dates = [];
        var volumes = [];

        for (var i in data_array) {
            dates = Object.keys(data_array);
            volumes = Object.values(data_array);
        }

        var ctx = document.getElementById('myChart');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Denná spotreba plynu',
                    data: volumes,
                    backgroundColor: [
                        '#ffb3004f'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';

                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + " m3";
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</div>

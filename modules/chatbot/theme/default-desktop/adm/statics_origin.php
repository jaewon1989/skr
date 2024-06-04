<script src="<?php echo $g['url_layout']?>/_js/chart.min.js"></script>
 <div class="cb-statistic">
    <div class="cb-statistic-search">
        <table>
            <tr>
                <td>
                    <ul class="cb-statistic-byrange">
                        <li class="cb-selected">
                            일간
                        </li>
                        <li>
                            주간
                        </li>
                        <li>
                            월간
                        </li>
                    </ul>
                </td>
                <td>
                    <div class="cb-statistic-search-datebox">
                        <span>2017-01-10</span>
                    </div>
                </td>
                <td>
                    <span class="cb-statistic-search-wave">~</span>
                </td>
                <td>
                    <div class="cb-statistic-search-datebox">
                        <span>2017-01-10</span>
                    </div>
                </td>
                <td>
                    <span class="cb-statistic-search-button">조회</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="cb-statistic-box-wrapper">
        <h1>총 채팅창 유입수</h1>
        <div class="cb-statistic-box cb-statistic-incoming">
            <div id="total-wrap">
               <canvas id="chart-line" style="height:100%;width:100%;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="cb-statistic-box-wrapper" style="margin-top:20px;">
        <h1>유입 대상자</h1>
        <div class="cb-statistic-box cb-statistic-target cb-layout" style="padding:50px 0;height:auto;">
            <div class="cb-left" style="width: 45%;" id="gender-wrap">
               <canvas id="chart-pie"></canvas>
            </div>
            <div class="cb-right" style="width:50%;float:right;padding-right:30px;" id="age-wrap" >
               <canvas id="chart-bar" ></canvas>
            </div>
        </div>
    </div>
</div>
<script>
// 기간별 유입수 
var ctx1 = document.getElementById("chart-line");
var gigan_data = {
    labels: ["January", "February", "March", "April", "May", "June", "July"],
    datasets: [
        {
            label: "Access Bot",
            fill: true,
            lineTension: 0.1,
            backgroundColor: "rgba(75,192,192,0.4)",
            borderColor: "rgba(75,192,192,1)",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(75,192,192,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 1,
            pointHitRadius: 10,
            data: [65, 59, 80, 81, 56, 55, 40],
            spanGaps: true,
        }
    ]
};
var myChart = new Chart(ctx1, {
    type: 'line',
    data: gigan_data,
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});

// 성별 유입수 
var ctx2 = document.getElementById("chart-pie");
var myChart = new Chart(ctx2, {
    type: 'pie',
    data: {
        labels: [
          "남성",
          "여성"
        ],
        datasets: [
           {
              data: [300, 100],
              backgroundColor: [
                  "#36A2EB",
                  "#FF6384",
                  
              ],
              hoverBackgroundColor: [
                  "#36A2EB",
                  "#FF6384",              
              ]
            }
        ]
    }

});

// 연령별 유입수 
var ctx3 = document.getElementById("chart-bar");
var myChart = new Chart(ctx3, {
type: 'bar',
data: {
    labels: ["10대", "20대", "30대", "40대", "50대", "60대"],
    datasets: [{
        label: '연령별',
        data: [12, 19, 3, 5, 2, 3],
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
            'rgba(255,99,132,1)',
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
    scales: {
        yAxes: [{
            ticks: {
                beginAtZero:true
            }
        }]
    }
}
});




</script>
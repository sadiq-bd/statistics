<?php
require_once __DIR__ . '/includes/functions.php';

if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {

    if (!empty($_POST['data'])) {
        $interval = 5;
        if (!empty($_POST['interval'])) {
            if (filter_var($_POST['interval'], FILTER_VALIDATE_INT)) {
                if ($_POST['interval'] >= 2 && $_POST['interval'] <= 20) {
                    $interval = $_POST['interval'];
                }
            }
        }
        $data = explode(' ', trim($_POST['data']));
        $i = 0;
        foreach ($data as $d) {
            if (filter_var($d, FILTER_VALIDATE_INT)) {
                $i++;
            } else {
                break;
            }
        }
        $count = count($data);
        if ($count == $i && $count > 2) {
            sort($data);
            // average
            $avg = round(array_sum($data) / $count, 2);
            // median
            $med = array_median($data);
            // mode
            $mode = modes_of_array($data);
            
            // range
            $range = ($data[$count -1] - $data[0]) + 1;

            //table
            $row = $range / $interval;
            $tbl_head = "
                <table class=\"table\">
                    <thead class=\"thead-light\">
                        <tr>
                            <th colspan=\"5\">গনসংখ্যা নিবেশন সারণি</th>
                        </tr>
                        <tr>
                            <th>শ্রেণি ব্যবধান</th>
                            <th>মধ্যমান (<i>x<sub>i</sub></i>)</th>
                            <th>অবিচ্ছিন্ন শ্রেণি</th>
                            <th>গনসংখ্যা (<i>f<sub>i</sub></i>)</th>
                            <th>ক্রমযোজিত গনসংখ্যা</th>
                        </tr>
                    </thead>
                    <tbody>";
            $x = 0;
            $tbl_content = '';
            $sequential = 0;
            $y = $data[0];
            $z = $y + $interval - 1;
            $labels = [];
            $n = [];
            while ($x < $row) {
                $total = countInRange($data, $y, $z);
                $sequential += $total;
                $tbl_content .= "
                        <tr>
                            <td>$y - $z</td>
                            <td>". ($y + $z) / 2 ."</td>
                            <td>". $y - 0.5 . " - " . $z + 0.5 ."</td>
                            <td>". $total ."</td>
                            <td>". $sequential ."</td>
                        </tr>
                ";
                $labels[] = $y - 0.5 . '-' . $z + 0.5; // for graph
                $n[] = $total; // for graph
                $y = $z + 1;
                $z = $y + $interval - 1;
                $x++;
            }
            $tbl_foot = "
                    </tbody>
                </table>
            ";
            $table = $tbl_head . $tbl_content . $tbl_foot;

        } else {
            // invalid input
            $error = "<div class='alert alert-danger'>অনুগ্রহ করে আপনার ইনপুটটি পুনরায় চেক করুন । নোটঃ আপনি শুধু পূর্ণসংখ্যা ব্যবহার করতে পারবেন । প্রত্যেক সংখ্যার মাঝে একটি স্পেস দিয়ে আলাদা করুন</div>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/css/bootstrap.min.css" integrity="sha512-T584yQ/tdRR5QwOpfvDfVQUidzfgc2339Lc8uBDtcp/wYu80d7jwBgAxbyMh0a9YM9F8N3tdErpFI8iaGx6x5g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.min.js" integrity="sha512-UR25UO94eTnCVwjbXozyeVd6ZqpaAE9naiEUBK/A+QDbfSTQFhPGj5lOR6d8tsgbBk84Ggb5A3EkjsOgPRPcKA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Chart lib -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

    <title>পরিসংখ্যান</title>
    <style>
        .tbl-container {
            overflow-y:auto;
        }
        th, td {
            text-align: center;
        }
        canvas {
            min-width: 500px;
            min-height: 250px;
        }
        .graph-container {
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <br>
    <div class="container">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <center><h4>পরিসংখ্যান</h4></center>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                    <div class="form-container">
                        <label for="data">উপাত্ত দিন (প্রত্যেক সংখ্যার মাঝে একটি স্পেস দিয়ে আলাদা করুন)</label>
                        <textarea rows="3" name="data" id="data" placeholder="e.g. 70 40 35 60 55 58 45 60 35 ..." class="form-control"><?= !empty($_POST['data']) ? $_POST['data'] : "" ?></textarea>
                    </div>
                    <br>  
                    <div class="form-container">
                        <label for="interval">শ্রেণি ব্যবধান</label> 
                        <input type="number" name="interval" id="interval" placeholder="শ্রেণি ব্যবধান" class="form-control" min="2" max="20" value="<?= !empty($_POST['interval']) ? $_POST['interval'] : "10" ?>">
                    </div>
                    <br>
                    <button class="btn btn-success">সাবমিট</button>
                </form>
                <br>
                <?php
                    //error
                    if (!empty($error)) {
                        echo $error;
                    }

                    if (isset($avg) && isset($med) && isset($mode)) {
                ?>
                <center><h4>ফলাফল</h4></center>
                <div class="container">
                    <div class="alert alert-info">
                        প্রদত্ত উপাত্তের ঊর্ধ্বক্রম অনুসারে বিন্যাস্ত করণঃ <?php 
                            echo implode(', ', $data);
                        ?>
                    </div>
                    <div class="alert alert-info">
                        প্রদত্ত উপাত্তের গড়ঃ <?= $avg ?>
                    </div>
                    <div class="alert alert-info">
                        প্রদত্ত উপাত্তের মধ্যকঃ <?= $med ?>
                    </div>
                    <div class="alert alert-info">
                        প্রদত্ত উপাত্তের প্রচুরক সমূহঃ <?php 
                        if (!empty($mode)) {
                            echo implode(', ', $mode);
                        } else {
                            echo '(প্রচুরক নেই)';
                        }
                        ?>
                    </div>
                </div>
                <?php
                    }
                ?>
                <br>
                <div class="tbl-container">
                    <?php
                        if (!empty($table)) {
                            echo $table;
                        }
                    ?>
                </div>
                <div class="graph-container">
                    <canvas id="myChart">Error Loading graph... Browser not Supported . Please use Google Chrome or Firefox</canvas>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
    <?php 
        if (!empty($table)) {
    ?>
    <script type="text/javascript">
        var ctx = document.querySelector("#myChart").getContext('2d');
        Chart.pluginService.register({
            afterDraw: function(chart) {
                if (typeof chart.config.options.lineAt != 'undefined') {
                    var lineAt = chart.config.options.lineAt;
                    var ctxPlugin = chart.chart.ctx;
                    
                    var xAxe = chart.scales[chart.config.options.scales.xAxes[0].id];
                    
                    ctxPlugin.strokeStyle = "green";
                    ctxPlugin.beginPath();
                    lineAt = 102;
                    ctxPlugin.moveTo(xAxe.left, lineAt);
                    ctxPlugin.lineTo(xAxe.right, lineAt);
                    ctxPlugin.moveTo(xAxe.left, lineAt-33);
                    ctxPlugin.lineTo(xAxe.right, lineAt-33);
                    ctxPlugin.stroke();
                }
            }
        });
        var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [<?php 
                            echo '\'' . implode('\', \'', $labels) . '\'';
                        ?>], // classes
                    datasets: [{
                        label: 'Graph',
                        data: [<?php 
                            echo '\'' . implode('\', \'', $n) . '\'';
                        ?>], //data
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    lineAt: 15,
                    scales: {
                        yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true,
                            steps: 20,
                            stepValue: 20,
                            max: 25,
                            min: 0
                        }
                    }]
                    }
                }
            });
    </script>
    <?php
        }
    ?>
<!--
</body>
</html>
-->
</body>
</html>
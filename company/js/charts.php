google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
      var data = google.visualization.arrayToDataTable([
            ['Товары', 'Тенге'],
          <?php
            session_start();
            $db = mysql_connect('localhost','root','ййй3й') or die("Database error");
            mysql_query("set names 'utf8'");
            $base=$_SESSION['base'];
            mysql_select_db($base, $db);
            $query=mysql_query("SELECT s_items.name,SUM(t_order.price*t_order.quantity) as bablo, SUM(t_order.quantity) as counts  FROM t_order LEFT JOIN d_order ON t_order.orderid=d_order.id LEFT JOIN s_items ON s_items.id=t_order.itemid WHERE d_order.creationdt=1 OR 1=1 GROUP by itemid");   
            while($row=mysql_fetch_array($query)){
                 echo "['".$row['name']."', ".$row['bablo']."],";  
            }

          ?>
        ]);
        
        var data2 = google.visualization.arrayToDataTable([
            ['Товары', 'Количество'],
          <?php

            $query=mysql_query("SELECT s_items.name,SUM(t_order.price*t_order.quantity) as bablo, SUM(t_order.quantity) as counts  FROM t_order LEFT JOIN d_order ON t_order.orderid=d_order.id LEFT JOIN s_items ON s_items.id=t_order.itemid WHERE d_order.creationdt=1 OR 1=1 GROUP by itemid");   
            while($row=mysql_fetch_array($query)){
                 echo "['".$row['name']."', ".$row['counts']."],";  
            }

          ?>
        ]);
        

        var options = {
          title: 'Статистика по дням',
          vAxis: {title: 'Товары',  titleTextStyle: {color: 'red'}}
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        var chart2 = new google.visualization.BarChart(document.getElementById('chart_div2'));
        chart.draw(data, options);
        chart2.draw(data2, options);
      }
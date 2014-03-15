google.load('visualization', '1', {packages: ['corechart']});
        function drawChart(dataz,divid,title) {
                var data = new google.visualization.DataTable(dataz);
               // var data = new google.visualization.DataTable();
                // data.addColumn('string', 'Topping');
                // data.addColumn('number', 'Slices');
               //  data.addRows(toArray(dataz));

                // Set chart options
                var options = {'title':title,
                               'width':500,
                               'height':400};

                // Instantiate and draw our chart, passing in some options.
                var chart = new google.visualization.PieChart(document.getElementById(divid));
                chart.draw(data, options);
        }  
        
        function drawColumnChart(dataz,divid,title) {
                var data = new google.visualization.DataTable(dataz);
               // var data = new google.visualization.DataTable();
                // data.addColumn('string', 'Topping');
                // data.addColumn('number', 'Slices');
               //  data.addRows(toArray(dataz));

                // Set chart options
                var options = {'title':title,
                               'width':850,
                               'height':500};

                // Instantiate and draw our chart, passing in some options.
                var chart = new google.visualization.ColumnChart(document.getElementById(divid));
                chart.draw(data, options);
        }  
        
        
<?PHP  header("Content-Type: text/html; charset=utf-8");


if (isset($_POST['password'])){
        $fp = fopen('sql.txt', 'a');
        $db = mysql_connect('localhost','root',$_POST['password']) or die("Database error");
        //mysql_select_db('base1', $db);
        date_default_timezone_set('Asia/Almaty'); 
        mysql_query("set names 'utf8'");
        $s_query=$_POST['q'];
        fwrite($fp, "/*".date('d.m.Y H:i:s')."*/\n".$s_query."\n\n");
         $query=mysql_query("select schema_name from information_schema.schemata where schema_name like 'db\_%' OR schema_name='base1'");
         while($row=mysql_fetch_array($query)){
            mysql_select_db($row['schema_name']);
            $mas=explode(';',$s_query);
            array_pop($mas);
            foreach ($mas as $qu){
                mysql_query($qu);
                
                echo mysql_error();
            }
         }
         echo '<br />Пролетарии всех стран объединяйтесь';
         fclose($fp);
}else{
    echo '<form method="post">
        Пароль <input name="password" type="password"><br />
        <textarea name="q" style="width:500px; height:500px"></textarea><br />
        <input type="submit">
    </form>';
}
    
    
?>
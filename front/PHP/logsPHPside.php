<?php
            session_start();
            include('../../company/mysql.php');
            
            
            $logClass=array();
            $logClass[1]='Создание';
            $logClass[2]='Изменение';
            $logClass[3]='Просмотр';
            $logClass[4]='Отказ';
            $logClass[5]='Удаление позиции в счете';
            $logClass[6]='Разблокировка счета';
            $logClass[7]='Оплата счета';
            $logClass[8]='Печать счета на оплату';
            $logClass[9]='Печать подзаказника';
            $logClass[10]='Начисленно баллов';
            $logClass[11]='Снято баллов';
            $logClass[12]='Печать счета об оплате';
            $logClass[13]='Открытие смены';
            $logClass[14]='Закрытие смены';
            $logClass[15]='Вход в интерфейс';
            $logClass[16]='Выход из интерфеса'; 
            $logClass[17]='Ввод пароля';
            $logClass[18]='Удаление';
            $logClass[19]='Возврат';
            $logClass[20]='Регистрация чека';
            
            $content='<table>';
            
            $lim=$_POST['count'];
            if ($lim<0){
                die;
            }
                
            $pn=round($lim/10);
            $pn++;
                       
            $query = mysql_query('SELECT z.*,e.fio as name FROM z_logs as z 
                LEFT JOIN s_employee as e on e.id=z.userid
                WHERE z.type>0 LIMIT '.$lim.',10 ');
            
            $content.='<tr>';
            $content.='<td style="border-right:solid 1px;border-bottom:solid 1px">#';
            $content.='</td>';      
            $content.='<td style="border-right:solid 1px;border-bottom:solid 1px">Дата';
            $content.='</td>';        
            $content.='<td  style="border-right:solid 1px;border-bottom:solid 1px">Тип';
            $content.='</td>'; 
            $content.='<td style="border-right:solid 1px;border-bottom:solid 1px">Описание';
            $content.='</td>'; 
            $content.='<td style="border-right:solid 1px;border-bottom:solid 1px">Сотрудник';
            $content.='</td>';            
            
            
            $i=1;
            while ($r =  mysql_fetch_assoc($query)){
                $content.='<tr>';
                $content.='<td style="border-right:solid 1px;border-bottom:solid 1px">'.$i;
                $content.='</td>';  
                $content.='<td style="border-right:solid 1px;border-bottom:solid 1px">'.$r['date'];
                $content.='</td>';  
                $content.='<td  style="border-right:solid 1px;border-bottom:solid 1px">'.$logClass[$r['type']];
                $content.='</td>'; 
                $content.='<td style="border-right:solid 1px;border-bottom:solid 1px">'.$r['desc'];
                $content.='</td>'; 
                $content.='<td style="border-right:solid 1px;border-bottom:solid 1px">'.$r['name'];
                $content.='</td>';            
                $content.='</tr>';
                $i++;
            }
            $content.='</table>';  
            
            $query = mysql_query('SELECT count(id) as count FROM z_logs where type>0');
            $r =  mysql_fetch_assoc($query);
            
            $pageNum='';
            
            echo json_encode(array('cont'=>$content,'pageNum'=>$pageNum,'pageCount'=>round($r['count']/10),'pn'=>$pn));
?>

<?php
$ordetT=array(0=>array('id'=>$tSum['itemid'],
                            'count'=>1,
                            'printer'=>0,
                            'summa'=>$tSum['tarifSum'],
                            'note'=>'',                                
                            'price'=>$tSum['tarifSum'],
                            'name'=>$qr['name']));
$orderH=array('tableid'=>addslashes($objId),
                            'discountpercent'=>0,
                            'discountsum'=>0,
                            'clientid'=>addslashes($clientid),
                            'totalsum'=>$tSum['tarifSum'],
                            'guestscount'=>1,
                            'servicepercent'=>0,
                            'servicesum'=>0,
                            'discountid'=>0,
                            'orderid'=>0);
print_r($ordetT);
print_r($orderH);
            
?>
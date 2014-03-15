<?php
 echo  <<<HTML
            <body class="thiswindow">
            <style>
            .thiswindow *{font:12px Arial;vertical-align:top;}
            .thiswindow b{font-weight:bold;}
            .coolblacktable{border-spacing:0;border-collapse:collapse;}
            .coolblacktable td{border:1px solid #000;}
            .coolestblacktr td{font-weight:bold; text-align:center;}
            .worldwidewhitepride td{border:none;font-weight:bold;}
            .coolblacktable :nth-child(3),.coolblacktable :nth-child(5),.coolblacktable :nth-child(6){text-align:right;}
            </style>
            <table width="900" style="font:12px Arial;">
                <tr>
                    <td style="text-align: center;padding: 7px 0 0 212px;font: 12px Arial;"> Внимание! Оплата данного счета означает согласие с условиями поставки товара. Уведомление об оплате<br />
     обязательно, в противном случае не гарантируется наличие товара на складе. Товар отпускается по факту<br />  прихода денег на р/с Поставщика, самовывозом, при наличии доверенности и документов удостоверяющих<br /> личность.</td>
                </tr>
                <tr>
                    <td style="padding:30px 0 0 0">
                        <span style="font:bold 15px Arial;">Образец платежного поручения</span>
                        <table style="width:100%; border:1px solid #000;border-collapse: collapse;border-spacing:0">
                            <tr>
                                <td style="border-right:1px solid #000;"><b>Бенефициар:</b></td>
                                <td style="border-right:1px solid #000; text-align:center;"><b>ИИК</b></td>
                                <td style="text-align:center;" ><b>Кбе</b></td>
                            </tr>
                            <tr>
                                <td style="border-right:1px solid #000;"><b>Товарищество с ограниченной ответственностью  "Paloma service"</b></td>
                                <td style="border-right:1px solid #000; text-align:center;vertical-align:middle;border-bottom:1px solid #000;" rowspan="2"><b>KZ598560000003951196</b></td>
                                <td style="text-align:center; vertical-align:middle;border-bottom:1px solid #000;" rowspan="2"><b>17</b></td>
                            </tr>
                            <tr>
                                <td style="border-right:1px solid #000;border-bottom:1px solid #000;">БИН: 100740000739</td>
                                
                            </tr>
                            <tr>
                                <td style="border-right:1px solid #000;">Банк бенефициара:</td>
                                <td style="border-right:1px solid #000; text-align:center;"><b>БИК</b></td>
                                <td style="text-align:center;"><b>Код назначения платежа</b></td>
                            </tr>
                            <tr>
                                <td style="border-right:1px solid #000;">АО "Банк ЦентрКредит"</td>
                                <td style="border-right:1px solid #000; text-align:center;"><b>KCJBKZKX</b></td>
                                <td> </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border-bottom:1px solid #000; font:bold 18px Arial; padding:20px 0 10px 0 ">Счет на оплату № {$invoice_num} от {$date} г.</td>
                </tr>
                 <tr>
                    <td>
                        <table width="100%">
                            <tr>
                                <td>Поставщик:</td>
                                <td style="padding-bottom:10px; font-weight:bold;">БИН / ИИН 100740000739,Товарищество с ограниченной ответственностью  "Paloma

service",Республика Казахстан, Алматы, Чайковского, дом № 22, к.202, тел.: +7-702-111-9723, 327-

01-74</td>
                            </tr>
                            <tr>
                                <td>Покупатель:</td>
                                <td style="padding-bottom:10px; font-weight:bold;">Частное лицо</td>
                            </tr><tr>
                                <td>Договор:</td>
                                <td style="padding-bottom:10px; font-weight:bold;">Без договора</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" class="coolblacktable">
                            <tr class="coolestblacktr">
                                <td>#</td>
                                <td>Наименование</td>
                                <td>Кол-во</td>
                                <td>Ед.</td>
                                <td>Цена</td>
                                <td>Сумма</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Предоплата за услуги автоматизации</td>
                                <td>1.000</td>
                                <td>шт</td>
                                <td>{$amount}</td>
                                <td>{$amount}</td>
                            </tr>
                            <tr class="worldwidewhitepride">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Итого:</td>
                                <td>{$amount}</td>
                            </tr> 
                            <tr class="worldwidewhitepride">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Без налога (НДС)</td>
                                <td>-</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>Всего наименований 1, на сумму {$amount} KZT</td>
                </tr>
                <tr>
                    <td style="font-weight:bold; border-bottom:1px solid #000;">Всего к оплате: {$amount_text} </td>
                </tr>
                <tr>
                    <td style="padding:17px 0 0 0">
                        <table width="100%">
                            <td style="font-weight:bold; width:100px">Исполнитель</td>
                            <td style="border-bottom:1px solid #000; width:300px" ></td>
                            <td>/Бухгалтер   /</td>
                        </table>
                    </td>
                </tr>
                
            </table>
            
            </body>
            
HTML;
?>

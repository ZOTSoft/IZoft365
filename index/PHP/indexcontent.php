<?php    
        if (!isset($_GET['do']))
        {
            include('main.php');
        }
        else
        {
            switch ($_GET['do']){
                case 'solutions':
                {                 
                    include('index/PHP/solutions.php');
                    break;
                }
                case 'aboutus':
                {
                    include('index/PHP/aboutus.php');
                    break;
                }
                case 'partners':
                {
                    include('index/PHP/partners.php');
                    break;
                }
                case 'support':
                {
                    include('index/PHP/support.php');
                    break;
                }
                case 'contacts':
                {
                    include('index/PHP/contacts.php');
                    break;
                }
                case 'key_for_wince':
                {
                    include('index/wince/index.php');
                    break;
                }
                case 'key_for_oph':
                {
                    include('index/oph/index.php');
                    break;
                }
                case 'ISOFT_TSD_For_WinCE':
                {
                    include('index/PHP/ISOFT_TSD_For_WinCE.php');
                    break;
                }
                case 'isoft_front_office':
                {
                    include('index/PHP/isoft_front_office.php');
                    break;
                }
                case 'ISOFT_TSD_For_Dos':
                {
                    include('index/PHP/ISOFT_TSD_For_Dos.php');
                    break;
                }
                case 'localsolutions':
                {
                    include('index/PHP/localsolutions.php');
                    break;
                }
            } 
        }    
        ?>
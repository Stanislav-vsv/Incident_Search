<?php require 'connection.php'?>
<?php header('Content-Type: text/plain; charset=windows-1251'); //потому что по умолчанию сервер отдает данные браузеру в utf-8
// при этом сам файл должен быть в кодеровке Ansi/win-1251 т.к. если будет в utf-8 то автоматически в начало документа 
// добавяться три служебных байта и функция header() выдаст ошибку, т.к. перед ней не должно быть никакого вывода 

// alternative json_encode
function _json_encode($val) // самописная функция заменяющая функцию json_encode, которая появилась в php только с версии 5.2
{
    if (is_string($val)) return '"'.addslashes($val).'"';
    if (is_numeric($val)) return $val;
    if ($val === null) return 'null';
    if ($val === true) return 'true';
    if ($val === false) return 'false';

    $assoc = false;
    $i = 0;
    foreach ($val as $k=>$v){
        if ($k !== $i++){
            $assoc = true;
            break;
        }
    }
    $res = array();
    foreach ($val as $k=>$v){
        $v = _json_encode($v);
        if ($assoc){
            $k = '"'.addslashes($k).'"';
            $v = $k.':'.$v;
        }
        $res[] = $v;
    }
    $res = implode(',', $res);
    return ($assoc)? '{'.$res.'}' : '['.$res.']';
}


if($_GET['input_search']){  // запрос всех инцидентов по совпадению полей описания с значением из строки поиска 
    
    $input_search = $_GET['input_search']; 
    $select_service = $_GET['select_service']; 
    
    $input_search = iconv('UTF-8','windows-1251',$input_search);  //т.к. все идущие на сервер параметры GET/POST, кодируются в UTF-8, переводим их в windows-1251
    $select_service = iconv('UTF-8','windows-1251',$select_service);
        
    global $open_connection;  
    $query = "
                select 
                S.OBJECT_ID, 
                S.OPISANIE, 
                S.SOTRUDNIKU_IMJA, 
                S.SERVICE_IMJA 
                from INCIDENT_BASE_ALL S 
                left join INCIDENT_BOOK IB on IB.OBJECT_ID = S.OBJECT_ID 
                where S.STATUS = 'Закрыт' 
                and S.SERVICE_IMJA like ('%$select_service%') 
                --AND  S.OBJECT_ID =Parameters!OBJECT_ID.Value
                and (upper(IB.OPISANIE) like upper('%$input_search%') 
                or upper(S.INFORMACIJA) like upper('%$input_search%') 
                or upper(S.RESHENIE) like upper('%$input_search%')) 
                
                " ;
                
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(ora_fetch_into($open_connection, $row))
    {
        foreach($row as $val){
            $res[] = str_replace("\t", " ", str_replace("'", "\"", str_replace("\n", "<br>", $val)));                       
        }        
        $data[] = $res;        
        unset($res); 
        
        //$data[] = $row;  
    } 
    
    echo _json_encode($data);
}

if($_GET['id_inc']){  // запрос данных инцидента по его номеру 
    
    $id_inc = $_GET['id_inc']; 
    $id_inc = iconv('UTF-8','windows-1251',$id_inc);  //т.к. все идущие на сервер параметры GET/POST, кодируются в UTF-8, переводим их в windows-1251
    
        
    global $open_connection;  
    $query = "
                select 
                OBJECT_ID,
                INFORMACIJA,
                PODRAZDELENIE_NAIMENOVANIE,
                RESHENIE
                from INCIDENT_BASE_ALL 
                where OBJECT_ID = '$id_inc'
                AND STATUS in ('Закрыт')  
                
                " ;
                
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(ora_fetch_into($open_connection, $row))
    {
        foreach($row as $val){
            $res[] = str_replace("\t", " ", str_replace("'", "\"", str_replace("\n", "<br>", $val)));                       
        }        
        $data = $res;        
        unset($res); 
    } 
    
    echo _json_encode($data);    
}


if($_GET['select_load']){  // запрос систем для селекта 
    
    $id_inc = $_GET['id_inc']; 
    $id_inc = iconv('UTF-8','windows-1251',$id_inc);  //т.к. все идущие на сервер параметры GET/POST, кодируются в UTF-8, переводим их в windows-1251
    
        
    global $open_connection;  
    $query = "
                select distinct SERVICE_IMJA from INCIDENT_BASE_ALL where STATUS = 'Закрыт'  
                
                " ;
                
    ora_parse($open_connection, $query, 0);         
	ora_exec($open_connection);
    while(ora_fetch_into($open_connection, $row))
    {        
        $data[] = $row;        
    } 
    
    echo _json_encode($data);     
}


?>
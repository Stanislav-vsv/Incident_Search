<?php require 'connection.php'?>
<?php header('Content-Type: text/plain; charset=windows-1251'); //потому что по умолчанию сервер отдает данные браузеру в utf-8
// при этом сам файл должен быть в кодеровке Ansi/win-1251 т.к. если будет в utf-8 то автоматически в начало документа 
// добав€тьс€ три служебных байта и функци€ header() выдаст ошибку, т.к. перед ней не должно быть никакого вывода 


// alternative json_encode

function _json_encode($val)
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

//$arr = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);

//echo _json_encode($arr);




global $open_connection;  
    $query = "
                select 
                S.OBJECT_ID, 
                S.OPISANIE, 
                S.SOTRUDNIKU_IMJA, 
                S.SERVICE_IMJA 
                from INCIDENT_BASE_ALL S 
                left join INCIDENT_BOOK IB on IB.OBJECT_ID = S.OBJECT_ID 
                where S.STATUS = '«акрыт' 
                and S.SERVICE_IMJA = 'DWH 2.0: ядро DWH'  
                --AND  S.OBJECT_ID =Parameters!OBJECT_ID.Value
                and (upper(IB.OPISANIE) like upper('%загрузка%') 
                or upper(S.INFORMACIJA) like upper('%загрузка%') 
                or upper(S.RESHENIE) like upper('%загрузка%')) 
                and S.OBJECT_ID in ('IM6229692')
                                
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
    } 
    
    //echo _json_encode($data);
    //print_r( str_replace("\n", "", $data[0][2]) );
    print_r($data);
    
    //echo "-------------------------------------------------------------------------------------------------------------------";
    
    //echo _json_encode($data[0][2]);
    
    


















?>
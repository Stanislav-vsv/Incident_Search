<?php require 'connection.php'?>
<?php header('Content-Type: text/plain; charset=windows-1251'); //������ ��� �� ��������� ������ ������ ������ �������� � utf-8
// ��� ���� ��� ���� ������ ���� � ��������� Ansi/win-1251 �.�. ���� ����� � utf-8 �� ������������� � ������ ��������� 
// ���������� ��� ��������� ����� � ������� header() ������ ������, �.�. ����� ��� �� ������ ���� �������� ������ 

// alternative json_encode
function _json_encode($val) // ���������� ������� ���������� ������� json_encode, ������� ��������� � php ������ � ������ 5.2
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


if($_GET['input_search']){  // ������ ���� ���������� �� ���������� ����� �������� � ��������� �� ������ ������ 
    
    $input_search = $_GET['input_search']; 
    $select_service = $_GET['select_service']; 
    
    $input_search = iconv('UTF-8','windows-1251',$input_search);  //�.�. ��� ������ �� ������ ��������� GET/POST, ���������� � UTF-8, ��������� �� � windows-1251
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
                where S.STATUS = '������' 
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

if($_GET['id_inc']){  // ������ ������ ��������� �� ��� ������ 
    
    $id_inc = $_GET['id_inc']; 
    $id_inc = iconv('UTF-8','windows-1251',$id_inc);  //�.�. ��� ������ �� ������ ��������� GET/POST, ���������� � UTF-8, ��������� �� � windows-1251
    
        
    global $open_connection;  
    $query = "
                select 
                OBJECT_ID,
                INFORMACIJA,
                PODRAZDELENIE_NAIMENOVANIE,
                RESHENIE
                from INCIDENT_BASE_ALL 
                where OBJECT_ID = '$id_inc'
                AND STATUS in ('������')  
                
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


if($_GET['select_load']){  // ������ ������ ��� ������� 
    
    $id_inc = $_GET['id_inc']; 
    $id_inc = iconv('UTF-8','windows-1251',$id_inc);  //�.�. ��� ������ �� ������ ��������� GET/POST, ���������� � UTF-8, ��������� �� � windows-1251
    
        
    global $open_connection;  
    $query = "
                select distinct SERVICE_IMJA from INCIDENT_BASE_ALL where STATUS = '������'  
                
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
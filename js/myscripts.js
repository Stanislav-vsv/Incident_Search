

function select_load(){
    
        $.ajax(
            {
                url: 'server.php',
                type: 'get',
                data: {'select_load': 1},
                success: function(server_data){
                    
                    var data = JSON.parse(server_data);
                    
                    var html_select = "<option hidden>DWH 2.0</option>"; // ���������� � ������� �������� ���� html �������                   
                                        
                    for (i = 0; i < data.length; i++){  
                            
                         html_select += "<option value='"+ data[i][0] +"'>"+ data[i][0] +"</option>";                          
                    }
                    
                    $('#select_service').html(html_select);                   
                }
            }
         );
         
        //console.log(select_value);
}


function getIncInfo(elem){ // ������� ������� ��������� ���������� �� ��������� ��� ����� �� ������ ������_��������� �� �������-������ ��������� ����������
         event.preventDefault();
         //console.log(elem); 
         $("#incident_result_search").addClass('no_visible');
         var id_inc = elem.id;         
         //$("#incident_result_search").append(id_inc);
         
         $.ajax(
            {
                url: 'server.php',
                type: 'get',
                data: {'id_inc': id_inc},
                success: function(server_data){
                    
                    //var data = JSON.parse(server_data.replace(/\n/g, "\\n"));
                    
                    var data = JSON.parse(server_data);
                    
                    /*
                    var html = '<table id="table">'; // ���������� � ������� �������� ���� html ��� ������� ���������� ����������
                    
                    for (i = 0; i < data.length; i++){
                          
                        html += "<tr>";                    
                        
                        for (j = 0; j < data[i].length; j++){
                            
                                html += "<td>" + data[i][j] + "</td>";   //.replace(/\n/g, "<br>")         
                        }                        
                        html += "</tr>";
                    }
                    
                    html += "</table>"; */                   
                    
                    var html_inc_info = "<h3> �������� �: <span style='color:red;'>" + data[0] + "</span></h3>"; 
                    html_inc_info += "<p><span style='color:red;'> ����������: </span><br>" + data[1] + "</p>";
                    html_inc_info += "<p><span style='color:red;'> �������������: </span><br>" + data[2] + "</p>";
                    html_inc_info += "<p><span style='color:red;'> �������: </span><br>" + data[3] + "</p>";
                    html_inc_info += "<button id='button_Ok'> Ok </button>";
                   
                    $("#incident_info").html(html_inc_info);
                    $("#incident_info").addClass('visible').hide().fadeIn(200); 
                    
                    $('#button_Ok').click(function(event){ // ������ ��� ������ ����������� ��� ����� �� ��� �������� 
                    $("#incident_info").removeClass('visible');   
                    $("#incident_result_search").removeClass('no_visible').hide().fadeIn(200);                 // ����� ���� ��� ��������� html ��� ���� ������. ���� ������ �����
                    });                         // ����������/�������� �� ��������� html �� �� ����� ����� ����� ���� � �������� �� �����
                }
            }
         );    
}     

function closeTable(elem){
    $("#result").removeClass('visible'); 
}


$(document).ready(function(){ 
   
    $('#form_search').submit(function(event){ 
        
        $("#incident_info").html('');
        
        event.preventDefault();        
        
        var form_param = $(this).serializeArray(); // ������ ��������, ������ �� ������� ��������� �������� �����.
                                                   // � ������� ������� ��� �������� name - �������� ��������� � value - ��������
                
        var arr_form_param = [];  // ������ ������ ��� ������ ���� ���������� �����      
        for (var index in form_param){ // ��������� �� ������� � ���������        
            for (var field in form_param[index]){ // ��������� �� ����� �������                    
                    if(field == 'name'){                         
                        arr_form_param[form_param[index][field]] = 0  //������� ������� ������� � ������ ������ �������� ���� 'name' ������                       
                    }
                    else if(field == 'value'){ arr_form_param[form_param[index]['name']] = form_param[index][field] } // ����������� �������� ������� � ������ 'name' �������� ���� 'value'
                }
        }        
        
        //console.log(form_param); 
        //console.log(arr_form_param); 
        
        for(i = 0; i <= arr_form_param.length; i++ ){
            
            //console.log(arr_form_param['input_search']);            
            
            if(arr_form_param['input_search'] == ""){
                $('#message').text('����� ������ ��������� ������');            
            }
            /*else if(!arr_form_param['select_service']){
                $('#message').text('�� ������� �������');            
            }
            else if(arr_form_param['select_service'] == '�������� �������'){
                $('#message').text('�� ������� �������');            
            }*/
            else{                             
                 $.ajax(
                    {
                        url: 'server.php',
                        type: 'get',
                        data: {  
                                'input_search': arr_form_param['input_search'],
                                'select_service': arr_form_param['select_service']
                              },
                        success: function(server_data){
                            
                            var data = JSON.parse(server_data); 
                            
                            if(!data){
                                $('#message').text('�� ������ ������� ������ �� �������');
                                $("#incident_result_search").html('');
                            }
                            else{
                                $('#message').html('');
                                $("#result").addClass('visible').hide().fadeIn(200); 
                                
                                var html = "<table id='table'>"; // ���������� � ������� �������� ���� html ��� ������� ���������� ����������
                                html += "<tr><th>��������</th><th>��������</th><th>�����������</th><th>�������<button id='button_close_table' onClick='closeTable(this)'>x</button></th></tr>";
                                for (i = 0; i < data.length; i++){
                                      
                                    html += "<tr>";                    
                                    
                                    for (j = 0; j < data[i].length; j++){
                                        
                                        if(j == 0){ //��������� ����� ��������� � ����� ������ � ������� 
                                              html += "<td><a href='#' id='"+ data[i][j] +"' onClick='getIncInfo(this)' >" + data[i][j] + "</a></td>";  
                                        }
                                        else{
                                            html += "<td>" + data[i][j] + "</td>";
                                        }                                                     
                                    }                        
                                    html += "</tr>";
                                }
                                
                                html += "</table>"; 
                                
                                $("#incident_result_search").html(html); 
                            }
                        } 
                    }                      
                );
            }            
        } 
        
        /*
        for (var i in form_param){
            for (var n in form_param[i]){
                    console.log( n + " - " + form_param[i][n]);
                }                        
        }   */ 
        
    }); // end #my_form
    
    
    /*
    $('#button_Ok').click(function(event){        
        
        //$("#result").removeClass('visible');
        $("#incident_info").html('');
        
    }); // end #my_button  */
    
      
}); //����� ready              


function setToast( message, background )
{
    document.querySelector('.toast').classList.remove('bg-success');
    document.querySelector('.toast').classList.remove('bg-warning');
    document.querySelector('.toast').classList.remove('bg-danger');
    document.querySelector('.toast').classList.remove('bg-info');
    document.querySelector('.toast').classList.add(background);
    document.querySelector('.toast-body').innerHTML = message;

    var toastElList = [].slice.call(document.querySelectorAll('.toast'))
    var toastList = toastElList.map(function(toastEl) {
                        return new bootstrap.Toast(toastEl)
                    });
    toastList.forEach(toast => toast.show()); // This show them
}


function createHTMLMenu(data) 
{
    let main_menu = document.getElementById('sidebarMenuList');
    
    if (main_menu && data.modules)
    {
            for (let i in data.modules) {
            let module = data.modules[i];
            let a = document.createElement('a');
            a.className = "nav-link"+((i==0)?(' active'):(''));
            a.href = '#';
            a.areaCurrent = module;
            a.addEventListener('click', () => { GetList(module, list_options); document.getElementById('search_on_module').value=''; });
            a.innerHTML = '' + module;

            let li = document.createElement('li');
            li.className = 'nav-item';
            li.appendChild(a);
            main_menu.insertBefore(li, main_menu.children[i]); // before predefined voices
            // main_menu.appendChild(li);
        }
        
        if (data.tables)
        {
            let li_label = document.createElement('li');
            let label = document.createElement('hr');
            li_label.appendChild(label);
            main_menu.appendChild(li_label);

            for (let i in data.tables) {
                let table = data.tables[i];
                let a = document.createElement('a');
                a.className = "nav-link"+((i==0)?(''):(''));
                a.href = '#';
                a.areaCurrent = table;
                a.addEventListener('click', () => { GetList(table, list_options); document.getElementById('search_on_module').value=''; });
                a.innerHTML = table;
    
                let li = document.createElement('li');
                li.className = 'nav-item';
                li.appendChild(a);
                main_menu.appendChild(li);
            }
        }

    }    
}


async function createMenuTables() 
{
    const response = await requestData('OPTIONS', '/all/', null).then(res => 
                                        {
                                        if (res.status == 200)
                                            res.json().then(data => { createHTMLMenu(data); });
                                        });
}



// All method request
async function requestData(method, url = '', data = null) 
{
    let headers = new Headers({
        // 'Authorization': 'Basic '+btoa('username:password'), 
        'Authorization': 'Bearer ' + localStorage.token,
        'Accept': 'application/json',
        //            'Host': 'dressapi',
    });

    var input = document.querySelector('input[type="file"]');

/*
    var data_form = new FormData(data);
    for(var x in data)
    if (data_form[x].type=='file')
    {
        data_form[x].value = data[x].files[0];                
    }
*/
    let params = {
        method: method, // *GET, POST, PUT, DELETE, etc.
        mode: 'cors', // no-cors, *cors, same-origin
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin', // include, *same-origin, omit
        redirect: 'follow', // manual, *follow, error
        referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        body: data // body data type must match "Content-Type" header
    };

    if (['POST', 'PATCH', 'PUT'].includes(method)) {
        headers.append('Content-Type', 'application/x-www-form-urlencoded');
        
        
//        for(var x in data)
//        if (data[x].type=='file')
//            data[x].value = data[x].files[0].name;                
            
        params.body = new URLSearchParams([...(new FormData(data))]) // body data type must match "Content-Type" header

    }
    params.headers = headers;
alert('URL: '+url);
    return await fetch(url, params);
}


function createTable(full_data, options) 
{
    let html = '<div class="table-responsive"><table class="table table-striped table-sm">';
    let head = false;
    // console.log(full_data.elements);
    if (typeof (full_data.elements) != 'undefined') 
    {
        let data = full_data.elements;
        for (let i in data) 
        {
            if (!head && typeof (data[i]) != 'string') 
            {
                html += '<tr>';
                if (typeof (full_data.metadata) != 'undefined')
                    html += '<th>Operations</th>';
                for (let col_name in data[i])
                {
                    if (col_name[0]=='_')
                        col_name = col_name.substring(1);
                    html += '<th>' + col_name.replaceAll('_',' ').replace(/(^\w|\s\w)/g, m => m.toUpperCase()) + '</th>';
                }

                html += '</tr>';
                head = true;
            }
            html += '<tr>';
            if (typeof (full_data.metadata) != 'undefined')
            {
                localStorage.current_module = full_data.metadata.module;
                html += '<td><input type="button" class="btn btn-secondary m-1" value="Details" onclick="ViewRow(\'' + full_data.metadata.module + '\', \'' + full_data.metadata.key + '\', ' + data[i]['id'] + ')"></td>';
                // html += '<input type="button" class="btn btn-warning m-1" value="Upd" onclick="UpdateRowForm(\'' + full_data.metadata.module + '\', ' + data[i]['id'] + ')">';
                // html += '<input type="button" class="btn btn-danger m-1" value="Del" onclick="DeleteRow(\'' + full_data.metadata.module + '\', ' + data[i]['id'] + ')"></td>';
            }
            for (let col_name in data[i])
                html += '<td>' + ((data[i][col_name]==null)?('ALL'):(data[i][col_name])) + '</td>';

            html += '</tr>';
        }
    }

    html += '</table></div>';

//    console.log(full_data.metadata);

    if (typeof (full_data.metadata) != 'undefined') 
    {
        if (typeof(full_data.permissions)!='undefined' && typeof(full_data.permissions.can_insert)!='undefined')
            document.getElementById('insertButton').innerHTML = '<input type="button" class="btn btn-success float-end" value="Add New" onclick="InsertRowForm(\''+full_data.metadata.module+'\')">';
        
        document.getElementById('moduleName').innerHTML = full_data.metadata.module;
        html = 'Page ' + full_data.metadata.page + '/' + full_data.metadata.total_pages + ' - ' + full_data.metadata.total_items + ' totale elements<br>' + html;
        

        // List of pages
        html += '<div class="btn-page-selector" role="toolbar" aria-label="Page Selector">' +
                '  <div class="btn-group me-2" role="group" aria-label="First group">';
        
        let start_page = Math.max(1,full_data.metadata.total_pages-10);
        for(let p=start_page; p<=full_data.metadata.total_pages && p<=start_page+20; p++)
            if (p==full_data.metadata.page)
                html += '    <button type="button" class="btn btn-primary"><strong>'+p+'</strong></button>';
            else
                html += '    <button type="button" class="btn btn-secondary" onclick="GetList(\''+full_data.metadata.module+'\', \''+options+'/p/'+p+'\')">'+p+'</button>';
        
                html += '  </div>' +
                '</div>';

    
    }

    document.querySelector('.results').innerHTML = html;
    
    return true;
}


function createForm(full_data, item) 
{
    // console.log(item);
    document.getElementById('insertButton').innerHTML = '';

    let html = '<form id="editForm"><div style="background:#eee" class="card mb-3"><div class="card-body"><div class="row">';
//    for(let i in full_data.structure)
//        html += full_data.structure[i]['field'] + '=' + full_data.structure[i]['html_type']+'<br>';
    
    // list of filed to popolate
    let popolateLists = [];

    for(let i in full_data.structure)
    {     
        let size = parseInt(full_data.structure[i]['max']);
        let field = full_data.structure[i]['field'];
        let display_name = full_data.structure[i]['display_name'];
        let value = ((item)?(item.elements[0][field]):(''));

        if (display_name)
        {
            if (display_name[0]=='_')
            display_name = display_name.substring(1);
            display_name = display_name.replaceAll('_',' ').replace(/(^\w|\s\w)/g, m => m.toUpperCase());
        }
        else
            continue;

        if (value=='' && typeof(full_data.structure[i]['default'])!='undefined')
            value = full_data.structure[i]['default'];
// console.log(field); 
        switch(full_data.structure[i]['html_type'])
        {
            case 'hidden':
                html += '<input value="'+value+'" name="'+field+'" type="'+full_data.structure[i]['html_type']+'" size="'+size+'" class="form-control" id="input_'+field+'" required>';
                break;

            case 'text':
            case 'number':
            case 'datetime': 
            case 'datetime-local': 
            case 'date': 
            case 'time': 
            case 'time-local': 
            case 'color':
            case 'email':
            case 'image':
            case 'password':
            case 'url':
            case 'tel':
                    html += '<div class="mb-3 row">' +
                                '<label class="col-sm-2 form-label fw-bold fs-6" for="input_'+field+'">'+display_name+'</label>' +
                                '<div class="col-sm-10"><input name="'+field+'" value="'+value+'" type="'+full_data.structure[i]['html_type']+'" size="'+size+'" class="form-control" id="input_'+field+'" required></div>' +
                                '</div>'+"\r\n";
                    break;
   
            case 'file':
                    html += '<div class="mb-3 row">' +
                                '<label class="col-sm-2 form-label fw-bold fs-6" for="input_'+field+'">'+display_name+'</label>';
                            
                    html += '<div class="col-sm-6"><input name="'+field+'" value="'+value+'" type="'+full_data.structure[i]['html_type']+'" class="form-control" id="input_'+field+'" required></div>';
                    if (value!='')
                    {
                        if (value.includes('.png') || value.includes('.webp') ||
                            value.includes('.jpg') || value.includes('.jpeg') || 
                            value.includes('.gif') 
                           )
                            html += '<div class="col-sm-4"><img class="img-fluid" src="../public-files/' + value+'"></div>'+"\r\n";
                        else
                            html += '<div class="col-sm-4"><a target="_blank" href="../public-files/'+value+'/">'+value+'</a></div>'+"\r\n";
                    }    
                    html += '</div>'+"\r\n";
                    break;
   
                case 'textarea':
                    html += '<div class="mb-3 row">' +
                         '<label for="input_'+field+'" class="col-sm-2 form-label fw-bold fs-6">'+display_name+'</label>' +
                         '<div class="col-sm-10"><textarea name="'+field+'" class="form-control" id="input_'+field+'" required>'+value+'</textarea></div>' +
                         // '<div class="invalid-feedback">' + field + '</div>' +
                         '</div>'+"\r\n";                        
                    break;

            case 'checkbox': 
                    html += '<div class="mb-3 row">' +
                        '<label for="input_'+field+'" class="col-sm-2 col-form-label fw-bold fs-6">'+display_name+'</label>' +
                        '<div class="col-sm-10"><input name="'+field+'" value="'+value+'" type="checkbox" class="form-control" id="input_'+field+'"></div>' +
                        // '<div class="invalid-feedback">Example invalid feedback text</div>' +
                        '</div>'+"\r\n";
                        break;

            case 'select': 
                    html += '<div class="mb-3 row">' +
                        '<label for="input_'+field+'" class="col-sm-2 col-form-label fw-bold fs-6">'+display_name+'</label>' +
                        '<div class="col-sm-10"><select name="'+field+'" class="form-control" id="input_'+field+'"></select></div>' +
                        // '<div class="invalid-feedback">Example invalid feedback text</div>' +
                        '</div>'+"\r\n";
                        
                        [rel_module,rel_sitems] = full_data.structure[i]['ref'].split(':');
                        [rel_id_name,rel_items] = rel_sitems.split('-');
                        popolateLists.push(['select','input_'+field, rel_module, '/page/1,500', rel_id_name, rel_items, value, (full_data.structure[i]['null']=='YES')]);
                        break;

            case 'datalist': 
                    html += '<div class="mb-3 row">' +
                        '<label for="input_'+field+'" class="col-sm-2 col-form-label fw-bold fs-6">'+display_name+'</label>' +
                        '<div class="col-sm-10"><datalist name="'+field+'" class="form-control" id="input_'+field+'"></datalist></div>' +
                        // '<div class="invalid-feedback">Example invalid feedback text</div>' +
                        '</div>'+"\r\n";
                        
                        [rel_module,rel_sitems] = full_data.structure[i]['ref'].split(':');
                        [rel_id_name,rel_items] = rel_sitems.split('-');
                        popolateLists.push(['datalist','input_'+field, rel_module, '/page/1,500', rel_id_name, rel_items, value, full_data.structure[i]['null']=='YES']);
                        break;

            case 'checkbox-list-ex': 
                    html += '<div class="mb-3 row">' +
                        '<label for="input_'+field+'" class="col-sm-2 col-form-label fw-bold fs-6">'+display_name+'</label>' +
                        '<div class="col-sm-10" id="input_'+field+'">';
                        
                    let vcbl = full_data.structure[i]['options'].split('|');
                    for( var x in vcbl)
                         html += '<input type="checkbox" name="'+field+'[]" value="'+vcbl[x]+'" class="form-check-input"'+((value==vcbl[x])?(' checked'):(''))+'>&nbsp;' + vcbl[x] + '<br>'; 
                        
                    html += '</div></div>' +"\r\n";
                        break;

            case 'radio-list': 
                    html += '<div class="mb-3 row">' +
                        '<label for="input_'+field+'" class="col-sm-2 col-form-label fw-bold fs-6">'+display_name+'</label>' +
                        '<div class="col-sm-10" id="input_'+field+'">';
                        
                    let vrl = full_data.structure[i]['options'].split('|');
                    for( var x in vrl)
                         html += '<input type="radio" name="'+field+'" value="'+vrl[x]+'" class="form-check-input"'+((value==vrl[x])?(' checked'):(''))+'>&nbsp;' + vrl[x] + '&nbsp;&nbsp;'; 
                        
                    html += '</div></div>' +"\r\n";
                        break;

            default: 
                    html += '<div class="mb-3 row"><h2>' + display_name + '</h2> ' + full_data.structure[i]['html_type'] + '</div>'+"\r\n";
                        break;

        }
    }
    html += '</div></div></div>';

    html += '<div class="row position-relative">';
    if (item)
        html += '  <input value="Save" type="button" class="btn btn-warning col-sm-3 col-lg-2 m-3 top-50 start-0" onclick="UpdateRow(\''+full_data.metadata.module+'\', document.getElementById(\'editForm\'), '+item.elements[0][full_data.metadata.key]+' )">';
    else
        html += '  <input value="Insert" type="button" class="btn btn-warning col-sm-3 col-lg-2 m-3 top-50 start-0" onclick="InsertRow(\''+full_data.metadata.module+'\', document.getElementById(\'editForm\') )">';
    html += '  <input value="Go to List" type="button" class="btn btn-secondary col-sm-3 col-lg-2 m-3 top-50 start-0" onclick="GetList(\''+full_data.metadata.module+'\', \'wr/ob/'+full_data.metadata.key+'-DESC\')">';
    if (item)
        html += '  <input value="Delete" type="button" class="btn btn-danger col-sm-3 col-lg-2 m-3 top-50 end-0" onclick="DeleteRow(\''+full_data.metadata.module+'\','+item.elements[0][full_data.metadata.key]+')">';
    html += '<br></div>';

    html += '</form>';

    document.querySelector('.results').innerHTML = html;

    for( let i in popolateLists)
    {
        let row = popolateLists[i];

        switch(row[0])
        {
            case 'datalist':
            case 'select':
                           // id_obj, rel_module,  options, rel_id_name, items to display
                popolateSelect(row[0],row[1], row[2], row[3], row[4], row[5], row[6], row[7]);
                break;

            case 'checkbox-list-ex':
                popolateList('checkbox', row[1], row[2], row[3], row[4], row[5], row[6]);
                break;
            
            case 'radio-list-ex':
                popolateList('radio', row[1], row[2], row[3], row[4], row[5], row[6]);
                break;
        }
    }

    return html;
}

//
// Get a list of module (Method GET). 
// The page 2 is options '/p/2' or 'p/2,10' (page 2 with 10 elements per page)
//
function popolateSelect(type, id_obj, rel_module, options, rel_id_name, rel_display_fields, value, with_null)
{
    let displayed_fields_separator = ' ';
    if (!options)
        options = '';
    else
        options = '/' + options;

    // document.querySelector('h3').innerHTML=module;
    requestData('GET','/' + rel_module + '/' + options)
        .then(res => {
            if (res.status == 200)
                res.json().then(data => 
                    { 
                        // console.log(data);
                        let frame_html = ''; 
                        
                        if (with_null) 
                            frame_html += '<option value="NULL">ALL</option>';
                        for(var x in data.elements)
                        {
                            let field_value = '';
                            if (rel_display_fields.includes(','))
                            {
                                let v = rel_display_fields.split(',');
                                for(let y in v)
                                    if (typeof(data.elements[x][v[y]])!='undefined')
                                        field_value += ((field_value=='')?(''):(displayed_fields_separator)) + data.elements[x][v[y]];
                            }
                            else
                                field_value = data.elements[x][rel_display_fields];

                            frame_html += '<option value="'+data.elements[x][rel_id_name]+'"'+((value==data.elements[x][rel_id_name])?(' selected'):(''))+'>'+field_value+'</option>';
                        }
                        document.getElementById(id_obj).innerHTML = frame_html;
                    });
        });
}


//
// Get a list of module (Method GET). 
// The page 2 is options '/p/2' or 'p/2,10' (page 2 with 10 elements per page)
//
function popolateList(type, id_obj, rel_module, options, rel_id_name, rel_display_fields)
{
    let displayed_fields_separator = ' ';
    if (!options)
        options = '';
    else
        options = '/' + options;

    // document.querySelector('h3').innerHTML=module;
    requestData('GET','/' + rel_module + '/' + options)
        .then(res => {
            if (res.status == 200)
                res.json().then(data => 
                    { 
                        // console.log(data);
                        let frame_html = ''; 
                        for(var x in data.elements)
                        {
                            let field_value = '';
                            if (rel_display_fields.includes(','))
                            {
                                let v = rel_display_fields.split(',');
                                for(let y in v)
                                    if (typeof(data.elements[x][v[y]])!='undefined')
                                        field_value += ((field_value=='')?(''):(displayed_fields_separator)) + data.elements[x][v[y]];
                            }
                            else
                                field_value = data.elements[x][rel_display_fields];

                            frame_html += '<input value="'+data.elements[x][rel_id_name]+'" type="'+type+'" class="form-radio-input" id="input_'+field_value.replaceAll(' ','_')+'">';
                            frame_html += ' <label for="input_'+field_value.replaceAll(' ','_')+'" class="col-sm-2 col-form-label fw-bold fs-6">'+field_value+'</label><br>';
                        }
                        document.getElementById(id_obj).innerHTML = frame_html;
                    });
        });
}


//
// Get a list of module (Method GET). 
// The page 2 is options '/p/2' or 'p/2,10' (page 2 with 10 elements per page)
//
function GetList(module, options)
{
    if (!options)
        options = '';
    else
        options = '/' + options;

    // document.querySelector('h3').innerHTML=module;
    requestData('GET','/' + module + options)
        .then(res => {
            if (res.status == 200)
                res.json().then(data => { createTable(data); });
        });
}


//
// Update Form
//
function InsertRowForm(module)
{
    //
    // Get Structure of module (Method OPTIONS)
    //
    requestData('OPTIONS','/'+module)
    .then(res2 => {
        if (res2.status==200)
            res2.json().then(data => { createForm(data);} );
        else
            setToast('Operation failed with status '+res2.status,'bg-danger'); 
        });

}



//
// Insert (Method POST)
//<
function InsertRow(module, formData)
{
    // let formData = new FormData();
    // formData.append('id', 1000);
    // formData.append(module, 'Lumen is better than DressApi!');
    // formData.append('id_page', 1);
    requestData('POST', '/'+module+'/',formData)
            .then(data => {
                if (data.status==201)
                    data.json().then(res => { 
                                                setToast('Item entered successfully','bg-success');
                                                GetList(module);
                                             } );
                else
                {
                    setToast('Operation failed with status '+data.status+"\n"+data.message,'bg-danger'); 
                    data.json().then(res => { if (typeof(res.message)!='undefined') setToast(res.message,'bg-danger'); } );
                }
            });
}


//
// View Single Row
//
function ViewRow(module, key, id)
{
    requestData('GET','/'+module+'/'+id+'/wr')
            .then(res => {
                if (res.status==200)
                {
                    res.json().then(data => { ViewDetails(data,key,id);} );
                }
            });

}


//
// View Details
//
function ViewDetails(data, key, id)
{
    let html = '<div class="table-responsive m-1"><table class="table table-striped ">';
    for(let i in data.elements[0])
    {
        html += '<tr><th class="bg-info">'+i.replaceAll('_',' ').replace(/(^\w|\s\w)/g, m => m.toUpperCase())+'</th>'+
                '<td>'+data.elements[0][i]+'<td></tr>';
    }
    html += '</table></div>';

    html += '<div class="row">';
    if (data.permissions.can_update)
        html += '  <input value="Modify" type="button" class="btn btn-warning col-sm-3 col-lg-2 m-3 top-50 start-0" onclick="UpdateRowForm(\''+data.metadata.module+'\','+id+' )">';
    html += '  <input value="Go to List" type="button" class="btn btn-secondary col-sm-3 col-lg-2 m-3 top-50 start-0" onclick="GetList(\''+data.metadata.module+'\', \'wr/ob/'+key+'-DESC\')">';
    if (data.permissions.can_update)
        html += '  <input value="Delete" type="button" class="btn btn-danger col-sm-3 col-lg-2 m-3 top-50 end-0" onclick="DeleteRow(\''+data.metadata.module+'\','+id+')">';
    html += '<br></div>';
    
    document.querySelector('.results').innerHTML = html;

    document.getElementById('insertButton').innerHTML = '';    
}


//
// Update Form
//
function UpdateRowForm(module, id)
{
    var record = null;
    // console.log(id);
    requestData('GET','/'+module+'/'+id)
            .then(res => {
                if (res.status==200)
                {
                    res.json().then(data => { record = data; } );
                    //
                    // Get Structure of module (Method OPTIONS)
                    //
                    requestData('OPTIONS','/'+module)
                    .then(res2 => {
                        if (res2.status==200)
                            res2.json().then(data => { createForm(data, record);} );
                        else
                            setToast('Operation failed with status '+res2.status,'bg-danger'); 
                     });
                }
            });

}


//
// Update (Method PUT/PATCH)
//
function UpdateRow(module, formData, id)
{
    // let formData = new FormData();
    // formData.append('id', 1000);
    // formData.append(module, 'Yii is better than DressApi!');
    // formData.append('id_page', 1);
    
    requestData('PATCH','/'+module+'/'+id, formData)
        .then(data => {
                        let msg = 'Operation '+((data.status==200)?'successful':'failed'); // +' with status '+data.status;
                        let jsonprom = data.json();
                        jsonprom.then(dta => 
                            {
                                if (dta.message && dta.message.length) 
                                    msg = dta.message;
                                setToast(msg,(data.status==200)?'bg-success':'bg-danger'); 
                            } );
                        if (!jsonprom)
                         setToast(msg,'bg-danger'); 
                    }
            );
}


//
// Delete (Method DELETE)
//
function DeleteRow(module, id)
{
    // let formData = new FormData();
    // formData.append('id', 1000);
    
    if (confirm('Are you sure to delete this element?'))
    {
        requestData('DELETE','/'+module+'/'+id)
            .then(data => {
                if (data.status==200)
                {
                    data.json().then(res => {  GetList(module,list_options); setToast(res.message,'bg-success'); } );
                }
                else
                    setToast('Operation failed with status '+data.status,'bg-danger'); 
            });
    }
}



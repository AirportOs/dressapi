function setToast( message, background )
{
    notificationShow('MESSAGE: '+message);
/*
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
*/
}

function createHTMLMenu(data) 
{
    let main_menu = document.getElementById('mainMenu');
    if (main_menu && data.modules)
        for (let i in data.modules) 
        {
            let module = data.modules[i];

            let a = document.createElement('a');
            a.className = "nav-link"+((i==0)?(' active'):(''));
            a.href = ''+module;
            a.areaCurrent = module;
            a.addEventListener('click', (e) => { GetList(module, list_options); document.getElementById('search_on_table').value=''; e.stopPropagation(); });
            a.innerHTML = module;

            let li = document.createElement('li');
            li.className = 'nav-item';
            li.appendChild(a);
            main_menu.insertBefore(li, main_menu.children[i]); // before predefined voices
            // main_menu.appendChild(li);
        }

        let main_menu_table_list = document.getElementById('mainMenuTableList');
        if (main_menu_table_list)
        {
            if (!data.tables)
                document.getElementById('mainMenuTable').style.display = 'none';
            else
                for (let i in data.tables) 
                {
                    let table = data.tables[i];
                    let a = document.createElement('a');
                    a.className = "list-item"+((i==0)?(' active'):(''));
                    a.href = '#table-'+table;
                    a.areaCurrent = table;
                    a.addEventListener('click', (e) => { GetList(table, list_options); document.getElementById('search_on_table').value=''; e.stopPropagation(); });
                    a.innerHTML = table;
        
                    let li = document.createElement('li');
                    li.className = 'nav-item';
                    li.appendChild(a);
                    main_menu_table_list.appendChild(li);
                }
        }
    
    // <li><a class="list-item" href="#"><span>Link list 1</span></a></li>
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

    let params = {
        method: method, // *GET, POST, PUT, DELETE, etc.
        mode: 'cors', // no-cors, *cors, same-origin
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin', // include, *same-origin, omit
        redirect: 'follow', // manual, *follow, error
        referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
    };
    
    let with_file = false;

    document.body.style.cursor = 'wait';

    if (data!=null)
    {
        let fd = new FormData(); // evt.currentTarget


        if (data && ['POST', 'PATCH', 'PUT'].includes(method))
        {
            for(var i in data)
            {
                try
                {
                    parseInt(i);
                    let item = data[i].name;
                    if (item==null || item=='')
                        break;
                    if (typeof(data[i].type)=='string' && data[i].type=='file') 
                    {
                        let file = data[i].files[0];
                
                        // fd.set(item, file);
                        fd.append(item, file);
//                        console.log(data[i].name + ' ' + data[i].files[0]);

                        // headers.append('Content-Type', 'multipart/form-data');
                        
                        with_file = true;
                    }
                    else
                        fd.append(item, data[i].value);

                }
                catch(e)
                {
                    break;
                }
            }
            params.body = fd; //  new URLSearchParams([...(new FormData(data))]); // body data type must match "Content-Type" header
        }

        let changedata = ['POST', 'PATCH', 'PUT'].includes(method);
        if (!with_file && data && changedata)
        {
            headers.append('Content-Type', 'application/x-www-form-urlencoded');
            params.body = new URLSearchParams([...(new FormData(data))]) // body data type must match "Content-Type" header
        }
    }    
    params.headers = headers;

    let ret = await fetch(url, params);
    
    document.body.style.cursor = 'pointer';
    return ret;
}

function createList(full_data) 
{
    const regex = /^\{\{start\selement\}\}(.*?){\{end\selement\}\}$/ms;
    const regex_items = /\{\{(.*?)\}\}/smg;
    let res = full_data.template.match(regex);
    let all_items = true;

    let element_template = '';

    if (res && res.length>1)
        element_template = res[1];
    else
        all_items = false;

//    console.log(content);
//    console.log(element_template);

    let html_elements = '';

    let head = false;
    // console.log(full_data.elements);
    if (typeof (full_data.elements) != 'undefined') 
    {
        let data = full_data.elements;
        for (let i in data) 
        {
            if (!head && typeof (data[i]) != 'string') 
            {
                let html_head = '';
                let m;
                
                // HEADER
                while ((m = regex_items.exec(element_template)) !== null)
                    if (!m[1].match('event-'))
                    {
                        if (m[1]!='*')
                        {
                            all_items = false;
                            html_head += '<th>'+m[1].replaceAll('_',' ').toUpperCase()+'</th>';
                        }
                        else  // ALL ITEMS
                        {
                            for (let col_name in data[i])
                                html_head += '<th>'+col_name.replaceAll('_',' ').toUpperCase()+'</th>';
                        }
                    }
                html_elements += '<tr>'+html_head+'</tr>';
                head = true;
            }

            // BODY
            let row = '';
            if (all_items)
            {
                row += '<tr>';
                for(let col_name in data[i])
                    row += '<td>'+data[i][col_name]+'</td>';
                row += '</tr>';
            }
            else
            {
                row = element_template;
                for(let col_name in data[i])
                {
                    row = row.replace('{{'+col_name+'}}',data[i][col_name]);
//    console.log(data[i][col_name]);
//    console.log('ROW: '+ row);
                }  
            }
            if (typeof (html_elements) != 'undefined')
                row = row.replaceAll('{{event-details}}','onclick="ViewRow(\'' + full_data.metadata.module + '\', \'' + full_data.metadata.key + '\', ' + data[i]['id'] + ')"');
            html_elements += row;

//            if (typeof (full_data.metadata) != 'undefined')
//            {
//                localStorage.current_module = full_data.metadata.module;
//                html += '<td><input type="button" class="btn btn-secondary m-1" value="Details" onclick="ViewRow(\'' + full_data.metadata.module + '\', \'' + full_data.metadata.key + '\', ' + data[i]['id'] + ')"></td>';
//                // html += '<input type="button" class="btn btn-warning m-1" value="Upd" onclick="UpdateRowForm(\'' + full_data.metadata.module + '\', ' + data[i]['id'] + ')">';
//                // html += '<input type="button" class="btn btn-danger m-1" value="Del" onclick="DeleteRow(\'' + full_data.metadata.module + '\', ' + data[i]['id'] + ')"></td>';
//            }
//            for (let col_name in data[i])
//                html += '<td>' + ((data[i][col_name]==null)?('ALL'):(data[i][col_name])) + '</td>';
//
        } // end for each element
    }

    let html = full_data.template.replace(res[0], html_elements).replaceAll('{{title}}', full_data.metadata.module);

    if (false && typeof (full_data.metadata) != 'undefined') 
    {
        if (typeof(full_data.permissions)!='undefined' && typeof(full_data.permissions.can_insert)!='undefined')
            document.getElementById('insertButton').innerHTML = '<input type="button" class="btn btn-success float-right" value="Add New" onclick="InsertRowForm(\''+full_data.metadata.module+'\')">';
        
        if (document.getElementById('moduleName')) 
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
                    html += '<th>' + col_name.replace('_',' ').toUpperCase() + '</th>';

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

    if (false && typeof (full_data.metadata) != 'undefined') 
    {
        if (typeof(full_data.permissions)!='undefined' && typeof(full_data.permissions.can_insert)!='undefined')
            document.getElementById('insertButton').innerHTML = '<input type="button" class="btn btn-success float-right" value="Add New" onclick="InsertRowForm(\''+full_data.metadata.module+'\')">';
        
        if (document.getElementById('moduleName')) 
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

    let html = '<form id="editForm" enctype="multipart/form-data"><div class="pt-3">';
    
    // list of filed to popolate
    let popolateLists = [];
    let with_file = false;

    for(let i in full_data.structure)
    {     
        let size = parseInt(full_data.structure[i]['max']) || 80;
        let field = full_data.structure[i]['field'];
        let display_name = full_data.structure[i]['display_name'];
        let default_value = ((full_data.structure[i]['default'])?(full_data.structure[i]['default']):(''));
        let value = ((item && item.elements.length)?(item.elements[0][field]):(default_value));
// console.log(field); 
        let html_type = full_data.structure[i]['html_type'];
        if (typeof(item)!='undefined' && typeof(full_data.structure[i]['html_type_on_modify'])=='string') // modify
            html_type = full_data.structure[i]['html_type_on_modify'];

        if (typeof(field)!='undefined')
        switch(html_type)
        {
            case 'none':
                break;

            case 'hidden':
                html += '<input value="'+value+'" name="'+field+'" type="'+html_type+'" size="'+size+'" class="form-control" id="input_'+field+'">';
                break;

            case 'text':
            case 'number':
            case 'datetime': 
            case 'date': 
            case 'time': 
            case 'color':
            case 'email':
            case 'image':
            case 'password':
            case 'url':
            case 'tel':
                    html += '<div class="form-group mb-4 pb-2">' +
                                '<input name="'+field+'" placeholder="'+display_name+'" value="'+value+'" type="'+html_type+'" size="'+size+'" class="form-control bg-light" id="input_'+field+'" required>' +
                                '<label class="active" for="input_'+field+'">'+display_name+'</label>' +
                        // '<div class="invalid-feedback">Example invalid feedback text</div>' +
                        '</div>'+"\r\n";
                    break;

            case 'file':
                    html += '<div class="form-group mb-4 pb-2">' +
                                '<input name="'+field+'" placeholder="'+display_name+'" value="'+value+'" type="file" size="'+size+'" class="form-control" id="input_'+field+'" required>' +
                                '<label class="active" for="input_'+field+'">'+display_name+'</label>' +
                        // '<div class="invalid-feedback">Example invalid feedback text</div>' +
                        '</div>'+"\r\n";
                        if (!with_file) { html += '<input type="hidden" name="MAX_FILE_SIZE" value="134217728" />'+"\r\n"; with_file = true; }

                    break;

            case 'readonly':
                    html += '<div class="form-group mb-4 pb-2">' +
                                '<input name="'+field+'" placeholder="'+display_name+'" value="'+value+'" type="text" size="'+size+'" class="form-control bg-light" id="input_'+field+'" readonly>' +
                                '<label class="active" for="input_'+field+'">'+display_name+'</label>' +
                        // '<div class="invalid-feedback">Example invalid feedback text</div>' +
                        '</div>'+"\r\n";
                    break;

            case 'textarea':
                    html += '<div class="form-group mb-4 pb-2">' +
                         '<textarea name="'+field+'" placeholder="'+display_name+'" class="form-control bg-light" id="input_'+field+'" required>'+value+'</textarea>' +
                         '<label class="active" for="input_'+field+'">'+display_name+'</label>' +
                         '</div>'+"\r\n";                        
                    break;

            case 'checkbox': 
                    html += '<div class="form-group mb-4 pb-2">' +
                        '<input name="'+field+'" value="'+value+'" type="checkbox" class="form-control" id="input_'+field+'">' +
                        '<label class="active" for="input_'+field+'" class="col-sm-2 col-form-label fw-bold fs-6">'+display_name+'</label>' +
                        '</div>'+"\r\n";
                        break;

            case 'select':
                    html += '<div class="form-group mb-4 pb-2">' +
                            '<label class="active" for="input_'+field+'">'+display_name+'</label>' +
                            '<select name="'+field+'" class="form-control form-control-md bg-light" title="Select one" id="input_'+field+'">' +
                            '</select>' +
                        '</div>';
                        
                        [rel_module,rel_sitems] = full_data.structure[i]['ref'].split(':');
                        [rel_id_name,rel_items] = rel_sitems.split('-');
                        popolateLists.push(['select','input_'+field, rel_module, '', rel_id_name, rel_items, value, (full_data.structure[i]['null']=='YES')]);
                        break;

            case 'datalist': 
                    html += '<div class="form-group mb-4 pb-2">' +
                        '<datalist name="'+field+'" placeholder="'+display_name+'" class="form-control" id="input_'+field+'"></datalist>' +
                        '<label class="active" for="input_'+field+'">'+display_name+'</label>' +
                        '</div>'+"\r\n";
                        
                        [rel_module,rel_sitems] = full_data.structure[i]['ref'].split(':');
                        [rel_id_name,rel_items] = rel_sitems.split('-');
                        popolateLists.push(['datalist','input_'+field, rel_module, '', rel_id_name, rel_items, value, full_data.structure[i]['null']=='YES']);
                        break;

            case 'checkbox-list-ex': 
                    let vcbl = full_data.structure[i]['options'].split('|');
                    html += '<div class="form-group mb-4">';
                    html += '  <label class="active">'+display_name+'</label>';
                    html += '  <div class="bg-light p-2">';
                    for( var x in vcbl)
                    {
                         html += '<div class="form-check pt-3'+((vcbl.length<5)?(' form-check-inline'):(''))+'">';
                         html += '  <input type="checkbox" name="'+field+'[]" value="'+vcbl[x]+'" id="input_'+field+'_'+x+'" class="form-check-input"'+((value==vcbl[x])?(' checked'):(''))+'>'; 
                         html += '  <label class="active" for="input_'+field+'_'+x+'">' + vcbl[x] + '</label>';
                         html += '</div>';
                    }
                    html += '  </div>';
                    html += '</div>' +"\r\n";
                    break;

            case 'radio-list': 
                    let vrl = full_data.structure[i]['options'].split('|');
                    html += '<div class="form-group mb-4">';
                    html += '  <label class="active">'+display_name+'</label>';
                    html += '  <div class="bg-light p-2">';
                    for( var x in vrl)
                    {
                         html += '<div class="form-check pt-3'+((vrl.length<5)?(' form-check-inline'):(''))+'">';
                         html += '  <input type="radio" name="'+field+'" value="'+vrl[x]+'" id="input_'+field+'_'+x+'" class="form-check-input"'+((value==vrl[x])?(' checked'):(''))+'>'; 
                         html += '  <label class="active" for="input_'+field+'_'+x+'">' + vrl[x] + '</label>';
                         html += '</div>';
                    }
                    html += '  </div>';
                    html += '</div>' +"\r\n";
                    break;

            default: 
                    html += '<div class="form-group mb-4 pb-2"><h2>' + display_name + '</h2> ' + html_type +
                            '<label class="active" placeholder="'+display_name+'" for="input_'+field+'">'+display_name+'</label>'+"</div>\r\n";
                        break;

        }
    }
    html += '</div>';

    html += '<div class="row position-relative">';
    if (item)
        html += '  <input value="Update" type="button" class="btn btn-warning col-sm-3 col-lg-2 m-3 top-50 start-0" onclick="UpdateRow(\''+full_data.metadata.module+'\', document.getElementById(\'editForm\'), '+item.elements[0][full_data.metadata.key]+' )">';
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
// Get a list of table (Method GET). 
// The page 2 is options '/p/2' or 'p/2,10' (page 2 with 10 elements per page)
//
function popolateSelect(type, id_obj, rel_module, options, rel_id_name, rel_display_fields, value, with_null)
{
    let displayed_fields_separator = ' ';
    if (!options)
        options = '';
    else
        options = '/' + options;

    // document.querySelector('h3').innerHTML=table;
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
                            frame_html += '<label for="input_'+field_value.replaceAll(' ','_')+'" class="col-sm-2 col-form-label fw-bold fs-6">'+field_value+'</label><br>';
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

//    window.history.pushState(module,'', '#' + module );
        // document.querySelector('h3').innerHTML=module;
    requestData('GET','/' + module + options)
        .then(res => {
            if (res.status == 200)
                res.json().then(data => { 
                    
                    // createTable(data);
                    createList(data);
                    let main_voices_menu = document.querySelectorAll('#mainMenu .nav-item .nav-link');
                    if (main_voices_menu)
                        for (let i in main_voices_menu)
                            if (typeof(main_voices_menu[i].areaCurrent)=='string') 
                            {
                                let voice = main_voices_menu[i];                
                                voice.classList.remove('active');
                                if (voice.areaCurrent==module) 
                                    voice.classList.add('active');
                            }
                });
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

    // window.history.pushState(module +'/insertForm/','', '#' + module +'/insertForm/');

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
                    setToast('Operation failed with status '+data.status,'bg-danger'); 
            });
}


//
// View Single Row
//
function ViewRow(module, key, id)
{
    // window.history.pushState(module + '/' + id,'', '#' + module + '/' + id + '');
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
    let html = data.template;
    for(let i in data.elements[0])
        html = html.split('{{'+i+'}}').join(data.elements[0][i]);

    // output standard
    //    let html = '<div class="table-responsive m-1"><table class="table table-striped ">';
    //    for(let i in data.elements[0])
    //        html += '<tr><th class="bg-info">'+i+'</th><td>'+data.elements[0][i]+'<td></tr>';
    //    html += '</table></div>';

    html += '<div class="row">';
    if (data.permissions.can_update)
        html += '<input href="#/News/updateForm/'+id+'" value="Modify" type="button" class="btn btn-warning col-sm-3 col-lg-2 m-3 top-50 start-0" onclick="UpdateRowForm(\''+data.metadata.module+'\','+id+' )">';
    html += '  <input href="#/News/" value="Go to List" type="button" class="btn btn-secondary col-sm-3 col-lg-2 m-3 top-50 start-0" onclick="GetList(\''+data.metadata.module+'\', \'wr/ob/'+key+'-DESC\')">';
    if (data.permissions.can_update)
        html += '  <input href="#/News/" value="Delete" type="button" class="btn btn-danger col-sm-3 col-lg-2 m-3 top-50 end-0" onclick="DeleteRow(\''+data.metadata.module+'\','+id+')">';
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

//    window.history.pushState(module +'/modifyForm/'+id,'', '#' + module +'/modifyForm/'+id);
    document.onHistoryGo = function() { console.log(document.href.location); return false; }
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
                        else
                            GetList(module, id+'/wr'); // back to detail 
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
                    data.json().then(res => {  GetList(module); setToast(res.message,'bg-success'); } );
                }
                else
                    setToast('Operation failed with status '+data.status,'bg-danger'); 
            });
    }
}



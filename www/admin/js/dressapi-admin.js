function createHTMLMenu(data) 
{
    let menu = document.getElementById('sidebarMenuList');
    for (let i in data) {
        let table = data[i];
        let a = document.createElement('a');
        a.className = "nav-link"+((i==0)?(' active'):(''));
        a.href = '#';
        a.areaCurrent = table;
        a.addEventListener('click', () => { GetList(table, list_options); });
        a.innerHTML = '<span data-feather="get_list"></span>' + table;

        let li = document.createElement('li');
        li.className = 'nav-item';
        li.appendChild(a);
        menu.appendChild(li);
    }
}

async function createMenuTables() 
{
    const response = await requestData('OPTIONS', '/api/all/', null).then(res => 
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

    if (['POST', 'PATCH', 'PUT'].includes(method)) {
        headers.append('Content-Type', 'application/x-www-form-urlencoded');
        params.body = new URLSearchParams([...(new FormData(data))]) // body data type must match "Content-Type" header
    }
    params.headers = headers;

    return await fetch(url, params);
}


function createTable(full_data) 
{
    let table = '<div class="table-responsive"><table class="table table-striped table-sm">';
    let head = false;
    // console.log(full_data.data);
    if (typeof (full_data.data) != 'undefined') {
        let data = full_data.data;
        for (let i in data) {
            if (!head && typeof (data[i]) != 'string') {
                table += '<tr>';
                for (let col_name in data[i])
                    table += '<th>' + col_name.toUpperCase() + '</th>';

                if (typeof (full_data.metadata) != 'undefined')
                    table += '<th colspan="2">Operations</th>';
                table += '</tr>';
                head = true;
            }
            table += '<tr>';
            for (let col_name in data[i])
                table += '<td>' + data[i][col_name] + '</td>';

            if (typeof (full_data.metadata) != 'undefined')
            {
                table += '<td><input type="button" class="btn btn-warning" value="Update" onclick="UpdateRowForm(\'' + full_data.metadata.table + '\', ' + data[i]['id'] + ')"></td>';
                table += '<td><input type="button" class="btn btn-danger" value="Delete" onclick="DeleteRow(\'' + full_data.metadata.table + '\', ' + data[i]['id'] + ')"></td>';
            }
            table += '</tr>';
        }
    }

    table += '</table></div>';

    if (typeof (full_data.metadata) != 'undefined') {
        let metadata = full_data.metadata;
        document.querySelector('.metadata').innerHTML = 'Page ' + metadata.page + '/' + metadata.total_pages + ' - '
            + metadata.items_per_page + ' items per page';
        document.getElementById('tableName').innerHTML = metadata.table;
    }
    else
        document.querySelector('.metadata').innerHTML = '';

    return table;
}


function createForm(full_data, item) 
{
    console.log(item);
/*
<form class="was-validated">
  <div class="mb-3">
    <label for="validationTextarea" class="form-label">Textarea</label>
    <textarea class="form-control is-invalid" id="validationTextarea" placeholder="Required example textarea" required></textarea>
    <div class="invalid-feedback">
      Please enter a message in the textarea.
    </div>
  </div>

  <div class="form-check mb-3">
    <input type="checkbox" class="form-check-input" id="validationFormCheck1" required>
    <label class="form-check-label" for="validationFormCheck1">Check this checkbox</label>
    <div class="invalid-feedback">Example invalid feedback text</div>
  </div>

  <div class="form-check">
    <input type="radio" class="form-check-input" id="validationFormCheck2" name="radio-stacked" required>
    <label class="form-check-label" for="validationFormCheck2">Toggle this radio</label>
  </div>
  <div class="form-check mb-3">
    <input type="radio" class="form-check-input" id="validationFormCheck3" name="radio-stacked" required>
    <label class="form-check-label" for="validationFormCheck3">Or toggle this other radio</label>
    <div class="invalid-feedback">More example invalid feedback text</div>
  </div>

  <div class="mb-3">
    <select class="form-select" required aria-label="select example">
      <option value="">Open this select menu</option>
      <option value="1">One</option>
      <option value="2">Two</option>
      <option value="3">Three</option>
    </select>
    <div class="invalid-feedback">Example invalid select feedback</div>
  </div>

  <div class="mb-3">
    <input type="file" class="form-control" aria-label="file example" required>
    <div class="invalid-feedback">Example invalid form file feedback</div>
  </div>

  <div class="mb-3">
    <button class="btn btn-primary" type="submit" disabled>Submit form</button>
  </div>
</form>
*/

    let htmlform = '<form>';
//    for(let i in full_data.data)
//        htmlform += full_data.data[i]['field'] + '=' + full_data.data[i]['html_type']+'<br>';
    
    // list of filed to popolate
    let popolateLists = [];

    for(let i in full_data.data)
    {
/*

        $type = 'text';
        if (str_contains($db_type, 'INT'))
            $type = 'number';
        elseif (str_contains($db_type, 'ENUM'))
            $type = 'radio';
        elseif (str_contains($db_type, 'SET'))
            $type = 'checkbox';
        elseif (str_contains($db_type, 'LOB') || str_contains($db_type,'TEXT'))
            $type = 'textarea';
        elseif (
            str_contains($db_type, 'FLOAT')  || str_contains($db_type, 'DOUBLE') ||
            str_contains($db_type, 'NUMBER') || str_contains($db_type, 'DEC')
        )
            $type = 'number';

        // SPECIAL HTML TYPES (changed by name)
        if ($type=='text' && $name!='')
        {
            if (str_contains($name, 'color')) $type = 'color';
            if (str_contains($name, 'email')) $type = 'email';
            if (str_contains($name, 'image')) $type = 'image';
            if (str_contains($name, 'password')) $type = 'password';
            if (str_contains($name, 'url'))  $type = 'url';
            if (str_contains($name, 'date')) $type = 'date';
            if (str_contains($name, 'datetime')) $type = 'datetime-local';
            if (str_contains($name, 'time')) $type = 'time';
            if (str_contains($name, 'file')) $type = 'file';
            if (str_contains($name, 'phone') || str_contains($name, 'cellular')) $type = 'tel';
        }

*/        
        
        let size = parseInt(full_data.data[i]['max']);
        let field = full_data.data[i]['field'];
        let display_name = full_data.data[i]['display_name'];
        let value = item.data[0][field];
                
        switch(full_data.data[i]['html_type'])
        {
            case 'hidden':
                htmlform += '<input value="'+value+'" type="'+full_data.data[i]['html_type']+'" size="'+size+'" class="form-control" id="input_'+field+'" required>';
                break;

            case 'text':
            case 'number':
            case 'datetime': 
            case 'date': 
            case 'time': 
                    htmlform += '<div class="mb-3 row">' +
                                '<label class="col-sm-2 form-label fw-bold fs-6" for="input_'+field+'">'+display_name+'</label>' +
                                '<div class="col-sm-10"><input value="'+value+'" type="'+full_data.data[i]['html_type']+'" size="'+size+'" class="form-control" id="input_'+field+'" required></div>' +
                                '</div>'+"\r\n";
                    break;
   
                case 'textarea':
                    htmlform += '<div class="mb-3 row">' +
                         '<label for="input_'+field+'" class="col-sm-2 form-label fw-bold fs-6">'+display_name+'</label>' +
                         '<div class="col-sm-10"><textarea class="form-control" id="input_'+field+'" required>'+value+'</textarea></div>' +
                         // '<div class="invalid-feedback">' + field + '</div>' +
                         '</div>'+"\r\n";                        
                    break;

            case 'checkbox': 
                    htmlform += '<div class="mb-3 row">' +
                        '<label for="input_'+field+'" class="col-sm-2 col-form-label fw-bold fs-6">'+display_name+'</label>' +
                        '<div class="col-sm-10"><input value="'+value+'" type="checkbox" class="form-control" id="input_'+field+'"></div>' +
                        // '<div class="invalid-feedback">Example invalid feedback text</div>' +
                        '</div>'+"\r\n";
                        break;

            case 'select': 
                    htmlform += '<div class="mb-3 row">' +
                        '<label for="input_'+field+'" class="col-sm-2 col-form-label fw-bold fs-6">'+display_name+'</label>' +
                        '<div class="col-sm-10"><select class="form-control" id="input_'+field+'"></select></div>' +
                        // '<div class="invalid-feedback">Example invalid feedback text</div>' +
                        '</div>'+"\r\n";
                        
                        [rel_table,rel_sitems] = full_data.data[i]['ref'].split(':');
                        [rel_id_name,rel_items] = rel_sitems.split('-');
                        popolateLists.push(['select','input_'+field, rel_table, '', rel_id_name, rel_items]);
                        break;

            case 'datalist': 
                    htmlform += '<div class="mb-3 row">' +
                        '<label for="input_'+field+'" class="col-sm-2 col-form-label fw-bold fs-6">'+display_name+'</label>' +
                        '<div class="col-sm-10"><datalist class="form-control" id="input_'+field+'"></datalist></div>' +
                        // '<div class="invalid-feedback">Example invalid feedback text</div>' +
                        '</div>'+"\r\n";
                        
                        [rel_table,rel_sitems] = full_data.data[i]['ref'].split(':');
                        [rel_id_name,rel_items] = rel_sitems.split('-');
                        popolateLists.push(['select','input_'+field, rel_table, '', rel_id_name, rel_items]);
                        break;

            case 'checkbox-list': 
                    htmlform += '<div class="mb-3 row">' +
                        '<input type="checkbox" size="'+size+'" class="form-check-input" id="input_'+field+'" required>' +
                        '<label for="input_'+field+'" class="col-sm-2 col-form-label fw-bold fs-6">'+display_name+'</label>' +
                        '<div class="col-sm-10"><input value="'+value+'" type="checkbox" class="form-checkbox-input" id="input_'+field+'"></div>' +
                        '<div class="invalid-feedback">Example invalid feedback text</div>' +
                        '</div>'+"\r\n";
                        break;

            case 'radio-list': 
                    htmlform += '<div class="mb-3 row">' +
                        '<input type="checkbox" size="'+size+'" class="form-check-input" id="input_'+field+'" required>' +
                        '<label for="input_'+field+'" class="col-sm-2 col-form-label fw-bold fs-6">'+display_name+'</label>' +
                        '<div class="col-sm-10"><input value="'+value+'" type="radio" class="form-radio-input" id="input_'+field+'"></div>' +
                        '<div class="invalid-feedback">Example invalid feedback text</div>' +
                        '</div>'+"\r\n";
                        break;
            default: 
                    htmlform += '<div class="mb-3 row"><h2>' + display_name + '</h2> ' + full_data.data[i]['html_type']
                        '</div>'+"\r\n";
                        break;

        }
    }

    htmlform += '</form>';

    document.querySelector('.results').innerHTML = htmlform;

    for( let i in popolateLists)
    {
        let row = popolateLists[i];

        switch(row[0])
        {
            case 'datalist':
            case 'select':
                           // id_obj, rel_table,  options, rel_id_name, items to display
                rel_list = popolateSelect(row[1], row[2], row[3], row[4], row[5]);
                break;
        }
    }

    return htmlform;
}


//
// Get a list of table (Method GET). 
// The page 2 is options '/p/2' or 'p/2,10' (page 2 with 10 elements per page)
//
function popolateSelect(id_obj, rel_table, options, rel_id_name, rel_display_fields)
{
    let displayed_fields_separator = ' ';
    if (!options)
        options = '';
    else
        options = '/' + options;

    // document.querySelector('h3').innerHTML=table;
    requestData('GET','/api/' + rel_table + '/' + options)
        .then(res => {
            if (res.status == 200)
                res.json().then(data => 
                    { 
                        console.log(data);
                        let frame_html = ''; 
                        for(var x in data.data)
                        {
                            let field_value = '';
                            if (rel_display_fields.includes(','))
                            {
                                let v = rel_display_fields.split(',');
                                for(let y in v)
                                    if (typeof(data.data[x][v[y]])!='undefined')
                                        field_value += ((field_value=='')?(''):(displayed_fields_separator)) + data.data[x][v[y]];
                            }
                            else
                                field_value = data.data[x][rel_display_fields];

                            frame_html += '<option value="'+data.data[x][rel_id_name]+'">'+field_value+'</option>';
                        }
                        document.getElementById(id_obj).innerHTML = frame_html;
                    });
        });
}


//
// Get a list of table (Method GET). 
// The page 2 is options '/p/2' or 'p/2,10' (page 2 with 10 elements per page)
//
function GetList(table, options)
{
    if (!options)
        options = '';
    else
        options = '/' + options;

    // document.querySelector('h3').innerHTML=table;
    requestData('GET','/api/' + table + options)
        .then(res => {
            if (res.status == 200)
                res.json().then(data => { document.querySelector('.results').innerHTML = createTable(data); });
        });
}


//
// Insert (Method POST)
//<
function InsertRow(table, formData)
{
    // let formData = new FormData();
    // formData.append('id', 1000);
    // formData.append(table, 'Lumen is better than DressApi!');
    // formData.append('id_page', 1);

    postData('/api/'+table+'/',formData)
            .then(data => {
                // if (data.status==201)
                    data.text().then(text => { 
                                                document.getElementById('message').innerHTML = ''+JSON.parse(text).message+'';
                                                GetList(table,list_options);
                                             } );
            });
}


//
// Update Form
//
function UpdateRowForm(table, id)
{
    var record = null;
    console.log(id);
    requestData('GET','/api/'+table+'/'+id)
            .then(res => {
                if (res.status==200)
                {
                    res.json().then(data => { record = data; } );
                    //
                    // Get Structure of table (Method OPTIONS)
                    //
                    requestData('OPTIONS','/api/'+table)
                    .then(res2 => {
                        if (res2.status==200)
                            res2.json().then(data => { createForm({'data':data}, record);} );
                    });
                }
            });

}



//
// Update (Method PUT/PATCH)
//
function UpdateRow(table, formData)
{
    // let formData = new FormData();
    // formData.append('id', 1000);
    // formData.append(table, 'Yii is better than DressApi!');
    // formData.append('id_page', 1);
    
    requestData('UPDATE','/api/'+table+'/'+id, formData)
        .then(data => {
                        if (data.status==200)
                            data.text().then(text => { document.querySelector('.results').innerHTML = '<pre>'+text+'<pre>';} );
                        else
                            console.log('status: '+data.status);
                    }
            );
}


//
// Delete (Method DELETE)
//
function DeleteRow(table, id)
{
    // let formData = new FormData();
    // formData.append('id', 1000);
    
    if (confirm('Are you sure to delete this element?'))
    {
        requestData('DELETE','/api/'+table+'/'+id)
            .then(data => {
                output.querySelector('.status').innerHTML = data.status+ ' (' + data.statusText + ')';
                if (data.status==200)
                {
                    data.json().then(res => {  GetList(table,list_options); document.getElementById('message').innerHTML = res.message;} );
                }
                else
                    console.log('status: '+data.status);
            });
    }
}



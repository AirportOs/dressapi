<h1>{{page_info::element::title}}</h1>

{{foreach data::elements elem}}
<div class="table-responsive">
    <table class="table table-striped table-sm">
    <tbody>
            {{foreach elem field}}
        <tr>
            <th>{{field::name}}</th>
            <td>{{field::value}}</td>
        </tr>
            {{end foreach field}}

    </tbody>
    </table>
</div>

<div class="row">
    {{if permission::can_update}}
    <a class="btn btn-warning col-sm-3 col-lg-2 m-3 top-50 start-0" href="/{{data::metadata::module}}/{{elem::id}}/modify-form">{{'Modify'}}</a>  
    {{end if permission::can_update}}

    {{if permission::can_read}}
    <a class="btn btn-secondary col-sm-3 col-lg-2 m-3 top-50 start-0" href="/{{data::metadata::module}}">{{'Go to List'}}</a>
    {{end if permission::can_read}}


    {{if permission::can_delete}}
    <a class="btn btn-danger col-sm-3 col-lg-2 m-3 top-50 end-0" href="/{{data::metadata::module}}/{{elem::id}}/delete" onclick="return confirm('{{\'Are you sure?\'}}')">{{'Delete'}}</a>
    {{end if permission::can_delete}}
</div>

{{end foreach elem}}

<?php 

echo "<pre>";print_r($this->data['permissions']);echo "</pre>";
?>
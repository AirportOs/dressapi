<h1>{{page_info::element::title}}</h1>
<div class="table-responsive">
    <table class="table table-striped table-sm">
    <tbody>
        {{foreach data::elements elem}}
            {{foreach elem field}}
        <tr>
            <th>{{field::name}}</th>
            <td>{{field::value}}</td>
        </tr>
            {{end foreach field}}
        {{end foreach elem}}

    </tbody>
    </table>
</div>

<?php 

echo "<pre>";print_r($this->data['permissions']);echo "</pre>";
?>
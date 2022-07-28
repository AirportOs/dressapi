<div class="container">
    <h1>{{page_info::element::title}}</h1>
    <h4>{{page_info::element::description}}</h4>
    <div class="table-responsive">
        <table class="table table-striped table-sm">

        <thead>
            <tr>
                <th>{{'Operations'}}</th>
                {{foreach data::columns name}}
                <th>{{name}}</th>
                {{end foreach name}}
            </tr>
        </thead>

        <tbody>

            {{foreach data::elements elem}}
            <tr>
                <td><a class="btn btn-secondary m-1" href="/{{data::metadata::module}}/{{elem::id}}" title="{{'Go to'}} {{'Details'}}">{{'Details'}}</a></td>
                {{foreach elem value}}
                <td>{{value}}</td>
                {{end foreach value}}
            </tr>
            {{end foreach elem}}
        </tbody>

        </table>
    </div>
</div>


<?php
/*
<!-- START EXAMPLE -->
<div class="container">
<b>BASE - {{creation_date}}</b>
<h1>{{title}}</h1>
<img src="/upload/img/{{img}}" style="float:right;width:40%;">
<h2><i>{{description}}</i></h2>
<h3>{{body}}</h3>
<p><br></p>
</div>
<!-- END EXAMPLE -->
*/


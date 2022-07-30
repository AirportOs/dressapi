    <h1>{{page_info::element::title}}</h1>
    <h4>{{page_info::element::description}}</h4>

    {{if permission::can_insert}}
    <a class="btn btn-success col-sm-3 col-lg-2 start-0" href="/{{data::metadata::module}}/{{elem::id}}/insert-form">
    <svg class="icon-white icon"><use href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-plus-circle"></use></svg>
    {{'Insert New'}}</a>  
    {{end if permission::can_insert}}


    {{if data::elements}}
    <div class="table-responsive">
        <table class="table table-striped table-sm">

        <thead>
            <tr>
                <th colspan="2">{{'Operations'}}</th>
                {{foreach data::columns name}}
                <th>{{name}}</th>
                {{end foreach name}}
            </tr>
        </thead>

        <tbody>

            {{foreach data::elements elem}}
            <tr>
                <td>
                    <a class="btn btn-primary" href="/{{data::metadata::module}}/{{elem::id}}" title="{{'Go to'}} {{'Details'}}"> <svg class="icon-white icon icon-sm"><use href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-password-visible"></use></svg></a>
                </td>
                <td>
                {{if permission::can_update}}
                    <a class="btn btn-secondary" href="/{{data::metadata::module}}/{{elem::id}}/modify-form" title="{{'Go to'}} {{'Modify'}}"> <svg class="icon-white icon icon-sm"><use href="/frameworks/bootstrap-italia/dist/svg/sprite.svg#it-pencil"></use></svg></a>
                {{end if permission::can_update}}
                </td>
                {{foreach elem value}}
                <td>{{value}}</td>
                {{end foreach value}}
            </tr>
            {{end foreach elem}}
        </tbody>

        </table>
    </div>
    {{end if data::elements}}
<div class="container">
</div>


<?php
/*
<!-- START EXAMPLE FOR SPECIFIC TYPE OF ELEMENT -->
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


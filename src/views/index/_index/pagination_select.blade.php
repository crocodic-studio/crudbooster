<form method='get' id='form-limit-paging' style="display:inline-block" action='{{Request::url()}}'>
    {!! CRUDBooster::getUrlParameters(['limit']) !!}
    <div class="input-group">
        <select onchange="$('#form-limit-paging').submit()" name='limit' style="width: 56px;"
                class='form-control input-sm'>
            <option {{($limit==5)?'selected':''}} value='5'>5</option>
            <option {{($limit==10)?'selected':''}} value='10'>10</option>
            <option {{($limit==20)?'selected':''}} value='20'>20</option>
            <option {{($limit==25)?'selected':''}} value='25'>25</option>
            <option {{($limit==50)?'selected':''}} value='50'>50</option>
            <option {{($limit==100)?'selected':''}} value='100'>100</option>
            <option {{($limit==200)?'selected':''}} value='200'>200</option>
        </select>
    </div>
</form>
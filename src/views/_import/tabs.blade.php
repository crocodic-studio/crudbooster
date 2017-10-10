<li style="background:#eeeeee">
    <a style="color:#111"
       onclick="if(confirm('Are you sure want to leave ?')) location.href='{{ CRUDBooster::mainpath("import-data") }}'"
       href='javascript:;'>
        {!! CB::icon('download') !!} Upload a File &raquo;
    </a>
</li>

<li style="background:#eeeeee">
    <a style="color:#111" href='#'>{!! CB::icon('cogs') !!} Adjustment &raquo;</a>
</li>

<li style="background:#ffffff" class='active'>
    <a style="color:#111" href='#'>
        {!! CB::icon('cloud-download') !!} Importing &raquo;
    </a>
</li>
        
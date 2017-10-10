<table id='table-parameters' class='table table-striped table-bordered'>
    <thead>
    <tr>
        <th width="3%">No</th>
        <th>Name</th>
        <th>Type</th>
        <th>Laravel Validation / Description / Value</th>
        <th width="8%" title='is Mandatory ?'>Mandatory</th>
        <th width="8%" title='is used ?'>Enable</th>
        <th width="5%">-</th>
    </tr>
    </thead>
    <tbody>
    <tr class='row-no-data'>
        <td colspan='7'>There is no data</td>
    </tr>
    </tbody>
    <tfoot style="display:none">
    <tr>
        <td>#</td>
        <td width="20%"><input class='form-control' name='params_name[]' type='text'/></td>
        <td width="20%"><select class='form-control' name='params_type[]'>
                <optgroup label='Common Validation'>
                    <option value='string'>String</option>
                    <option value='integer'>Integer</option>
                    <option value='email'>Email</option>
                    <option value='image'>Image (jpeg, png, bmp, gif, or svg)</option>
                    <option value='file'>File Upload</option>
                    <option value='exists'>Exists (table,column)</option>
                    <option value='unique'>Unique (table,column,except)</option>
                    <option value='password'>Password</option>
                    <option value='search'>Search</option>
                    <option value='custom'>Custom (Not In Table)</option>
                </optgroup>
                <optgroup label='Other Validation'>
                    <option value='array'>Array</option>
                    <option value='alpha'>Alpha</option>
                    <option value='alpha_num'>Alpha Numeric</option>
                    <option value='alpha_spaces'>Alpha Spaces</option>
                    <option value='base64_file'>Base64 File</option>
                    <option value='boolean'>Boolean</option>
                    <option value='date'>Date (Y-m-d)</option>
                    <option value='date_format:Y-m-d H:i:s'>DateTime (Y-m-d H:i:s)</option>
                    <option value='date_format'>Date Format Custom</option>
                    <option value='digits'>Digits</option>
                    <option value='digits_between'>Digits Between (Min,Max)</option>
                    <option value='in'>In (a,b,c)</option>
                    <option value='json'>Json Valid</option>
                    <option value='mimes'>Mimes Type</option>
                    <option value='min'>Min</option>
                    <option value='max'>Max</option>
                    <option value='numeric'>Numeric</option>
                    <option value='not_in'>Not In (a,b,c)</option>
                    <option value='url'>URL Valid</option>
                </optgroup>
                <optgroup label='Other'>
                    <option value='ref'>Child Table References</option>
                </optgroup>
            </select>
        </td>
        <td>
            <input class='form-control' type='text' name='params_config[]'>
        </td>
        <td>
            <select class='form-control params_required' name='params_required[]'>
                <option value='1'>YES</option>
                <option value='0'>NO</option>
            </select>
        </td>
        <td>
            <select class='form-control params_used' name='params_used[]'>
                <option value='1'>YES</option>
                <option value='0'>NO</option>
            </select>
        </td>
        <td class='col-delete'>
            <a class='btn btn-primary' href='javascript:void(0)' onclick='addParam()'>
                <i class='fa fa-plus'></i>
            </a>
        </td>
    </tr>
    </tfoot>
</table>
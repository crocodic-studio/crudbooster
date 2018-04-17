@if(count($apikeys)==0)
    <tr class='no-secretkey'>
        <td colspan='5' align="center">There is no secret key found,
            <a href='javascript:void(0)' onclick='generate_secret_key()'>
                Click here to generate one
            </a>
        </td>
    </tr>
@endif
<table>
    <thead>
        <tr>
           <th>#</th>
           <th>Tên thiết bị</th>
           <th>Mã thiết bị</th>
           <th>Số serial</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datalist as $key => $val)
            <tr>
                <td>{{$key + 1}}</td>
                <td>{{$val->device_name ?? "N/A"}}</td>
                <td>{{$val->device_code ?? "N/A"}}</td>
                <td>{{$val->serial ?? "N/A"}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
   
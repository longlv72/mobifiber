<div>BÁO CÁO KHÁCH HÀNG</div>
<table>
    <thead>
        <tr>
           <th>#</th>
           <th>Tên khách hàng</th>
           <th>Mã khách hàng</th>
           <th>Địa chỉ email</th>
           <th>Số điện thoại</th>
           <th>Số giấy tờ</th>
           <th>Loại khách hàng</th>
        </tr>
    </thead>
    <tbody>
        @php
            $TYPE = [
                "1" => "Cá nhân",
                "2" => "Doanh nghiệp"
            ];
        @endphp
        @foreach($datalist as $key => $val)
            <tr>
                <td>{{$key + 1}}</td>
                <td>{{$val->last_name}} {{$val->firstname}}</td>
                <td>{{$val->code ?? "N/A"}}</td>
                <td>{{$val->email ?? "N/A"}}</td>
                <td>{{$val->phone ?? "N/A"}}</td>
                <td>{{$val->cccd ?? "N/A"}}</td>
                <td>{{array_key_exists($val->type, $TYPE) ? $TYPE[$val->type] : "N/A"}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
   
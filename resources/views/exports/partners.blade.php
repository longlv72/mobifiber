<table>
    <thead>
        <tr>
           <th>#</th>
           <th>Tên Đối Tác</th>
           <th>Mã Đối Tác</th>
           <th>Địa Chỉ Email</th>
           <th>Số điện thoại</th>
           <th>Tên Ngân Hàng</th>
           <th>Số Tài Khoản</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datalist as $key => $val)
            <tr>
                <td>{{$key + 1}}</td>
                <td>{{$val->partner_name ?? "N/A"}}</td>
                <td>{{$val->partner_code ?? "N/A"}}</td>
                <td>{{$val->email ?? "N/A"}}</td>
                <td>{{$val->phone ?? "N/A"}}</td>
                <td>{{$val->bank_name ?? "N/A"}}</td>
                <td>{{$val->number_bank ?? "N/A"}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
   
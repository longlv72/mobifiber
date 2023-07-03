<table>
    <thead>
        <tr>
           <th>#</th>
           <th>Họ Tên</th>
           <th>Tên Đăng Nhập</th>
           <th>Email</th>
           <th>Số Điện Thoại</th>
           <th>Địa Chỉ</th>
           <th>Loại Nhân Viên</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datalist as $key => $val)
            <tr>
                <td>{{$key + 1}}</td>
                <td>{{$val->lastname ?? ""}} {{$val->firstname ?? ""}}</td>
                <td>{{$val->username ?? "N/A"}}</td>
                <td>{{$val->email ?? "N/A"}}</td>
                <td>{{$val->phone ?? "N/A"}}</td>
                <td>{{$val->address ?? "N/A"}}</td>
                <td>{{$val->role ? $val->role->role_name : "N/A"}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
   
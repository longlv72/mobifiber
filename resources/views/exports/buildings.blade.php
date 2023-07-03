<table>
    <thead>
        <tr>
           <th>#</th>
           <th>Tên Tòa Nhà</th>
           <th>Mã Tòa Nhà</th>
           <th>Chủ Sở Hữu</th>
           <th>Địa Chỉ</th>
           <th>Kinh Độ</th>
           <th>Vĩ Độ</th>
           <th>Tỉ Lệ Chia Sẻ</th>
           <th>Loại Hình Hợp Tác</th>
           <th>Đầu Mối Tòa Nhà</th>
           <th>SDT Liên Hệ</th>
        </tr>
    </thead>
    <tbody>
        @php
            $TYPE = [
                "1" => "Tự Triển Khai",
                "2" => "Một Phần",
                "3" => "Toàn Trình"
            ];
        @endphp
        @foreach($datalist as $key => $val)
            <tr>
                <td>{{$key + 1}}</td>
                <td>{{$val->building_name ?? "N/A"}}</td>
                <td>{{$val->building_code ?? "N/A"}}</td>
                <td>{{$val->building_company ?? "N/A"}}</td>
                <td>{{$val->address ?? "N/A"}}</td>
                <td>{{$val->longitude ?? "N/A"}}</td>
                <td>{{$val->latitude ?? "N/A"}}</td>
                <td>{{$val->percent_share ? $val->percent_share . "%" : "N/A"}}</td>
                <td>{{array_key_exists($val->cooperate_type, $TYPE) ? $TYPE[$val->cooperate_type] : "N/A"}}</td>
                <td>{{$val->contact_name ?? "N/A"}}</td>
                <td>{{$val->contact_phone ?? "N/A"}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
   
<table>
    <thead>
        <tr>
           <th>#</th>
           <th>Tên Gói Cước</th>
           <th>Mã Gói Cước</th>
           <th>Quyết Định Số</th>
           <th>Giá Trước Thuế</th>
           <th>Giá Sau Thuế</th>
           <th>T/G Sử Dụng</th>
           <th>T/G Khuyến Mại</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datalist as $key => $val)
            <tr>
                <td>{{$key + 1}}</td>
                <td>{{$val->package_name ?? "N/A"}}</td>
                <td>{{$val->package_code ?? "N/A"}}</td>
                <td>{{$val->decision ?? "N/A"}}</td>
                <td>{{$val->prices ?? "N/A"}}</td>
                <td>{{$val->prices_vat ?? "N/A"}}</td>
                <td>{{$val->time_used ? $val->time_used . " tháng" : "N/A"}}</td>
                <td>{{$val->promotion_time ? $val->promotion_time . " tháng" : "N/A"}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
   
<table>
    <thead>
        <tr>
           <th>#</th>
           <th>Mã Hợp Đồng</th>
           <th>Loại Hình Hợp Tác</th>
           <th>Email Đối Tác</th>
           <th>SĐT Đối Tác</th>
           <th>Tên Khách Hàng</th>
           <th>Email Khách Hàng</th>
           <th>SDT Khách Hàng</th>
           <th>Tên, Mã Gói Cước</th>
           <th>Ngày Bắt Đầu Gói Cước</th>
           <th>Giá Tiền Gói Cước</th>
           <th>T/G Sử dụng Gói Cước</th>
           <th>Tên Thiết Bị</th>
           <th>Serial</th>
           <th>SKU</th>
           <th>Mã Hóa Đơn</th>
           <th>Số Tiền Hóa Đơn</th>
           <th>Ngày Lập</th>
           <th>Tên Tòa Nhà</th>
           <th>Tên Liên Hệ</th>
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
                <td>{{$val->code ?? "N/A"}}</td>
                <td>{{array_key_exists($val->type_cooperate, $TYPE) ? $TYPE[$val->type_cooperate] : "N/A"}}</td>
                <td>{{$val->partner ? ($val->partner->email ?? "N/A") : "N/A"}}</td>
                <td>{{$val->partner ? ($val->partner->phone ?? "N/A") : "N/A"}}</td>
                <td>{{$val->customer ? ($val->customer->last_name . $val->customer->firstname) : "N/A"}}</td>
                <td>{{$val->customer ? ($val->customer->email ?? "N/A") : "N/A"}}</td>
                <td>{{$val->customer ? ($val->customer->phone ?? "N/A") : "N/A"}}</td>
                <td>{{$val->package ? ($val->package->package_name . " - " . $val->package->package_code) : "N/A"}}</td>
                <td>{{$val->start_date_package ?? "N/A"}}</td>
                <td>{{$val->package_price ?? "N/A"}}</td>
                <td>{{$val->package_time_used ? ($val->package_time_used . " tháng") : "N/A"}}</td>
                <td>{{$val->device ? $val->device->device_name ?? "N/A" : "N/A"}}</td>
                <td>{{$val->device ? $val->device->device_code ?? "N/A" : "N/A"}}</td>
                <td>{{$val->bill_code ?? "N/A"}}</td>
                <td>{{$val->bill_prices ?? "N/A"}}</td>
                <td>{{$val->bill_date ?? "N/A"}}</td>
                <td>{{$val->building ? $val->building->building_name ?? "N/A" : "N/A"}}</td>
                <td>{{$val->building ? $val->building->contact_name ?? "N/A" : "N/A"}}</td>
                <td>{{$val->building ? $val->building->contact_phone ?? "N/A" : "N/A"}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
   
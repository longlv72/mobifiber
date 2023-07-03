<h4>Kính gửi Anh/Chị,</h4>

<p>Anh/Chị đã được giao công việc {{ $job->reason_job->value_setting}} từ {{ Auth::user()->lastname}} {{ Auth::user()->firstname}}.</p>

<p>Chi tiết công việc:</p>
<div>{{$job->reason_job->value_setting}}</div>
<div>{{$job->descriptions}}</div>
<br>
@if ($job->building || $job->address)    
    <div>Địa điểm tại: {{ $job->building ? ('tòa nhà ' . $job->building->building_name . " " . $job->building->address) : ($job->address ? $job->address->address : '')}}</div>
@endif
<br>
@if ($job->customer)
    <div>Thông tin liên hệ: {{ $job->customer->last_name }} {{ $job->customer->firstname }} - {{ $job->customer->phone }}</div>
@endif

<p>Trân trọng.</p>
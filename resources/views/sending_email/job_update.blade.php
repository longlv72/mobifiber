<h4>Kính gửi Anh/Chị,</h4>

<p>Công việc {{ $job->reason_job->value_setting}} tại {{ $job->building ? ('tòa nhà ' . $job->building->building_name . " " . $job->building->address) : ($job->address ? $job->address->address : '')}} đã có sự thay đổi.</p>

<p>{{$content}}</p>

<p>Trân trọng.</p>
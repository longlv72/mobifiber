@extends('layouts.app')
@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Báo cáo phát triển thuê bao</h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-2">
                            <select class="form-select" id="sltCreatedBy">
                                <option value="0">Tất cả nhân viên</option>
                                @if ($list_employees)
                                    @foreach ($list_employees as $item)
                                        <option value="{{ $item->id }}">{{ $item->username }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-xl-4">
                            <div class="input-group">
                                <input type="date" id="txtStartDate" class="form-control" value="{{ date('Y-m-01') }}" />
                                <label class="input-group-text">đến</label>
                                <input type="date" id="txtEndDate" class="form-control" value="{{ date('Y-m-d') }}" />
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <button type="button" id="btnSearchReportGrowth" class="btn btn-primary"><i class="bx bx-search"></i></button>
                        </div>
                        <div class="col-xl-12 pt-3">
                            <table class="table table-bordered" id="tblReportCustomerGrowth">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ URL::asset($custom_asset . '/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/js/accounting.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/js/pages_js/list_report.js') }}"></script>
@endsection
@section('styles')
    <link href="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset($custom_asset . '/assets/libs/select2/dist/css/select2.min.css') }}" rel="stylesheet"
        type="text/css" />
@endsection

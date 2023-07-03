@extends('layouts.app')
@section('main_content')
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Doanh thu khách hàng</h4>
                    <div>
                        <select class="form-select form-select-sm" id="sltYearCustomer">
                            @for ($i = date('Y') - 10; $i <= date('Y') + 1; $i++)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>Năm
                                    {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div><!-- end card header -->
                <div class="card-body p-0 pb-2">
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="w-100">
                                <div id="revenues_customer_report_chart">
                                </div>
                            </div><!-- end card body -->
                        </div>
                        <div class="col-xl-4 px-4">
                            <table class="table table-bordered" id="tblReportCustomerRevenue">

                            </table>
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
    <script src="{{ URL::asset($custom_asset . '/assets/js/accounting.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/js/pages_js/list_report.js') }}"></script>
@endsection
@section('styles')
    <link href="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.css') }}" rel="stylesheet"
        type="text/css" />
@endsection

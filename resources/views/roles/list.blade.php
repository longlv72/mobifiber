@extends('layouts.app')
@section('main_content')
<input type="text" id="role_group_id" value="{{$role_id}}" hidden />
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="list_role_table" class="table table-striped table-bordered dataTable no-footer mt-3">

                    </table>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="_token" value="{{ csrf_token() }}">
@endsection

@section('scripts')
    <script src="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.js') }}"></script>
    <script src="{{ URL::asset($custom_asset . '/assets/js/pages_js/list_role.js') }}"></script>
@endsection
@section('styles')
    <link href="{{ URL::asset($custom_asset . '/assets/libs/bootstrap_table/bootstrap-table.css') }}" rel="stylesheet"
        type="text/css" />
@endsection

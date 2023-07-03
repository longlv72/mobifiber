<?php

namespace App\Http\Controllers;

use App\Facads\ODB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use PDO;

class ReportController extends CoreController
{
    public $data = [];
    public function __construct()
    {
    }
    public function listReport()
    {
        return view('report.list');
    }

    public function view_growth()
    {
        $this->data['list_employees'] = getAllEmployees();
        return view('report.growth', $this->data);
    }

    public function listReportCustomer()
    {
        return view('report.customer');
    }
    public function executeProcedureWithCursor($procedureName,  $bindings)
    {
        $cursors = [];
        $result = [];
        $pdo = DB::getPdo();
        $command  = sprintf('begin %s(:%s); end;', $procedureName, implode(', :', Arr::pluck($bindings, 'name')));
        $stmt = $pdo->prepare($command);

        foreach ($bindings as $key => $row) {
            if (isset($row['value']))
                $stmt->bindParam(":" . $row['name'], $row['value'], $row['type']);
            else
                $stmt->bindParam(":" . $row['name'],  $result[$row['name']], $row['type']);

            if ($row['type'] === PDO::PARAM_STMT)
                $cursors[$row['name']] = $result[$row['name']];
        }
        $stmt->execute();
        $stmt->closeCursor();

        foreach ($cursors as $key => $cursor) {
            oci_execute($cursor, OCI_DEFAULT);
            oci_fetch_all($cursor, $result[$key], 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC);
            oci_free_cursor($cursor);
        }

        return $result;
    }
    public function reportCustomerExist(Request $request)
    {
        $customer_id = $request->customer_id;
        $year = $request->year;
        $bindings = [
            ['name' => 'cus_in', 'type' => ODB::STRING, 'value' => $customer_id],
            ['name' => 'year', 'type' => ODB::INTEGER, 'value' => $year],
            ['name' => 'data_out', 'type' => ODB::CURSOR]
        ];
        $result = $this->executeProcedureWithCursor('PKG_REPORT.report_customer', $bindings);
        $data = collect($result['data_out']);
        $list_month = $data->pluck('MONTH_REPORT')->all();
        $list_price = $data->pluck('REVENUE')->all();
        $list_price_vat = $data->pluck('REVENUE_VAT')->all();
        return response()->json([
            'month_report' => $list_month,
            'revenue' => $list_price,
            'revenue_vat' => $list_price_vat
        ]);
    }

    public function reportCustomerExistAll(Request $request)
    {
        $year = $request->year;
        $bindings = [
            ['name' => 'year', 'type' => ODB::INTEGER, 'value' => $year],
            ['name' => 'data_out', 'type' => ODB::CURSOR]
        ];
        $result = $this->executeProcedureWithCursor('PKG_REPORT.report_customer_all', $bindings);
        $data = collect($result['data_out']);
        $list_month = $data->pluck('MONTH_REPORT')->all();
        $list_price = $data->pluck('REVENUE')->all();
        $list_price_vat = $data->pluck('REVENUE_VAT')->all();
        return response()->json([
            'data' => $data,
            'month_report' => $list_month,
            'revenue' => $list_price,
            'revenue_vat' => $list_price_vat
        ]);
    }

    public function reportRevenueMonth(Request $request)
    {
        $year = $request->year;
        $query = DB::select(DB::raw("
            select monthrp.month_report as month_report, COALESCE(datarp.prices, 0) as prices, COALESCE(datarp.prices_vat, 0) as prices_vat
            from
            (
                select level as month_report from dual connect by level >= 1 and level <=12
            ) monthrp
            left join
            (
                select to_number(to_char(c.start_date_package, 'MM')) as month_report,
                sum(c.bill_prices) as prices,
                sum(c.bill_prices_vat) as prices_vat
                from contracts c
                join packages p on c.package_id = p.id
                where to_number(to_char(c.start_date_package, 'YYYY')) = :year
                group by to_number(to_char(c.start_date_package, 'MM'))
            ) datarp on monthrp.month_report = datarp.month_report
            order by monthrp.month_report
        "), ['year' => $year]);
        $data = collect($query);
        $list_month = $data->pluck('month_report')->all();
        $list_price = $data->pluck('prices')->all();
        $list_price_vat = $data->pluck('prices_vat')->all();
        return response()->json([
            'data_table' => $data,
            'list_month' => $list_month,
            'list_price' => $list_price,
            'list_price_vat' => $list_price_vat
        ]);
    }
    public function view_reportgrowth(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $user_id = $request->user_id;
        $bindings = [
            ['name' => 'user_id', 'type' => ODB::INTEGER, 'value' => $user_id],
            ['name' => 'start_date', 'type' => ODB::STRING, 'value' => $startDate],
            ['name' => 'end_date', 'type' => ODB::STRING, 'value' => $endDate],
            ['name' => 'data_out', 'type' => ODB::CURSOR]
        ];
        $result = $this->executeProcedureWithCursor('PKG_REPORT.report_growth', $bindings);
        $data = collect($result['data_out']);
        return response()->json([
            'rows' => $data,
            'total' => count($data)
        ]);
    }
}

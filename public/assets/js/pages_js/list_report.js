$(function () {
    window.list_report = {
        init: function () {
            $('#sltYear').change(function () {
                list_report.chart_revenues();
            });
            if ($('#tblReportRevenue').length > 0) {
                list_report.chart_revenues();
            }
            if ($('#tblReportCustomerRevenue').length) {
                list_report.chart_customer_revenues();
            }
            $('#sltYearCustomer').change(function () {
                list_report.chart_customer_revenues();
            });
            if($('#tblReportCustomerGrowth').length > 0){
                $('#sltCreatedBy').select2();
                list_report.report_growth();
            }
            $('#btnSearchReportGrowth').click(function(){
                list_report.report_growth();
            });
        },
        chart_revenues: function () {
            $.ajax({
                url: '/report-revenus-month',
                type: 'get',
                data: {
                    year: $('#sltYear').val()
                },
                dataType: 'json',
                success: function (res) {
                    $("#revenues_report_chart").empty();
                    var options = {
                        series: [{
                            name: 'Tổng tiền',
                            data: res.list_price
                        }, {
                            name: 'Tổng tiền (bao gồm VAT)',
                            data: res.list_price_vat
                        }],
                        chart: {
                            type: 'bar',
                            height: 570,
                            toolbar: {
                                show: false
                            },
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '55%',
                                endingShape: 'rounded'
                            },
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        xaxis: {
                            categories: res.list_month,
                        },
                        yaxis: {
                            labels: {
                                formatter: function (value) {
                                    if (value < 1000) {
                                        return accounting.formatMoney(value, "đ", 0, ".", ",", "%v%s");
                                    }
                                    else if (value >= 1000 && value < 1000000) {
                                        return accounting.formatMoney(value / 1000, " K", 0, ".", ",", "%v%s");
                                    }
                                    else if (value >= 1000000 && value < 1000000000) {
                                        return accounting.formatMoney(value / 1000000, " M", 0, ".", ",", "%v%s");
                                    }
                                    else if (value >= 1000000000) {
                                        return accounting.formatMoney(value / 1000000000, " B", 0, ".", ",", "%v%s");
                                    }
                                }
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function (value) {
                                    return accounting.formatMoney(value, "đ", 0, ".", ",", "%v%s");
                                }
                            },
                            x: {
                                formatter: function (value) {
                                    return 'Tháng ' + value;
                                }
                            }
                        }
                    };
                    var chart = new ApexCharts(document.querySelector("#revenues_report_chart"), options);
                    chart.render();
                    $('#tblReportRevenue').bootstrapTable('destroy');
                    $('#tblReportRevenue').bootstrapTable({
                        data: res.data_table,
                        columns: [{
                            title: "Tháng",
                            align: 'center',
                            valign: 'left',
                            formatter: function (value, row, index) {
                                return row.month_report;
                            }
                        },
                        {
                            title: "Tổng doanh thu",
                            align: 'center',
                            valign: 'left',
                            formatter: function (value, row, index) {
                                return accounting.formatMoney(row.prices, "đ", 0, ".", ",", "%v%s");
                            }
                        },
                        {
                            title: "Tổng doanh thu (bao gồm VAT)",
                            align: 'center',
                            valign: 'left',
                            formatter: function (value, row, index) {
                                return accounting.formatMoney(row.prices_vat, "đ", 0, ".", ",", "%v%s");
                            }
                        }]
                    });
                }
            });
        },
        chart_customer_revenues: function () {
            $.ajax({
                url: '/report-customer-exist-all',
                type: 'get',
                data: {
                    year: $('#sltYearCustomer').val()
                },
                dataType: 'json',
                success: function (res) {
                    $("#revenues_customer_report_chart").empty();
                    var options = {
                        series: [{
                            name: 'Tổng tiền',
                            data: res.revenue
                        }, {
                            name: 'Tổng tiền (bao gồm VAT)',
                            data: res.revenue_vat
                        }],
                        chart: {
                            type: 'bar',
                            height: 570,
                            toolbar: {
                                show: false
                            },
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '55%',
                                endingShape: 'rounded'
                            },
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        xaxis: {
                            categories: res.month_report,
                        },
                        yaxis: {
                            labels: {
                                formatter: function (value) {
                                    if (value < 1000) {
                                        return accounting.formatMoney(value, "đ", 0, ".", ",", "%v%s");
                                    }
                                    else if (value >= 1000 && value < 1000000) {
                                        return accounting.formatMoney(value / 1000, " K", 0, ".", ",", "%v%s");
                                    }
                                    else if (value >= 1000000 && value < 1000000000) {
                                        return accounting.formatMoney(value / 1000000, " M", 0, ".", ",", "%v%s");
                                    }
                                    else if (value >= 1000000000) {
                                        return accounting.formatMoney(value / 1000000000, " B", 0, ".", ",", "%v%s");
                                    }
                                }
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function (value) {
                                    return accounting.formatMoney(value, "đ", 0, ".", ",", "%v%s");
                                }
                            },
                            x: {
                                formatter: function (value) {
                                    return 'Tháng ' + value;
                                }
                            }
                        }
                    };
                    var chart = new ApexCharts(document.querySelector("#revenues_customer_report_chart"), options);
                    chart.render();
                    $('#tblReportCustomerRevenue').bootstrapTable('destroy');
                    $('#tblReportCustomerRevenue').bootstrapTable({
                        data: res.data,
                        columns: [{
                            title: "Tháng",
                            align: 'center',
                            valign: 'left',
                            formatter: function (value, row, index) {
                                return row.MONTH_REPORT;
                            }
                        },
                        {
                            title: "Tổng doanh thu",
                            align: 'center',
                            valign: 'left',
                            formatter: function (value, row, index) {
                                return accounting.formatMoney(row.REVENUE, "đ", 0, ".", ",", "%v%s");
                            }
                        },
                        {
                            title: "Tổng doanh thu (bao gồm VAT)",
                            align: 'center',
                            valign: 'left',
                            formatter: function (value, row, index) {
                                return accounting.formatMoney(row.REVENUE_VAT, "đ", 0, ".", ",", "%v%s");
                            }
                        }]
                    });
                }
            });
        },
        report_growth: function () {
            $('#tblReportCustomerGrowth').bootstrapTable('destroy');
            $('#tblReportCustomerGrowth').bootstrapTable({
                url: '/report-list-growth',
                queryParams: function (p) {
                    var param = $.extend(true, {
                        offset: p.offset,
                        limit: p.limit,
                        user_id: $('#sltCreatedBy').val(),
                        start_date: $('#txtStartDate').val(),
                        end_date: $('#txtEndDate').val()
                    }, p);
                    return param;
                },
                destroy: true,
                striped: true,
                sidePagination: 'client',
                pagination: true,
                paginationHAlign: 'right',
                paginationVAlign: "bottom",
                search: false,
                undefinedText: "N/A",
                pageSize: 10,
                pageList: [10, 50, 100],
                columns: [
                    {
                        title: "Thuê bao",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            var name = `<div>Họ tên: ${row.FIRSTNAME} ${row.LAST_NAME}</div>`;
                            var phone = `<div>Thuê bao: ${row.PHONE}</div>`;
                            var html = name + phone;
                            return html;
                        }
                    },
                    {
                        title: "Doanh thu",
                        align: 'center',
                        valign: 'left',
                        formatter: function (value, row, index) {
                            return accounting.formatMoney(row.PACKAGE_PRICE, " đ", 0, ".", ",", "%v%s");
                        }
                    }
                ],
                formatNoMatches: function () {
                    return 'Chưa có dữ liệu';
                },
            })
        }
    }
});
$(document).ready(function () {
    list_report.init();
});

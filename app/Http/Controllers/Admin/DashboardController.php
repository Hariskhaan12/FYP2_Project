<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\DashboardService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    use ResponseTrait;
    public $dashboardService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dashboardService->allTransactionList();
        }
        $data['pageTitle'] = __('Dashboard');
        $data['activeDashboard'] = 'active';
        $dashboardService = new DashboardService();
        $data['totalAlumni'] = $dashboardService->totalAlumni();
        $data['currentMember'] = $dashboardService->currentMember();
        $data['totalUpcomingEvent'] = $dashboardService->totalUpcomingEvent();
        $data['memberThisMonth'] = $dashboardService->dashboardService();
        $data['transactionThisMonth'] = $dashboardService->transactionThisMonth();
        $data['chart'] = $dashboardService->dashboardDailyMembershipPaymentChart();
        $d = array();
        $preDateCount = 15;
        for ($i = 0; $i <=  $preDateCount; $i++) {
            $dateval = date("M d", strtotime('-' . $i . ' days'));
            $d[] = $dateval;
            if(in_array($dateval,$data['chart']['days'])){
                $chartPrice[] = $data['chart']['price'][$dateval] ;
            }
            else{
                $chartPrice[] = 0;
            }
        }
        $data['chartPrice'] = json_encode(array_reverse($chartPrice));
        $data['dayList'] = json_encode(array_reverse($d));
        $topEventTickets = $dashboardService->dashboardTopEventTicketChart();
        $data['totalTickets'] = json_encode($topEventTickets['totalTicket']);
        $data['eventNames'] = json_encode($topEventTickets['eventName']);
        return view('admin.dashboard', $data);
    }

}

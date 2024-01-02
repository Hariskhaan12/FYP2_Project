<?php


namespace App\Http\Services;

use App\Models\News;
use App\Models\Post;
use App\Models\User;
use App\Models\Event;
use App\Models\Alumni;
use App\Models\EventTicket;
use App\Models\Notice;
use App\Models\JobPost;
use App\Models\Transaction;
use App\Traits\ResponseTrait;
use App\Models\UserMembershipPlan;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    use ResponseTrait;

    public function getUpcomingEvent(){
        $upcomingEvents = Event::where('date', '>', now())->orderBy('date', 'ASC')->where('status', STATUS_ACTIVE)->with('category')->limit(2)->get();
        return $this->success($upcomingEvents);
    }

    public function getLatestJobs(){
        $latestJobs = JobPost::orderBy('application_deadline', 'DESC')->where('status', STATUS_ACTIVE)->limit(2)->get();
        return $this->success($latestJobs);
    }

    public function getLatestNotice(){
        $latestNotices = Notice::orderBy('id', 'DESC')->where('status', STATUS_ACTIVE)->limit(2)->get();
        return $this->success($latestNotices);
    }

    public function getLatestNews(){
        $latestNews = News::orderBy('id', 'DESC')->where('status', STATUS_ACTIVE)->with(['category', 'author'])->limit(2)->get();
        return $this->success($latestNews);
    }

    public function getMorePost($request){
        $data['posts'] = Post::orderBy('id', 'DESC')->where('status', STATUS_ACTIVE)->with(['comments', 'likes:id', 'author', 'media.file_manager'])->withCount('replies')->paginate(4);
        $response['html'] = View::make('alumni.partials.post', $data)->render();
        return $this->success($response);
    }

    public function totalAlumni()
    {
        return Alumni::count();
    }

    public function currentMember()
    {
        return User::whereHas('currentMembership')->count();
    }

    public function totalUpcomingEvent()
    {
        return Event::where('date', '>', now())->orderBy('date', 'ASC')->where('status', STATUS_ACTIVE)->count();
    }

    public function dashboardService()
    {
        return UserMembershipPlan::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();
    }

    public function transactionThisMonth()
    {
        return Transaction::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('amount');
    }

    public function allTransactionList()
    {
        $transaction = Transaction::with('user')->orderBy('id','DESC');
        return datatables($transaction)
            ->addColumn('name', function ($data) {
                return htmlspecialchars($data->user->name);
            })
            ->addColumn('amount', function ($data) {
                return showPrice($data->amount);
            })
            ->addColumn('created_at', function ($data) {
                return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('jS F, h:i:s A');
            })
            ->rawColumns(['created_at', 'name', 'amount'])
            ->make(true);
    }

    public function dashboardDailyMembershipPaymentChart()
    {
        $first_day_of_the_current_month = Carbon::now()->startOfMonth();
        $current_month_days_count = $first_day_of_the_current_month->diff(now());
        $last_day_of_the_current_month = Carbon::now()->endOfMonth();
        $transactionData = Transaction::whereBetween('payment_time', [$first_day_of_the_current_month, $last_day_of_the_current_month])
        ->groupBy(DB::raw("DATE_FORMAT(payment_time,'%Y-%m-%d')"))
        ->orderBy('payment_time','desc')
        ->select(DB::raw("DATE_FORMAT(payment_time,'%b %d') as day, sum(amount) as total"))
        ->get();
        $price = [];
        foreach($transactionData as $rows){
            $price[$rows->day] = $rows->total;
        }
        $membershipChartData['mainData'] = $transactionData;
        $membershipChartData['days'] = $transactionData->pluck('day')->toArray();
        $membershipChartData['price'] = $price;
        $membershipChartData['current_month_days_count'] = $current_month_days_count->d;


        return $membershipChartData;
    }
    public function dashboardTopEventTicketChart()
    {
        $eventTickets = EventTicket::join('events', 'events.id', '=', 'event_tickets.event_id')
        ->groupBy('event_id')
        ->select('events.title as event_name',DB::raw("count(ticket_number) as total_ticket"))
        ->orderBy('total_ticket','desc')
        ->skip(0)->take(5)->get();
       $eventTicketData['mainData'] = $eventTickets;
        $eventTicketData['totalTicket'] =  $eventTickets->pluck('total_ticket')->toArray();
        $eventTicketData['eventName'] =  $eventTickets->pluck('event_name')->toArray();
        return $eventTicketData;
    }
}

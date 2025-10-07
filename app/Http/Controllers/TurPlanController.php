<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\District;
use App\Models\TurPlan;
use App\Models\Region;
use Carbon\Carbon;


class TurPlanController extends Controller
{
    public $user_id;

    public function __construct(){
        $this->user_id = auth()->user()->id;
        $this->user = User::find($this->user_id);
        $this->role = $this->user->role;
    }

    public function index(){
        if ($this->role == User::ROLE_AGENT){
            $currentDate = Carbon::now();

            if ($currentDate->day == 25) {
                $month = $currentDate->addMonth()->month;
            } else {
                $month = $currentDate->month;
            }

            $month_name = config('month.months')[$month];

            $list =$this->getturplans($month);

            return view('user.turplan', compact('month', 'month_name', 'list'));
        } else {
            $regionIds = json_decode($this->user->additional, true);

            $this->users = Region::whereIn('id', $regionIds)
                    ->with(['users' => function ($query) {
                        $query->where('role', User::ROLE_AGENT)->orderBy('name', 'asc');
                    }])
                    ->orderBy('name', 'asc')->get();

            return view('user.turplanmanager', [
                'pagename' => "Turplanini ko'rmoqchi bo'lgan agentni tanlang",
                'users'    => $this->users,
                'back_route' => route('user.main.index')
            ]);
        }
    }

    public function edit($month_id){
        $currentDate = Carbon::now();

        if ($currentDate->day == 25) {
            $month = $currentDate->addMonth()->month;
        } else {
            $month = $currentDate->month;
        }

        $month_name = config('month.months')[$month];

        return view('user.turplanedit', [
            'list' => $this->getturplans($month_id),
            'api_token' => session('api_token'),
            'user_id' => $this->user_id,
            'month' => $month_id,
            'month_name' => $month_name
        ]);
    }

    public function getturplans($month, $year = null, $user_id = null){
        if (is_null($year)) $year = date('Y');
        if ($user_id === null) $user_id = $this->user_id;

        $turplans = TurPlan::where('user_id', $user_id)->where('year', $year)->where('month', $month)->get();

        $turplan_amount = [];
        foreach ($turplans as $turplan){
            $turplan_amount[$turplan['pharmacy_id']] = $turplan['amount'];
        }

        $districts = District::with('pharmacies')->where('user_id', $user_id)->get();

        $list = [];
        foreach ($districts as $district){
            $list2 = [];
            $summ = 0;
            foreach ($district->pharmacies as $pharmacy){
                $id = $pharmacy->id;
                $amount = isset($turplan_amount[$id]) ? $turplan_amount[$id] : 0;
                $summ += $amount;
                $list2[] = ['name' => $pharmacy->name, 'id' => $id, 'amount' => $amount];
            }
            $list[] = ['name' => $district->name, 'amount' => $summ, 'pharmacies' => $list2];
        }

        return $list;
    }

    public function usershow(string $id, string $month_id = null){
        if ($month_id == null){
            $currentDate = Carbon::now();

            if ($currentDate->day == 25) {
                $month = $currentDate->addMonth()->month;
            } else {
                $month = $currentDate->month;
            }

            $month_name = config('month.months')[$month];

            $list =$this->getturplans($month, null, $id);

            $user = User::find($id);
            $pagename = $user->name.' '.$user->lastname;

            return view('user.turplanmanager', compact('month', 'month_name', 'list', 'pagename'));
        } else {

        }
    }
}

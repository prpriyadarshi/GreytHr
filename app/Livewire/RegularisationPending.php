<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EmployeeDetails;
use App\Models\RegularisationDates;
use App\Models\RegularisationNew1;
use Carbon\Carbon;
use App\Models\Regularisations;
class RegularisationPending extends Component
{
    public $data;
    
    public $regularisationrequest;

    public $ManagerId;
    public $ManagerName;

    public $totalEntries;
    public $regularisationEntries;

    public $empDetails;
    public $id;
    public function mount($id)
    {
        $this->regularisationrequest = RegularisationDates::with('employee')->find($id);

        $this->empDetails = EmployeeDetails::join('company_shifts', function($join) {
            $join->on('employee_details.shift_type', '=', 'company_shifts.shift_name')
                 ->whereRaw('JSON_CONTAINS(employee_details.company_id, CONCAT(\'"\', company_shifts.company_id, \'"\'))');
        })
        ->where('employee_details.emp_id', $this->regularisationrequest->emp_id)
        ->select('employee_details.*', 'company_shifts.shift_name', 'company_shifts.shift_start_time', 'company_shifts.shift_end_time')
        ->first();
    
        
        $this->ManagerId=$this->regularisationrequest->employee->manager_id;
        $this->ManagerName=EmployeeDetails::select('first_name','last_name')->where('emp_id',$this->ManagerId)->first();
        $this->regularisationEntries = json_decode($this->regularisationrequest->regularisation_entries, true);
        $this->regularisationEntries = array_reverse($this->regularisationEntries);
        $this->totalEntries = count($this->regularisationEntries);
    }
 
    public function render()
    {
        $this->data = RegularisationDates::where('is_withdraw',0)->get();
        $today = Carbon::today();
   
        return view('livewire.regularisation-pending',['regularisationrequest'=>$this->regularisationrequest,'today'=>$today]);
    }
}

<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\EmployeeDetails;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Models\LeaveRequest;
use App\Models\EmployeeLeaveBalances;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ReportManagement extends Component
{
    public $currentSection = " ";

    public $searching = 0;

    public $sno;
    public $employeeType="";
    public $leaveType;
    public $search;
    public $fromDate;
    public $sortBy;
    public $toDate;
    public $hireDate;

    public $notFound;

    public $peoples;
    public $transactionType='all';

    public $employees;
    public $dateErrorMessage;
    public $leaveBalance = [];
    public function showContent($section)
    {

        $this->currentSection = $section;
        $this->resetFields();
    }
    public function updateLeaveType()
    {
        $this->leaveType = $this->leaveType;
    }

    public function  updateSortBy()
    {
        $this->sortBy = $this->sortBy;
    }

    public function  updateEmployeeType()
    {
        $this->employeeType = $this->employeeType;
    }

    public function closeShiftSummaryReport()
    {
        $this->currentSection = '';
    }
    public function closeAbsentReport()
    {
        $this->currentSection = '';
    }
    public function showContent1($section)
    {

        $this->currentSection = $section;
    }
    public function searchfilter()
    {
        $this->searching = 1;
    }
    public function close()
    {
        $this->currentSection = '';
        $this->resetErrorBag();
        $this->resetFields();
    }

    public function updateFromDate()
    {
        $this->fromDate = $this->fromDate;
    }
    public function updateToDate()
    {
        $this->toDate = $this->toDate;
    }



    public $myTeam;
    protected $rules = [
        'fromDate' => 'required|date',
        'toDate' => 'required|date|after_or_equal:fromDate',
        'leaveType' => 'required',
        'employeeType' => 'required',
    ];
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->resetErrorBag($propertyName); // Clear errors for the updated property
    }

    public function resetFields()
    {
        $this->fromDate = null;
        $this->toDate = null;
        $this->leaveType = null;
        $this->employeeType = null;
        $this->leaveBalance = [];
    }

    public function downloadLeaveAvailedReportInExcel()
    {
        $this->validate([
            'fromDate' => 'required|date',
            'toDate' => 'required|date|after_or_equal:fromDate',
            'leaveType' => 'required',
            'employeeType' => 'required',
        ], [
            'fromDate.required' => 'From date is required.',
            'toDate.required' => 'To date is required.',
            'toDate.after_or_equal' => 'To date must be a date after or equal to the from date.',
            'leaveType.required' => 'Leave type is required.',
            'employeeType.required' => 'Employee type is required.',
        ]);

        $companyId = Auth::user()->company_id;
        $this->peoples = EmployeeDetails::with('starredPeople')
            ->where('company_id', $companyId)
            ->where('employee_status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $this->sno = 0;
        $employeeId = auth()->guard('emp')->user()->emp_id;
        $managerDetails = EmployeeDetails::where('emp_id', $employeeId)->first();

        $query = LeaveRequest::join('employee_details', 'leave_applications.emp_id', '=', 'employee_details.emp_id')
            ->where('leave_type', $this->leaveType)
            ->select('leave_applications.*', 'employee_details.first_name', 'employee_details.last_name')
            ->orderBy('leave_applications.emp_id', 'asc');

        if ($this->employeeType == 'past') {
            $query->where(function ($query) {
                $query->where('employee_details.employee_status', 'resigned')
                    ->orWhere('employee_details.employee_status', 'terminated');
            });
        } else {
            $query->where('employee_details.employee_status', $this->employeeType);
        }

        // Apply date filters if set
        if ($this->fromDate && $this->toDate) {
            $fromDate = Carbon::parse($this->fromDate)->startOfDay();
            $toDate = Carbon::parse($this->toDate)->endOfDay();
            $query->whereBetween('leave_applications.from_date', [$fromDate, $toDate]);
        }

        $this->myTeam = $query->get();

        $data = [
            ['List of Employees'],
            ['Sl No.', 'Employee No', 'Name', 'Manager No', 'Manager Name', 'Leave Type', 'From', 'To', 'Days', 'Reason', 'Applied date', 'Approved Date', 'Approver']
        ];

        foreach ($this->myTeam as $leaveAvailedRep) {
            $fromDate = Carbon::parse($leaveAvailedRep->from_date)->format('d-m-Y');
            $toDate = Carbon::parse($leaveAvailedRep->to_date)->format('d-m-Y');
            $createdAt = Carbon::parse($leaveAvailedRep->created_at)->format('d-m-Y');
            $updatedAt = Carbon::parse($leaveAvailedRep->updated_at)->format('d-m-Y');
            $leaveDays = $leaveAvailedRep->calculateLeaveDays(
                $leaveAvailedRep->from_date,
                $leaveAvailedRep->from_session,
                $leaveAvailedRep->to_date,
                $leaveAvailedRep->to_session
            );
            $data[] = [
                ++$this->sno,
                $leaveAvailedRep->emp_id,
                $leaveAvailedRep->first_name . ' ' . $leaveAvailedRep->last_name,
                $managerDetails->emp_id,
                $managerDetails->first_name . ' ' . $managerDetails->last_name,
                $leaveAvailedRep->leave_type,
                $fromDate,
                $toDate,
                $leaveDays,
                $leaveAvailedRep->reason,
                $createdAt,
                $updatedAt,
                $managerDetails->first_name . ' ' . $managerDetails->last_name
            ];
        }

        $filePath = storage_path('app/leave_availed_report.xlsx');
        $writer = SimpleExcelWriter::create($filePath);

        foreach ($data as $row) {
            $writer->addRow($row);
        }

        return response()->download($filePath, 'leave_availed_report.xlsx');
    }

    public $filteredEmployees;

    public function searchfilterleave()
    {
        $this->searching = 1;
        $loggedInEmpId = Auth::guard('emp')->user()->emp_id;
        $employees = EmployeeDetails::where('manager_id', $loggedInEmpId)->get();
        $nameFilter = $this->search;

        $filteredEmployees = $employees->filter(function ($employee) use ($nameFilter) {
            return stripos($employee->first_name, $nameFilter) !== false ||
                stripos($employee->last_name, $nameFilter) !== false ||
                stripos($employee->emp_id, $nameFilter) !== false ||
                stripos($employee->job_title, $nameFilter) !== false ||
                stripos($employee->city, $nameFilter) !== false ||
                stripos($employee->state, $nameFilter) !== false;
        });

        if ($filteredEmployees->isEmpty()) {
            $this->notFound = true;
        } else {
            $this->notFound = false;
        }
    }

public  function updateTransactionType($event){
    $this->transactionType=$event;
//   dd($this->transactionType);

}


    public function dayWiseLeaveTransactionReport()
    {

        $this->validate([
            'fromDate' => 'required|date',
            'toDate' => 'required|date|after_or_equal:fromDate',
            'transactionType' => 'required',
            'employeeType' => 'required',
        ],
         [
            'fromDate.required' => 'From date is required.',
            'toDate.required' => 'To date is required.',
            'toDate.after_or_equal' => 'To date must be a date after or equal to the from date.',
            'transactionType.required' => 'Leave type is required.',
            'employeeType.required' => 'Employee type is required.',
        ]);

        // dd($this->fromDate,$this->toDate,$this->transactionType,$this->employeeType);

        $loggedInEmpId = Auth::guard('emp')->user()->emp_id;

        $query = LeaveRequest::select(
            DB::raw('DATE(from_date) as date_only'), // Extract date part only
            DB::raw('count(*) as total_requests'), // Aggregate function to count requests
            'leave_applications.emp_id',
            'employee_details.first_name',
            'employee_details.last_name',
            'leave_applications.leave_type',
            'leave_applications.from_date as leave_from_date',
            'leave_applications.to_date as leave_to_date',
            'leave_applications.reason',
            'leave_applications.from_session',
            'leave_applications.to_session',
            'leave_applications.status',
        )
            // ->where('leave_applications.status', 'approved')
            ->where(function ($query) use ($loggedInEmpId) {
                $query->whereJsonContains('applying_to', [['manager_id' => $loggedInEmpId]])
                    ->orWhereJsonContains('cc_to', [['emp_id' => $loggedInEmpId]]);
            })
            ->where(function ($query) {
                $query->whereBetween('from_date', [$this->fromDate, $this->toDate])
                    ->orWhereBetween('to_date', [$this->fromDate, $this->toDate])
                    ->orWhere(function ($query) {
                        $query->where('from_date', '<=', $this->toDate)
                            ->where('to_date', '>=', $this->fromDate);
                    });
            })
            ->join('employee_details', 'leave_applications.emp_id', '=', 'employee_details.emp_id')
            ->groupBy('date_only', 'leave_applications.emp_id', 'employee_details.first_name', 'employee_details.last_name', 'leave_applications.leave_type', 'leave_applications.from_date', 'leave_applications.to_date', 'leave_applications.reason', 'leave_applications.from_session', 'leave_applications.to_session','leave_applications.status'); // Group by the date-only field

            if ($this->transactionType !== 'all') {
                $query->where('leave_applications.status', $this->transactionType);
            }

           if($this->employeeType=='active'){
            $query->where('employee_details.employee_status', $this->employeeType);
           }else{
            $query->where(function ($query) {
                $query->where('employee_details.employee_status', 'resigned')
                    ->orWhere('employee_details.employee_status', 'terminated');
            });
           }
         

            $query = $query->orderBy('date_only', 'asc')
            ->get();
   

        // Aggregate data by date
        $aggregatedData = $query->groupBy('date_only')->map(function ($group) {
            return [
                'date' => Carbon::parse($group->first()->date_only)->format('d M Y'),
                // 'total_requests' => $group->sum('total_requests'),
                'details' => $group->map(function ($item) {
                    $leaveRequest = new LeaveRequest();
                    $leaveDays = $leaveRequest->calculateLeaveDays(
                        $item->leave_from_date,
                        $item->from_session,
                        $item->leave_to_date,
                        $item->to_session
                    );
                    return [
                        'emp_id' => $item->emp_id,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                        'leave_type' => $item->leave_type,
                        'leave_from_date' => $item->leave_from_date,
                        'leave_to_date' => $item->leave_to_date,
                        'reason' => $item->reason,
                        'from_session' => $item->from_session,
                        'to_session' => $item->to_session,
                        'leave_days' => $leaveDays,
                        'leave_status'=>$item->status,
                    ];
                })
            ];
        });
        // dd( $aggregatedData);

        $employeeDetails = EmployeeDetails::where('emp_id', $loggedInEmpId)->first();
        $pdf = Pdf::loadView('daywiseLeaveTransactionReportPdf', [
            'employeeDetails' => $employeeDetails,
            'leaveTransactions' => $aggregatedData,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
        ]);

        return response()->streamDownload(function() use($pdf){
            echo $pdf->stream();
        }, 'Daywise_leave_transactions.pdf');
    }
    public function leaveBalanceAsOnADayReport()
    {
        // Validate the 'toDate' input
        $this->validate([
            'toDate' => 'required|date',
        ], [
            'toDate.required' => 'Date is required.',
        ]);
    
        // Check if any employees are selected
        if (empty($this->leaveBalance)) {
            return redirect()->back()->with('error', 'Select at least one employee detail');
        } else {
            // Fetch employee details for selected employees
         
            $loggedInEmpId = Auth::guard('emp')->user()->emp_id;
            $employees = EmployeeDetails::where('manager_id', $loggedInEmpId)
            ->whereIn('emp_id', $this->leaveBalance)
            ->select('emp_id', 'first_name', 'last_name')
            ->get();
           

        // Fetch leave balances and join with employee details
        $leaveBalances = EmployeeLeaveBalances::select(
            'employee_leave_balances.emp_id',
            DB::raw('COALESCE(JSON_EXTRACT(leave_balance, "$.Casual Leave"), 0) AS casual_leave_balance'),
            DB::raw('COALESCE(JSON_EXTRACT(leave_balance, "$.Casual Leave Probation"), 0) AS casual_leave_probation_balance'),
            DB::raw('COALESCE(JSON_EXTRACT(leave_balance, "$.Loss Of Pay"), 0) AS loss_of_pay_balance'),
            DB::raw('COALESCE(JSON_EXTRACT(leave_balance, "$.Sick Leave"), 0) AS sick_leave_balance')
        )
        ->whereIn('employee_leave_balances.emp_id', $this->leaveBalance)
        ->where('employee_leave_balances.status', 'Granted')
        ->whereDate('employee_leave_balances.to_date', '<=', $this->toDate)
        ->get()
        ->keyBy('emp_id');
      

        // Combine employee details with their leave balances
        $combinedData = $employees->map(function ($employee) use ($leaveBalances) {
            $leaveBalance = $leaveBalances->get($employee->emp_id);
            return [
                'emp_id' => $employee->emp_id,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'casual_leave_balance' => $leaveBalance ? $leaveBalance->casual_leave_balance : 0,
                'casual_leave_probation_balance' => $leaveBalance ? $leaveBalance->casual_leave_probation_balance : 0,
                'loss_of_pay_balance' => $leaveBalance ? $leaveBalance->loss_of_pay_balance : 0,
                'sick_leave_balance' => $leaveBalance ? $leaveBalance->sick_leave_balance : 0,
                
                // Add more leave types if needed
            ];
        });



                $employeeDetails = EmployeeDetails::where('emp_id', $loggedInEmpId)->first();
                
                $pdf = Pdf::loadView('leaveBalanceAsOnDayReport', [
                    'employeeDetails' => $employeeDetails,
                    'leaveTransactions' => $combinedData,
                    'fromDate' => $this->fromDate,
                    'toDate' => $this->toDate,
                ]);
            
                return response()->streamDownload(function() use($pdf) {
                    echo $pdf->stream();
                }, 'leave_balance_as_on_a_day.pdf');

        }
    }
    


   
    public function downloadLeaveTransactionReport()
    {
        $this->validate([
            'fromDate' => 'required|date',
            'toDate' => 'required|date|after_or_equal:fromDate',
            'transactionType' => 'required',
            'employeeType' => 'required',
        ], [
            'fromDate.required' => 'From date is required.',
            'toDate.required' => 'To date is required.',
            'toDate.after_or_equal' => 'To date must be a date after or equal to the from date.',
            'transactionType.required' => 'Leave type is required.',
            'employeeType.required' => 'Employee type is required.',
        ]);
    
        $loggedInEmpId = Auth::guard('emp')->user()->emp_id;
    
        $query = LeaveRequest::select(
            DB::raw('DATE(from_date) as date_only'),
            DB::raw('count(*) as total_requests'),
            'leave_applications.emp_id',
            'employee_details.first_name',
            'employee_details.last_name',
            'leave_applications.leave_type',
            'leave_applications.from_date as leave_from_date',
            'leave_applications.to_date as leave_to_date',
            'leave_applications.reason',
            'leave_applications.created_at',
            'leave_applications.from_session',
            'leave_applications.to_session',
            'leave_applications.status'
        )
        ->join('employee_details', 'leave_applications.emp_id', '=', 'employee_details.emp_id')
        ->when($this->leaveType && $this->leaveType != 'all', function ($query) {
           
            $leaveTypes = [
                'lop' => 'Loss of Pay',
                'casual_leave' => 'Casual Leave',
                'sick' => 'Sick Leave',
                'petarnity' => 'Petarnity Leave',
                'casual_leave_probation' => 'Casual Leave Probation',
                'maternity' => 'Maternity Leave',
                'marriage_leave' => 'Marriage Leave',
            ];

            if (array_key_exists($this->leaveType, $leaveTypes)) {
                $query->where('leave_applications.leave_type', $leaveTypes[$this->leaveType]);
            }
        })
        ->where(function ($query) use ($loggedInEmpId) {
            $query->whereJsonContains('applying_to', [['manager_id' => $loggedInEmpId]])
                ->orWhereJsonContains('cc_to', [['emp_id' => $loggedInEmpId]]);
        })
        ->where(function ($query) {
            $query->whereBetween('from_date', [$this->fromDate, $this->toDate])
                ->orWhereBetween('to_date', [$this->fromDate, $this->toDate])
                ->orWhere(function ($query) {
                    $query->where('from_date', '<=', $this->toDate)
                        ->where('to_date', '>=', $this->fromDate);
                });
        });
    
        if ($this->transactionType === 'availed' || $this->transactionType === 'all') {
            $query->where('leave_applications.status', 'approved');
        }
    
        if ($this->employeeType == 'active') {
            $query->where('employee_details.employee_status', $this->employeeType);
        } else {
            $query->where(function ($query) {
                $query->where('employee_details.employee_status', 'resigned')
                    ->orWhere('employee_details.employee_status', 'terminated');
            });
        }
    
        $query = $query->groupBy(
            'date_only',
            'leave_applications.emp_id',
            'employee_details.first_name',
            'employee_details.last_name',
            'leave_applications.leave_type',
            'leave_applications.from_date',
            'leave_applications.to_date',
            'leave_applications.reason',
            'leave_applications.created_at',
            'leave_applications.from_session',
            'leave_applications.to_session',
            'leave_applications.status'
        )
        ->orderBy('date_only', 'asc')
        ->get();
    
       
    
        // Aggregate data by date
        $aggregatedData = $query->groupBy('date_only')->map(function ($group) {
            return [
                'date' => Carbon::parse($group->first()->date_only)->format('d M Y'),
                'details' => $group->map(function ($item) {
                    $leaveRequest = new LeaveRequest();
                    $leaveDays = $leaveRequest->calculateLeaveDays(
                        $item->leave_from_date,
                        $item->from_session,
                        $item->leave_to_date,
                        $item->to_session
                    );
                    return [
                        'emp_id' => $item->emp_id,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                        'leave_type' => $item->leave_type,
                        'leave_from_date' => $item->leave_from_date,
                        'leave_to_date' => $item->leave_to_date,
                        'reason' => $item->reason,
                        'created_at' => $item->created_at,
                        'from_session' => $item->from_session,
                        'to_session' => $item->to_session,
                        'leave_days' => $leaveDays,
                        'leave_status' => $item->status,
                    ];
                })
            ];
        });
    
        $employeeDetails = EmployeeDetails::where('emp_id', $loggedInEmpId)->first();
        $pdf = Pdf::loadView('leaveTransactionReportPdf', [
            'employeeDetails' => $employeeDetails,
            'leaveTransactions' => $aggregatedData,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
        ]);
    
        return response()->streamDownload(function() use($pdf) {
            echo $pdf->stream();
        }, 'leave_transactions_report.pdf');
    }
    
    public function downloadNegativeLeaveBalanceReport()
    {
        $this->validate([
            'toDate' => 'required|date',
            'employeeType' => 'required',
        ], [
            'toDate.required' => 'Date is required.',
            'employeeType.required' => 'Employee type is required.',
        ]);
    
        $loggedInEmpId = Auth::guard('emp')->user()->emp_id;
    
        $query = LeaveRequest::select(
            DB::raw('DATE(to_date) as date_only'),
            DB::raw('count(*) as total_requests'),
            'leave_applications.emp_id',
            'employee_details.first_name',
            'employee_details.last_name',
            'employee_details.hire_date',
            'employee_details.job_role',
            'leave_applications.leave_type',
            'leave_applications.from_date as leave_from_date',
            'leave_applications.to_date as leave_to_date',
            'leave_applications.from_session',
            'leave_applications.to_session',
            'leave_applications.status'
        )
        ->join('employee_details', 'leave_applications.emp_id', '=', 'employee_details.emp_id')
        ->when($this->leaveType && $this->leaveType != 'all', function ($query) {
           
            $leaveTypes = [
                'lop' => 'Loss of Pay',
                'casual_leave' => 'Casual Leave',
                'sick' => 'Sick Leave',
                'petarnity' => 'Petarnity Leave',
                'casual_leave_probation' => 'Casual Leave Probation',
                'maternity' => 'Maternity Leave',
                'marriage_leave' => 'Marriage Leave',
            ];

            if (array_key_exists($this->leaveType, $leaveTypes)) {
                $query->where('leave_applications.leave_type', $leaveTypes[$this->leaveType]);
            }
        })
        ->where(function ($query) use ($loggedInEmpId) {
            $query->whereJsonContains('applying_to', [['manager_id' => $loggedInEmpId]])
                ->orWhereJsonContains('cc_to', [['emp_id' => $loggedInEmpId]]);
        })
        ->where('to_date', '<=', $this->toDate);

       
    
    
        if ($this->employeeType == 'active') {
            $query->where('employee_details.employee_status', $this->employeeType);
        } else {
            $query->where(function ($query) {
                $query->where('employee_details.employee_status', 'resigned')
                    ->orWhere('employee_details.employee_status', 'terminated');
            });
        }
       
       
    
        $query = $query->groupBy(
            'date_only',
            'leave_applications.emp_id',
            'employee_details.first_name',
            'employee_details.last_name',
            'employee_details.hire_date',
            'employee_details.job_role',
            'leave_applications.leave_type',
            'leave_applications.to_date',
            'leave_applications.from_date',
            'leave_applications.from_session',
            'leave_applications.to_session',
            'leave_applications.status',
        )
        ->orderBy('date_only', 'asc')
        ->get();

 
       
    
        // Aggregate data by date
        $aggregatedData = $query->groupBy('date_only')->map(function ($group) {
            return [
                'date' => Carbon::parse($group->first()->date_only)->format('d M Y'),
                'details' => $group->map(function ($item) {
                    $leaveRequest = new LeaveRequest();
                    $leaveDays = $leaveRequest->calculateLeaveDays(
                        $item->leave_from_date,
                        $item->from_session,
                        $item->leave_to_date,
                        $item->to_session
                    );
                    return [
                        'emp_id' => $item->emp_id,
                        'first_name' => $item->first_name,
                        'last_name' => $item->last_name,
                        'hire_date' => $item->hire_date,
                        'job_role' => $item->job_role,
                        'leave_type' => $item->leave_type,
                        'leave_from_date' => $item->leave_from_date,
                        'leave_to_date' => $item->leave_to_date,
                        'from_session' => $item->from_session,
                        'to_session' => $item->to_session,
                        'leave_days' => $leaveDays,
                        'leave_status' => $item->status,
                    ];
                })
            ];
        });


    
        $employeeDetails = EmployeeDetails::where('emp_id', $loggedInEmpId)->first();
        $pdf = Pdf::loadView('negativeLeaveBalanceReportPdf', [
            'employeeDetails' => $employeeDetails,
            'leaveTransactions' => $aggregatedData,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
        ]);
    
        return response()->streamDownload(function() use($pdf) {
            echo $pdf->stream();
        }, 'negative_leave_balance_report.pdf');
    }

    public function render()
    {
        $loggedInEmpId = Auth::guard('emp')->user()->emp_id;

        $search = '';
        if ($this->searching == 1) {
            $this->employees = EmployeeDetails::where('manager_id', $loggedInEmpId)
            ->join('emp_parent_details', 'employee_details.emp_id', '=', 'emp_parent_details.emp_id')
            ->select('employee_details.*', 'emp_parent_details.*')
            ->where(function($query) use ($search) {
                $query->where('employee_details.first_name', 'like', '%' . $search . '%')
                      ->orWhere('employee_details.last_name', 'like', '%' . $search . '%')
                      ->orWhere('emp_parent_details.mother_occupation', 'like', '%' . $search . '%')
                      ->orWhere('emp_parent_details.father_occupation', 'like', '%' . $search . '%');
            })
            ->get();
        }
        else
        {
            $this->employees = EmployeeDetails::where('manager_id', $loggedInEmpId)
        ->join('emp_parent_details', 'employee_details.emp_id', '=', 'emp_parent_details.emp_id')
        ->select('employee_details.*', 'emp_parent_details.*')
        ->get();
        }

        // For Leave Balance On Day


        $this->employees = EmployeeDetails::where('manager_id', $loggedInEmpId)->select('emp_id', 'first_name', 'last_name')->get();
        if ($this->searching == 1) {
            $nameFilter = $this->search; // Assuming $this->search contains the name filter
            $this->filteredEmployees = $this->employees->filter(function ($employee) use ($nameFilter) {
                return stripos($employee->first_name, $nameFilter) !== false ||
                    stripos($employee->last_name, $nameFilter) !== false ||
                    stripos($employee->emp_id, $nameFilter) !== false ||
                    stripos($employee->job_title, $nameFilter) !== false ||
                    stripos($employee->city, $nameFilter) !== false ||
                    stripos($employee->state, $nameFilter) !== false;
            });


            if ($this->filteredEmployees->isEmpty()) {
                $this->notFound = true; // Set a flag indicating that the name was not found
            } else {
                $this->notFound = false;
            }
        } else {
            $this->filteredEmployees = $this->employees;
        }

        return view('livewire.report-management');
    }
}

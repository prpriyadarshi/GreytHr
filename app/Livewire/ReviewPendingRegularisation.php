<?php

namespace App\Livewire;

use App\Helpers\FlashMessageHelper;
use App\Mail\RegularisationApprovalMail;
use App\Mail\RegularisationCombinedMail;
use App\Mail\RegularisationRejectionMail;
use App\Models\EmployeeDetails;
use App\Models\RegularisationDates;
use App\Models\RegularisationNew1;
use App\Models\SwipeRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ReviewPendingRegularisation extends Component
{
    public $regularisationdescription;
    public $regularisationrequest;

    public $employeeEmailForApproval;
    public $regularisationEntries;
    public $ManagerId;
    public $ManagerName;
    public $id;

    public $totalEntries;

    public $remarks = [];

    public $employeeEmailForRejection;
   
    public $empid;
    public $empName;

    public $countofregularisations;
    public $employeeDetails;
    public $regularisationdescrip;
    public function mount($id,$count)
    {
       

        // Loop through each request and reject it
       
        $this->empid = Auth::guard('emp')->user()->emp_id;
        $this->empName = EmployeeDetails::where('emp_id', $this->empid)->first();
        $this->regularisationrequest = RegularisationDates::with('employee')->find($id);
        $subordinateEmpId=$this->regularisationrequest->emp_id;
        $this->employeeDetails = Employeedetails::where('emp_id', $subordinateEmpId)->first();
        $this->ManagerId=$this->regularisationrequest->employee->manager_id;
        
        $this->ManagerName=EmployeeDetails::select('first_name','last_name')->where('emp_id',$this->ManagerId)->first();
        $this->regularisationEntries = json_decode($this->regularisationrequest->regularisation_entries, true);
        $this->countofregularisations=$count;
        $this->totalEntries = count($this->regularisationEntries);
        Log::info('Remarks are: ' . json_encode($this->remarks));

    }
   
    public function approve($date)
    {
        // dd('approve'.$date);
      
        $remark = $this->remarks[$date] ?? null;

        // Log the remark for debugging purposes
        Log::info('Remark:', ['remark' => $remark]);

        // Find the entry with the matching date and update its status
        foreach ($this->regularisationEntries as &$entry) {
            if ($entry['date'] === $date) {
                $entry['status'] = 'approved';
                $entry['remark'] = $remark; // Optionally add the remark to the entry
                FlashMessageHelper::flashSuccess('Regularization Request Approved Successfully');
                
                break;
            }

        }

        // Save the updated regularisationEntries back to the database
        $this->regularisationrequest->regularisation_entries = json_encode($this->regularisationEntries);
        $this->regularisationrequest->save();
    }

    public function rejectAll($id)
    {
        
        $currentDateTime = Carbon::now();
        $item = RegularisationDates::find($id);
        if(empty($this->remarks))
        {

        }
        else
        {
            $item->approver_remarks=$this->remarks;
        }
        $item->status=3;
        $item->rejected_date = $currentDateTime; 
        $item->rejected_by=$this->ManagerName->first_name . ' ' . $this->ManagerName->last_name;
        $item->save();

        $this->countofregularisations--;
      
       
      
       
        $this->sendRejectionMail($id);
        FlashMessageHelper::flashSuccess('Regularisation Request rejected successfully');
        return redirect()->route('review');
   
    }
    public function sendRejectionMail($id)
    {
        
        $item = RegularisationDates::find($id); // Ensure you have the correct ID to fetch data
 
        $regularisationEntriesforRejection = json_decode($item->regularisation_entries, true); // Decode the JSON entries
        
    $employee = EmployeeDetails::where('emp_id', $item->emp_id)->first();
    // Prepare the HTML table
    
    $this->employeeEmailForRejection=$employee->email;
  
    $details = [
     
        'regularisationRequests'=>$regularisationEntriesforRejection,
        'sender_id'=>$employee->emp_id,
        'sender_remarks'=>$item->employee_remarks,
        'receiver_remarks'=>$item->approver_remarks,
       
    ];
 
 
    // Send email to manager
      Mail::to($this->employeeEmailForRejection)->send(new RegularisationRejectionMail($details));
    }
    public function reject($date)
    {
        // dd('reject'.$date);
        $remark = $this->remarks[$date] ?? null;

        // Log the remark for debugging purposes
        Log::info('Remark:', ['remark' => $remark]);

        // Find the entry with the matching date and update its status
        foreach ($this->regularisationEntries as &$entry) {
            if ($entry['date'] === $date) {
                $entry['status'] = 'rejected';
               
                $entry['remark'] = $remark; // Optionally add the remark to the entry
                FlashMessageHelper::flashSuccess('Regularization Request Rejected Successfully');
                break;
            }
        }

        // Save the updated regularisationEntries back to the database
        $this->regularisationrequest->regularisation_entries = json_encode($this->regularisationEntries);
        $this->regularisationrequest->save();
    }
   
    public function submitRegularisation()
    {
        Log::info('Method `submitRegularisation` started.');
     
        // Check if regularisation request exists
        if ($this->regularisationrequest) {
            Log::info('Regularisation request found.', ['request' => $this->regularisationrequest]);
    
            // Validation
            $validationErrors = [];
            foreach ($this->regularisationEntries as $entry) {
                Log::info('Validating entry.', ['entry' => $entry]);
    
                $date = $entry['date'];
                
                // Check if status is not set
                if (!isset($entry['status']) || empty($entry['status'])) {
                    $validationErrors[] = "Status (approve/reject) must be selected for the date: $date.";
                    Log::warning("Validation error: Status missing for date $date.");
                }
            
                // Check if remarks are empty or contain only whitespace
                if (!isset($entry['remark']) || empty(trim($entry['remark']))) {
                    $validationErrors[] = "Remarks are required for the date: $date.";
                    Log::warning("Validation error: Remarks missing or invalid for date $date.");
                }
                Log::info('Validating entry.', ['entry' => $entry]);
            }
    
            if (!empty($validationErrors)) {
                Log::info('Validation failed. Errors:', ['errors' => $validationErrors]);
                foreach ($validationErrors as $error) {
                    FlashMessageHelper::flashError($error);
                }
                Log::info('Exiting method due to validation failure.');
                return;
            }
    
            Log::info('Validation passed.');
    
            // Update the regularisation request
            Log::info('Updating regularisation request.');
            $this->regularisationrequest->status = 13;
            $this->regularisationrequest->regularisation_date = Carbon::now();
            $this->regularisationrequest->save();
            Log::info('Regularisation request updated.', [
                'status' => $this->regularisationrequest->status,
                'date' => $this->regularisationrequest->regularisation_date
            ]);
    
            // Process each regularisation entry
            foreach ($this->regularisationEntries as $entry) {
                Log::info('Processing entry.', ['entry' => $entry]);
    
                if ($entry['status'] === 'approved') {
                    Log::info('Entry approved. Creating swipe records.', [
                        'in_time' => $entry['from'],
                        'out_time' => $entry['to'],
                        'date' => $entry['date']
                    ]);
    
                    // Create IN SwipeRecord
                    $swipeIn = new SwipeRecord();
                    $swipeIn->emp_id = $this->regularisationrequest->employee->emp_id;
                    $swipeIn->in_or_out = 'IN';
                    $swipeIn->swipe_time = $entry['from'];
                    $swipeIn->created_at = $entry['date'];
                    $swipeIn->is_regularized = 1;
                    $swipeIn->save();
                    Log::info('IN swipe record saved.', ['swipe_in' => $swipeIn]);
    
                    // Create OUT SwipeRecord
                    $swipeOut = new SwipeRecord();
                    $swipeOut->emp_id = $this->regularisationrequest->employee->emp_id;
                    $swipeOut->in_or_out = 'OUT';
                    $swipeOut->swipe_time = $entry['to'];
                    $swipeOut->created_at = $entry['date'];
                    $swipeOut->is_regularized = 1;
                    $swipeOut->save();
                    Log::info('OUT swipe record saved.', ['swipe_out' => $swipeOut]);
                } else {
                    Log::info('Entry not approved. No swipe records created.', ['entry_status' => $entry['status']]);
                }
            }
    
            // Send notification email
            Log::info('Sending notification email for regularisation.');
            $this->sendmailForRegularisation($this->regularisationrequest);
            Log::info('Notification email sent.');
    
            // Success message
            FlashMessageHelper::flashSuccess('Regularisation status submitted successfully.');
            Log::info('Regularisation submission complete. Redirecting to review page.');
    
            return redirect()->route('review');
        } else {
            Log::error('Regularisation request not found.');
            FlashMessageHelper::flashError('Regularisation request not found.');
        }
    }
    

    public function sendmailForRegularisation($r1)
    {
       
        $employee = EmployeeDetails::where('emp_id', $this->regularisationrequest->emp_id)->first();
        $this->employeeEmailForApproval=$employee->email;
        $employee_remarks=$r1->employee_remarks;
        $regularisationEntriesforCombined = json_decode($r1->regularisation_entries, true);

        $details = [
            'employee_remarks'=>$employee_remarks,
            'regularisationRequests'=>$regularisationEntriesforCombined,
            'sender_id'=>$employee->emp_id,
            'sender_first_name'=>$employee->first_name,
            'sender_last_name'=>$employee->last_name,
          
           
           
        ];
        Mail::to($this->employeeEmailForApproval)->send(new RegularisationCombinedMail($details));
    }
    public function render()
    {
        $today = Carbon::today();
        return view('livewire.review-pending-regularisation',['regularisationdesc'=>$this->regularisationdescription,'today'=>$today]);
    }
}

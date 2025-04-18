<?php
/*Created by:Pranita Priyadarshi*/
/*This submodule will be showing users swipe time and also ur attendance status*/
// File Name                       : Attendance.php
// Description                     : This file contains the implementation of a EmployeesAttendance by this we can know attendance of particular employees in a month.
// Creator                         : Pranita Priyadarshi
// Email                           : priyadarshipranita72@gmail.com
// Organization                    : PayG.
// Date                            : 2023-03-07
// Framework                       : Laravel (10.10 Version)
// Programming Language            : PHP (8.1 Version)
// Database                        : MySQL
// Models                          : LeaveRequest,EmployeeDetails,HolidayCalendar,SwipeRecord
namespace App\Livewire;

use App\Helpers\FlashMessageHelper;
use App\Models\AttendanceException;
use App\Models\EmployeeDetails;
use App\Models\SwipeRecord;
use App\Models\HolidayCalendar;
use App\Models\LeaveRequest;
use App\Models\RegularisationDates;
use Livewire\Component;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Torann\GeoIP\Facades\GeoIP;
use Illuminate\Support\Facades\Http;
use function PHPSTORM_META\elementType;

class Attendance extends Component
{
    public $currentDate2;
    public $hours;

    public $totalDaysForFormattedModalTitle;
    public $country;

    public $city;

    public $employeeShiftDetailsForCalendar;
    public $postal_code;
    public $totalWorkingPercentage;
    public $minutesFormatted;


    public $avgWorkHoursFromJuly = 0;


    public $employeeSecondShiftDetails = null;



    public $last_out_time;

    public $excessHrs;
    public $percentageDifference;

    public $employeeShiftDetails;
    public $currentDate;
    public $date1;

    public $shiftStartTime;

    public $shiftEndTime;
    public $avgSignInTime;


    public $leaveTypes = [];
    public $averageWorkHours;

    public $totalnumberofEarlyOut = 0;
    public $percentageOfWorkHrs;
    public $percentageOfWorkHours;

    public $averageWorkHoursForModalTitle = 0;
    public $CurrentDate;
    public $avgSignOutTime;

    public $first_in_time_for_date;

    public $employeeSecondShift;
    public $last_out_time_for_date;
    public $swipe_records_count;
    public $clickedDate;
    public $currentWeekday;

    public $timeDifferences;

    public $totalWorkingDays;
    public $calendar;
    public $selectedDate;
    public $shortFallHrs;
    public $work_hrs_in_shift_time;
    public $swipe_record;
    public $holiday;
    public $leaveApplies;
    public $first_in_time;
    public $year;
    public $currentDate2record;
    public $month;
    public $actualHours = [];
    public $firstSwipeTime;
    public $secondSwipeTime;
    public $swiperecords;
    public $currentDate1;

    public $startDateForInsights;
    public $toDate;

    public $currentDate2recordin;

    public $currentDate2recordout;

    public $showCalendar = true;
    public $date2;
    public $modalTitle = '';

    public $countofAbsent;
    public $view_student_swipe_time;
    public $view_student_in_or_out;
    public $swipeRecordId;

    public $swiperecordsfortoggleButton;
    public $swiperecord;
    public $from_date;
    public $to_date;
    public $status;

    public $dynamicDate;
    public $view_student_emp_id;
    public $view_employee_swipe_time;
    public $currentDate2recordexists;


    public $defaultfaCalendar = 1;
    public $dateclicked;
    public $view_table_in;

    public $count;

    public $employeeHireDate;
    public $view_table_out;
    public $employeeDetails;
    public $changeDate = 0;
    public $student;
    public $selectedRecordId = null;

    public $toggleButton = false;
    public $regularised_by;

    public $regularised_date;

    public $regularised_reason;

    public $regularised_date_to_check;

    public $avgWorkingHrsForModalTitle;
    public $legend = true;
    public $isNextMonth = 0;
    public $record;

    public $dateToCheck;

    public $Swiperecords;
    public $employeeId;



    public $employeeIdForRegularisation;

    public $totalDurationFormatted;

    public $avgDurationFormatted;
    public $openattendanceperiod = false;

    public $leavestatusforsession1;

    public $leavestatusforsession2;
    public $averageFormattedTime = '00:00';
    public $totalDurationFormatted1;
    public $errorMessage;
    public $showRegularisationDialog = false;

    public $totalnumberofHolidayForFormattedDate;
    public $distinctDates;
    public $isPresent;
    public $table;
    public $previousMonth;
    public $session1 = 0;
    public $session2 = 0;
    public $session1ArrowDirection = 'right';
    public $session2ArrowDirection = 'right';

    public $averageHoursWorked;

    public $totalcount = 0;

    public $averageMinutesWorked;
    public $avgSwipeInTime = null;
    public $avgSwipeOutTime = null;
    public $totalmodalDays;

    public $avgWorkHoursPreviousMonth;
    public $averageworkhours;

    public $totalLateInSwipes = 0;
    public $totalnumberofLeaves = 0;

    public $timeDifferenceInMinutesForCalendar = 0;
    public $start_date_for_insights;
    public $averageWorkHrsForCurrentMonth = null;
    public $averageFormattedTimeForCurrentMonth;
    public $holidayCountForInsightsPeriod;
    public $weekendDays = 0;
    public $daysWithRecords = 0;

    public $previousMonthTotalMinutes;

    public $currentMonthTotalMinutes;
    public $averageFirstInTime = 0;

    public $averageLastOutTime = 0;
    public $totalnumberofAbsents = 0;
    public $percentageinworkhrsforattendance;
    public $leaveTaken = 0;
    public $totalHoursWorked = 0;

    public $totalMinutesWorked = 0;
    public $avgWorkHours = 0;
    public $avgLateIn = 0;
    public $avgEarlyOut = 0;

    public $k, $k1;
    public $showMessage = false;

    public $employee;
    //This function will help us to toggle the arrow present in session fields


    public function closeRegularisationModal()
    {
        try {
            // Attempt to perform the action
            $this->showRegularisationDialog = false;
        } catch (\Exception $e) {
            // Handle the exception
            // You can log the error, show a user-friendly message, or handle it as needed
            // For example, you might log the exception message:
            error_log('Error closing regularisation modal: ' . $e->getMessage());

            // Optionally, you can display a user-friendly message or perform other actions
            // $this->showErrorMessage('An error occurred while closing the modal.');
        }
    }
    public function calculateDifferenceInAvgWorkHours($date)
    {
        // Get the current month and previous month dates
        $currentMonthStart = \Carbon\Carbon::parse($date . '-01')->startOfMonth()->toDateString();
        $currentMonthEnd = \Carbon\Carbon::parse($date)->endOfMonth()->toDateString(); // Today's date
        $previousMonthDate = \Carbon\Carbon::parse($date)->subMonth()->format('Y-m');

        // Log the initial dates
        

        if (
            \Carbon\Carbon::parse($currentMonthEnd)->greaterThan(\Carbon\Carbon::today()) &&
            \Carbon\Carbon::parse($currentMonthEnd)->isSameMonth(\Carbon\Carbon::today()) &&
            \Carbon\Carbon::parse($currentMonthEnd)->isSameYear(\Carbon\Carbon::today())
        ) {
            $currentMonthEnd = \Carbon\Carbon::today()->toDateString(); // Set to today's date if greater
           
        } elseif (\Carbon\Carbon::parse($currentMonthEnd)->greaterThan(\Carbon\Carbon::today())) {
        
            return '-';
        }

        $previousMonthStart = \Carbon\Carbon::parse($previousMonthDate . '-01')->startOfMonth()->toDateString();
        $previousMonthEnd = \Carbon\Carbon::parse($previousMonthDate)->endOfMonth()->toDateString(); // End of previous month

        // Log previous month dates
       

        if (
            \Carbon\Carbon::parse($previousMonthEnd)->greaterThan(\Carbon\Carbon::today()) &&
            \Carbon\Carbon::parse($previousMonthEnd)->isSameMonth(\Carbon\Carbon::today()) &&
            \Carbon\Carbon::parse($previousMonthEnd)->isSameYear(\Carbon\Carbon::today())
        ) {
            $previousMonthEnd = \Carbon\Carbon::today()->toDateString(); // Set to today's date if greater
        
        } elseif (\Carbon\Carbon::parse($previousMonthEnd)->greaterThan(\Carbon\Carbon::today())) {
         
            return '-';
        }

        // Log before calculation
       

        // Calculate average work hours for current and previous months
        $avgWorkHoursCurrentMonth = $this->calculateAverageWorkHoursAndPercentage($currentMonthStart, $currentMonthEnd);
        $avgWorkHoursPreviousMonth = $this->calculateAverageWorkHoursAndPercentage($previousMonthStart, $previousMonthEnd);

        // Log average work hours
        

        // Convert the average work hours (HH:MM) to total minutes for comparison
        list($currentMonthHours, $currentMonthMinutes) = explode(':', $avgWorkHoursCurrentMonth);
        list($previousMonthHours, $previousMonthMinutes) = explode(':', $avgWorkHoursPreviousMonth);

        $this->currentMonthTotalMinutes = ($currentMonthHours * 60) + $currentMonthMinutes;
        $this->previousMonthTotalMinutes = ($previousMonthHours * 60) + $previousMonthMinutes;

        // Log total minutes
        


        // Calculate the difference in minutes
        $differenceInMinutes = $this->currentMonthTotalMinutes - $this->previousMonthTotalMinutes;

       
        if ($this->previousMonthTotalMinutes != 0) {
            $this->percentageDifference = ($differenceInMinutes / $this->previousMonthTotalMinutes) * 100;
        } else {
            $this->percentageDifference = 0;
        }

        // Convert the difference back to hours and minutes
        $hoursDifference = intdiv($differenceInMinutes, 60);
        $minutesDifference = $differenceInMinutes % 60;

        

        return $this->percentageDifference;
    }


    public function calculateAverageWorkHoursAndPercentage($startDate, $endDate)
{
    
    $employeeId = auth()->guard('emp')->user()->emp_id;
   
    // Retrieve swipe records within the given date range
    $records = SwipeRecord::where('emp_id', $employeeId)
        ->whereDate('created_at', '>=', $startDate)
        ->whereDate('created_at', '<=', $endDate)
        ->orderBy('created_at', 'asc')
        ->get();

   
    // Group by date and fetch the first IN and last OUT for each date
    $dailyRecords = $records->groupBy(function ($record) {
        return Carbon::parse($record->created_at)->toDateString();
    })->map(function ($groupedRecords) {
        $firstIn = $groupedRecords->where('in_or_out', 'IN')->sortByDesc('created_at')->first();
        $lastOut = $groupedRecords->where('in_or_out', 'OUT')->sortByDesc('created_at')->first();
        
       

        return collect(['first_in' => $firstIn, 'last_out' => $lastOut])->filter();
    });

    
    // Retrieve approved leave requests
    $leaveRequests = LeaveRequest::where('emp_id', $employeeId)
        ->where('leave_applications.leave_status', 2)
        ->where('leave_applications.from_session', 'Session 1')
        ->where('leave_applications.to_session', 'Session 2')
        ->where(function ($query) use ($startDate, $endDate) {
            $query->whereDate('from_date', '<=', $endDate)
                  ->whereDate('to_date', '>=', $startDate);
        })
        ->join('status_types', 'status_types.status_code', '=', 'leave_applications.leave_status')
        ->select('leave_applications.*', 'status_types.status_name')
        ->get();

   
    $totalMinutes = 0;
    $workingDaysCount = 0;
    $today = Carbon::now();
    $isCurrentMonth = Carbon::parse($startDate)->isSameMonth($today) && Carbon::parse($endDate)->isSameMonth($today);

    $currentDate = Carbon::parse($startDate);
    $endDate = Carbon::parse($endDate);

    while ($currentDate <= $endDate) {
        $dateStr = $currentDate->toDateString();

        if ($isCurrentMonth && $currentDate->isSameDay($today)) {
        
            $currentDate->addDay();
            continue;
        }

        $isWeekend = $currentDate->isWeekend();
        $isHoliday = HolidayCalendar::where('date', $dateStr)->exists();
        $isOnLeave = $leaveRequests->contains(fn($leave) => $currentDate->between($leave->from_date, $leave->to_date));
        $isOnFullDayLeave = $this->isEmployeeFullDayLeaveOnDate($dateStr, $employeeId);
        $halfDayLeaveData = $this->isEmployeeHalfDayLeaveOnDate($dateStr, $employeeId);
        $isOnHalfDayLeave = $halfDayLeaveData['sessionCheck'];
        $isOnHalfDayLeaveForDifferentSessions = $halfDayLeaveData['doubleSessionCheck'];

        
        if (!$isWeekend && !$isHoliday && !$isOnLeave && !$isOnFullDayLeave && !$isOnHalfDayLeave && !$isOnHalfDayLeaveForDifferentSessions) {
            $workingDaysCount++;
        } elseif ($isOnHalfDayLeave && !$isWeekend && !$isHoliday && !$isOnLeave) {
            $workingDaysCount += 0.5;
        }

        $currentDate->addDay();
    }

    foreach ($dailyRecords as $date => $swipesForDay) {
        $inTime = null;
        $dayMinutes = 0;
        $carbonDate = Carbon::parse($date);

        $isWeekend = $carbonDate->isWeekend();
        $isHoliday = HolidayCalendar::where('date', $carbonDate->toDateString())->exists();
        $isOnLeave = $leaveRequests->contains(fn($leave) => $carbonDate->between($leave->from_date, $leave->to_date));

        if (!$isWeekend && !$isHoliday && !$isOnLeave) {
            foreach ($swipesForDay as $swipe) {
                if ($swipe->in_or_out === 'IN') {
                    $inTime = Carbon::parse($swipe->swipe_time);
                }

                if ($swipe->in_or_out === 'OUT' && $inTime) {
                    $outTime = Carbon::parse($swipe->swipe_time);
                    $diff = $inTime->diffInMinutes($outTime);
                    $dayMinutes += $diff;
                    $inTime = null;

                   
                }
            }

            if ($inTime && $dayMinutes === 0) {
                $dayMinutes = 0;
            }

            $totalMinutes += $dayMinutes;
        }

        
    }

    $averageMinutes = $workingDaysCount > 0 ? $totalMinutes / $workingDaysCount : 0;
    $hours = intdiv($averageMinutes, 60);
    $minutes = $averageMinutes % 60;
    $averageWorkHours = sprintf('%02d:%02d', $hours, $minutes);

    

    return $averageWorkHours;
}

    public function toggleSession1Fields()
    {
        try {
            $this->session1 = !$this->session1;
            $this->session1ArrowDirection = ($this->session1) ? 'left' : 'right';
        } catch (\Exception $e) {
           

            // Optionally, you can set some default values or handle the error in a user-friendly way
            $this->session1 = false;
            $this->session1ArrowDirection = 'right';

            // You can also set a session message or an error message to inform the user
            FlashMessageHelper::flashError('An error occurred while toggling session fields. Please try again later.');
        }
    }
    //This function will help us to toggle the arrow present in session fields
    public function toggleSession2Fields()
    {
        try {
            $this->session2 = !$this->session2;
            $this->session2ArrowDirection = ($this->session2) ? 'left' : 'right';
            // dd($this->session1);
        } catch (\Exception $e) {
            
            // Optionally, you can set some default values or handle the error in a user-friendly way
            $this->session2 = false;
            $this->session2ArrowDirection = 'right';

            // You can also set a session message or an error message to inform the user
            FlashMessageHelper::flashError('An error occurred while toggling session fields. Please try again later.');
        }
    }
    public  $averageWorkingHours, $percentageOfHoursWorked, $yearA, $monthA;

    public function calculateMetrics()
    {
        $employeeId = auth()->guard('emp')->user()->emp_id;

        // Get the current date

        // Specify the current month and year
        $swipes = SwipeRecord::where('emp_id', $employeeId)
            ->whereYear('created_at', $this->yearA)
            ->whereMonth('created_at', $this->monthA)
            ->orderBy('swipe_time')
            ->get();


        // Initialize arrays to store in and out swipes
        $inSwipes = [];
        $outSwipes = [];

        // Process swipe records
        foreach ($swipes as $swipe) {
            $date = Carbon::parse($swipe->swipe_time)->toTimeString();
            if ($swipe->in_or_out === 'IN') {
                if (!isset($inSwipes[$date])) {
                    $inSwipes[$date] = [];
                }
                $inSwipes[$date][] = $swipe;
            } elseif ($swipe->in_or_out === 'OUT') {
                if (!isset($outSwipes[$date])) {
                    $outSwipes[$date] = [];
                }
                $outSwipes[$date][] = $swipe;
            }
        }

        // Calculate total hours worked in the month
        $totalHoursWorked = 0;
        foreach ($inSwipes as $date => $inSwipeArray) {
            if (isset($outSwipes[$date])) {
                foreach ($inSwipeArray as $index => $inSwipe) {
                    if (isset($outSwipes[$date][$index])) {
                        $inTime = Carbon::parse($inSwipe->swipe_time);
                        $outTime = Carbon::parse($outSwipes[$date][$index]->swipe_time);
                        $totalHoursWorked += $outTime->diffInHours($inTime);
                    }
                }
            }
        }

        // Calculate the number of working days in the current month
        $startDate = Carbon::create($this->yearA, $this->monthA, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $workingDaysCount = 0;

        while ($startDate->lte($endDate)) {
            if ($startDate->isWeekday()) {
                $workingDaysCount++;
            }
            $startDate->addDay();
        }

        // Define the standard daily hours
        $standardDailyHours = 9;

        // Calculate the standard monthly hours
        $standardMonthlyHours = $standardDailyHours * $workingDaysCount;

        // Calculate the average working hours for the month
        $this->averageWorkingHours = $workingDaysCount > 0 ? $totalHoursWorked / $workingDaysCount : 0;

        // Calculate the percentage of hours worked relative to the standard monthly hours
        $this->percentageOfHoursWorked = $standardMonthlyHours > 0 ? ($totalHoursWorked / $standardMonthlyHours) * 100 : 0;
    }

    public function mount($startDateForInsights = null, $toDate = null)
{
    try {
       
        // Fetch employee details
        $this->employee = EmployeeDetails::where('emp_id', auth()->guard('emp')->user()->emp_id)->select('emp_id', 'first_name', 'last_name', 'shift_type', 'hire_date')->first();
        
        if (!$this->employee) {
            Log::error('No employee details found for the authenticated user');
            throw new \Exception('No employee details found for the authenticated user');
        }

        $this->employeeHireDate = $this->employee->hire_date;
       
        // Set date ranges
        $this->from_date = Carbon::now()->subMonth()->startOfMonth()->toDateString();
       
        $this->start_date_for_insights = Carbon::now()->startOfMonth()->format('Y-m-d');
        
        $this->to_date = Carbon::now()->subDay()->toDateString();
        
        $this->startDateForInsights = $startDateForInsights ?? now()->startOfMonth()->toDateString();
       
        $this->toDate = $toDate ?? now()->subDay()->toDateString();
        
        
        // Update modal title
        $this->updateModalTitle();
        

        // Calculate average working hours
        $this->calculateAvgWorkingHrs($this->from_date, $this->to_date, $this->employee->emp_id);
        

        // Parse dates
        $fromDate = Carbon::createFromFormat('Y-m-d', $this->from_date);
       
        $toDate = Carbon::createFromFormat('Y-m-d', $this->to_date);
       
        $currentDate = Carbon::parse($this->from_date);
       
        $endDate = Carbon::parse($this->to_date);
       
        // Initialize counters
        $totalHoursWorked = 0;
        $totalMinutesWorked = 0;

        // Get IP address and location
        $ip = request()->ip() === '127.0.0.1' ? Http::get('https://api64.ipify.org')->body() : request()->ip();
        
        $ipUrl = env('FINDIP_API_URL', 'https://ipapi.co');
      
        $response = Http::get("{$ipUrl}/{$ip}/json/");
        
        if ($response->successful()) {
            $location = $response->json();
            
            $lat = $location['lat'] ?? null;
            $lon = $location['lon'] ?? null;
            $this->country = $location['country_name'] ?? null;
            $this->city = $location['city'] ?? null;
            $this->postal_code = $location['postal'] ?? null;
        } else {
            Log::error('Failed to fetch IP location data', ['ip' => $ip, 'response' => $response->body()]);
        }

        // Fetch employee shift details
        $this->employeeShiftDetails = DB::table('employee_details')
            ->join('company_shifts', function ($join) {
                $join->on(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(employee_details.company_id, '$[0]'))"), '=', 'company_shifts.company_id')
                    ->on('employee_details.shift_type', '=', 'company_shifts.shift_name');
            })
            ->where('employee_details.emp_id', auth()->guard('emp')->user()->emp_id)
            ->select('company_shifts.shift_start_time', 'company_shifts.shift_end_time', 'company_shifts.shift_name', 'employee_details.*')
            ->first();

       
        if ($this->employeeShiftDetails) {
            $this->shiftStartTime = Carbon::parse($this->employeeShiftDetails->shift_start_time)->format('H:i');
            $this->shiftEndTime = Carbon::parse($this->employeeShiftDetails->shift_end_time)->format('H:i');
           
        } else {
            $this->shiftStartTime = null;
            $this->shiftEndTime = null;
        }

        // Loop through each day to calculate total time differences
        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->toDateString();
          
            $inTimes = SwipeRecord::where('emp_id', auth()->guard('emp')->user()->emp_id)
                ->where('in_or_out', 'IN')
                ->whereDate('created_at', $dateString)
                ->pluck('swipe_time');

            $outTimes = SwipeRecord::where('emp_id', auth()->guard('emp')->user()->emp_id)
                ->where('in_or_out', 'OUT')
                ->whereDate('created_at', $dateString)
                ->pluck('swipe_time');

         
            $totalDifferenceForDay = 0;

            foreach ($inTimes as $index => $inTime) {
                if (isset($outTimes[$index])) {
                    $inCarbon = Carbon::parse($inTime);
                    $outCarbon = Carbon::parse($outTimes[$index]);
                    $difference = $outCarbon->diffInSeconds($inCarbon);
                    $totalDifferenceForDay += $difference;
                   
                }
            }

            $currentDate->addDay();
        }

        // Group swipe records by date
        $swipeRecords = SwipeRecord::where('emp_id', auth()->guard('emp')->user()->emp_id)->get();
        
        $groupedDates = $swipeRecords->groupBy(function ($record) {
            return Carbon::parse($record->created_at)->format('Y-m-d');
        });

        $this->distinctDates = $groupedDates->mapWithKeys(function ($records, $key) {
            $inRecord = $records->where('in_or_out', 'IN')->first();
            $outRecord = $records->where('in_or_out', 'OUT')->last();

            return [
                $key => [
                    'in' => "IN",
                    'first_in_time' => optional($inRecord)->swipe_time,
                    'last_out_time' => optional($outRecord)->swipe_time,
                    'out' => "OUT",
                ]
            ];
        });

      
        // Set current date properties
        $this->currentDate = date('d');
        $this->currentWeekday = date('D');
        $this->currentDate1 = date('d M Y');
        $this->swiperecords = SwipeRecord::all();
        
        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::now();
        $this->year = now()->year;
        $this->month = now()->month;
       
        $this->generateCalendar();
       
        $this->percentageinworkhrsforattendance = $this->calculateDifferenceInAvgWorkHours(\Carbon\Carbon::now()->format('Y-m'));
       
        $this->averageWorkHrsForCurrentMonth = $this->calculateAverageWorkHoursAndPercentage($startOfMonth->toDateString(), $today->copy()->subDay()->toDateString());
       
    } catch (\Exception $e) {
        Log::error('Error in mount method', ['error' => $e->getMessage()]);

        $this->from_date = now()->startOfMonth()->toDateString();
        $this->to_date = now()->toDateString();
        $this->distinctDates = collect();
        $this->currentDate = date('d');
        $this->currentWeekday = date('D');
        $this->currentDate1 = date('d M Y');
        $this->swiperecords = collect();
        $this->year = now()->year;
        $this->month = now()->month;

        FlashMessageHelper::flashError('An error occurred while initializing the component. Please try again later.');
    }
}



    public function showTable()
    {
        try {
            // Your code that might throw an exception
            $this->defaultfaCalendar = 0;
        } catch (\Exception $e) {
            // Handle the exception
            // You can log the error or set an error message
        
            // Optionally, you can set a user-friendly message to be displayed
            $errorMessage = 'An error occurred while updating the calendar. Please try again later.';
        }
    }

    public function showBars()
    {
        try {
            
            $this->defaultfaCalendar = 1;
            $this->showMessage = false;
        } catch (\Exception $e) {
          
            FlashMessageHelper::flashError('An error occurred while showing the bars. Please try again later.');
        }
    }
    //This function will help us to calculate the number of public holidays in a particular month
    protected function getPublicHolidaysForMonth($year, $month)
    {
        try {
            return HolidayCalendar::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();
        } catch (\Exception $e) {
       
            FlashMessageHelper::flashError('An error occurred while fetching public holidays. Please try again later.');
            return collect(); // Return an empty collection to handle the error gracefully
        }
    }

    public function showlargebox($k)
    {
        try {
            $this->k1 = $k;
            $this->dispatchBrowserEvent('refreshModal', ['k1' => $this->k1]);
        } catch (\Exception $e) {
           
            FlashMessageHelper::flashError('An error occurred while showing the large box. Please try again later.');
        }
    }
    private function isEmployeeRegularisedOnDate($date)
    {
        try {
            $employeeId = auth()->guard('emp')->user()->emp_id;
            return SwipeRecord::where('emp_id', $employeeId)->whereDate('created_at', $date)->where('is_regularized', 1)->exists();
        } catch (\Exception $e) {
           
            FlashMessageHelper::flashError('An error occurred while checking employee presence. Please try again later.');
            return false; // Return false to handle the error gracefully
        }
    }
    //This function will help us to check if the employee is present on this particular date or not
    private function isEmployeePresentOnDate($date)
    {
        try {
            $employeeId = auth()->guard('emp')->user()->emp_id;
            $intime = SwipeRecord::where('emp_id', $employeeId)->where('in_or_out', 'IN')->whereDate('created_at', $date)->exists();


            return $intime;
        } catch (\Exception $e) {
          
            FlashMessageHelper::flashError('An error occurred while checking employee presence. Please try again later.');
            return false; // Return false to handle the error gracefully
        }
    }
    private function isHolidayOnDate($date)
    {
        $checkHoliday = HolidayCalendar::where('date', $date)->exists();
        return $checkHoliday;
    }
    //This function will help us to check if the employee is on leave for this particular date or not
    private function isEmployeeLeaveOnDate($date, $employeeId)
    {
        try {
            $employeeId = auth()->guard('emp')->user()->emp_id;
            return LeaveRequest::where('emp_id', $employeeId)
                ->where('leave_applications.leave_status', 2)
                ->where('from_session', 'Session 1')
                ->where('to_session', 'Session 2')
                ->where(function ($query) use ($date) {
                    $query->whereDate('from_date', '<=', $date)
                        ->whereDate('to_date', '>=', $date);
                })
                ->join('status_types', 'status_types.status_code', '=', 'leave_applications.leave_status') // Join with status_types
                ->exists();
        } catch (\Exception $e) {
            
            FlashMessageHelper::flashError('An error occurred while checking employee leave. Please try again later.');
            return false; // Return false to handle the error gracefully
        }
    }
    private function isEmployeeHalfDayLeaveOnDate($date, $employeeId)
    {
        try {
            $employeeId = auth()->guard('emp')->user()->emp_id;
            $sessionArray = [];
            $leaveRecord = null;
            $isBeforeToDate = $this->isEmployeeFullDayLeaveOnDate($date, $employeeId);


            $session1Check = LeaveRequest::where('emp_id', $employeeId)
                ->where('leave_applications.leave_status', 2)
                ->where('from_session', 'Session 1')
                ->where('to_session', 'Session 1')
                ->whereDate('from_date', '<=', $date)
                ->whereDate('to_date', '>=', $date)
                ->exists();
            $session2Check = LeaveRequest::where('emp_id', $employeeId)
                ->where('leave_applications.leave_status', 2)
                ->where('from_session', 'Session 2')
                ->where('to_session', 'Session 2')
                ->whereDate('from_date', '<=', $date)
                ->whereDate('to_date', '>=', $date)
                ->exists();



            if ($session1Check) {
            } elseif ($session2Check) {
            } else {
            }

            if ($session1Check) {
                // Case when both sessions are 'Session 1'
                $leaveRecord = LeaveRequest::where('emp_id', $employeeId)
                    ->where('leave_applications.leave_status', 2)
                    ->where('from_session', 'Session 1')
                    ->where('to_session', 'Session 1')
                    ->where(function ($query) use ($date) {
                        $query->whereDate('from_date', '<=', $date)
                            ->whereDate('to_date', '>=', $date);
                    })
                    ->join('status_types', 'status_types.status_code', '=', 'leave_applications.leave_status')
                    ->exists();
                $sessionArray[] = 'Session 1';
            }

            if ($session2Check) {
                // Case when sessions are not both 'Session 1'
                $leaveRecord = LeaveRequest::where('emp_id', $employeeId)
                    ->where('leave_applications.leave_status', 2)
                    ->where('from_session', 'Session 2')
                    ->where('to_session', 'Session 2')
                    ->where(function ($query) use ($date) {
                        $query->whereDate('from_date', '<=', $date)
                            ->whereDate('to_date', '>=', $date);
                    })
                    ->join('status_types', 'status_types.status_code', '=', 'leave_applications.leave_status')
                    ->exists();
                $sessionArray[] = 'Session 2';
            }



            return [
                'session' => $sessionArray,
                'leaveRecord' => $leaveRecord,
                'sessionCheck' => (($session1Check xor $session2Check) xor $isBeforeToDate) ? true : false,
                'doubleSessionCheck' => ($session1Check && $session2Check) ? true : false

        ];
    } catch (\Exception $e) {
       

            FlashMessageHelper::flashError('An error occurred while checking employee leave. Please try again later.');

            return [
                'session' => null,
                'leaveRecord' => false,
            ];
        }
    }

    private function caluclateNumberofLeaves($startDate, $endDate, $employeeId)
    {
        $countofleaves = 0;
        $currentDate = $startDate->copy();
        $flag = false;

        while ($currentDate->lt($endDate)) {
            $flag = LeaveRequest::where('emp_id', $employeeId)
                ->where('leave_applications.leave_status', 2)
                ->where(function ($query) use ($currentDate) {
                    $query->whereDate('from_date', '<=', $currentDate)
                        ->whereDate('to_date', '>=', $currentDate);
                })
                ->join('status_types', 'status_types.status_code', '=', 'leave_applications.leave_status') // Join with status_types table
                ->exists();
            if ($flag == true) {
                $countofleaves++;
            }
            $currentDate->addDay();
            $flag = false;
        }

        return $countofleaves;
    }
    private function getLeaveTypeForSession1($date, $employeeId)
    {
        try {
            return LeaveRequest::where('emp_id', $employeeId)
                ->whereDate('from_date', '<=', $date)
                ->whereDate('to_date', '>=', $date)
                ->where('from_session', 'Session 1')
                ->where('to_session', 'Session 1')
                ->where('leave_status', 2)
                ->value('leave_type');
        } catch (\Exception $e) {
          
            FlashMessageHelper::flashError('An error occurred while fetching leave type. Please try again later.');
            return null; // Return null to handle the error gracefully
        }
    }
    private function getLeaveTypeForSession2($date, $employeeId)
    {
        try {
            return LeaveRequest::where('emp_id', $employeeId)
                ->whereDate('from_date', '<=', $date)
                ->whereDate('to_date', '>=', $date)
                ->where('from_session', 'Session 2')
                ->where('to_session', 'Session 2')
                ->where('leave_status', 2)
                ->value('leave_type');
        } catch (\Exception $e) {
           
            FlashMessageHelper::flashError('An error occurred while fetching leave type. Please try again later.');
            return null; // Return null to handle the error gracefully
        }
    }
    //This function will help us to check the leave type of employee
    private function getLeaveType($date, $employeeId)
    {
        try {
            return LeaveRequest::where('emp_id', $employeeId)
                ->whereDate('from_date', '<=', $date)
                ->whereDate('to_date', '>=', $date)
                ->where('leave_status', 2)
                ->value('leave_type');
        } catch (\Exception $e) {
           
            FlashMessageHelper::flashError('An error occurred while fetching leave type. Please try again later.');
            return null; // Return null to handle the error gracefully
        }
    }
    private function isDateRegularized($date, $employeeId)
    {
        $records = RegularisationDates::where('emp_id', $employeeId)->get();

        foreach ($records as $record) {
            $regularisationEntries = json_decode($record->regularisation_entries, true);
            foreach ($regularisationEntries as $entry) {
                if ($entry['date'] === $date) {
                    return true;
                }
            }
        }

        return false;
    }
    private function isEmployeeFullDayLeaveOnDate($date, $employeeId)
    {
        try {
            $employeeId = auth()->guard('emp')->user()->emp_id;

            $sessionCheck = LeaveRequest::where('emp_id', $employeeId)
                ->where('leave_applications.leave_status', 2)
                ->where('from_session', 'Session 1')
                ->where('to_session', 'Session 1')
                ->exists();

            if ($sessionCheck) {
                // Case when both sessions are 'Session 1'
                $leaveRecord = LeaveRequest::where('emp_id', $employeeId)
                    ->where('leave_applications.leave_status', 2)
                    ->where('from_session', 'Session 1')
                    ->where('to_session', 'Session 1')
                    ->where(function ($query) use ($date) {
                        $query->whereDate('from_date', '<=', $date)
                            ->whereDate('to_date', '>', $date);
                    })
                    ->join('status_types', 'status_types.status_code', '=', 'leave_applications.leave_status')
                    ->exists();
            } else {
                // Case when sessions are not both 'Session 1'
                $leaveRecord = LeaveRequest::where('emp_id', $employeeId)
                    ->where('leave_applications.leave_status', 2)
                    ->where('from_session', 'Session 2')
                    ->where('to_session', 'Session 2')
                    ->where(function ($query) use ($date) {
                        $query->whereDate('from_date', '<=', $date)
                            ->whereDate('to_date', '>', $date);
                    })
                    ->join('status_types', 'status_types.status_code', '=', 'leave_applications.leave_status')
                    ->exists();
            }
            return $leaveRecord;
        } catch (\Exception $e) {
            Log::error('Error in isEmployeeHalfDayLeaveOnDate method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while checking employee leave. Please try again later.');
            return false; // Return false to handle the error gracefully
        }
    }
    public function findExceptioninAttendance($date)
    {
        $attendanceException = AttendanceException::where('emp_id', auth()->guard('emp')->user()->emp_id)
            ->whereDate('from_date', '<=', $date) // from_date should be before or equal to $date
            ->whereDate('to_date', '>=', $date)   // to_date should be after or equal to $date
            ->value('status');
        return $attendanceException;
    }


    public function isEmployeeAssignedDifferentShift($date, $empId)
    {
        $shiftExists = false;
        $shiftType = null;

        $employee = EmployeeDetails::where('emp_id', $empId)->first();

        // Return array with default values if employee not found or no shift entries assigned
        if (!$employee || empty($employee->shift_entries_from_hr)) {
            return [
                'shiftExists' => $shiftExists,
                'shiftType' => $shiftType,
            ];
        }

        $shiftEntries = json_decode($employee->shift_entries_from_hr, true);
        $date = Carbon::parse($date);

        foreach ($shiftEntries as $shift) {
            $fromDate = Carbon::parse($shift['from_date']);
            $toDate = Carbon::parse($shift['to_date']);

            if ($date->between($fromDate, $toDate)) {
                $shiftExists = true;
                $shiftType = $shift['shift_type'];
            } elseif ($date->isSameDay($fromDate) || $date->isSameDay($toDate)) {
                $shiftExists = true;
                $shiftType = $shift['shift_type'];
            }
        }

        return [
            'shiftExists' => $shiftExists,
            'shiftType' => $shiftType,
        ];
    }


    //This function will help us to create the calendar
    public function generateCalendar()
    {
        try {
            $this->leaveTypes = [];
            $leavestatusforsession1 = null;
            $leavestatusforsession2 = null;
            $this->employeeShiftDetailsForCalendar = DB::table('employee_details')
                ->join('company_shifts', function ($join) {
                    $join->on(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(employee_details.company_id, '$[0]'))"), '=', 'company_shifts.company_id')
                        ->on('employee_details.shift_type', '=', 'company_shifts.shift_name');
                })
                ->where('employee_details.emp_id', auth()->guard('emp')->user()->emp_id)
                ->select('company_shifts.shift_start_time', 'company_shifts.shift_end_time', 'company_shifts.shift_name', 'employee_details.*')
                ->first();

            $employeeId = auth()->guard('emp')->user()->emp_id;



            $firstDay = Carbon::create($this->year, $this->month, 1);

            $daysInMonth = $firstDay->daysInMonth;

            $today = now();



            $calendar = [];
            $dayCount = 1;

            // Fetch public holidays for the current month
            $publicHolidays = $this->getPublicHolidaysForMonth($this->year, $this->month);



            $firstDayOfWeek = $firstDay->dayOfWeek;



            $startOfPreviousMonth = $firstDay->copy()->subMonth();

            $publicHolidaysPreviousMonth = $this->getPublicHolidaysForMonth(
                $startOfPreviousMonth->year,
                $startOfPreviousMonth->month
            );

            $lastDayOfPreviousMonth = $firstDay->copy()->subDay();



            for ($i = 0; $i < ceil(($firstDayOfWeek + $daysInMonth) / 7); $i++) {
                $week = [];

                for ($j = 0; $j < 7; $j++) {
                    if ($i === 0 && $j < $firstDay->dayOfWeek) {
                        $previousMonthDays = $lastDayOfPreviousMonth->copy()->subDays($firstDay->dayOfWeek - $j - 1);


                        $week[] = [
                            'day' => $previousMonthDays->day,
                            'isToday' => false,
                            'isPublicHoliday' => in_array($previousMonthDays->toDateString(), $publicHolidaysPreviousMonth->pluck('date')->toArray()),
                            'isCurrentMonth' => false,
                            'isPreviousMonth' => true,
                            'isRegularised' => false,
                            'backgroundColor' => '',
                            'status' => '',
                            'leavestatusforsession1' => null,
                            'leavestatusforsession2' => null,
                            'onHalfDayLeave' => '',
                            'onFullDayLeave' => '',
                            'onleave' => '',
                            'halfdaypresentforsession1' => false,
                            'halfdaypresentforsession2' => false,
                            'session2leave' => null,
                            'assignedDifferentShift' => null

                        ];
                    } elseif ($dayCount <= $daysInMonth) {
                        $date = Carbon::create($this->year, $this->month, $dayCount);
                     
                        $isOnSecondSessionLeave=[];
                        $isAbsentFor = false;
                        $isHalfDayPresent = false;
                        $isHalfDayPresentForSession1 = false;
                        $isHalfDayPresentForSession2 = false;
                        $leaveTypeForSession1 = null;
                        $leaveTypeForSession2 = null;
                        $halfdaypresent = null;
                        $isBeforeToDate = null;
                        $isToday = $dayCount === $today->day && $this->month === $today->month && $this->year === $today->year;
                        $isexceptioninAttendance = $this->findExceptioninAttendance($date);

                        $isPublicHoliday = in_array($date->toDateString(), $publicHolidays->pluck('date')->toArray()) || $isexceptioninAttendance == 'Holiday';

                        $isHoliday = HolidayCalendar::where('date', $date->toDateString())->exists();
                        $isexceptioninHoliday = ($isexceptioninAttendance == 'Holiday') ? true : false;
                        $isRegularised = $this->isEmployeeRegularisedOnDate($date->toDateString());

                        $isEmployeeAssignedDifferentShift = $this->isEmployeeAssignedDifferentShift($date->toDateString(), $employeeId)['shiftExists'];
                        $employeesecondshifttype = $this->isEmployeeAssignedDifferentShift($date->toDateString(), $employeeId)['shiftType'];

                        $isOnLeave = $this->isEmployeeLeaveOnDate($date->toDateString(), $employeeId) || $isexceptioninAttendance == 'Leave';


                        $isBeforeToDate = $this->isEmployeeFullDayLeaveOnDate($date->toDateString(), $employeeId);

                        if ($isBeforeToDate) {
                            $isOnHalfDayLeave = false;
                            $isOnSecondSessionLeave[] = [];
                        } else {
                            $isOnHalfDayLeave = $this->isEmployeeHalfDayLeaveOnDate($date->toDateString(), $employeeId)['leaveRecord'];
                            $isOnSecondSessionLeave[] = $this->isEmployeeHalfDayLeaveOnDate($date->toDateString(), $employeeId)['session'];
                        }



                        if (count($isOnSecondSessionLeave[0]) > 1) {


                            $leaveTypeForSession1 = $this->getLeaveTypeForSession1($date->toDateString(), $employeeId);

                            $leaveTypeForSession2 = $this->getLeaveTypeForSession2($date->toDateString(), $employeeId);
                        } else {


                            $leaveType = $this->getLeaveType($date->toDateString(), $employeeId);
                        }



                        if ($leaveType && !in_array($leaveType, $this->leaveTypes)) {
                            $this->leaveTypes[] = $leaveType;
                        }
                        // Get leave types for session 1 and session 2
                        $leaveTypeForSession1 = $this->getLeaveTypeForSession1($date->toDateString(), $employeeId);


                        $leaveTypeForSession2 = $this->getLeaveTypeForSession2($date->toDateString(), $employeeId);
                        // Add session leave types to leaveTypes array if they are not already present
                        if ($leaveTypeForSession1 && !in_array($leaveTypeForSession1, $this->leaveTypes)) {
                            $this->leaveTypes[] = $leaveTypeForSession1;
                        }

                        if ($leaveTypeForSession2 && !in_array($leaveTypeForSession2, $this->leaveTypes)) {
                            $this->leaveTypes[] = $leaveTypeForSession2;
                        }

                        $backgroundColor = $isPublicHoliday ? 'background-color: IRIS;' : '';
                        if (!$isOnLeave && !$isHoliday && !$date->isWeekend() && $isexceptioninAttendance != "Present") {
                            $isPresentOnDate = $this->isEmployeePresentOnDate($date);
                            $isEmployeeRegularisedOnDate = $this->isEmployeeRegularisedOnDate($date);
                            if ($isPresentOnDate || $isEmployeeRegularisedOnDate) {

                                // Fetch both IN and OUT records together to minimize queries
                                // Default to IN if no OUT time
                                $swipeRecords = SwipeRecord::where('emp_id', $employeeId)
                                    ->whereDate('created_at', $date->toDateString())
                                    ->whereIn('in_or_out', ['IN', 'OUT'])
                                    ->get();
                                $swipeoutRecords = SwipeRecord::where('emp_id', $employeeId)
                                    ->whereDate('created_at', $date->toDateString())
                                    ->whereIn('in_or_out', ['OUT'])
                                    ->orderByDesc('created_at')
                                    ->get();
                                if ($isEmployeeRegularisedOnDate) {
                                    $inSwipeTime = SwipeRecord::where('emp_id', $employeeId)->where('in_or_out', 'IN')->where('is_regularized', 1)->whereDate('created_at', $date->toDateString())->first();
                                    $outSwipeTime = SwipeRecord::where('emp_id', $employeeId)
                                        ->where('in_or_out', 'OUT')
                                        ->where('is_regularized', 1)
                                        ->whereDate('created_at', $date->toDateString())
                                        ->first() ?? $inSwipeTime;
                                } else {
                                    $inSwipeTime = $swipeRecords->firstWhere('in_or_out', 'IN');
                                    $outSwipeTime = $swipeoutRecords->firstWhere('in_or_out', 'OUT') ?? $inSwipeTime;
                                }


                                if ($inSwipeTime) {
                                } else {
                                }

                                if ($outSwipeTime) {
                                } else {
                                }
                                if ($inSwipeTime && $outSwipeTime) {
                                    $inTime = Carbon::parse($inSwipeTime->swipe_time)->format('H:i:s');
                                    $outTime = Carbon::parse($outSwipeTime->swipe_time)->format('H:i:s');


                                    if (!empty($this->employeeShiftDetailsForCalendar)) {
                                        $shiftStartTimeForCalendar = \Carbon\Carbon::parse($this->employeeShiftDetailsForCalendar->shift_start_time);
                                        $shiftEndTimeForCalendar = \Carbon\Carbon::parse($this->employeeShiftDetailsForCalendar->shift_end_time);
                                    } else {
                                        $shiftStartTimeForCalendar = null;
                                        $shiftEndTimeForCalendar = null;
                                    }


                                    if (!empty($shiftStartTimeForCalendar)) {
                                        $startTimeForSession1 = \Carbon\Carbon::createFromTime(
                                            $shiftStartTimeForCalendar->hour,
                                            $shiftStartTimeForCalendar->minute,
                                            $shiftStartTimeForCalendar->second
                                        );
                                    } else {
                                        $startTimeForSession1 = null;
                                    }


                                    // 10:00 AM
                                    if (!empty($shiftEndTimeForSession1)) {
                                        $shiftEndTimeForSession1 = $shiftStartTimeForCalendar->copy()->addHours(4)->addMinutes(30);

                                        $endTimeForSession1 = Carbon::createFromTime(
                                            $shiftEndTimeForSession1->hour,
                                            $shiftEndTimeForSession1->minute,
                                            $shiftEndTimeForSession1->second
                                        );
                                    } else {

                                        $shiftEndTimeForSession1 = null;

                                        $endTimeForSession1 = null;
                                    }
                                    if (!empty($shiftStartTimeForCalendar)) {
                                        $shiftStartTimeForSession2 = $shiftStartTimeForCalendar->copy()->addHours(4)->addMinutes(31);
                                        $startTimeForSession2 = Carbon::createFromTime($shiftStartTimeForSession2->hour, $shiftStartTimeForSession2->minute, $shiftStartTimeForSession2->second); // 10:00 AM
                                        $endTimeForSession2 = Carbon::createFromTime(
                                            $shiftEndTimeForCalendar->hour,
                                            $shiftEndTimeForCalendar->minute,
                                            $shiftEndTimeForCalendar->second
                                        );
                                    } else {
                                        $shiftStartTimeForSession2 = null;
                                        $startTimeForSession2 = null; // 10:00 AM
                                        $endTimeForSession2 = null;
                                    }



                                    $timeDifference = Carbon::parse($inTime)->diffInMinutes(Carbon::parse($outTime)); // Calculate difference in minutes


                                    $hours = floor($timeDifference / 60);
                                    $minutes = $timeDifference % 60;

                                    if ($timeDifference == 0) {
                                        $isAbsentFor = true;
                                    } elseif ($timeDifference < 270) {
                                        $isHalfDayPresent = true;


                                        if ($startTimeForSession1 !== null && Carbon::parse($inTime)->gte(Carbon::parse($startTimeForSession1))) {
                                            $isHalfDayPresentForSession1 = true;
                                        } else {
                                            $isHalfDayPresentForSession2 = true;
                                        }
                                        // Between 4 hours and 8 hours, mark as half-day present

                                    }
                                } elseif ($inSwipeTime == null) {
                                    $isAbsentFor = false;
                                }
                            } else {
                                $inSwipeTime = null;
                                $outSwipeTime = null;
                                $timeDifference = null; // Calculate difference in minutes

                            }
                        }
                        if ($isBeforeToDate) {


                            switch ($leaveType) {
                                case 'Casual Leave Probation':
                                    $status = 'CLP';
                                    break;
                                case 'Sick Leave':
                                    $status = 'SL';
                                    break;
                                case 'Loss Of Pay':
                                    $status = 'LOP';
                                    break;
                                case 'Casual Leave':
                                    $status = 'CL';
                                    break;
                                case 'Marriage Leave':
                                    $status = 'ML';
                                    break;
                                case 'Paternity Leave':
                                    $status = 'PL';
                                    break;
                                case 'Maternity Leave':
                                    $status = 'MTL';
                                    break;
                                default:
                                    $status = 'L';
                                    break;
                            }
                        } elseif ($isOnHalfDayLeave) {


                            if ($leaveTypeForSession1) {
                                switch ($leaveTypeForSession1) {
                                    case 'Casual Leave Probation':
                                        $leavestatusforsession1 = 'CLP';
                                        break;
                                    case 'Sick Leave':
                                        $leavestatusforsession1 = 'SL';

                                        break;
                                    case 'Loss Of Pay':
                                        $leavestatusforsession1 = 'LOP';

                                        break;
                                    case 'Casual Leave':
                                        $leavestatusforsession1 = 'CL';

                                        break;
                                    case 'Marriage Leave':
                                        $leavestatusforsession1 = 'ML';
                                        break;
                                    case 'Paternity Leave':
                                        $leavestatusforsession1 = 'PL';
                                        break;
                                    case 'Maternity Leave':
                                        $leavestatusforsession1 = 'MTL';
                                        break;
                                    default:
                                        $leavestatusforsession1 = 'L';
                                        break;
                                }
                            }
                            if ($leaveTypeForSession2) {
                                switch ($leaveTypeForSession2) {
                                    case 'Casual Leave Probation':
                                        $leavestatusforsession2 = 'CLP';
                                        break;
                                    case 'Sick Leave':
                                        $leavestatusforsession2 = 'SL';
                                        break;
                                    case 'Loss Of Pay':
                                        $leavestatusforsession2 = 'LOP';
                                        break;
                                    case 'Casual Leave':
                                        $leavestatusforsession2 = 'CL';
                                        break;
                                    case 'Marriage Leave':
                                        $leavestatusforsession2 = 'ML';
                                        break;
                                    case 'Paternity Leave':
                                        $leavestatusforsession2 = 'PL';
                                        break;
                                    case 'Maternity Leave':
                                        $leavestatusforsession2 = 'MTL';
                                        break;
                                    default:
                                        $leavestatusforsession2 = 'L';
                                        break;
                                }
                            }
                            if ($leaveType) {
                                switch ($leaveType) {
                                    case 'Casual Leave Probation':
                                        $status = 'CLP';
                                        break;
                                    case 'Sick Leave':
                                        $status = 'SL';
                                        break;
                                    case 'Loss Of Pay':
                                        $status = 'LOP';
                                        break;
                                    case 'Casual Leave':
                                        $status = 'CL';
                                        break;
                                    case 'Marriage Leave':
                                        $status = 'ML';
                                        break;
                                    case 'Paternity Leave':
                                        $status = 'PL';
                                        break;
                                    case 'Maternity Leave':
                                        $status = 'MTL';
                                        break;
                                    default:
                                        $status = 'L';
                                        break;
                                }
                            }
                            $isAbsent = !$this->isEmployeePresentOnDate($date->toDateString()) || $isAbsentFor;
                            if ($isAbsent) {
                                $halfdaypresent = 'A';
                            } elseif ($isHalfDayPresent) {
                                $halfdaypresent = 'HP';
                            } else {
                                $halfdaypresent = 'P';
                            }
                        } elseif ($isOnLeave) {
                            switch ($leaveType) {
                                case 'Casual Leave Probation':
                                    $status = 'CLP';
                                    break;
                                case 'Sick Leave':
                                    $status = 'SL';
                                    break;
                                case 'Loss Of Pay':
                                    $status = 'LOP';
                                    break;
                                case 'Casual Leave':
                                    $status = 'CL';
                                    break;
                                case 'Marriage Leave':
                                    $status = 'ML';
                                    break;
                                case 'Paternity Leave':
                                    $status = 'PL';
                                    break;
                                case 'Maternity Leave':
                                    $status = 'MTL';
                                    break;
                                default:
                                    $status = 'L';
                                    break;
                            }
                        } else {
                            $isAbsent = !$this->isEmployeePresentOnDate($date->toDateString()) || $isAbsentFor;
                            if ($isexceptioninAttendance === 'Present') {
                                $status = 'P';
                            } elseif ($isAbsent) {
                                $status = 'A';
                            } elseif ($isHalfDayPresent) {
                                $status = 'HP';
                            } else {
                                $status = 'P';
                            }
                        }


                        $week[] = [
                            'day' => $dayCount,
                            'isToday' => $isToday,
                            'isPublicHoliday' => $isPublicHoliday,
                            'isCurrentMonth' => true,
                            'isRegularised' => $isRegularised,
                            'isPreviousMonth' => false,
                            'backgroundColor' => $backgroundColor,
                            'onleave' => $isOnLeave,
                            'onHalfDayLeave' => $isOnHalfDayLeave,
                            'onFullDayLeave' => $isBeforeToDate,
                            'status' => $status,
                            'leavestatusforsession1' => $leavestatusforsession1,
                            'leavestatusforsession2' => $leavestatusforsession2,
                            'halfdaypresentforsession1' => $isHalfDayPresentForSession1,
                            'halfdaypresentforsession2' => $isHalfDayPresentForSession2,
                            'halfdaypresent' => $halfdaypresent,
                            'session2leave' => $isOnSecondSessionLeave,
                            'assignedDifferentShift' => $employeesecondshifttype,
                        ];

                        $dayCount++;
                    } else {
                        $week[] = [
                            'day' => $dayCount - $daysInMonth,
                            'isToday' => false,
                            'isPublicHoliday' => false,
                            'isCurrentMonth' => false,
                            'isRegularised' => false,
                            'isNextMonth' => true,
                            'backgroundColor' => '',
                            'onleave' => false,
                            'onHalfDayLeave' => false,
                            'status' => '',
                            'leavestatusforsession1' => null,
                            'leavestatusforsession2' => null,
                            'halfdaypresentforsession1' => false,
                            'halfdaypresentforsession2' => false,
                            'halfdaypresent' => '',
                            'onFullDayLeave' => '',
                            'session2leave' => null,
                            'assignedDifferentShift' => null,
                        ];
                        $dayCount++;
                    }
                }
                $calendar[] = $week;
            }

           
            
            $this->calendar = $calendar;
        } catch (\Exception $e) {
          
            FlashMessageHelper::flashError('An error occurred while generating the calendar. Please try again later.');

            $this->calendar = []; // Set calendar to empty array in case of error
        }
    }
    //This function will help us to check the details related to the particular date in the calendar
    public function updateDate($date1)
{
    try {
        // Log::info("updateDate method invoked", ['date1' => $date1]);

        // Parse the date
        $parsedDate = Carbon::parse($date1);
        // Log::debug("Parsed date", ['parsedDate' => $parsedDate->toDateString()]);

        $this->dateToCheck = $date1;
        // Log::debug("dateToCheck set", ['dateToCheck' => $this->dateToCheck]);

        // Check if the date is in the past
        if ($date1 < Carbon::now()->format('Y-m-d')) {
            $this->changeDate = 1;
            // Log::info("Date is in the past, changeDate set to 1", [
            //     'currentDate' => Carbon::now()->toDateString(),
            //     'date1' => $date1,
            //     'changeDate' => $this->changeDate
            // ]);
        } else {
            // Log::info("Date is not in the past", [
            //     'currentDate' => Carbon::now()->toDateString(),
            //     'date1' => $date1
            // ]);
        }
    } catch (\Exception $e) {
        Log::error("Error in updateDate", [
            'date1' => $date1,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        FlashMessageHelper::flashError('An error occurred while updating the date. Please try again later.');
    }
}
    //This function will help us to check whether the employee is absent 'A' or present 'P'
    public function dateClicked($date1)
{
    try {
        // Log::info("dateClicked invoked", ['date1' => $date1]);

        // Set selected date
        $this->selectedDate = $date1;
        // Log::debug("Selected date set", ['selectedDate' => $this->selectedDate]);

        // Check swipe statuses
        $isSwipedIn = SwipeRecord::whereDate('created_at', $date1)
            ->where('in_or_out', 'In')
            ->exists();
        $isSwipedOut = SwipeRecord::whereDate('created_at', $date1)
            ->where('in_or_out', 'Out')
            ->exists();

        // Log::debug("Swipe status", [
        //     'isSwipedIn' => $isSwipedIn,
        //     'isSwipedOut' => $isSwipedOut
        // ]);

        // Get employee second shift details
        $shiftData = $this->isEmployeeAssignedDifferentShift($date1, auth()->guard('emp')->user()->emp_id);
        $this->employeeSecondShift = $shiftData['shiftType'] ?? null;
        // Log::debug("Employee second shift", ['employeeSecondShift' => $this->employeeSecondShift]);

        $this->employeeSecondShiftDetails = DB::table('employee_details')
            ->join('company_shifts', function ($join) {
                $join->on(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(employee_details.company_id, '$[0]'))"), '=', 'company_shifts.company_id');
            })
            ->where('company_shifts.shift_name', $this->employeeSecondShift)
            ->where('emp_id', auth()->guard('emp')->user()->emp_id)
            ->select('company_shifts.shift_start_time', 'company_shifts.shift_end_time', 'company_shifts.shift_name', 'employee_details.*')
            ->first();

        // Log::debug("Employee second shift details", ['employeeSecondShiftDetails' => $this->employeeSecondShiftDetails]);

        // // Determine employee status based on swipe records
        if (!$isSwipedIn) {
            // Employee did not swipe in
            $this->status = 'A';
            // Log::info("Employee did not swipe in", ['status' => $this->status, 'date' => $date1]);
        } elseif (!$isSwipedOut) {
            // Employee swiped in but not out
            $this->status = 'P';
            // Log::info("Employee swiped in but not out", ['status' => $this->status, 'date' => $date1]);
        } else {
            // Both swipe in and swipe out are present
            // Log::info("Employee swiped in and out", ['date' => $date1]);
        }

        $this->updateDate($date1);
        $this->dateclicked = $date1;
        // Log::info("dateClicked processing completed", ['dateclicked' => $this->dateclicked]);
    } catch (\Exception $e) {
        Log::error("Error in dateClicked", [
            'date' => $date1,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        FlashMessageHelper::flashError('An error occurred while processing the date click. Please try again later.');
    }
}
    public function updatedFromDate($value)
    {
        try {
            // Additional logic if needed when from_date is updated
            $this->start_date_for_insights = $value;
            $this->updateModalTitle();
        } catch (\Exception $e) {
           
            FlashMessageHelper::flashError('An error occurred while updating the from date. Please try again later.');
        }
    }

    private function calculateWorkHrsForAbsentEmployees($date)
{

   
    $isEmployeeRegularisedOnDate = $this->isEmployeeRegularisedOnDate($date);
    if($isEmployeeRegularisedOnDate)
    {
        $inSwipeRecord = SwipeRecord::where('emp_id',auth()->guard('emp')->user()->emp_id)->where('in_or_out','IN')->where('is_regularized',1)->whereDate('created_at',$date)->first();
        $outSwipeRecord = SwipeRecord::where('emp_id',auth()->guard('emp')->user()->emp_id)->where('in_or_out','OUT')->where('is_regularized',1)->whereDate('created_at',$date)->first();
    }
    else
    {
        $inSwipeRecord = SwipeRecord::where('emp_id', auth()->guard('emp')->user()->emp_id)
        ->where('in_or_out', 'IN')
        ->whereDate('created_at', $date)
        ->first();

            // Fetch OUT swipe record
            $outSwipeRecord = SwipeRecord::where('emp_id', auth()->guard('emp')->user()->emp_id)
                ->where('in_or_out', 'OUT')
                ->whereDate('created_at', $date)
                ->orderByDesc('created_at')
                ->first();
        }
        // Fetch IN swipe record


        // Log fetched swipe records
    
        if ($inSwipeRecord && $outSwipeRecord) {
            // Parse swipe times using Carbon
            $inTime = Carbon::parse($inSwipeRecord->swipe_time);
            $outTime = Carbon::parse($outSwipeRecord->swipe_time);

         
            // Calculate the difference
            $timeDifference = $inTime->diff($outTime);
            $formattedDifference = $timeDifference->format('%h hours %i minutes');
            $totalMinutes = $inTime->diffInMinutes($outTime);

            // Log calculated results
          
            return [
                'formatted_difference' => $formattedDifference,
                'total_minutes' => $totalMinutes,
            ];
        }

        // Log missing swipe records case
       
        return null; // Return null if records are not found
    }
    public function updatedToDate($value)
    {
        try {
            // Additional logic if needed when to_date is updated
            $this->to_date = $value;
            $this->updateModalTitle();
        } catch (\Exception $e) {
            Log::error('Error in updatedToDate method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while updating the to date. Please try again later.');
        }
    }
    public function openlegend()
    {
        $this->legend = !$this->legend;
    }
    private function calculateTotalNumberOfAbsents($startDate, $endDate)
    {
        $absentDays = 0;

        // Add a log entry for the start and end date
      
        $totalMinutes = null;
        // Loop through each date between start and end date
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {

            // Log the current date being checked
           
            if (!$date->isWeekend()) {
                
                $holiday = HolidayCalendar::where('date', $date)->exists();
              
                if (!$holiday) {
                    $isOnLeave = $this->isEmployeeLeaveOnDate($date->format('Y-m-d'), auth()->guard('emp')->user()->emp_id);
                    $isOnFullDayLeave = $this->isEmployeeFullDayLeaveOnDate($date->format('Y-m-d'), auth()->guard('emp')->user()->emp_id);
                    $isOnHalfDayLeave = $this->isEmployeeHalfDayLeaveOnDate($date->format('Y-m-d'), auth()->guard('emp')->user()->emp_id)['sessionCheck'];

                    $isOnHalfDayLeaveforDifferentSessions = $this->isEmployeeHalfDayLeaveOnDate($date->format('Y-m-d'), auth()->guard('emp')->user()->emp_id)['doubleSessionCheck'];
                    if (!$isOnLeave && !$isOnFullDayLeave && !$isOnHalfDayLeaveforDifferentSessions && !$isOnHalfDayLeave) {
                        $isAbsent = !$this->isEmployeePresentOnDate($date->format('Y-m-d'));
                        $totalWorkHrs = $this->calculateWorkHrsForAbsentEmployees($date->format('Y-m-d'));
                        if ($totalWorkHrs !== null) {
                            $totalMinutes = $totalWorkHrs['total_minutes'];

                            // Log the total minutes fetched
                            }

                        if ($isAbsent || ($totalMinutes == 0) || $totalWorkHrs == null) {
                            $absentDays++;
                            // Log the increm   ent of absent days
                          } elseif ($totalMinutes < 270) {
                            $absentDays += 0.5;
                         }
                    }
                    if ($isOnHalfDayLeave) {

                        $isAbsent = !$this->isEmployeePresentOnDate($date->format('Y-m-d'));


                        if ($isAbsent) {
                            $absentDays += 0.5;
                            // Log the increment of absent days
                          }
                    }
                }
            }
            // Check if the employee is absent on the current date


            // Log the result of the absence check



        }

        // Log the final absent days count
      
        return $absentDays;
    }
    private function calculateTotalNumberofHolidays($startDate, $endDate)
    {
        // Log the start of the method
      
        // Initialize the total number of holidays
        $totalnumberofHolidays = 0;

        // Ensure $startDate and $endDate are Carbon instances
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Loop through each day between the start and end dates
        while ($startDate->lte($endDate)) {
            $dateString = $startDate->toDateString();

            // Log the current date being processed
            Log::debug('Processing date: ' . $dateString);

            // Check if the current date is a holiday
            $isHoliday = HolidayCalendar::where('date', $dateString)->exists();

            // Log whether the current date is a holiday
            Log::debug('Is ' . $dateString . ' a holiday? ' . ($isHoliday ? 'Yes' : 'No'));

            if ($isHoliday) {
                $totalnumberofHolidays++;
                Log::debug('Incrementing total number of holidays. Current count: ' . $totalnumberofHolidays);
            }

            // Move to the next day
            $startDate->addDay();
        }

        // Log the final count of holidays
        
        return $totalnumberofHolidays;
    }

    private function calculateTotalDaysForModalTite($startDate, $endDate)
    {
        $totalDaysForModalTitle = 0;

        // Iterate through the date range
        while ($startDate->lte($endDate)) {
            $dateString = $startDate->toDateString();
            $isHoliday = HolidayCalendar::where('date', $dateString)->exists();

            // Check if the day is not Saturday (6) or Sunday (7)

            if ($startDate->isWeekend()) {
               
            } elseif ($isHoliday) {
               
            } else {
                $totalDaysForModalTitle++;
            }

            // Move to the next day
            $startDate->addDay();
        }

        return $totalDaysForModalTitle;
    }
    
    private function updateModalTitle()
{
    try {
        // Log::info('updateModalTitle method started.', [
        //     'startDateForInsights' => $this->startDateForInsights,
        //     'toDate' => $this->toDate
        // ]);

        $formattedFromDate = Carbon::parse($this->startDateForInsights)->format('Y-m-d');
        $formattedToDate = Carbon::parse($this->toDate)->format('Y-m-d');

        // Log::debug('Formatted dates:', [
        //     'formattedFromDate' => $formattedFromDate,
        //     'formattedToDate' => $formattedToDate
        // ]);

        if ($formattedFromDate > $formattedToDate) {
            // Log::warning('Start date is greater than end date.', [
            //     'formattedFromDate' => $formattedFromDate,
            //     'formattedToDate' => $formattedToDate
            // ]);
                 $formattedFromDateForModalTitle = Carbon::parse($this->startDateForInsights)->format('d M');
                $formattedToDateForModalTitle = Carbon::parse($this->toDate)->format('d M');
                $this->modalTitle = "Insights for Attendance Period $formattedFromDateForModalTitle - $formattedToDateForModalTitle";
            $this->addError('date_range', 'The start date cannot be greater than the end date.');
            return;
        }

        if ($formattedFromDate >= Carbon::today()->format('Y-m-d') && $formattedToDate >= Carbon::today()->format('Y-m-d')) {
            $formattedFromDateForModalTitle = Carbon::parse($this->startDateForInsights)->format('d M');
            $formattedToDateForModalTitle = Carbon::parse($this->toDate)->format('d M');
            $this->modalTitle = "Insights for Attendance Period $formattedFromDateForModalTitle - $formattedToDateForModalTitle";
            // Log::info('Date range falls in future, setting default values.');
            // $this->setDefaultModalValues();
            // return;
        }

        Log::info('Calculating insights for past dates.');
        $fromDatetemp = Carbon::parse($formattedFromDate);
        $toDatetemp = $formattedToDate > Carbon::today()->format('Y-m-d') ? Carbon::yesterday() : Carbon::parse($formattedToDate);

        // Log::debug('Parsed date range for calculations.', [
        //     'fromDatetemp' => $fromDatetemp,
        //     'toDatetemp' => $toDatetemp
        // ]);
    
        $formattedFromDateForModalTitle = Carbon::parse($this->startDateForInsights)->format('d M');
        $formattedToDateForModalTitle = Carbon::parse($this->toDate)->format('d M');
        $this->modalTitle = "Insights for Attendance Period $formattedFromDateForModalTitle - $formattedToDateForModalTitle";
        $insights = $this->calculatetotalLateInSwipes($fromDatetemp, $toDatetemp);
        $outsights = $this->calculatetotalEarlyOutSwipes($fromDatetemp, $toDatetemp);

        // Log::debug('Insights calculations:', [
        //     'totalLateInSwipes' => $insights['lateSwipeCount'] ?? 'N/A',
        //     'totalEarlyOut' => $outsights['EarlyOutCount'] ?? 'N/A'
        // ]);

        $this->totalWorkingDays = $this->calculateTotalWorkingDays($fromDatetemp, $toDatetemp);
        $this->totalnumberofLeaves = $this->calculateTotalNumberOfLeaves(Carbon::parse($formattedFromDate), $toDatetemp);
        $this->totalnumberofAbsents = $this->calculateTotalNumberOfAbsents(Carbon::parse($formattedFromDate), $toDatetemp);
        $this->totalLateInSwipes = $insights['lateSwipeCount'];
        $this->totalnumberofEarlyOut = $outsights['EarlyOutCount'];
        $this->averageWorkHoursForModalTitle = $this->calculateAverageWorkHoursAndPercentage(Carbon::parse($formattedFromDate), $toDatetemp);
        $this->averageLastOutTime = $outsights['averageLastOutTime'];
        $this->averageFirstInTime = $insights['averageFirstInTime'];

        // Log::info('Successfully updated modal title and insights.');
    } catch (\Exception $e) {
        Log::error('Error in updateModalTitle method: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        FlashMessageHelper::flashError('An error occurred while updating the modal title. Please try again later.');
    }
}

private function calculatetotalLateInSwipes($startDate, $endDate)
{
    // Parse start and end dates using Carbon
    $startDate = Carbon::parse($startDate);
    $endDate = Carbon::parse($endDate);
    $lateSwipeCount = 0;
    $firstInCount = 0;
    $totalFirstInSeconds = 0;

    // Log::info("Calculating late swipes from {$startDate} to {$endDate}");

    // Iterate through the date range
    while ($startDate->lte($endDate)) {
        $tempStartDate = $startDate->toDateString();

        // Log::info("Checking date: {$tempStartDate}");

        // Check if the date is a holiday, weekend, or employee is on leave
        $isweekend = $startDate->isWeekend();
        $isHoliday = HolidayCalendar::whereDate('date', $tempStartDate)->exists();
        $isOnLeave = $this->isEmployeeLeaveOnDate($tempStartDate, auth()->guard('emp')->user()->emp_id);
        $isPresent = SwipeRecord::where('emp_id', auth()->guard('emp')->user()->emp_id)
            ->where('in_or_out', 'IN')
            ->whereDate('created_at', $tempStartDate)
            ->first();

        // Log::info("Weekend: " . ($isweekend ? 'Yes' : 'No') . ", Holiday: " . ($isHoliday ? 'Yes' : 'No') . ", On Leave: " . ($isOnLeave ? 'Yes' : 'No') . ", Present: " . ($isPresent ? 'Yes' : 'No'));

        // If not a holiday, weekend, or leave day, and the employee is present
        if (!$isHoliday && !$isweekend && !$isOnLeave && !empty($isPresent)) {
            // Check for late swipes
            $firstInCount++;

            try {
                $timePart = Carbon::parse($isPresent->swipe_time)->format('H:i:s');
                $swipeTime = Carbon::createFromFormat('H:i:s', $timePart);
                $limitTime = Carbon::createFromTime(10, 0, 0);
                
            } catch (\Exception $e) {
                $timePart = Carbon::parse($isPresent->swipe_time)->format('H:i');
                $swipeTime = Carbon::createFromFormat('H:i', $timePart);
                $limitTime = Carbon::createFromTime(10, 0);
                
            }

            // Log::info("Swipe Time: " . $swipeTime->format('H:i:s'));

            $totalFirstInSeconds += $swipeTime->diffInSeconds(Carbon::createFromTime(0, 0, 0));

            $lateSwipeExists = $swipeTime->greaterThan($limitTime);
            // Log::info("Late Swipe: " . ($lateSwipeExists ? 'Yes' : 'No'));

            if ($lateSwipeExists) {
                $lateSwipeCount++;
            }
        }

        // Move to the next day
        $startDate->addDay();
    }

    if ($firstInCount > 0) {
        $averageFirstInSeconds = $totalFirstInSeconds / $firstInCount;
        $averageFirstInTime = Carbon::createFromTime(0, 0, 0)->addSeconds($averageFirstInSeconds);
    } else {
        $averageFirstInTime = null;
    }

    // Log::info("Total Late Swipes: {$lateSwipeCount}");
    // Log::info("Average First In Time: " . ($averageFirstInTime ? $averageFirstInTime->format('H:i:s') : 'N/A'));

    return [
        'averageFirstInTime' => $averageFirstInTime ? $averageFirstInTime->format('H:i:s') : 'N/A',
        'lateSwipeCount' => $lateSwipeCount,
    ];
}
    private function calculatetotalEarlyOutSwipes($startDate, $endDate)
    {
    
        // Parse start and end dates using Carbon
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $earlyOutCount = 0;
        $lastOutCount = 0;
        $totalLastOutSeconds = 0;
        
        // Iterate through the date range
        while ($startDate->lte($endDate)) {
            $tempStartDate = $startDate->toDateString();

            // Check if the date is a holiday, weekend, or employee is on leave
            $isHoliday = HolidayCalendar::whereDate('date', $tempStartDate)->exists();
            $isweekend = $startDate->isWeekend();
            $isOnLeave = $this->isEmployeeLeaveOnDate($tempStartDate, auth()->guard('emp')->user()->emp_id);
            $isPresent = SwipeRecord::where('emp_id', auth()->guard('emp')->user()->emp_id)->where('in_or_out', 'OUT')->whereDate('created_at', $tempStartDate)->orderByDesc('created_at')->first();

            if (empty($isPresent)) {
                $isPresent = SwipeRecord::where('emp_id', auth()->guard('emp')->user()->emp_id)->where('in_or_out', 'IN')->whereDate('created_at', $tempStartDate)->first();
            }


            // Log the status of the current day
          
            // If not a holiday, weekend, or leave day, and the employee is present
            if (!$isHoliday && !$isweekend && !$isOnLeave && !empty($isPresent)) {
                // Check for late swipes
                $lastOutCount++;
                try {
                    $timePart = Carbon::parse($isPresent->swipe_time)->format('H:i:s');
                    $swipeTime = Carbon::createFromFormat('H:i:s', $timePart);
                    $limitTime = Carbon::createFromTime(19, 0, 0);
                } catch (\Exception $e) {
                    $timePart = Carbon::parse($isPresent->swipe_time)->format('H:i');
                    $swipeTime = Carbon::createFromFormat('H:i', $timePart);
                    $limitTime = Carbon::createFromTime(19, 0);
                }
                $totalLastOutSeconds += $swipeTime->diffInSeconds(Carbon::createFromTime(0, 0, 0));
                if ($swipeTime->lessThan($limitTime)) {
                    $earlyOutExists = true;
                } else {
                    // Swipe time is 10:00 AM or later
                    $earlyOutExists = false;
                }


                // Log the late swipe check
              
                // Increment late swipe count if a late swipe is found
                if ($earlyOutExists) {
                    $earlyOutCount++;
                    }
            }

            // Move to the next day
            $startDate->addDay();
        }
        if ($lastOutCount > 0) {
            $averageLastOutSeconds = $totalLastOutSeconds / $lastOutCount;
            $averageLastOutTime = Carbon::createFromTime(0, 0, 0)->addSeconds($averageLastOutSeconds);
        } else {
            $averageLastOutTime = null;  // No valid first in records
        }
        return [
            'averageLastOutTime' => $averageLastOutTime ? $averageLastOutTime->format('H:i:s') : 'N/A',
            'EarlyOutCount' => $earlyOutCount,

        ];
    }
    private function calculateAvgWorkingHrs($employeeId)
    {
        $currentDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $this->averageFormattedTime = '00:00';
        $standardWorkingMinutesPerDay = 9 * 60;
        $totalMinutesWorked = 0;  // Initialize total minutes worked
        $daysWithRecords = 0;
        while ($currentDate->lt($endDate)) {
            $SwipeInRecord = SwipeRecord::where('emp_id', $employeeId)->whereDate('created_at', $currentDate)->where('in_or_out', 'IN')->first();
            $SwipeOutRecord = SwipeRecord::where('emp_id', $employeeId)->whereDate('created_at', $currentDate)->where('in_or_out', 'OUT')->first();
            if ($SwipeInRecord && $SwipeOutRecord) {
                // Get the swipe times
                $swipeInTime = Carbon::parse($SwipeInRecord->swipe_time);
                $swipeOutTime = Carbon::parse($SwipeOutRecord->swipe_time);

                $timeDifferenceInMinutes = $swipeOutTime->diffInMinutes($swipeInTime);
                $workingHoursPercentage = ($timeDifferenceInMinutes / $standardWorkingMinutesPerDay) * 100;
                // Add the time difference to the total minutes worked
                $totalMinutesWorked += $timeDifferenceInMinutes;

                // Increment the count of days with records
                $daysWithRecords++;
                // echo " (" . round($workingHoursPercentage, 2) . "% of standard working hours)";
            }
            $currentDate->addDay();
        }
        if ($daysWithRecords > 0) {
            $averageMinutes = $totalMinutesWorked / $daysWithRecords;
            $averageHours = floor($averageMinutes / 60);
            $averageRemainingMinutes = $averageMinutes % 60;

            $this->averageFormattedTimeForCurrentMonth = sprintf('%02d:%02d', $averageHours, $averageRemainingMinutes);

            // Return or use the average formatted time

        }
        // $this->averageFormattedTime=$this->calculateAvgWorkHours()-$this->calculateAvgWorkHoursForPreviousMonth();
        $totalPossibleWorkingMinutes = $daysWithRecords * $standardWorkingMinutesPerDay;

        // Calculate the percentage of total minutes worked
        if ($totalPossibleWorkingMinutes > 0) {
            $this->totalWorkingPercentage = ($totalMinutesWorked / $totalPossibleWorkingMinutes) * 100;
        } else {
            $this->totalWorkingPercentage = 0;
        }
    }
    public function opentoggleButton()
    {

        $this->toggleButton = !$this->toggleButton;
    }
    private function countHolidaysBetweenDates($startDate, $endDate)
    {
        $holidayCount = HolidayCalendar::whereBetween('date', [$startDate, $endDate])->get();


        return $holidayCount;
    }
    private function countWeekendsBetweenDates($startDate, $endDate)
    {
        $weekendCount = 0;

        // Iterate through the date range
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            // Check if the current day is a Saturday (6) or Sunday (7)
            if ($currentDate->isSaturday() || $currentDate->isSunday()) {
                $weekendCount++;
            }
            // Move to the next day
            $currentDate->addDay();
        }

        return $weekendCount;
    }


    private function calculateTotalWorkingDays($startDate, $endDate)
    {
        $workingDays = 0;

        // Iterate through the date range
        while ($startDate->lte($endDate)) {
            $dateString = $startDate->toDateString();
            $isHoliday = HolidayCalendar::where('date', $dateString)->exists();
            $isOnLeave = $this->isEmployeeLeaveOnDate($startDate, auth()->guard('emp')->user()->emp_id);
            $isOnHalfDayLeave = $this->isEmployeeHalfDayLeaveOnDate($startDate, auth()->guard('emp')->user()->emp_id)['sessionCheck'];
            $isOnFullDayLeave = $this->isEmployeeFullDayLeaveOnDate($startDate, auth()->guard('emp')->user()->emp_id);
            $isOnHalfDayLeaveForDifferentSessions = $this->isEmployeeHalfDayLeaveOnDate($startDate, auth()->guard('emp')->user()->emp_id)['doubleSessionCheck'];

            // Check if the day is not Saturday (6) or Sunday (7)
            if (!$startDate->isWeekend() && !$isOnHalfDayLeave && !$isHoliday && !$isOnLeave && !$isOnFullDayLeave && !$isOnHalfDayLeaveForDifferentSessions) {
                $workingDays++;
                
            } elseif ($isOnHalfDayLeave) {
                $workingDays += 0.5;
               
            } else {
                if ($startDate->isWeekend()) {
                   
                } elseif ($isOnLeave || $isOnFullDayLeave || $isOnHalfDayLeaveForDifferentSessions) {
                    
                }
            }

            // Move to the next day
            $startDate->addDay();
        }

        return $workingDays;
    }

    private function calculateTotalNumberOfLeaves($startDate, $endDate)
    {

        $leaveCount = 0;

       
        // Iterate through the date range
        while ($startDate->lte($endDate)) {
            $tempStartDate = $startDate->toDateString();

            // Check if the current date is a holiday
            $isHoliday = HolidayCalendar::where('date', $tempStartDate)->exists();

            // Skip weekends (Saturday and Sunday) or holidays
            if ($startDate->isWeekend()) {
               } elseif ($isHoliday) {
               } else {
                // Log current date and status
              
                // Check if employee is on leave on this date
                $isOnLeave = $this->isEmployeeLeaveOnDate($tempStartDate, auth()->guard('emp')->user()->emp_id);
                $isOnHalfDayLeave = $this->isEmployeeHalfDayLeaveOnDate($tempStartDate, auth()->guard('emp')->user()->emp_id)['sessionCheck'];
                $isOnFullDayLeave = $this->isEmployeeFullDayLeaveOnDate($tempStartDate, auth()->guard('emp')->user()->emp_id);
                $isOnHalfDayLeaveForDifferentSessions = $this->isEmployeeHalfDayLeaveOnDate($tempStartDate, auth()->guard('emp')->user()->emp_id)['doubleSessionCheck'];
               
                if ($isOnLeave || $isOnFullDayLeave || $isOnHalfDayLeaveForDifferentSessions) {
                    $leaveCount++;
                } elseif ($isOnHalfDayLeave) {
                    $leaveCount += 0.5;
                }
            }

            // Move to the next day
            $startDate->addDay();
        }

       
        return $leaveCount;
    }
    public function calculateTotalDays()
    {
        try {

            $startDate = Carbon::parse($this->start_date_for_insights);
            $endDate = Carbon::parse($this->to_date);
            $originalEndDate = $endDate->copy();
            $this->updateModalTitle();
        } catch (\Exception $e) {
            Log::error('Error in calculateTotalDays method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while calculating total days. Please try again later.');
        }
    }
    private function calculateNumberofWeekends($startDate, $endDate)
    {
        $weekendDays = 0;
        $currentDate = $startDate->copy();
        while ($currentDate->lt($endDate)) {
            if ($currentDate->isSaturday() || $currentDate->isSunday()) {
                $weekendDays++;
            }
            $currentDate->addDay();
        }

        return $weekendDays;
    }
    private function calculateWorkingDays($startDate, $endDate, $employeeId)
    {
        try {

            $workingDays = 0;
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                if ($currentDate->isWeekday() && !$this->isEmployeeLeaveOnDate($currentDate, $employeeId) && $this->isEmployeePresentOnDate($currentDate)) {
                    $workingDays++;
                }
                $currentDate->addDay();
            }

            return $workingDays;
        } catch (\Exception $e) {
            Log::error('Error in calculateWorkingDays method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while calculating working days. Please try again later.');

            return 0;
        }
    }
    private function calculateWorkingDaysForModalTitle($startDate, $endDate, $employeeId)
    {
        try {

            $workingDays = 0;
            $currentDate = $startDate->copy();

            while ($currentDate->lt($endDate)) {
                if ($currentDate->isWeekday() && !$this->isEmployeeLeaveOnDate($currentDate, $employeeId) && $this->isEmployeePresentOnDate($currentDate)) {
                    $workingDays++;
                }
                $currentDate->addDay();
            }

            return $workingDays;
        } catch (\Exception $e) {
            Log::error('Error in calculateWorkingDays method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while calculating working days. Please try again later.');

            return 0;
        }
    }

    private function calculateActualHours($swipe_records)
    {
        try {
            $this->actualHours = [];

            for ($i = 0; $i < count($swipe_records) - 1; $i += 2) {
                $firstSwipeTime = strtotime($swipe_records[$i]->swipe_time);
                $secondSwipeTime = strtotime($swipe_records[$i + 1]->swipe_time);

                $timeDifference = $secondSwipeTime - $firstSwipeTime;

                $hours = floor($timeDifference / 3600);
                $minutes = floor(($timeDifference % 3600) / 60);

                $this->actualHours[] = sprintf("%02dhrs %02dmins", $hours, $minutes);
            }
        } catch (\Exception $e) {
            Log::error('Error in calculateActualHours method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while calculating actual hours. Please try again later.');
        }
    }
    public function viewDetails($id)
    {
        try {
            $this->showSR = true;
            $student = SwipeRecord::find($id);
            $this->view_student_emp_id = $student->emp_id;
            $this->view_student_swipe_time = $student->swipe_time;
            $this->view_student_in_or_out = $student->in_or_out;
        } catch (\Exception $e) {
            Log::error('Error in viewDetails method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while viewing details. Please try again later.');
        }
    }
    public function closeViewStudentModal()
    {
        try {
            $this->view_student_emp_id = '';
            $this->view_student_swipe_time = '';
            $this->view_student_in_or_out = '';
        } catch (\Exception $e) {
            Log::error('Error in closeViewStudentModal method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while closing view student modal. Please try again later.');
        }
    }
    public $show = false;
    public $show1 = false;
    public function showViewStudentModal()
    {
        try {
            $this->show = true;
        } catch (\Exception $e) {
            Log::error('Error in showViewStudentModal method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while showing view student modal. Please try again later.');
        }
    }

    public function showViewTableModal()
    {
        try {
            $this->show1 = true;
        } catch (\Exception $e) {
            Log::error('Error in showViewTableModal method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while showing view table modal. Please try again later.');
        }
    }

    public $showSR = false;
    public function openSwipes()
    {
        try {
            $this->showSR = true;
        } catch (\Exception $e) {
            Log::error('Error in openSwipes method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while opening swipes. Please try again later.');
        }
    }
    public function closeSWIPESR()
    {
        try {
            $this->showSR = false;
        } catch (\Exception $e) {
            Log::error('Error in closeSWIPESR method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while closing SWIPESR. Please try again later.');
        }
    }
    public function close1()
    {
        try {
            $this->show1 = false;
        } catch (\Exception $e) {
            Log::error('Error in close1 method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while closing 1. Please try again later.');
        }
    }
    public function calculateAvgWorkHoursForPreviousMonth()
    {
        // Get the start and end dates of the previous month
        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate = Carbon::now()->subMonth()->endOfMonth();

        // Retrieve all SwipeRecord entries for the previous month
        $records = SwipeRecord::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('swipe_time') // Ensure we order records by swipe time
            ->get();

        // Initialize variables
        $totalHours = 0;
        $recordCount = 0;

        // Group records by date
        $groupedRecords = $records->groupBy(function ($record) {
            return Carbon::parse($record->swipe_time)->toDateString();
        });

        // Iterate through each group (each day)
        foreach ($groupedRecords as $date => $dayRecords) {
            $swipeIn = $dayRecords->where('in_or_out', 'IN')->first();
            $swipeOut = $dayRecords->where('in_or_out', 'OUT')->last();

            if ($swipeIn && $swipeOut) {
                $swipeInTime = Carbon::parse($swipeIn->swipe_time);
                $swipeOutTime = Carbon::parse($swipeOut->swipe_time);

                // Calculate the difference in hours and add to total hours
                $totalHours += $swipeOutTime->diffInHours($swipeInTime);
                $recordCount++;
            }
        }

        // Calculate average hours worked
        $avgWorkHours = $recordCount > 0 ? $totalHours / $recordCount : 0;

        return $avgWorkHours;
    }
    public function calculateAvgWorkHours()
    {
        // Get the start and end dates of the current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Retrieve all SwipeRecord entries for the current month
        $records = SwipeRecord::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();

        // Initialize total hours
        $totalHours = 0;
        $recordCount = 0;

        // Iterate through records and calculate total working hours
        foreach ($records as $record) {
            $swipeIn = Carbon::parse($record->swipe_in);
            $swipeOut = Carbon::parse($record->swipe_out);

            // Calculate the difference in hours and add to total hours
            $totalHours += $swipeOut->diffInHours($swipeIn);
            $recordCount++;
        }

        // Calculate average hours worked
        $avgWorkHours = $recordCount > 0 ? $totalHours / $recordCount : 0;

        return $avgWorkHours;
    }
    public function beforeMonth()
    {
        try {
            $date = Carbon::create($this->year, $this->month, 1)->subMonth();
            $this->year = $date->year;
            $this->month = $date->month;

            $this->startDateForInsights = $date->startOfMonth()->toDateString();
            $this->toDate = $date->endOfMonth()->toDateString();
            $today = Carbon::today();
            $this->generateCalendar();
            $this->updateModalTitle();
            $startDateOfPreviousMonth = $date->startOfMonth()->toDateString();
            $endDateOfPreviousMonth = $date->endOfMonth()->toDateString();

            if ($today->year == $date->year && $today->month == $date->month && $endDateOfPreviousMonth > $today->toDateString()) {
                // Adjust $endDateOfPreviousMonth to today's date since it's greater than today

                $this->averageWorkHrsForCurrentMonth = $this->calculateAverageWorkHoursAndPercentage($startDateOfPreviousMonth, $today->toDateString());
            } elseif ($today->year >= $date->year && $today->month >= $date->month && $endDateOfPreviousMonth > $today->toDateString()) {

                $this->averageWorkHrsForCurrentMonth = '-';
            } else {
                $this->averageWorkHrsForCurrentMonth = $this->calculateAverageWorkHoursAndPercentage($startDateOfPreviousMonth, $endDateOfPreviousMonth);
            }
            //$this->averageWorkHrsForCurrentMonth = $this->calculateAverageWorkHoursAndPercentage($startDateOfPreviousMonth, $endDateOfPreviousMonth);


            // $previousMonthStart = $date->subMonth()->startOfMonth()->toDateString();
            $this->percentageinworkhrsforattendance = $this->calculateDifferenceInAvgWorkHours($date->format('Y-m'));

            $this->dateClicked($date->startOfMonth()->toDateString());
        } catch (\Exception $e) {
            Log::error('Error in beforeMonth method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while navigating to the previous month. Please try again later.');
        }
    }

    public function nextMonth()
    {
        try {
            $date = Carbon::create($this->year, $this->month, 1)->addMonth();
            $this->year = $date->year;
            $this->month = $date->month;
            $today = Carbon::today();
            $this->startDateForInsights = $date->startOfMonth()->toDateString();

            if (
                Carbon::parse($this->toDate)->greaterThan(Carbon::today()) && // Check if toDate is greater than today
                Carbon::parse($this->toDate)->isSameMonth(Carbon::today()) && // Check if the month is the same as today
                Carbon::parse($this->toDate)->isSameYear(Carbon::today())     // Check if the year is the same as today
            ) {
                $this->toDate = Carbon::now()->subDay()->toDateString(); // Set toDate to today's date
            } else {
                $this->toDate = $date->endOfMonth()->toDateString();
            }
            $this->generateCalendar();
            $this->updateModalTitle();
            $this->changeDate = 1;
            $this->dateClicked($date->toDateString());
            $nextdate = Carbon::create($date->year, $date->month, 1)->addMonth();
            $lastDateOfNextMonth = $date->endOfMonth()->toDateString();
            $startDateOfPreviousMonth = $date->startOfMonth()->toDateString();
            $endDateOfPreviousMonth = $date->endOfMonth()->toDateString();

            if ($today->year == $date->year && $today->month == $date->month && $endDateOfPreviousMonth > $today->toDateString()) {
                // Adjust $endDateOfPreviousMonth to today's date since it's greater than today


                $this->averageWorkHrsForCurrentMonth = $this->calculateAverageWorkHoursAndPercentage($startDateOfPreviousMonth, $today->copy()->subDay()->toDateString());
            } elseif ($today->year >= $date->year && $today->month >= $date->month && $endDateOfPreviousMonth > $today->toDateString()) {
                $this->averageWorkHrsForCurrentMonth = '-';
            } else {

                $this->averageWorkHrsForCurrentMonth = $this->calculateAverageWorkHoursAndPercentage($startDateOfPreviousMonth, $endDateOfPreviousMonth);
            }
            $this->percentageinworkhrsforattendance = $this->calculateDifferenceInAvgWorkHours($date->format('Y-m'));
        } catch (\Exception $e) {
            Log::error('Error in nextMonth method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while navigating to the next month. Please try again later.');
        }
    }

    public function openattendanceperiodModal()
    {
        $this->openattendanceperiod = true;
    }
    public function closeattendanceperiodModal()
    {
        $this->openattendanceperiod = false;
        $this->start_date_for_insights = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->to_date = Carbon::now()->subDay()->toDateString();
        $this->updateModalTitle();
    }
    public function checkDateInRegularisationEntries($d)
    {
        try {
            $this->showRegularisationDialog = true;
            $employeeId = auth()->guard('emp')->user()->emp_id;

            $regularisationRecords = RegularisationDates::where('emp_id', $employeeId)
                ->whereIn('regularisation_dates.status', [2, 13]) // Include both approved (2) and status (13)
                ->join('status_types', 'status_types.status_code', '=', 'regularisation_dates.status') // Join with status_types table
                ->select('regularisation_dates.*', 'status_types.status_name') // Select fields from both tables
                ->get();

            $dateFound = false;
            $result = null;

            foreach ($regularisationRecords as $record) {
                $entries = json_decode($record->regularisation_entries, true);

                foreach ($entries as $entry) {
                    if (isset($entry['date']) && $entry['date'] === $d) {
                        $dateFound = true;
                        $result = [
                            'date' => $entry['date'],
                            'reason' => $entry['reason'],
                            'approved_date' => $record['approved_date'],
                            'approved_by' => $record['approved_by']
                        ];
                        break 2; // Exit both loops
                    }
                }
            }


            if ($result) {
                $this->regularised_date_to_check = $result['date'];
                $this->regularised_by = $result['approved_by'];
                $this->regularised_date = $result['approved_date'];
                $this->regularised_reason = $result['reason'];
            } else {
                $this->regularised_date_to_check = null;
                $this->regularised_by = null;
                $this->regularised_date = null;
                $this->regularised_reason = null;
            }
        } catch (\Exception $e) {
            // Handle the exception
            Log::error('Error in checkDateInRegularisationEntries method: ' . $e->getMessage());
            // Optionally, you can set a user-friendly message to be displayed
            $this->errorMessage = 'An error occurred while checking the regularisation entries. Please try again later.';

            // Reset the fields in case of an error
            $this->regularised_date_to_check = null;
            $this->regularised_by = null;
            $this->regularised_date = null;
            $this->regularised_reason = null;
        }
    }

    public function render()
    {
        try {
            $this->dynamicDate = now()->format('Y-m-d');
            Log::info('Dynamic Date set: ' . $this->dynamicDate);

            $employeeId = auth()->guard('emp')->user()->emp_id;
            $this->employeeIdForRegularisation = $employeeId;
            Log::info('Employee ID: ' . $employeeId);

            $this->swiperecord = SwipeRecord::where('swipe_records.emp_id', $employeeId)
                ->where('is_regularized', 1)
                ->join('employee_details', 'swipe_records.emp_id', '=', 'employee_details.emp_id')
                ->select('swipe_records.*', 'employee_details.first_name', 'employee_details.last_name')
                ->get();
            Log::info('Regularized swipe records retrieved', ['records_count' => $this->swiperecord->count()]);

            $currentDate = Carbon::now()->format('Y-m-d');
            $holiday = HolidayCalendar::all();
            $today = Carbon::today();
            $data = SwipeRecord::join('employee_details', 'swipe_records.emp_id', '=', 'employee_details.emp_id')
                ->where('swipe_records.emp_id', $employeeId)
                ->whereDate('swipe_records.created_at', $today)
                ->select('swipe_records.*', 'employee_details.first_name', 'employee_details.last_name')
                ->get();
            Log::info('Today\'s swipe records retrieved', ['count' => $data->count()]);

            $this->holiday = $holiday;
            $this->leaveApplies = LeaveRequest::where('emp_id', $employeeId)->get();
            Log::info('Leave requests retrieved', ['count' => $this->leaveApplies->count()]);

            if ($this->changeDate == 1) {
                $this->currentDate2 = $this->dateclicked;
                Log::info('Date clicked: ' . $this->currentDate2);

                if ($this->currentDate2 == date('Y-m-d')) {
                    $this->currentDate2recordin = '-';
                    $this->currentDate2recordout = '-';
                    Log::info('Date is today. No swipe records set yet.');
                }

                if ($this->isEmployeeRegularisedOnDate($this->currentDate2)) {
                    Log::info('Employee is regularized on date: ' . $this->currentDate2);
                    $this->currentDate2recordin = SwipeRecord::where('emp_id', $employeeId)->whereDate('created_at', $this->currentDate2)->where('in_or_out', 'IN')->where('is_regularized', 1)->first();
                    $this->currentDate2recordout = SwipeRecord::where('emp_id', $employeeId)->whereDate('created_at', $this->currentDate2)->where('in_or_out', 'OUT')->where('is_regularized', 1)->first();
                } else {
                    Log::info('Employee is NOT regularized on date: ' . $this->currentDate2);
                    $this->currentDate2recordin = SwipeRecord::where('emp_id', $employeeId)->whereDate('created_at', $this->currentDate2)->where('in_or_out', 'IN')->first();
                    $this->currentDate2recordout = SwipeRecord::where('emp_id', $employeeId)->whereDate('created_at', $this->currentDate2)->where('in_or_out', 'OUT')->orderBy('updated_at', 'desc')->first();
                }

                if (isset($this->currentDate2recordin) && isset($this->currentDate2recordout)) {
                    $this->first_in_time = Carbon::parse($this->currentDate2recordin->swipe_time)->format('H:i');
                    $this->last_out_time = Carbon::parse($this->currentDate2recordout->swipe_time)->format('H:i');

                    Log::info("Swipe IN time: {$this->first_in_time}, Swipe OUT time: {$this->last_out_time}");

                    $firstInTime = Carbon::createFromFormat('H:i', $this->first_in_time);
                    $lastOutTime = Carbon::createFromFormat('H:i', $this->last_out_time);

                    if ($lastOutTime < $firstInTime) {
                        $lastOutTime->addDay();
                        Log::info('Adjusted last out time to next day');
                    }

                    $this->first_in_time_for_date = $firstInTime;
                    $this->last_out_time_for_date = $lastOutTime;

                    $this->timeDifferenceInMinutesForCalendar = $firstInTime->diffInMinutes($lastOutTime);
                    $this->hours = floor($this->timeDifferenceInMinutesForCalendar / 60);
                    $minutes = $this->timeDifferenceInMinutesForCalendar % 60;
                    $this->minutesFormatted = str_pad($minutes, 2, '0', STR_PAD_LEFT);

                    Log::info("Total worked time: {$this->hours} hours and {$this->minutesFormatted} minutes");
                } elseif (!isset($this->currentDate2recordout) && isset($this->currentDate2recordin)) {
                    $this->first_in_time = Carbon::parse($this->currentDate2recordin->swipe_time)->format('H:i');
                    $this->last_out_time = $this->first_in_time;
                    Log::info("Only IN swipe found at: {$this->first_in_time}");
                } elseif (!in_array($this->currentDate2, ['Saturday', 'Sunday'])) {
                    $this->first_in_time = null;
                    $this->last_out_time = null;
                    Log::info("No IN/OUT swipe records and not a weekend.");
                } else {
                    $this->first_in_time = '-';
                    $this->last_out_time = '-';
                    Log::info("Weekend detected: {$this->currentDate2}");
                }

                if (Carbon::parse($this->currentDate2)->isWeekend() || $this->isHolidayOnDate($this->currentDate2) || $this->first_in_time == $this->last_out_time) {
                    $this->shortFallHrs = '-';
                    $this->work_hrs_in_shift_time = '-';
                    $this->excessHrs = '-';
                    Log::info("No shortfall or excess hours (Weekend/Holiday/Same in-out time)");
                } else {
                    $standardMinutesForShortFall = 9 * 60;
                    $timeDifferenceForShortFall = $standardMinutesForShortFall - $this->timeDifferenceInMinutesForCalendar;

                    if ($timeDifferenceForShortFall == 0) {
                        $this->shortFallHrs = '08:59'; // Intentional? Log it.
                        $this->excessHrs = '-';
                    } elseif ($this->timeDifferenceInMinutesForCalendar > $standardMinutesForShortFall) {
                        $this->shortFallHrs = '-';
                        $timeDifferenceForExcess = $this->timeDifferenceInMinutesForCalendar - $standardMinutesForShortFall;
                        $excesshours = floor($timeDifferenceForExcess / 60);
                        $excessminutes = $timeDifferenceForExcess % 60;
                        $this->excessHrs = sprintf('%02d:%02d', $excesshours, $excessminutes);
                    } else {
                        $shortfallhours = floor($timeDifferenceForShortFall / 60);
                        $shortfallminutes = $timeDifferenceForShortFall % 60;
                        $this->shortFallHrs = sprintf('%02d:%02d', $shortfallhours, $shortfallminutes);
                        $this->excessHrs = '-';
                    }

                    $this->work_hrs_in_shift_time = '09:00';
                    Log::info("Shortfall: {$this->shortFallHrs}, Excess: {$this->excessHrs}");
                }

                $this->currentDate2recordexists = SwipeRecord::where('emp_id', $employeeId)->whereDate('created_at', $this->currentDate2)->exists();
                Log::info("Record exists on date {$this->currentDate2}: " . ($this->currentDate2recordexists ? 'Yes' : 'No'));
            } else {
                $this->currentDate2 = Carbon::now()->format('Y-m-d');
                Log::info('No date change detected. Defaulting to today: ' . $this->currentDate2);
            }

            $swipe_records = SwipeRecord::where('emp_id', $employeeId)->whereDate('created_at', $this->currentDate2)->get();
            $this->swipe_records_count = $swipe_records->count();
            $this->swiperecordsfortoggleButton = $swipe_records;
            Log::info('Swipe records on current date', ['count' => $this->swipe_records_count]);

            $swipe_records1 = SwipeRecord::where('emp_id', $employeeId)->orderBy('created_at', 'desc')->get();
            Log::info('Swipe records fetched for history', ['count' => $swipe_records1->count()]);

            $this->calculateActualHours($swipe_records);
            Log::info('Actual hours calculated.');

            return view('livewire.attendance', [
                'Holiday' => $this->holiday,
                'Swiperecords' => $swipe_records,
                'SwiperecordsCount' => $this->swipe_records_count,
                'Swiperecords1' => $swipe_records1,
                'data' => $data,
                'CurrentDateTwoRecord' => $this->currentDate2record,
                'ChangeDate' => $this->changeDate,
                'CurrentDate2recordexists' => $this->currentDate2recordexists,
                'avgLateIn' => $this->avgLateIn,
                'avgEarlyOut' => $this->avgEarlyOut,
                'avgSignOutTime' => $this->avgSwipeOutTime,
                'modalTitle' => $this->modalTitle,

            ]);
        } catch (\Exception $e) {
            Log::error('Error in render method: ' . $e->getMessage());
            FlashMessageHelper::flashError('An error occurred while rendering the page. Please try again later.');
            return view('livewire.attendance'); // Return an empty view or handle it as appropriate
        }
    }
}
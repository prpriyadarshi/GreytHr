<?php

namespace App\Livewire;

use App\Models\HelpDesks;
use App\Models\HolidayCalendar;
use App\Models\Request;
use Livewire\Component;
use Carbon\Carbon;
class AuthChecking extends Component
{
    public $activeTab = 'active';
    public $records;

    public $selectedCatalog = 'active';
    public $requestCategories='';
    public $selectedNew = 'active';
    public function confirmByAdmin($taskId)
    {
        $task = HelpDesks::find($taskId);
        if ($task) {
            $task->update([
                'status' => 'Open',
                'category' => $task->category ?? 'N/A',
                'mail'   => $task->mail ?? 'N/A',
            ]);
        }
        return redirect()->to('/HelpDesk');
    }

    public function openForDesks($taskId)
    {
        $task = HelpDesks::find($taskId);

        if ($task) {
            $task->update(['status' => 'Completed']);
        }
        return redirect()->to('/HelpDesk');
    }

    public function closeForDesks($taskId)
    {
        $task = HelpDesks::find($taskId);

        if ($task) {
            $task->update(['status' => 'Open']);
        }
        return redirect()->to('/HelpDesk');
    }
    public function pendingForDesks($taskId)
    {
        $task = HelpDesks::find($taskId);

        if ($task) {
            $task->update(['status' => 'Pending']);
        }
        return redirect()->to('/HelpDesk');
    }
    public $forIT, $forHR, $forFinance, $forAdmin;

    public function render()
    {
        $requestCategories = Request::select('Request', 'category')->get();
        if (auth()->guard('it')->check()) {
            $companyId = auth()->guard('it')->user()->company_id;
    
            $this->forIT = HelpDesks::with('emp')
                ->whereHas('emp', function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->orderBy('created_at', 'desc')
                ->whereIn('category', $this->requestCategories['IT'])
                ->get();
        } elseif (auth()->guard('hr')->check()) {
            $companyId = auth()->guard('hr')->user()->company_id;
    
            $this->forHR = HelpDesks::with('emp')
                ->whereHas('emp', function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->orderBy('created_at', 'desc')
                ->whereIn('category', $this->requestCategories['HR'])
                ->get();
        } elseif (auth()->guard('finance')->check()) {
            $companyId = auth()->guard('finance')->user()->company_id;
    
            $this->forFinance = HelpDesks::with('emp')
                ->whereHas('emp', function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->orderBy('created_at', 'desc')
                ->whereIn('category', $this->requestCategories['Finance'])
                ->get();
        }
    
        if (auth()->guard('admins')->check()) {
            $companyId = auth()->guard('admins')->user()->company_id;
            $thresholdDate = Carbon::now()->subDays(7);
    
            // Get holidays within the last 7 days
            $holidays = HolidayCalendar::whereBetween('date', [$thresholdDate->startOfDay(), Carbon::now()->endOfDay()])
                ->pluck('date')
                ->map(function($date) {
                    return Carbon::parse($date)->format('Y-m-d'); // Normalize date format
                })
                ->toArray();
    
            // Count the number of non-holiday days in the last 7 days
            $nonHolidayDays = 0;
            $currentDate = Carbon::now()->startOfDay();
    
            while ($currentDate->greaterThanOrEqualTo($thresholdDate->startOfDay())) {
                $formattedDate = $currentDate->format('Y-m-d');
    
                // Check if it's a weekend or a holiday
                if (!in_array($formattedDate, $holidays) && !in_array($currentDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                    $nonHolidayDays++;
                }
    
                $currentDate->subDay(); // Move to the previous day
            }
    
            HelpDesks::where('status', 'Recent')
                ->where('created_at', '<=', $thresholdDate)
                ->update(['status' => 'Open']);
    
            $this->forAdmin = HelpDesks::with('emp')
                ->whereHas('emp', function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }
    
        // Tab-specific logic for IT
        if ($this->activeTab == 'active') {
            $this->forIT = HelpDesks::with('emp')
                ->where('status', 'Open')
                ->orderBy('created_at', 'desc')
                ->whereIn('category', $this->requestCategories['IT'])
                ->get();
        } elseif ($this->activeTab == 'pending') {
            $this->forIT = HelpDesks::with('emp')
                ->where('status', 'Pending')
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($this->activeTab == 'closed') {
            $this->forIT = HelpDesks::with('emp')
                ->where('status', 'Completed')
                ->orderBy('created_at', 'desc')
                ->get();
        }
    
        // Tab-specific logic for HR
        if ($this->activeTab == 'active') {
            $this->forHR = HelpDesks::with('emp')
                ->where('status', 'Open')
                ->orderBy('created_at', 'desc')
                ->whereIn('category', $this->requestCategories['HR'])
                ->get();
        } elseif ($this->activeTab == 'pending') {
            $this->forHR = HelpDesks::with('emp')
                ->where('status', 'Pending')
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($this->activeTab == 'closed') {
            $this->forHR = HelpDesks::with('emp')
                ->where('status', 'Completed')
                ->orderBy('created_at', 'desc')
                ->get();
        }
    
        // Tab-specific logic for Finance
        if ($this->activeTab == 'active') {
            $this->forFinance = HelpDesks::with('emp')
                ->where('status', 'Open')
                ->orderBy('created_at', 'desc')
                ->whereIn('category', $this->requestCategories['Finance'])
                ->get();
        } elseif ($this->activeTab == 'pending') {
            $this->forFinance = HelpDesks::with('emp')
                ->where('status', 'Pending')
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($this->activeTab == 'closed') {
            $this->forFinance = HelpDesks::with('emp')
                ->where('status', 'Completed')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        if ($requestCategories->isNotEmpty()) {
            // Group categories by their request
            $this->requestCategories = $requestCategories->groupBy('Request')->map(function ($group) {
                return $group->unique('category'); // Ensure categories are unique
            });
        } else {
            // Handle the case where there are no requests
            $this->requestCategories = collect(); // Initialize as an empty collection
        }

        return view('livewire.auth-checking');
    }
}

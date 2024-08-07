<div>
    @php
    $employeeId = auth()->guard('emp')->user()->emp_id;
    $managerId = DB::table('employee_details')
    ->join('companies', 'employee_details.company_id', '=', 'companies.company_id')
    ->where('employee_details.manager_id', $employeeId)
    ->select('companies.company_logo', 'companies.company_name')
    ->first();
    @endphp
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <!-- <i class='bx bxs-smile icon'></i> -->
            <img class="m-auto" src="{{ asset('images/hr_new_blue.png') }}" alt="Company Logo" style="width: 6em !important;">
        </a>
        <ul class="side-menu">
            <li><a href="/" class="active"><i class='fas fa-home icon'></i> Home</a></li>

            <li>
                <a href="#"><i class='fas fa-clock icon'></i> Time Sheets <i class='fa fa-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="/time-sheet">Time Sheet</a></li>
                    @if ($managerId)
                    <li>
                        <a href="/team-time-sheets">
                            Team Time Sheets
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            <li><a href="/Feeds"><i class='fas fa-rss icon'></i> Feeds</a></li>
            <li><a href="/PeoplesList"><i class='fas fa-users icon'></i> People</a></li>
            <!-- <li class="divider" data-text="main">Main</li> -->
            <li>
                <a href="#"><i class='fas fa-file-alt icon'></i> To Do <i class='fa fa-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="/tasks">Tasks</a></li>
                    <li><a href="/employees-review">Review</a></li>
                </ul>
            </li>
            <li>
                <a href="#"><i class='fas fas-solid fa-money-bill-transfer icon'></i> Salary <i class='fa fa-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="/slip">Payslips</a></li>
                    <li><a href="/ytd">YTD Reports</a></li>
                    <li><a href="/itstatement">IT Statement</a></li>
                    <li><a href="/formdeclaration">IT Declaration</a></li>
                    <li><a href="/reimbursement">Reimbursement</a></li>
                    <li><a href="/investment">Proof of Investment</a></li>
                    <li><a href="//salary-revision">Salary Revision</a></li>
                </ul>
            </li>
            <li>
                <a href="#"><i class='fas fa-file-alt icon'></i> Leave <i class='fa fa-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="/leave-form-page">Leave Apply</a></li>
                    <li><a href="/leave-balances">Leave Balances</a></li>
                    <li><a href="/leave-calender">Leave Calendar</a></li>
                    <li><a href="/holiday-calendar">Holiday Calendar</a></li>
                    @if($managerId)
                    <li>
                        <a href="//team-on-leave-chart">
                            @livewire('team-on-leave')
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            <li>
                <a href="#"><i class='fas fa-clock icon'></i> Attendance <i class='fa fa-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="/Attendance">Attendance Info</a></li>
                    @if ($managerId)
                    <li>
                        <a href="/whoisinchart">
                            @livewire('whoisin')
                        </a>
                    </li>
                    <li>
                        <a href="/employee-swipes-data">
                            @livewire('employee-swipes')
                        </a>
                    </li>
                    <li>
                        <a href="/attendance-muster-data">
                            @livewire('attendance-muster')
                        </a>
                    </li>
                    <li>
                        <a href="/shift-roaster-data">
                            @livewire('shift-roaster-submodule')
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            <li><a href="/document"><i class='fas fa-folder icon'></i>Document Center</a></li>
            <li>
                <a href="#"><i class='fas fa-headset icon'></i> Helpdesk <i class='fa fa-chevron-right icon-right'></i></a>
                <ul class="side-dropdown">
                    <li><a href="/HelpDesk">New Requests</a></li>
                    <li><a href="/users">Connect</a></li>
                </ul>
            </li>
            <li><a href="/delegates"><i class='fas fa-user-friends icon'></i> Workflow Delegates</a></li>
            @if ($managerId)
            <li>
                <a href="/reports">

                    <i class="fas fa-file-alt icon" style="color:#6c7e90"></i> Reports
                </a>
            </li>
            @endif
            <!-- <li class="divider" data-text="table and forms">Table and forms</li>
			<li><a href="#"><i class='bx bx-table icon' ></i> Tables</a></li>
			<li>
				<a href="#"><i class='bx bxs-notepad icon' ></i> Forms <i class='bx bx-chevron-right icon-right' ></i></a>
				<ul class="side-dropdown">
					<li><a href="#">Basic</a></li>
					<li><a href="#">Select</a></li>
					<li><a href="#">Checkbox</a></li>
					<li><a href="#">Radio</a></li>
				</ul>
			</li> -->
        </ul>
        <!-- <div class="ads">
			<div class="wrapper">
				<a href="#" class="btn-upgrade">Upgrade</a>
				<p>Become a <span>PRO</span> member and enjoy <span>All Features</span></p>
			</div>
		</div> -->
    </section>
    <!-- SIDEBAR -->

    <!-- NAVBAR -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='fas fa-bars toggle-sidebar'></i>
            <form action="#">
                <div class="input-group">
                    <input type="text" class="form-control" aria-label="Search..." placeholder="Search...">
                    <span class="input-group-text"><i class='fa fa-search icon'></i></span>
                </div>
            </form>
            <div>
                @livewire('notification')
            </div>
            <span class="divider"></span>
            <div class="profile">
                <div class="d-flex brandLogoDiv">
                    @livewire('company-logo')
                    @if(!empty($loginEmployeeProfile->image) && $loginEmployeeProfile->image !== 'null')
                    <img class="navProfileImg" src="{{ $loginEmployeeProfile->image_url }}" alt="">
                    @else
                    <img class="navProfileImg" src="{{ asset('images/user.jpg') }}" alt="">
                    @endif
                </div>
                <ul class="profile-link">
                    <li><a href="/ProfileInfo"><i class='fas fa-user-circle icon'></i> Profile</a></li>
                    <li><a href="/Settings"><i class='fas fa-cog'></i> Settings</a></li>
                </ul>
            </div>
            <div>@livewire('log-out')</div>
        </nav>
        <!-- NAVBAR -->
    </section>
    <!-- Logout Modal -->
    @if( $showLogoutModal == true)
    <div class="modal" id="logoutModal" tabindex="4" style="display: block;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style=" background-color: rgb(2, 17, 79);">
                    <h6 class="modal-title " id="logoutModalLabel" style="align-items: center;">Confirm Logout</h6>
                </div>
                <div class="modal-body text-center" style="font-size: 16px;">
                    Are you sure you want to logout?
                </div>
                <div class="d-flex justify-content-center p-3" style="gap: 10px;">
                    <button type="button" class="submit-btn mr-3" wire:click="confirmLogout">Logout</button>
                    <button type="button" class="cancel-btn1" wire:click="cancelLogout">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif

    <!-- NAVBAR -->
</div>
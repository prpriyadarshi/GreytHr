<div>
    <div class="detail-container ">
        <div class="row m-0 p-0">
            <div class="col-md-4 p-0 m-0 mb-2 ">
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb d-flex align-items-center ">
                        <li class="breadcrumb-item"><a type="button" style="color:#fff !important;" class="submit-btn"
                                href="{{ route('leave-form-page') }}">Back</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Leave - View Details</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="headers-details">
            <h6>Leave Applied on {{ $leaveRequest->created_at->format('d M, Y') }} </h6>
        </div>
        <div class="approved-leave d-flex gap-3">
            <div class="heading rounded mb-3">
                <div class="heading-2 rounded">
                    <div class="d-flex flex-row justify-content-between rounded">
                        <div class="field">
                            <span class="normalTextValue">
                                @if (strtoupper($leaveRequest->status) == 'WITHDRAWN')
                                Withdrawn by
                                @elseif(strtoupper($leaveRequest->status) == 'APPROVED')
                                Approved by
                                @else
                                Pending with
                                @endif
                            </span>
                            <br>
                            @if (strtoupper($leaveRequest->status) == 'WITHDRAWN')
                            <span class="normalText">
                                {{ ucwords(strtoupper($this->leaveRequest->employee->first_name)) }}
                                {{ ucwords(strtoupper($this->leaveRequest->employee->last_name)) }}
                            </span>
                            @elseif(!empty($leaveRequest['applying_to']))
                            @foreach ($leaveRequest['applying_to'] as $applyingTo)
                            <span class="normalText">
                                {{ ucwords(strtoupper($applyingTo['report_to'])) }}
                            </span>
                            @endforeach
                            @endif
                        </div>

                        <div>
                            <span>
                                @if (strtoupper($leaveRequest->status) == 'APPROVED')
                                <span class="approvedColor mt-2">{{ strtoupper($leaveRequest->status) }}</span>
                                @elseif(strtoupper($leaveRequest->status) == 'REJECTED')
                                <span class="rejectColor mt-2">{{ strtoupper($leaveRequest->status) }}</span>
                                @else
                                <span class="otherStatus mt-2">{{ strtoupper($leaveRequest->status) }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="middle-container">
                        <div class="view-container m-0 p-0">
                            <div class="first-col m-0 p-0 d-flex gap-4">
                                <div class="field p-2">
                                    <span class="normalTextValue">From date</span> <br>
                                    <span class="normalText" style="font-weight:600;">
                                        {{ $leaveRequest->from_date->format('d M, Y') }}<br><span
                                            style="color: #494F55;font-size: 9px; ">{{ $leaveRequest->from_session }}</span></span>
                                </div>
                                <div class="field p-2">
                                    <span class="normalTextValue">To date</span> <br>
                                    <span class="normalText"
                                        style="font-weight:600;">{{ $leaveRequest->to_date->format('d M, Y') }}
                                        <br><span
                                            style="color: #494F55;font-size: 9px; ">{{ $leaveRequest->to_session }}</span></span>
                                </div>
                                <div class="vertical-line"></div>
                            </div>
                            <div class="box" style="display:flex; text-align:center; padding:5px;">
                                <div class="field p-2">
                                    <span class="normalTextValue">No. of days</span> <br>
                                    <span class="normalText" style=" font-weight: 600;">
                                        {{ $this->calculateNumberOfDays($leaveRequest->from_date, $leaveRequest->from_session, $leaveRequest->to_date, $leaveRequest->to_session) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row m-0 p-0 d-flex align-items-center">
                        <div class="col-md-4 m-0 p-0">
                            <div class="pay-bal ">
                                <span class="normalTextValue">Balance:</span>
                                @if ($leaveBalances)
                                @if ($leaveRequest->leave_type === 'Sick Leave' && isset($leaveBalances['sickLeaveBalance']))
                                <span class="normalText">{{ $leaveBalances['sickLeaveBalance'] }}</span>
                                @elseif($leaveRequest->leave_type === 'Casual Leave Probation' && isset($leaveBalances['casualProbationLeaveBalance']))
                                <span
                                    class="normalText">{{ $leaveBalances['casualProbationLeaveBalance'] }}</span>
                                @elseif($leaveRequest->leave_type === 'Casual Leave' && isset($leaveBalances['casualLeaveBalance']))
                                <span class="normalText">{{ $leaveBalances['casualLeaveBalance'] }}</span>
                                @elseif($leaveRequest->leave_type === 'Loss Of Pay' && isset($leaveBalances['lossOfPayBalance']))
                                <span class="normalText">{{ $leaveBalances['lossOfPayBalance'] }}</span>
                                @elseif($leaveRequest->leave_type === 'Loss Of Pay' && isset($leaveBalances['marriageLeaveBalance']))
                                <span class="normalText">{{ $leaveBalances['marriageLeaveBalance'] }}</span>
                                @elseif($leaveRequest->leave_type === 'Loss Of Pay' && isset($leaveBalances['maternityLeaveBalance']))
                                <span class="normalText">{{ $leaveBalances['maternityLeaveBalance'] }}</span>
                                @elseif($leaveRequest->leave_type === 'Loss Of Pay' && isset($leaveBalances['paternityLeaveBalance']))
                                <span class="normalText">{{ $leaveBalances['paternityLeaveBalance'] }}</span>
                                @endif
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 m-0 p-0">
                            <span class="leave-type">{{ $leaveRequest->leave_type }}</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="pending-details">
                    <div class="data">
                        <span class="normalText" style="font-size:0.8rem;">Details</span>
                        <div class="row m-0 p-0">
                            <div class="col-md-8 m-0 p-0">
                                <div class="custom-grid-container text-start">
                                    <div class="custom-grid-item">
                                        <span class="custom-label">Applied to</span>
                                        <span class="custom-label">Reason</span>
                                        <span class="custom-label">Contact</span>
                                        @if (!empty($leaveRequest->cc_to))
                                        <span class="custom-label">CC to</span>
                                        @endif
                                        @if (!empty($leaveRequest->file_paths))
                                        <span class="custom-label">Attachments</span>
                                        @endif
                                    </div>

                                    <div class="custom-grid-item">
                                        @if (!empty($leaveRequest['applying_to']))
                                        <span
                                            class="custom-value">{{ ucwords(strtolower($applyingTo['report_to'])) }}</span>
                                        @else
                                        <span class="custom-value">-</span>
                                        @endif

                                        <span class="custom-value">{{ ucfirst($leaveRequest->reason) }}</span>
                                        <span class="custom-value">{{ ucfirst($leaveRequest->contact_details) }}</span>
                                        @if (!empty($leaveRequest->cc_to))
                                        <span class="custom-value">
                                            @if (is_string($leaveRequest->cc_to))
                                            @foreach (json_decode($leaveRequest->cc_to, true) as $ccToItem)
                                            <span class="custom-cc-item">
                                                {{ ucwords(strtolower($ccToItem['full_name'])) }}
                                                (#{{ $ccToItem['emp_id']['emp_id'] }})
                                            </span>
                                            @if (!$loop->last)
                                            ,
                                            @endif
                                            @endforeach
                                            @else
                                            @foreach ($leaveRequest->cc_to as $ccToItem)
                                            <span class="custom-cc-item">
                                                {{ ucwords(strtolower($ccToItem['full_name'])) }}
                                                (#{{ $ccToItem['emp_id']['emp_id'] }})
                                            </span>
                                            @if (!$loop->last)
                                            ,
                                            @endif
                                            @endforeach
                                            @endif
                                        </span>
                                        @endif


                                        @if (!empty($leaveRequest->file_paths))
                                        @php

                                        // Check if $leaveRequest->file_paths is a string or an array
                                        $fileDataArray = is_string($leaveRequest->file_paths)
                                        ? json_decode($leaveRequest->file_paths, true)
                                        : $leaveRequest->file_paths;

                                        // Separate images and files
                                        $images = array_filter(
                                        $fileDataArray,
                                        fn($fileData) => strpos($fileData['mime_type'], 'image') !== false,
                                        );
                                        $files = array_filter(
                                        $fileDataArray,
                                        fn($fileData) => strpos($fileData['mime_type'], 'image') === false,
                                        );

                                        @endphp


                                        {{-- view file popup --}}
                                        @if ($showViewImageDialog)
                                        <div class="modal custom-modal" tabindex="-1" role="dialog" style="display: block;">
                                            <div class="modal-dialog custom-modal-dialog custom-modal-dialog-centered custom-modal-lg" role="document">
                                                <div class="modal-content custom-modal-content">
                                                    <div class="modal-header custom-modal-header">
                                                        <h5 class="modal-title view-file">View Image</h5>
                                                    </div>
                                                    <div class="modal-body custom-modal-body">
                                                        <div class="swiper-container">
                                                            <div class="swiper-wrapper">
                                                                @foreach ($images as $image)
                                                                @php
                                                                $base64File = $image['data'];
                                                                $mimeType = $image['mime_type'];
                                                                @endphp
                                                                <div class="swiper-slide">
                                                                    <img src="data:{{ $mimeType }};base64,{{ $base64File }}" class="img-fluid" alt="Image">
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer custom-modal-footer">
                                                        <button type="button" class="submit-btn" wire:click.prevent="downloadImage">Download</button>
                                                        <button type="button" class="cancel-btn1" wire:click="closeViewImage">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-backdrop fade show blurred-backdrop"></div>
                                        @endif
                                        @if ($showViewFileDialog)
                                        <div class="modal" tabindex="-1" role="dialog" style="display: block;">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title view-file">View Files</h5>
                                                    </div>
                                                    <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                                                        <ul class="list-group list-group-flush">
                                                            @foreach ($files as $file)

                                                            @php

                                                            $base64File = $file['data'];

                                                            $mimeType = $file['mime_type'];

                                                            $originalName = $file['original_name'];

                                                            @endphp

                                                            <li>

                                                                <a href="data:{{ $mimeType }};base64,{{ $base64File }}"

                                                                    download="{{ $originalName }}"

                                                                    style="text-decoration: none; color: #007BFF; margin: 10px;">

                                                                    {{ $originalName }}

                                                                </a>

                                                            </li>

                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="cancel-btn1" wire:click="closeViewFile">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-backdrop fade show blurred-backdrop"></div>
                                        @endif
                                        <!-- Trigger Links -->
                                        @if (!empty($images) && count($images) > 1)
                                        <a href="#" wire:click.prevent="showViewImage"
                                            style="text-decoration: none; color: #007BFF;font-size: 12px; color: #007BFF; text-transform: capitalize;">
                                            View Images
                                        </a>
                                        @elseif(!empty($images) && count($images) == 1)
                                        <a href="#" wire:click.prevent="showViewImage"
                                            style="text-decoration: none; color: #007BFF;font-size: 12px; color: #007BFF; text-transform: capitalize;">
                                            View Image
                                        </a>
                                        @endif

                                        @if (!empty($files) && count($files) > 1)
                                        <a href="#" wire:click.prevent="showViewFile"
                                            style="text-decoration: none; color: #007BFF;font-size: 12px; color: #007BFF; text-transform: capitalize;">
                                            Download Files
                                        </a>
                                        @elseif(!empty($files) && count($files) == 1)
                                        <a href="#" wire:click.prevent="showViewFile"
                                            style="text-decoration: none; color: #007BFF;font-size: 12px; color: #007BFF; text-transform: capitalize;">
                                            Download File
                                        </a>
                                        @endif



                                        @endif




                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="side-container">
                <h6 class="normalTextValue text-start mb-2"> Application Timeline </h6>
                <div class="d-flex gap-2">
                    <div class="mt-4">
                        <div class="cirlce"></div>
                        <div class="v-line"></div>
                        <div class=cirlce></div>
                    </div>
                    <div class="mt-4 d-flex flex-column" style="gap: 60px;">
                        <div class="group">
                            <div>
                                <h5 class="normalText text-start">
                                    @if (strtoupper($leaveRequest->status) == 'WITHDRAWN')
                                    Withdrawn <br><span class="normalText text-start">by</span>
                                    <span class="normalTextValue text-start">
                                        {{ ucwords(strtolower($this->leaveRequest->employee->first_name)) }}
                                        {{ ucwords(strtolower($this->leaveRequest->employee->last_name)) }}
                                    </span>
                                    @elseif(strtoupper($leaveRequest->status) == 'PENDING')
                                    <span class="normalTextValue text-start"> Pending <br> with</span>
                                    @if (!empty($leaveRequest['applying_to']))
                                    @foreach ($leaveRequest['applying_to'] as $applyingTo)
                                    <span class="normalText text-start">
                                        {{ ucwords(strtolower($applyingTo['report_to'])) }}
                                    </span>
                                    @endforeach
                                    @endif
                                    @else
                                    Rejected by
                                    <span class="normalText">
                                        {{ ucwords(strtolower($applyingTo['report_to'])) }}</span>
                                    @endif
                                    <br>
                                </h5>
                            </div>

                        </div>
                        <div>
                            <div class="d-flex flex-column">
                                <h5 class="mb-0 normalText text-start">Submitted
                                </h5>
                                <span class="normalTextValue text-start"
                                    style="font-size:0.625rem;">{{ $leaveRequest->created_at->format('d M, Y g:i A') }}</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
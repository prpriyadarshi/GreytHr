<div class="container-fluid my-5">
    <div class="d-flex justify-content-between mb-2">
        <div class="col-md-6 ">
            <h6 class="text-start text-5xl font-bold py-3 px-4 employees-details-chat" style="margin-left:55px;">Users</h6>
        </div>
        <div class="col-md-6 input-group">
            <input type="text" class="form-control" placeholder="Search..." wire:model.debounce.500ms="searchTerm" aria-label="Search" aria-describedby="search-addon" wire:keydown.enter="filter">
            <button class="submit-btn" wire:click="filter" id="search-addon" style="height:37px; line-height: 2;">Search</button>
        </div>

    </div>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 gap-5 justify-content-center">
        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $employeeDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        
        <div class="col mb-4">
            <div class="card h-100">
                <div class="col d-flex align-items-center justify-content-center mt-4">
                    <!--[if BLOCK]><![endif]--><?php if($employee->image): ?>
                    <div>
                        <img src="<?php echo e(asset('storage/' . $employee->image)); ?>" height="50" width="50" style="border-radius:50%;">
                    </div>
                    <?php else: ?>
                    <div>
                        <img src="https://th.bing.com/th/id/OIP.Ii15573m21uyos5SZQTdrAHaHa?rs=1&pid=ImgDetMain" height="50" width="50" alt="Default Image">
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="card-body text-center">

                    <div class="chat-employee-name"><?php echo e(ucwords(strtolower($employee->first_name))); ?>&nbsp;<?php echo e(ucwords(strtolower($employee->last_name))); ?></div>
                    <?php
                    // Example job title
                    $jobTitle = $employee->job_title;

                    // Convert "II" to "I" and "III" to "III" in the job title
                    $convertedTitle = preg_replace('/\bII\b/', 'I', $jobTitle);
                    $convertedTitle = preg_replace('/\bII\b/', 'II', $jobTitle);
                    $convertedTitle = preg_replace('/\bIII\b/', 'III', $convertedTitle);
                    ?>
                    <p class="card-text px-4 mb-0" style="display: inline-block;"><?php echo e($convertedTitle); ?></p>
                    <div class="d-flex justify-content-between mt-3">
                        <div class="chat-emp-head d-flex flex-column align-items-start gap-1">
                            <span>Employee Id</span>
                            <span>Join Date</span>
                        </div>
                        <div class="chat-emp-details d-flex flex-column align-items-end gap-1">
                            <span><?php echo e($employee->emp_id); ?></span>
                            <span><?php echo e(\Carbon\Carbon::parse($employee->hire_date)->format('d M, Y')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex gap-4 justify-content-center">
                    <!-- Call Icon -->
                    <button class="cancel-btn  px-4 d-flex align-items-center justify-content-center" style="border:1px solid rgb(12,17,79); border-radius: 10px; width: 30px; height: 30px;cursor:pointer;">
                        <i class="fas fa-phone-alt fa-rotate-90" style=" font-size: 13px;padding:5px;"></i>
                    </button>
                    <!-- Chat Icon -->
                    <button class="submit-btn  px-4 d-flex align-items-center justify-content-center" style=" border-radius: 10px; width: 30px; height: 30px;cursor:pointer;" wire:click="message('<?php echo e($employee->emp_id); ?>')">
                        <i class="fas fa-comment" style=" font-size: 14px;padding:5px;"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="col text-center">No employees found.</p>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div><?php /**PATH C:\xampp\htdocs\GreytHr\resources\views/livewire/employee.blade.php ENDPATH**/ ?>
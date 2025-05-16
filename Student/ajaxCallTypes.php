<?php
    $tid = intval($_GET['tid']);
    
    if($tid == 2){ // Single Date
        
        echo '
            <div class="form-group row mb-3">
                <div class="col-xl-6">
                    <label class="form-control-label">Select Date<span class="text-danger ml-2">*</span></label>
                    <input type="date" class="form-control" name="singleDate" id="exampleInputFirstName" placeholder="Select Date">
                </div>
            </div>';
    }
    else if($tid == 3){ // Date Range
        
        echo '
            <div class="form-group row mb-3">
                <div class="col-xl-6">
                    <label class="form-control-label">From Date<span class="text-danger ml-2">*</span></label>
                    <input type="date" class="form-control" name="fromDate" id="exampleInputFirstName" placeholder="From Date">
                </div>
                <div class="col-xl-6">
                    <label class="form-control-label">To Date<span class="text-danger ml-2">*</span></label>
                    <input type="date" class="form-control" name="toDate" id="exampleInputFirstName" placeholder="To Date">
                </div>
            </div>';
    }
?>
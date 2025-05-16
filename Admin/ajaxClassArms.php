<?php
include '../Includes/dbcon.php';

$cid = intval($_GET['cid']);
$currentArmId = isset($_GET['currentArmId']) ? intval($_GET['currentArmId']) : 0;

if ($cid <= 0) {
    echo '<select required name="classArmId" class="form-control mb-3">';
    echo '<option value="">Invalid Class Selected</option>';
    echo '</select>';
    exit;
}

// Get all class sections for the selected class
$query = "SELECT tblclassarms.Id, tblclassarms.classArmName, tblclassarms.isAssigned 
          FROM tblclassarms
          WHERE tblclassarms.classId = $cid
          ORDER BY tblclassarms.classArmName ASC";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo '<select required name="classArmId" class="form-control mb-3">';
    echo '<option value="">Error loading sections</option>';
    echo '</select>';
    exit;
}

$count = mysqli_num_rows($result);

echo '<select required name="classArmId" class="form-control mb-3">';
echo '<option value="">--Select Class Section--</option>';

if ($count > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $disabled = ($row['isAssigned'] == '1' && $row['Id'] != $currentArmId) ? 'disabled' : '';
        $selected = ($row['Id'] == $currentArmId) ? 'selected' : '';
        echo '<option value="'.$row['Id'].'" '.$selected.' '.$disabled.'>'.$row['classArmName'];
        if ($row['isAssigned'] == '1' && $row['Id'] != $currentArmId) {
            echo ' (Assigned)';
        }
        echo '</option>';
    }
} else {
    echo '<option value="" disabled>No sections available</option>';
}

echo '</select>';
?>
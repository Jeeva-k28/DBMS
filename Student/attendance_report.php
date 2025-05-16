<?php 
error_reporting(0);
include '../Includes/dbcon.php';

// Validate that user is a Student
validate_session('Student');

$filename="Attendance Report";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename-".date('d-m-Y').".xls\"");

?>

<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Admission No</th>
            <th>Student ID</th>
            <th>Class</th>
            <th>Class Arm</th>
            <th>Session</th>
            <th>Term</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $admissionNumber = $_SESSION['admissionNumber'];
        
        $query = "SELECT tblattendance.Id,tblattendance.status,tblattendance.dateTimeTaken,
        tblclass.className,tblclassarms.classArmName,tblsessionterm.sessionName,
        tblsessionterm.termId,tblterm.termName,tblstudents.firstName,tblstudents.lastName,
        tblstudents.admissionNumber,tblstudents.student_id
        FROM tblattendance
        INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
        INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
        INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
        INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
        INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
        WHERE tblattendance.admissionNo = '$admissionNumber'
        ORDER BY tblattendance.dateTimeTaken DESC";

        $rs = $conn->query($query);
        $num = $rs->num_rows;
        $sn=0;
        if($num > 0) {
            while ($rows = $rs->fetch_assoc()) {
                $sn = $sn + 1;
                echo"
                    <tr>
                        <td>".$sn."</td>
                        <td>".$rows['firstName']."</td>
                        <td>".$rows['lastName']."</td>
                        <td>".$rows['admissionNumber']."</td>
                        <td>".$rows['student_id']."</td>
                        <td>".$rows['className']."</td>
                        <td>".$rows['classArmName']."</td>
                        <td>".$rows['sessionName']."</td>
                        <td>".$rows['termName']."</td>
                        <td>".($rows['status'] == '1' ? 'Present' : 'Absent')."</td>
                        <td>".$rows['dateTimeTaken']."</td>
                    </tr>";
            }
        }
        ?>
    </tbody>
</table>
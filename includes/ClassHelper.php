<?php
class ClassHelper
{

/**
 * Summary of getProgram
 * @param mysqli $conn
 * @return string  it will return the Course Dropdown
 */
    public function getProgram($conn){
       $getProgramSql = $conn->query("SELECT ID, Name,Short_Name FROM Courses WHERE Status = 1 AND University_ID = ".UNIVERSITY_ID . " ORDER BY Name ASC");
       $option = "";
       if($getProgramSql->num_rows ==0){
        $option = "<option>No Record Found !</option>";
       }
       while ($row = $getProgramSql->fetch_assoc()) {
        $option .= "<option value='" . $row['ID'] . "'>" . htmlspecialchars($row['Name'], ENT_QUOTES, 'UTF-8') . "</option>";
       }
       return $option;
    }

    public function getSpecialization($conn, $course_id){
        $getProgramSql = $conn->query("SELECT Sub_Courses.ID, Sub_Courses.Name,Courses.Short_Name FROM Sub_Courses LEFT JOIN Courses on Course_ID = Sub_Courses.ID WHERE Sub_Courses.Status = 1 AND Sub_Courses.University_ID = ".UNIVERSITY_ID." ORDER BY Sub_Courses.Name ASC");
        $option = "";
        if($getProgramSql->num_rows ==0){
         $option = "<option>No Record Found !</option>";
        }
        while ($row = $getProgramSql->fetch_assoc()) {
            $option .= "<option value=" . $row['ID'] . ">" . htmlspecialchars($row['Name'], ENT_QUOTES, 'UTF-8') . "</option>";
        }
        return $option;
     }

    public function getDurationFunc($duration, $category, $uni_id)
    {
        return $duration;
    }

    public function getUserSubCourse($conn, $added_for, $role, $uni_id)
    {
        if ($role === "Center" || $role === "Sub-Center") {
            $userSQL = "SELECT Sub_Course_ID AS ID, Sub_Courses.Name, Sub_Courses.Short_Name FROM Sub_Courses left join Students ON Sub_Courses.ID =Students.Sub_Course_ID  WHERE `Added_By` = $added_for  GROUP BY Sub_Course_ID";
        } else {
            $userSQL = "SELECT ID, Name,Short_Name  FROM Sub_Courses WHERE Status =1 AND University_ID = " . $uni_id . " order by Name ASC";
        }
        $option = "";
        $userSqlQuery = $conn->query($userSQL);
        if ($userSqlQuery->num_rows == 0) {
            $option = "<option>Sub-Course not assign to this user</option>";
        } else {
            while ($row = $userSqlQuery->fetch_assoc()) {
                $option .= "<option value=" . $row['ID'] . ">" . $row['Name'] . '(' . $row['Short_Name'] . ")</option>";
            }
        }
        return $option;
    }
}


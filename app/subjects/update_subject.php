<?php
  require '../../includes/db-config.php';
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $fetch_data = "SELECT * FROM Syllabi WHERE id = ?";
    if ($stmt = $conn->prepare($fetch_data)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
?>
            <!-- <div class="col-sm-12"> -->
            <!-- <div class="col-sm-12"> -->
            <div class="modal-header" style="width: 800px;">
                <h5 class="modal-title" id="myModalLabel">Edit Subject</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height: 450px;">
                <form id="resultForm" action="/app/subjects/editsyll" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="ID" value="<?php echo $data['ID'] ?>" id="ID">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="coursetype">Universities Type</label>
                                <select class="form-control" id="universities" onchange="selectcourse(this.value)" name="universities">
                                    <option value="">Select Universities Type</option>
                                    <?php
                                    $sql = "SELECT ID, Name FROM Universities WHERE Status=1";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $universitiesId = $row["ID"];
                                            $universitiesName = $row["Name"];
                                            $selected = ($universitiesId == $data['University_ID']) ? 'selected' : '';
                                            echo '<option value="' . $universitiesId . '" ' . $selected . '>' . $universitiesName . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="coursetype">Course Type</label>
                                <select class="form-control" id="coursetype" onchange="getSpecialization(this.value)" name="coursetype">
                                    <option value="">Select Course Type</option>
                                    <?php
                                    $stmt = $conn->prepare("SELECT ID, Name FROM Courses WHERE Status=1 ORDER BY Name ASC");
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $courseId = $row["ID"];
                                            $courseName = $row["Name"];

                                            $selected = ($courseId == $data['Course_ID']) ? 'selected' : '';

                                            echo '<option value="' . htmlspecialchars($courseId) . '" ' . $selected . '>' . htmlspecialchars($courseName) . '</option>';
                                        }
                                    }
                                    $stmt->close();
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subcourse_id">Sub Course Type</label>
                                <select class="form-control" id="subcourse_id" name="subcourse_id" onchange="getsemester(this.value);">
                                    <option value="">Select Sub Course Type</option>
                                    <?php
                                    $stmt = $conn->prepare("SELECT ID, Name FROM Sub_Courses WHERE Status=1 ORDER BY Name ASC");
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $subCourseId = $row["ID"];
                                            $subCourseName = $row["Name"];
                                            $selected = ($subCourseId == $data['Sub_Course_ID']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($subCourseId) . '" ' . $selected . '>' . htmlspecialchars($subCourseName) . '</option>';
                                        }
                                    }
                                    $stmt->close();
                                    ?>
                                </select>
                            </div>
                        </div>




                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="semester">Duration</label>
                                <select class="form-control" id="seme" name="seme">
                                    <option value="">Select Duration Type</option>
                                </select>
                            </div>
                        </div>

                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subjectname">Subject Name</label>
                                <input type="text" class="form-control" name="subjectname" id="subjectname" value="<?php echo $data['Name']; ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subjectcode">Subject Code</label>
                                <input type="text" class="form-control" name="subjectcode" id="subjectcode" value="<?php echo $data['Code']; ?>">
                            </div>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="paper_type">Select Paper Type</label>
                                <select class="form-control" name="paper_type" id="paper_type">
                                    <option value="">Select Paper Type</option>
                                    <option value="Theory" <?php echo (isset($data['Paper_Type']) && $data['Paper_Type'] == 'Theory') ? 'selected' : ''; ?>>Theory</option>
                                    <option value="Practical" <?php echo (isset($data['Paper_Type']) && $data['Paper_Type'] == 'Practical') ? 'selected' : ''; ?>>Practical</option>
                                    <option value="Project" <?php echo (isset($data['Paper_Type']) && $data['Paper_Type'] == 'Project') ? 'selected' : ''; ?>>Project</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subjectcredit">Subject Credit</label>
                                <input type="text" class="form-control" name="subjectcredit" id="subjectcredit" value="<?php echo $data['Credit']; ?>">
                            </div>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-6">
                            <label for="minMarks">Minimum Marks</label>
                            <input type="text" class="form-control" name="minMarks" id="minMarks" value="<?php echo $data['Min_Marks']; ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="maxMarks">Maximum Marks</label>
                            <input type="text" class="form-control" name="maxMarks" id="maxMarks" value="<?php echo $data['Max_Marks']; ?>">
                        </div>
                    </div>
                    </br>
                    <button type="submit" id="update" class="btn btn-success btn btn-lg">Update</button>
                    <button type="button" class="btn btn-danger btn btn-lg" data-dismiss="modal">Close</button>
                </form>
            </div>
            <!-- </div> -->

            <!-- </div> -->

<?php
        } else {
            echo "No record found!";
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
?>
<script>
    function getSpecialization(courseId) {
        $.ajax({
            type: 'POST',
            url: '/app/assignments/get_subcourses',
            data: {
                courseId: courseId
            },
            success: function(response) {
                $('#subcourse_id').html(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
</script>
<script>
    function getsemester(subCourseId) {
        var ID = $('#ID').val();
        var University_id = $('#universities').val();
        $.ajax({
            type: 'POST',
            url: '/app/assignments/getsemester',
            data: {
                subCourseId: subCourseId,
                ID: ID,
                University_id: University_id
            },
            success: function(response) {
                console.log(response);
                $('#seme').html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error: " + xhr.responseText);
            }
        });
    }
    $(document).ready(function() {
        setTimeout(() => {
            var subcourseid = $('#subcourse_id').val();
            getsemester(subcourseid);
        }, 1000);
    })
</script>
<script>
    function selectcourse(universitiesId) {
        // alert("Hiii");
        // var universityId = $("#universities");
        $.ajax({
            type: 'POST',
            url: '/app/subjects/getcourses',
            data: {
                universitiesId: universitiesId
            },
            success: function(response) {
                // console.log(response);
                $('#coursetype').html(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
</script>
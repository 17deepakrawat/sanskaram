<?php
ini_set('display_errors', 1);
  if(isset($_GET['university_id']) && isset($_GET['id'])){
    require '../../includes/db-config.php';
    $id = intval($_GET['id']);
    $university_id = intval($_GET['university_id']);

    $course_types_array = [];


    $alloted_course_types = $conn->query("SELECT Course_Type_ID FROM Center_Course_Types WHERE `User_ID` = $id AND University_ID = $university_id");
    while($alloted_course_type = $alloted_course_types->fetch_assoc()){
      $course_types_array[] = $alloted_course_type['Course_Type_ID'];
    }

?>
  
    <script type="text/javascript">
      $('#course_type').val([<?=implode(",", $course_types_array)?>]).select2({
        placeholder: 'Course Type'
      }).change();
    </script>

    <script type="text/javascript">
      function getVocationalCourse(){
        var type_ids = $('#course_type').val();
        $.ajax({
          url: '/app/sub-centers/vocational-courses?ids='+type_ids+'&university_id=<?=$university_id?>&user_id=<?=$id?>',
          type:'GET',
          success: function(data) {
            $('#vocational_course').html(data);
          }
        })
      }

    </script>


<?php } ?>

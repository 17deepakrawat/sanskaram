<?php
   require '../../includes/db-config.php';
   require '../../includes/ClassHelper.php';

   $id = intval($_GET['course_id']);
   $getClassFunc = new ClassHelper();
   echo   $sub_course = $getClassFunc->getSpecialization($conn, $id);


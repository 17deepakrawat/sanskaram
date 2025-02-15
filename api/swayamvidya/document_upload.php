<?php

function uploadDocuments($inserted_id, $documnetArray)
{
    if (isset($inserted_id)) {
        require '../../includes/db-config.php';
        session_start();
        $allowed_file_extensions = array("jpeg", "jpg", "png", "JPG", "PNG", "JPEG");
        $high_academics_folder = '../../uploads/marksheet/high_school/';
        $inter_academics_folder = '../../uploads/marksheet/intermediate/';
        $ug_academics_folder = '../../uploads/marksheet/under_graduate/';
        $pg_academics_folder = '../../uploads/marksheet/post_graduate/';
        $other_academics_folder = '../../uploads/marksheet/other/';
        $photo_folder = '../../uploads/photo/';
        $aadhar_folder = '../../uploads/aadhar/';
        $signature_folder = '../../uploads/signature/';
        $migration_folder = '../../uploads/migration/';
        $affidavit_folder = '../../uploads/affidavit/';
        $other_certificate_folder = '../../uploads/other_certificates/';

        $inserted_id = intval($inserted_id);

        $step = $conn->query("SELECT Step, Sub_Courses.Eligibility FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID WHERE Students.ID = $inserted_id");
        $step = mysqli_fetch_array($step);
        $eligibility = $step['Eligibility'];
        $step = $step['Step'];

        $eligibility = !empty($eligibility) ? json_decode($eligibility, true) : [];

        if (count($eligibility) == 0) {
            return json_encode(array('status' => 'error', 'message' => 'Eligibility not configured'));
        }


        $high_subject = mysqli_real_escape_string($conn, $documnetArray['high_subject']);
        $high_marksheet_reference_no = mysqli_real_escape_string($conn, $documnetArray['high_marksheet_reference_no']);
        $high_subject = strtoupper(strtolower($high_subject));
        $high_year = mysqli_real_escape_string($conn, $documnetArray['high_year']);
        $high_board = mysqli_real_escape_string($conn, $documnetArray['high_board']);
        $high_board = strtoupper(strtolower($high_board));
        $high_obtained = array_key_exists('high_obtained', $documnetArray) ? mysqli_real_escape_string($conn, $documnetArray['high_obtained']) : NULL;
        $high_max = array_key_exists('high_max', $documnetArray) ? mysqli_real_escape_string($conn, $documnetArray['high_max']) : NULL;
        $high_total = mysqli_real_escape_string($conn, $documnetArray['high_total']);
        $high_total = strtoupper(strtolower($high_total));

        if (in_array('High School', $eligibility) && empty($high_total)) {
            echo json_encode(['status' => 'error', 'message' => 'High School details are required!']);
            exit();
        }

        if (!empty($high_total)) {
            if (isset($documnetArray['high_marksheet']) && count($documnetArray['high_marksheet']) > 0) {
                foreach ($documnetArray['high_marksheet'] as $key => $base64_string) {

                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                        $image_type = $matches[1];
                        $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                        $base64_string = base64_decode($base64_string);
                    } else {
                        echo "Invalid Base64 format for image $key<br>";
                        continue;
                    }

                    $high_marksheet_extension = $image_type;
                    $high_marksheet_name = $inserted_id . "_High_Marksheet_" . $key . "." . $high_marksheet_extension;
                    if (in_array($high_marksheet_extension, $allowed_file_extensions)) {
                        if (file_exists($high_academics_folder . $high_marksheet_name)) {
                            unlink($high_academics_folder . $high_marksheet_name);
                        }
                        if (file_put_contents($high_academics_folder . $high_marksheet_name, $base64_string)) {
                            $high_marksheets[] = str_replace('../..', '', $high_academics_folder) . $high_marksheet_name;
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Unable to upload High School marksheet!']);
                            exit();
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'High School Marksheet should be image!']);
                        exit();
                    }
                }
                $high_marksheet = implode("|", $high_marksheets);
            } else {
                $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'High School'");
                if ($check->num_rows == 0) {
                    echo json_encode(['status' => 400, 'message' => 'High School Marksheet is required!']);
                    exit();
                } else {
                    $high_marksheet = mysqli_fetch_assoc($check);
                    $high_marksheet = $high_marksheet['Location'];
                }
            }

            $check = $conn->query("SELECT ID FROM Student_Academics WHERE Student_ID = $inserted_id AND Type = 'High School'");
            if ($check->num_rows > 0) {
                $update_details = $conn->query("UPDATE Student_Academics SET `Year` = '$high_year', Subject = '$high_subject', `Board/Institute` = '$high_board', `Marks_Obtained` = '$high_obtained', `Max_Marks` = '$high_max', `Total_Marks` = '$high_total', `marksheet_reference_no` = '$high_marksheet_reference_no'  WHERE Student_ID = $inserted_id AND Type = 'High School'");
            } else {
                $update_details = $conn->query("INSERT INTO Student_Academics (Student_ID, Type, `Year`, Subject, `Board/Institute`, `Marks_Obtained`, `Max_Marks`, `Total_Marks`,`marksheet_reference_no`) VALUES ($inserted_id, 'High School', '$high_year', '$high_subject', '$high_board', '$high_obtained', '$high_max', '$high_total','$high_marksheet_reference_no')");
            }
            if (!$update_details) {
                echo json_encode(['status' => 'error', 'message' => 'Unable to update high school details.']);
                exit();
            }

            $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'High School'");
            if ($check->num_rows > 0) {
                $update = $conn->query("UPDATE Student_Documents SET Location = '$high_marksheet' WHERE Student_ID = $inserted_id AND Type = 'High School'");
            } else {
                $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'High School', '$high_marksheet')");
            }
        }

        $inter_subject = mysqli_real_escape_string($conn, $documnetArray['inter_subject']);
        $inter_marksheet_reference_no = mysqli_real_escape_string($conn, $documnetArray['inter_marksheet_reference_no']);

        $inter_subject = strtoupper(strtolower($inter_subject));
        $inter_year = mysqli_real_escape_string($conn, $documnetArray['inter_year']);
        $inter_board = mysqli_real_escape_string($conn, $documnetArray['inter_board']);
        $inter_board = strtoupper(strtolower($inter_board));
        $inter_obtained = array_key_exists('inter_obtained', $documnetArray) ? mysqli_real_escape_string($conn, $documnetArray['inter_obtained']) : NULL;
        $inter_max = array_key_exists('inter_max', $documnetArray) ? mysqli_real_escape_string($conn, $documnetArray['inter_max']) : NULL;
        $inter_total = mysqli_real_escape_string($conn, $documnetArray['inter_total']);
        $inter_total = strtoupper(strtolower($inter_total));

        if (in_array('Intermediate', $eligibility) && empty($inter_total)) {
            echo json_encode(['status' => 'error', 'message' => 'Intermediate details are required!']);
            exit();
        }

        if (!empty($inter_total)) {
            if (isset($documnetArray['inter_marksheet']) && count($documnetArray['inter_marksheet']) > 0) {
                foreach ($documnetArray['inter_marksheet'] as $key => $base64_string) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                        $image_type = $matches[1];
                        $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                        $base64_string = base64_decode($base64_string);
                    } else {
                        echo "Invalid Base64 format for image $key<br>";
                        continue;
                    }
                    $inter_marksheet_extension =  $image_type;
                    $inter_marksheet_name = $inserted_id . "_Inter_Marksheet_" . $key . "." . $inter_marksheet_extension;
                    if (in_array($inter_marksheet_extension, $allowed_file_extensions)) {
                        if (file_exists($inter_academics_folder . $inter_marksheet_name)) {
                            unlink($inter_academics_folder . $inter_marksheet_name);
                        }
                        if (file_put_contents($inter_academics_folder . $inter_marksheet_name, $base64_string)) {
                            $inter_marksheets[] = str_replace('../..', '', $inter_academics_folder) . $inter_marksheet_name;
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Unable to upload Intermediate marksheet!']);
                            exit();
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Intermediate Marksheet should be image!']);
                        exit();
                    }
                }
                $inter_marksheet = implode("|", $inter_marksheets);
            } else {
                $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Intermediate'");
                if ($check->num_rows == 0) {
                    echo json_encode(['status' => 'error', 'message' => 'Intermediate Marksheet is required!']);
                    exit();
                } else {
                    $inter_marksheet = mysqli_fetch_assoc($check);
                    $inter_marksheet = $inter_marksheet['Location'];
                }
            }

            $check = $conn->query("SELECT ID FROM Student_Academics WHERE Student_ID = $inserted_id AND Type = 'Intermediate'");
            if ($check->num_rows > 0) {
                $update_details = $conn->query("UPDATE Student_Academics SET `Year` = '$inter_year', Subject = '$inter_subject', `Board/Institute` = '$inter_board', `Marks_Obtained` = '$inter_obtained', `Max_Marks` = '$inter_max', `Total_Marks` = '$inter_total', `marksheet_reference_no`= '$inter_marksheet_reference_no' WHERE Student_ID = $inserted_id AND Type = 'Intermediate'");
            } else {
                $update_details = $conn->query("INSERT INTO Student_Academics (Student_ID, Type, `Year`, Subject, `Board/Institute`, `Marks_Obtained`, `Max_Marks`, `Total_Marks`,`marksheet_reference_no`) VALUES ($inserted_id, 'Intermediate', '$inter_year', '$inter_subject', '$inter_board', '$inter_obtained', '$inter_max', '$inter_total','$inter_marksheet_reference_no')");
            }
            if (!$update_details) {
                echo json_encode(['status' => 'error', 'message' => 'Unable to update Intermediate details.']);
                exit();
            }

            $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Intermediate'");
            if ($check->num_rows > 0) {
                $update = $conn->query("UPDATE Student_Documents SET Location = '$inter_marksheet' WHERE Student_ID = $inserted_id AND Type = 'Intermediate'");
            } else {
                $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Intermediate', '$inter_marksheet')");
            }
        }

        $ug_subject = mysqli_real_escape_string($conn, $documnetArray['ug_subject']);
        $ug_marksheet_reference_no = mysqli_real_escape_string($conn, $documnetArray['ug_marksheet_reference_no']);

        $ug_subject = strtoupper(strtolower($ug_subject));
        $ug_year = mysqli_real_escape_string($conn, $documnetArray['ug_year']);
        $ug_board = mysqli_real_escape_string($conn, $documnetArray['ug_board']);
        $ug_board = strtoupper(strtolower($ug_board));
        $ug_obtained = array_key_exists('ug_obtained', $documnetArray) ? mysqli_real_escape_string($conn, $documnetArray['ug_obtained']) : NULL;
        $ug_max = array_key_exists('ug_max', $documnetArray) ? mysqli_real_escape_string($conn, $documnetArray['ug_max']) : NULL;
        $ug_total = mysqli_real_escape_string($conn, $documnetArray['ug_total']);
        $ug_total = strtoupper(strtolower($ug_total));

        if (in_array('UG', $eligibility) && empty($ug_total)) {
            echo json_encode(['status' => 'error', 'message' => 'Graduation details are required!']);
            exit();
        }

        if (!empty($ug_total)) {
            if (isset($documnetArray['ug_marksheet']) && count($documnetArray['ug_marksheet']) > 0) {
                foreach ($documnetArray['ug_marksheet'] as $key => $base64_string) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                        $image_type = $matches[1];
                        $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                        $base64_string = base64_decode($base64_string);
                    } else {
                        echo "Invalid Base64 format for image $key<br>";
                        continue;
                    }
                    $ug_marksheet_extension = $image_type;
                    $ug_marksheet_name = $inserted_id . "_UG_Marksheet_" . $key . "." . $ug_marksheet_extension;
                    if (in_array($ug_marksheet_extension, $allowed_file_extensions)) {
                        if (file_exists($ug_academics_folder . $ug_marksheet_name)) {
                            unlink($ug_academics_folder . $ug_marksheet_name);
                        }
                        if (file_put_contents($ug_academics_folder . $ug_marksheet_name, $base64_string)) {
                            $ug_marksheets[] = str_replace('../..', '', $ug_academics_folder) . $ug_marksheet_name;
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Unable to upload UG marksheet!']);
                            exit();
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'UG Marksheet should be image!']);
                        exit();
                    }
                }
                $ug_marksheet = implode("|", $ug_marksheets);
            } else {
                $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'UG'");
                if ($check->num_rows == 0) {
                    echo json_encode(['status' => 400, 'message' => 'UG Marksheet is required!']);
                    exit();
                } else {
                    $ug_marksheet = mysqli_fetch_assoc($check);
                    $ug_marksheet = $ug_marksheet['Location'];
                }
            }

            $check = $conn->query("SELECT ID FROM Student_Academics WHERE Student_ID = $inserted_id AND Type = 'UG'");
            if ($check->num_rows > 0) {
                $update_details = $conn->query("UPDATE Student_Academics SET `Year` = '$ug_year', Subject = '$ug_subject', `Board/Institute` = '$ug_board', `Marks_Obtained` = '$ug_obtained', `Max_Marks` = '$ug_max', `Total_Marks` = '$ug_total', `marksheet_reference_no` = '$ug_marksheet_reference_no' WHERE Student_ID = $inserted_id AND Type = 'UG'");
            } else {
                $update_details = $conn->query("INSERT INTO Student_Academics (Student_ID, Type, `Year`, Subject, `Board/Institute`, `Marks_Obtained`, `Max_Marks`, `Total_Marks`,`marksheet_reference_no`) VALUES ($inserted_id, 'UG', '$ug_year', '$ug_subject', '$ug_board', '$ug_obtained', '$ug_max', '$ug_total','$ug_marksheet_reference_no')");
            }
            if (!$update_details) {
                echo json_encode(['status' => 400, 'message' => 'Unable to update UG details.']);
                exit();
            }

            $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'UG'");
            if ($check->num_rows > 0) {
                $update = $conn->query("UPDATE Student_Documents SET Location = '$ug_marksheet' WHERE Student_ID = $inserted_id AND Type = 'UG'");
            } else {
                $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'UG', '$ug_marksheet')");
            }
        }

        $pg_subject = mysqli_real_escape_string($conn, $documnetArray['pg_subject']);
        $pg_marksheet_reference_no = mysqli_real_escape_string($conn, $documnetArray['pg_marksheet_reference_no']);
        $pg_subject = strtoupper(strtolower($pg_subject));
        $pg_year = mysqli_real_escape_string($conn, $documnetArray['pg_year']);
        $pg_board = mysqli_real_escape_string($conn, $documnetArray['pg_board']);
        $pg_board = strtoupper(strtolower($pg_board));
        $pg_obtained = array_key_exists('pg_obtained', $documnetArray) ? mysqli_real_escape_string($conn, $documnetArray['pg_obtained']) : NULL;
        $pg_max = array_key_exists('pg_max', $documnetArray) ? mysqli_real_escape_string($conn, $documnetArray['pg_max']) : NULL;
        $pg_total = mysqli_real_escape_string($conn, $documnetArray['pg_total']);
        $pg_total = strtoupper(strtolower($pg_total));

        if (in_array('PG', $eligibility) && empty($pg_total)) {
            echo json_encode(['status' => 'error', 'message' => 'Post Graduate details are required!']);
            exit();
        }

        if (!empty($pg_total)) {
            if (isset($documnetArray['ug_marksheet']) && count($documnetArray['ug_marksheet']) > 0) {
                foreach ($documnetArray['ug_marksheet'] as $key => $base64_string) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                        $image_type = $matches[1];
                        $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                        $base64_string = base64_decode($base64_string);
                    } else {
                        echo "Invalid Base64 format for image $key<br>";
                        continue;
                    }
                    $pg_marksheet_extension = $image_type;
                    $pg_marksheet_name = $inserted_id . "_PG_Marksheet_" . $key . "." . $pg_marksheet_extension;
                    if (in_array($pg_marksheet_extension, $allowed_file_extensions)) {
                        if (file_exists($pg_academics_folder . $pg_marksheet_name)) {
                            unlink($pg_academics_folder . $pg_marksheet_name);
                        }
                        if (file_put_contents($pg_academics_folder . $pg_marksheet_name, $base64_string)) {
                            $pg_marksheets[] = str_replace('../..', '', $pg_academics_folder) . $pg_marksheet_name;
                        } else {
                            echo json_encode(['status' => 'error', 'message' => 'Unable to upload PG marksheet!']);
                            exit();
                        }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'PG Marksheet should be image!']);
                        exit();
                    }
                }
                $pg_marksheet = implode("|", $pg_marksheets);
            } else {
                $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'PG'");
                if ($check->num_rows == 0) {
                    echo json_encode(['status' => 'error', 'message' => 'PG Marksheet is required!']);
                    exit();
                } else {
                    $pg_marksheet = mysqli_fetch_assoc($check);
                    $pg_marksheet = $pg_marksheet['Location'];
                }
            }

            $check = $conn->query("SELECT ID FROM Student_Academics WHERE Student_ID = $inserted_id AND Type = 'PG'");
            if ($check->num_rows > 0) {
                $update_details = $conn->query("UPDATE Student_Academics SET `Year` = '$pg_year', Subject = '$pg_subject', `Board/Institute` = '$pg_board', `Marks_Obtained` = '$pg_obtained', `Max_Marks` = '$pg_max', `Total_Marks` = '$pg_total', `marksheet_reference_no`= '$pg_marksheet_reference_no' WHERE Student_ID = $inserted_id AND Type = 'PG'");
            } else {
                $update_details = $conn->query("INSERT INTO Student_Academics (Student_ID, Type, `Year`, Subject, `Board/Institute`, `Marks_Obtained`, `Max_Marks`, `Total_Marks`,`marksheet_reference_no`) VALUES ($inserted_id, 'PG', '$pg_year', '$pg_subject', '$pg_board', '$pg_obtained', '$pg_max', '$pg_total','$pg_marksheet_reference_no')");
            }
            if (!$update_details) {
                echo json_encode(['status' => 400, 'message' => 'Unable to update PG details.']);
                exit();
            }

            $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'PG'");
            if ($check->num_rows > 0) {
                $update = $conn->query("UPDATE Student_Documents SET Location = '$pg_marksheet' WHERE Student_ID = $inserted_id AND Type = 'PG'");
            } else {
                $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'PG', '$pg_marksheet')");
            }
        }

        $other_subject = mysqli_real_escape_string($conn, $documnetArray['other_subject']);
        $other_marksheet_reference_no = mysqli_real_escape_string($conn, $documnetArray['other_marksheet_reference_no']);

        $other_subject = strtoupper(strtolower($other_subject));
        $other_year = mysqli_real_escape_string($conn, $documnetArray['other_year']);
        $other_board = mysqli_real_escape_string($conn, $documnetArray['other_board']);
        $other_board = strtoupper(strtolower($other_board));
        $other_obtained = array_key_exists('other_obtained', $documnetArray) ? mysqli_real_escape_string($conn, $documnetArray['other_obtained']) : NULL;
        $other_max = array_key_exists('other_max', $documnetArray) ? mysqli_real_escape_string($conn, $documnetArray['other_max']) : NULL;
        $other_total = mysqli_real_escape_string($conn, $documnetArray['other_total']);
        $other_total = strtoupper(strtolower($other_total));

        if (in_array('Other', $eligibility) && empty($other_total)) {
            echo json_encode(['status' => 'error', 'message' => 'Other details are required!']);
            exit();
        }

        if (!empty($other_total)) {
            if (isset($documnetArray['other_marksheet']) && count($documnetArray['ug_marksheet']) > 0) {
                foreach ($documnetArray['other_marksheet'] as $key => $base64_string) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                        $image_type = $matches[1];
                        $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                        $base64_string = base64_decode($base64_string);
                    } else {
                        echo "Invalid Base64 format for image $key<br>";
                        continue;
                    }
                    $other_marksheet_extension =  $image_type;
                    $other_marksheet_name = $inserted_id . "_other_Marksheet_" . $key . "." . $other_marksheet_extension;
                    if (in_array($other_marksheet_extension, $allowed_file_extensions)) {
                        if (file_exists($other_academics_folder . $other_marksheet_name)) {
                            unlink($other_academics_folder . $other_marksheet_name);
                        }
                        if (file_put_contents($other_academics_folder . $other_marksheet_name, $base64_string)) {
                            $other_marksheets[] = str_replace('../..', '', $other_academics_folder) . $other_marksheet_name;
                        } else {
                            echo json_encode(['status' => 503, 'message' => 'Unable to upload Other marksheet!']);
                            exit();
                        }
                    } else {
                        echo json_encode(['status' => 302, 'message' => 'Other Marksheet should be image!']);
                        exit();
                    }
                }
                $other_marksheet = implode("|", $other_marksheets);
            } else {
                $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Other'");
                if ($check->num_rows == 0) {
                    echo json_encode(['status' => 'error', 'message' => 'Other Marksheet is required!']);
                    exit();
                } else {
                    $other_marksheet = mysqli_fetch_assoc($check);
                    $other_marksheet = $other_marksheet['Location'];
                }
            }

            $check = $conn->query("SELECT ID FROM Student_Academics WHERE Student_ID = $inserted_id AND Type = 'Other'");
            if ($check->num_rows > 0) {
                $update_details = $conn->query("UPDATE Student_Academics SET `Year` = '$other_year', Subject = '$other_subject', `Board/Institute` = '$other_board', `Marks_Obtained` = '$other_obtained', `Max_Marks` = '$other_max', `Total_Marks` = '$other_total', `marksheet_reference_no` = '$other_marksheet_reference_no' WHERE Student_ID = $inserted_id AND Type = 'Other'");
            } else {
                $update_details = $conn->query("INSERT INTO Student_Academics (Student_ID, Type, `Year`, Subject, `Board/Institute`, `Marks_Obtained`, `Max_Marks`, `Total_Marks`,`marksheet_reference_no`) VALUES ($inserted_id, 'Other', '$other_year', '$other_subject', '$other_board', '$other_obtained', '$other_max', '$other_total', '$other_marksheet_reference_no')");
            }
            if (!$update_details) {
                echo json_encode(['status' => 'error', 'message' => 'Unable to update Other details.']);
                exit();
            }

            $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Other'");
            if ($check->num_rows > 0) {
                $update = $conn->query("UPDATE Student_Documents SET Location = '$other_marksheet' WHERE Student_ID = $inserted_id AND Type = 'Other'");
            } else {
                $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Other', '$other_marksheet')");
            }
        }

        ///photo
        if (isset($documnetArray['photo']) && count($documnetArray['photo']) > 0) {
            foreach ($documnetArray['photo'] as $key => $base64_string) {
                if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                    $image_type = $matches[1];
                    $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                    $base64_string = base64_decode($base64_string);
                } else {
                    echo "Invalid Base64 format for image $key<br>";
                    continue;
                }
                $photo_extension = $image_type;
                $photo = $inserted_id . "." . $photo_extension;
                if (in_array($photo_extension, $allowed_file_extensions)) {
                    if (!file_put_contents($photo_folder . $photo, $base64_string)) {
                        echo json_encode(['status' => 503, 'message' => 'Unable to upload photo!']);
                        exit();
                    } else {
                        $photo[] = str_replace('../..', '', $photo_folder) . $photo;
                    }
                } else {
                    echo json_encode(['status' => 302, 'message' => 'Photo should be image!']);
                    exit();
                }
            }
            $photo = implode("|", $photo);
            $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Photo'");
            if ($check->num_rows > 0) {
                $update = $conn->query("UPDATE Student_Documents SET Location = '$photo' WHERE Student_ID = $inserted_id AND Type = 'Photo'");
            } else {
                $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Photo', '$photo')");
            }
        } else {
            $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Photo'");
            if ($check->num_rows == 0) {
                echo json_encode(['status' => 400, 'message' => 'Photo is required!']);
                exit();
            } else {
                $update = true;
            }
        }

        // Aadhar
        if (isset($documnetArray['aadhar']) && count($documnetArray['aadhar']) > 0) {
            foreach ($documnetArray['aadhar'] as $key => $base64_string) {
                if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                    $image_type = $matches[1];
                    $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                    $base64_string = base64_decode($base64_string);
                } else {
                    echo "Invalid Base64 format for image $key<br>";
                    continue;
                }
                $aadhar_extension = $image_type;
                $aadhar_name = $inserted_id . "_Aadhar_" . $key . "." . $aadhar_extension;
                if (in_array($aadhar_extension, $allowed_file_extensions)) {
                    if (file_exists($aadhar_folder . $aadhar_name)) {
                        unlink($aadhar_folder . $aadhar_name);
                    }
                    if (file_put_contents($aadhar_folder . $aadhar_name, $base64_string)) {
                        $aadhars[] = str_replace('../..', '', $aadhar_folder) . $aadhar_name;
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Unable to upload Aadhar!']);
                        exit();
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Aadhar should be image!']);
                    exit();
                }
            }
            $aadhar = implode("|", $aadhars);
            $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Aadhar'");
            if ($check->num_rows > 0) {
                $update = $conn->query("UPDATE Student_Documents SET Location = '$aadhar' WHERE Student_ID = $inserted_id AND Type = 'Aadhar'");
            } else {
                $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Aadhar', '$aadhar')");
            }
        }

        // Student's Signature
        if (isset($documnetArray['student_signature']) && count($documnetArray['student_signature']) > 0) {
            $base64_string = $documnetArray['student_signature'][0];
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                $image_type = $matches[1];
                $base64_string = substr($base64_string, strpos($base64_string, ',') + 1); 
                $base64_string = base64_decode($base64_string);
                $student_signature_extension = $image_type;
                $student_signature = $inserted_id . "_Student_Signature." . $student_signature_extension;
                if (in_array($student_signature_extension, $allowed_file_extensions)) {
                    if (!file_put_contents($signature_folder . $student_signature, $base64_string)) {
                        echo json_encode(['status' => 503, 'message' => 'Unable to upload Student Signature!']);
                        exit();
                    } else {
                        $student_signature = str_replace('../..', '', $signature_folder) . $student_signature;
                        $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Student Signature'");
                        if ($check->num_rows > 0) {
                            $update = $conn->query("UPDATE Student_Documents SET Location = '$student_signature' WHERE Student_ID = $inserted_id AND Type = 'Student Signature'");
                        } else {
                            $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Student Signature', '$student_signature')");
                        }
                    }
                } else {
                    echo json_encode(['status' => 302, 'message' => 'Student Signature should be image!']);
                    exit();
                }
            } else {
                echo "Invalid Base64 format for image $key<br>";
            }
        }

        // Parent's Signature
        if (isset($documnetArray['']) && count($documnetArray['parent_signature']) > 0) {
            $base64_string = $documnetArray['parent_signature'][0];
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                $image_type = $matches[1];
                $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                $base64_string = base64_decode($base64_string);

                $parent_signature_extension = $image_type;
                $parent_signature = $inserted_id . "_Parent_Signature." . $parent_signature_extension;
                if (in_array($parent_signature_extension, $allowed_file_extensions)) {
                    if (!file_put_contents($signature_folder . $parent_signature, $base64_string)) {
                        echo json_encode(['status' => 'error', 'message' => 'Unable to upload Parent Signature!']);
                        exit();
                    } else {
                        $parent_signature = str_replace('../..', '', $signature_folder) . $parent_signature;
                        $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Parent Signature'");
                        if ($check->num_rows > 0) {
                            $update = $conn->query("UPDATE Student_Documents SET Location = '$parent_signature' WHERE Student_ID = $inserted_id AND Type = 'Parent Signature'");
                        } else {
                            $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Parent Signature', '$parent_signature')");
                        }
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Parent Signature should be image!']);
                    exit();
                }
            } else {
                echo "Invalid Base64 format for image $key<br>";
            }
        }

        // Migration
        if (isset($documnetArray['migration']) && count($documnetArray['migration']) > 0) {
            foreach ($documnetArray['migration'] as $key => $base64_string) {
                if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                    $image_type = $matches[1];
                    $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                    $base64_string = base64_decode($base64_string);
                } else {
                    echo "Invalid Base64 format for image $key<br>";
                    continue;
                }
                $migration_extension = $image_type ;
                $migration_name = $inserted_id . "_Migration_" . $key . "." . $migration_extension;
                if (in_array($migration_extension, $allowed_file_extensions)) {
                    if (file_exists($migration_folder . $migration_name)) {
                        unlink($migration_folder . $migration_name);
                    }
                    if (file_put_contents($migration_folder . $migration_name, $base64_string)) {
                        $migrations[] = str_replace('../..', '', $migration_folder) . $migration_name;
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Unable to upload Migration!']);
                        exit();
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Migration should be image!']);
                    exit();
                }
            }
            $migration = implode("|", $migrations);
            $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Migration'");
            if ($check->num_rows > 0) {
                $update = $conn->query("UPDATE Student_Documents SET Location = '$migration' WHERE Student_ID = $inserted_id AND Type = 'Migration'");
            } else {
                $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Migration', '$migration')");
            }
        }

        // Affidavit
        if (isset($documnetArray['affidavit']) && count($documnetArray['affidavit']) > 0) {
            foreach ($documnetArray['affidavit'] as $key => $base64_string) {
                if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                    $image_type = $matches[1];
                    $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                    $base64_string = base64_decode($base64_string);
                } else {
                    echo "Invalid Base64 format for image $key<br>";
                    continue;
                }
                $affidavit_extension = $image_type;
                $affidavit_name = $inserted_id . "_Affidavit_" . $key . "." . $affidavit_extension;
                if (in_array($affidavit_extension, $allowed_file_extensions)) {
                    if (file_exists($affidavit_folder . $affidavit_name)) {
                        unlink($affidavit_folder . $affidavit_name);
                    }
                    if (file_put_contents($affidavit_folder . $affidavit_name, $base64_string)) {
                        $affidavits[] = str_replace('../..', '', $affidavit_folder) . $affidavit_name;
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Unable to upload affidavit!']);
                        exit();
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Affidavit should be image!']);
                    exit();
                }
            }
            $affidavit = implode("|", $affidavits);
            $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Affidavit'");
            if ($check->num_rows > 0) {
                $update = $conn->query("UPDATE Student_Documents SET Location = '$affidavit' WHERE Student_ID = $inserted_id AND Type = 'Affidavit'");
            } else {
                $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Affidavit', '$affidavit')");
            }
        }

        // Other Certificates
        if (isset($documnetArray['other_certificate']) && count($documnetArray['affidavit']) > 0) {
            foreach ($documnetArray['other_certificate'] as $key => $base64_string) {
                if (preg_match('/^data:image\/(\w+);base64,/', $base64_string, $matches)) {
                    $image_type = $matches[1];
                    $base64_string = substr($base64_string, strpos($base64_string, ',') + 1);
                    $base64_string = base64_decode($base64_string);
                } else {
                    echo "Invalid Base64 format for image $key<br>";
                    continue;
                }
                $other_certificate_extension =  $image_type;
                $other_certificate_name = $inserted_id . "_other_certificate_" . $key . "." . $other_certificate_extension;
                if (in_array($other_certificate_extension, $allowed_file_extensions)) {
                    if (file_exists($other_certificate_folder . $other_certificate_name)) {
                        unlink($other_certificate_folder . $other_certificate_name);
                    }
                    if (file_put_contents($other_certificate_folder . $other_certificate_name, $base64_string)) {
                        $other_certificates[] = str_replace('../..', '', $other_certificate_folder) . $other_certificate_name;
                    } else {
                        echo json_encode(['status' => 503, 'message' => 'Unable to upload other_certificate!']);
                        exit();
                    }
                } else {
                    echo json_encode(['status' => 302, 'message' => 'other_certificate should be image!']);
                    exit();
                }
            }
            $other_certificate = implode("|", $other_certificates);
            $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Other Certificate'");
            if ($check->num_rows > 0) {
                $update = $conn->query("UPDATE Student_Documents SET Location = '$other_certificate' WHERE Student_ID = $inserted_id AND Type = 'Other Certificate'");
            } else {
                $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Other Certificate', '$other_certificate')");
            }
        }
        if ($update) {
            return json_encode(['status' => 'success', 'message' => 'Documents saved successfully!']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Something went wrong!']);
        }
    }
}

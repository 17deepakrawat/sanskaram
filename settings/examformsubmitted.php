<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
    .select2-container .select2-selection {
        border-radius: 10px;
        height: 48px !important;
        font-size: 17px;
        font-family: system-ui;
    }

    .select2-container .select2-selection .select2-selection__arrow {
        top: auto;
        bottom: 11px;
    }

    .select2-container--open .select2-selection {
        box-shadow: none;
        border: 1px solid #2b303b !important;
    }

    .select2-results .select2-results__option--highlighted {
        background-color: #55638d !important;
        border-radius: 3px;
        color: #ffffff !important;
    }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<div class="page-container ">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <?php unset($_SESSION['current_session']);
    unset($_SESSION['current_session']); ?>
    <div class="page-content-wrapper ">
        <!-- START PAGE CONTENT -->
        <div class="content ">
            <!-- START JUMBOTRON -->
            <div class="jumbotron" data-pages="parallax">
                <div class=" container-fluid   sm-p-l-0 sm-p-r-0">
                    <div class="inner">
                        <!-- START BREADCRUMB -->
                        <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
                            <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                            for ($i = 1; $i <= count($breadcrumbs); $i++) {
                                if (count($breadcrumbs) == $i) : $active = "active";
                                    $crumb = explode("?", $breadcrumbs[$i]);
                                    echo '<li class="breadcrumb-item ' . $active . '">' . 'Examform Submitted' . '</li>';
                                endif;
                            }
                            ?>
                            <button type="button" class=" custom_add_button" id="exportCSV"> Report Download <i class="uil uil-down-arrow ml-2"></i> </button>
                        </ol>
                        <!-- END BREADCRUMB -->

                    </div>
                </div>
            </div>
            <div class=" container-fluid">
                <div class="card card-transparent">
                    <div class="row" id="assignments"></div>
                    <div class="card-header">
                        <div class="row d-flex justify-content-between">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="full-width" style="width:40px" data-init-plugin="select2"
                                        id="sessions" onchange="changeSession(this.value)">
                                        <option value="All">All</option>
                                        <?php
                                        $role_query = "";
                                        if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') {
                                            $role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
                                            $role_query = str_replace('{{ column }}', 'Added_For', $role_query);
                                        }
                                        $sessions = $conn->query("SELECT Admission_Sessions.ID,Admission_Sessions.Name,Admission_Sessions.Current_Status FROM Admission_Sessions LEFT JOIN Students ON Admission_Sessions.ID = Students.Admission_Session_ID WHERE Admission_Sessions.University_ID = '" . $_SESSION['university_id'] . "' $role_query GROUP BY Name ORDER BY Admission_Sessions.ID ASC");
                                        while ($session = mysqli_fetch_assoc($sessions)) { ?>
                                            <option value="<?= $session['Name'] ?>" <?php print $session['Current_Status'] == 1 ? 'selected' : '' ?>><?= $session['Name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <input type="text" id="e-book-search-table"
                                    class="form-control pull-right p-2 fw-bold custom_search_section" placeholder="Search">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover nowrap " id="students-table">
                                <thead>
                                    <tr>
                                        <th>Addmission Session</th>
                                        <th>Student Name</th>
                                        <th>Enrollment No</th>
                                        <th>Unique ID</th>
                                        <th>Course Name</th>
                                        <th>Sub Course Name</th>
                                        <th>Center/Sub-Center Name</th>
                                        <th>Semester</th>
                                        <th>University Name</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT -->
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
        <script type="text/javascript">
            $(function() {
                var role = '<?= $_SESSION['Role'] ?>';
                var show = role == 'Administrator' ? true : false;
                var table = $('#students-table');
                var settings = {
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    'ajax': {
                        'url': '/app/examform/server'
                    },
                    'columns': [{
                            data: "adm"
                        },
                        {
                            data: "student_name"
                        },
                        {
                            data: "enrollment_no"
                        },
                        {
                            data: "uniqueid"
                        },
                        {
                            data: "coursename"
                        },
                        {
                            data: "sub_course_name"
                        },
                        {
                            data: "Center_SubCeter"
                        },
                        {
                            data: "semester"
                        },
                        {
                            data: "universityname"
                        },
                    ],
                    "sDom": "<t><'row'<p i>>",
                    "destroy": true,
                    "scrollCollapse": true,
                    "oLanguage": {
                        "sLengthMenu": "_MENU_ ",
                        "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
                    },
                    "aaSorting": [],
                    "iDisplayLength": 25,
                };
                table.dataTable(settings);
                $('#e-book-search-table').keyup(function() {
                    table.fnFilter($(this).val());
                });
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                updateSession('All');
            })

            function changeSession(value) {
                $('input[type=search]').val('');
                updateSession();
            }

            function updateSession() {
                var session_id = $('#sessions').val();
                $.ajax({
                    url: '/app/examform/filter',
                    data: {
                        session_id: session_id
                    },
                    type: 'POST',
                    success: function(data) {
                        $('.table').DataTable().ajax.reload(null, false);
                    }
                })
            }
        </script>

        <script>
            $(document).ready(function() {
                $('#exportCSV').on('click', function() {
                    var exportUrl = '/app/examform/excelexport?format=csv';
                    window.location.href = exportUrl;
                });
            });
        </script>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
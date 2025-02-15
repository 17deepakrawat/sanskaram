<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- START PAGE CONTENT WRAPPER -->
    <div class="page-content-wrapper ">
        <!-- START PAGE CONTENT -->
        <div class="content ">
            <!-- START JUMBOTRON -->
            <div class="jumbotron" data-pages="parallax">
                <div class=" container-fluid sm-p-l-0 sm-p-r-0">
                <div class="inner">
                    <!-- START BREADCRUMB -->
                    <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
                        <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                        for ($i = 1; $i <= count($breadcrumbs); $i++) {
                            if (count($breadcrumbs) == $i) :
                            $active = "active";
                            $crumb = explode("?", $breadcrumbs[$i]);
                            echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                            endif;
                        } ?>
                        <li>
                            <span class="btn btn-primary cursor-pointer" onclick="showUnAssignDocketIdCenter()" style = "font-size:0.70rem;" data-toggle="tooltip" data-placement="top" title="Assign Docket_id">Select Center</span> 
                        </li>
                    </ol>
                    <!-- END BREADCRUMB -->
                </div>
                </div>
            </div>
            <!-- END JUMBOTRON -->
            <!-- START CONTAINER FLUID -->
            <div class="container-fluid">
                <!-- BEGIN PlACE PAGE CONTENT HERE -->
                <div class="card card-transparent">   
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs nav-tabs-linetriangle" data-init-reponsive-tabs="dropdownfx">
                        <li class="nav-item">
                            <a class="active" data-toggle="tab" data-target="#all-marksheet" href="#"><span>All MarkSheet Record - <span id="all_marksheet_count">0</span></span></a>
                        </li>
                        <li class="nav-item">
                            <a data-toggle="tab" data-target="#not-dispatched" href="#"><span>Not Dispatched - <span id="not_dispatched_count">0</span></span></a>
                        </li>
                        <li class="nav-item">
                            <a data-toggle="tab" data-target="#dispatched" href="#"><span>Dispatched - <span id="dispatched_count">0</span></span></a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="all-marksheet">
                            <div class="row d-flex justify-content-end">
                                <div class="col-md-2 d-flex justify-content-start">
                                    <input type="text" id="all_marksheet_entry-search" class="form-control pull-right" placeholder="Search">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover nowrap" id="all-marksheet-breakout-table">
                                    <thead>
                                        <tr>
                                            <th>Sl.No</th>
                                            <th>Center Name</th>
                                            <th>MarkSheet Upload At</th>
                                            <th>No. Of MarkSheet</th>
                                            <th>Docket_Id</th>
                                            <th>Upload File</th>
                                            <th>Dispatch Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="not-dispatched">
                            <div class="row d-flex justify-content-end">
                                <div class="col-md-2 d-flex justify-content-start">
                                    <input type="text" id="not-dispatched-marksheet_entry-search" class="form-control pull-right" placeholder="Search">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover nowrap" id="not-dispatched-marksheet-breakout-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px !important;">Sl.No</th>
                                            <th>Center Name</th>
                                            <th>MarkSheet Upload At</th>
                                            <th>No. Of MarkSheet</th>
                                            <th>Docket_Id</th>
                                            <th>Upload File</th>
                                            <th>Dispatch Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="dispatched">
                            <div class="row d-flex justify-content-end">
                                <div class="col-md-2 d-flex justify-content-start">
                                    <input type="text" id="dispatched-marksheet_entry-search" class="form-control pull-right" placeholder="Search">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover nowrap" id="dispatched-marksheet-breakout-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px !important;">Sl.No</th>
                                            <th>Center Name</th>
                                            <th>MarkSheet Upload At</th>
                                            <th>No. Of MarkSheet</th>
                                            <th>Docket_Id</th>
                                            <th>Upload File</th>
                                            <th>Dispatch Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PLACE PAGE CONTENT HERE -->
            </div>
            <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->   
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
<script type="text/javascript">

var allMarksheetTable = $('#all-marksheet-breakout-table');
var notDispatchedMarksheetTable = $('#not-dispatched-marksheet-breakout-table');
var dispatchedMarkSheetTable = $('#dispatched-marksheet-breakout-table');

var allMarksheetTableSetting = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
        'url':'/app/document-issuence/marksheet/all-marksheet_breakout-server',
        'type': 'POST',
        complete: function(xhr, responseText) {
            $('#all_marksheet_count').html(xhr.responseJSON.iTotalDisplayRecords);
        }
    },
    'columns': [{ 
            data: "Slno",
        },{ 
            data: "Center_Name",
            render : function(data,type,row){
            return '<b>'+data+'</b>';
            }
        },{
            data : "insert_date" , 
            render : function(data,type,row) {
              	console.log(data);
                return "<b>"+data+"</b>";
            }
        },{ 
            data: "Marksheet_count" , 
            render : function(data,type,row) {
                return '<div class = "cursor-pointer" onclick="showAllMarkSheetDetails(&#39;' + row.Center_id + '&#39;,&#39;' + row.Docket_Id + '&#39;)" ><b>'+data+'</b></div>';
            }
        },{ 
            data: "Docket_Id" ,
            render: function(data, type, row) {
            if(data != null) {
                var edit = '<span class="badge badge-success cursor-pointer p-2" style="font-size:13px;">'+data+'</span>';
                return edit;
            } else {
                var edit = '<span class="badge badge-secondary cursor-pointer" onclick="checkForCenterDocketId(&#39;' + row.Center_id + '&#39;)">Assign Docket_Id</span>'
                return edit;
            }
        }
        },{ 
            data: "upload_file",
            render : function(data,type,row) {
                if (data != '') {
                    var edit = '<a href="'+data+'" class="btn btn-primary btn-animated from-left" download><span style = "font-size:0.70rem;">Download</span><span class="hidden-block"><i class = "uil uil-file-download"></i></span></a>'; 
                } else {
                    var edit = '<span class="text-primary cursor-pointer" onclick="uploadFile(&#39;' + row.Docket_Id + '&#39;)">Upload</span>';
                }
                return edit;
            }
        },{ 
            data: "Dispatch_status",
            render : function(data,type,row) {
                if(data == '1') {
                    var edit = '<span class="badge badge-secondary cursor-pointer" onclick="fillDispatchDetails(&#39;' + row.Docket_Id + '&#39;)">Not Dispatched</span>';
                    return edit;
                } else {
                    var edit = '<span class="badge badge-success cursor-pointer p-2" style = "font-size:0.70rem;" onclick = "viewDispatchedDetails(&#39;' + row.Docket_Id + '&#39;)">Dispatched on '+row.dispatch_date+'</span>';
                    return edit;
                }
            }
        }
    ],
    "sDom": "<t><'row'<p i>>",
    "destroy": true,
    "scrollCollapse": true,
    "oLanguage": {
        "sLengthMenu": "_MENU_ ",
        "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
    },
    "aaSorting": [],
    "iDisplayLength": 10,
    "drawCallback": function( settings ) {
        $('[data-toggle="tooltip"]').tooltip();
    },
};

var notDispatchedMarksheetTableSetting = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
        'url':'/app/document-issuence/marksheet/not-dispatched-marksheet_breakout-server.php',
        'type': 'POST',
        complete: function(xhr, responseText) {
            $('#not_dispatched_count').html(xhr.responseJSON.iTotalDisplayRecords);
        }
    },
    'columns': [{ 
            data: "Slno",
        },{ 
            data: "Center_Name",
            render : function(data,type,row){
                return '<b>'+data+'</b>';
            }
        },{
            data : "insert_date" , 
            render : function(data,type,row) {
                return "<b>"+data+"</b>";
            }
        },{ 
            data: "Marksheet_count" , 
            render : function(data,type,row) {
                return '<div class = "cursor-pointer" onclick="showAllMarkSheetDetails(&#39;' + row.Center_id + '&#39;,&#39;' + row.Docket_Id + '&#39;)" ><b>'+data+'</b></div>';
            }
        },{ 
            data: "Docket_Id" ,
            render: function(data, type, row) {
                if(data != null) {
                    var edit = '<span class="badge badge-success cursor-pointer p-2" style="font-size:13px;">'+data+'</span>';
                    return edit;
                } else {
                    var edit = '<span class="badge badge-secondary cursor-pointer" onclick="checkForCenterDocketId(&#39;' + row.Center_id + '&#39;)">Assign Docket_Id</span>'
                    return edit;
                }
            }
        },{ 
            data: "upload_file",
            render : function(data,type,row) {
                if (data != '') {
                    var edit = '<a href="'+data+'" class="btn btn-primary btn-animated from-left" download><span style = "font-size:0.70rem;">Download</span><span class="hidden-block"><i class = "uil uil-file-download"></i></span></a>';
                } else {
                    var edit = '<span class="text-primary cursor-pointer" onclick="uploadFile(&#39;' + row.Docket_Id + '&#39;)">Upload</span>';
                }
                return edit;
            }
        },{ 
            data: "Dispatch_status",
            render : function(data,type,row) {
                if(data == '1') {
                    var edit = '<span class="badge badge-secondary cursor-pointer" onclick="fillDispatchDetails(&#39;' + row.Docket_Id + '&#39;)">Not Dispatched</span>';
                    return edit;
                } else {
                    var edit = '<span class="badge badge-success cursor-pointer" onclick = "viewDispatchedDetails(&#39;' + row.Docket_Id + '&#39;)">Dispatched on '+row.dispatch_date+'</span>';
                    return edit;
                }
            }
        }
    ],
    "sDom": "<t><'row'<p i>>",
    "destroy": true,
    "scrollCollapse": true,
    "oLanguage": {
        "sLengthMenu": "_MENU_ ",
        "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
    },
    "aaSorting": [],
    "iDisplayLength": 10,
    "drawCallback": function( settings ) {
        $('[data-toggle="tooltip"]').tooltip();
    },
};

var dispatchedMarkSheetTableSetting = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
        'url':'/app/document-issuence/marksheet/dispatched-marksheet_breakout-server',
        'type': 'POST',
        complete: function(xhr, responseText) {
            $('#dispatched_count').html(xhr.responseJSON.iTotalDisplayRecords);
        }
    },
    'columns': [{ 
            data: "Slno"
        },{ 
            data: "Center_Name",
            render : function(data,type,row){
                return '<b>'+data+'</b>';
            }
        },{
            data : "insert_date" , 
            render : function(data,type,row) {
                return "<b>"+data+"</b>";
            }
        },{ 
            data: "Marksheet_count" , 
            render : function(data,type,row) {
                return '<div class = "cursor-pointer" onclick="showAllMarkSheetDetails(&#39;' + row.Center_id + '&#39;,&#39;' + row.Docket_Id + '&#39;)" ><b>'+data+'</b></div>';
            }
        },{ 
            data: "Docket_Id" ,
            render: function(data, type, row) {
                if(data != '') {
                    var edit = '<span class="badge badge-success cursor-pointer p-2" style="font-size:13px;">'+data+'</span>';
                    return edit;
                } else {
                    var edit = '<span class="badge badge-secondary cursor-pointer" onclick="checkForCenterDocketId(&#39;' + row.Center_id + '&#39;)">Assign Docket_Id</span>'
                    return edit;
                }
            }
        },{ 
            data: "upload_file",
            render : function(data,type,row) {
                if (data != '') {
                    var edit = '<a href="'+data+'" class="btn btn-primary btn-animated from-left" download><span style = "font-size:0.70rem;">Download</span><span class="hidden-block"><i class = "uil uil-file-download"></i></span></a>';
                } else {
                    var edit = '<span class="text-primary cursor-pointer" onclick="uploadFile(&#39;' + row.Docket_Id + '&#39;)">Upload</span>';
                }
                return edit;
            }
        },{ 
            data: "Dispatch_status",
            render : function(data,type,row) {
                if(data == '1') {
                    var edit = '<span class="badge badge-secondary cursor-pointer" onclick="fillDispatchDetails(&#39;' + row.Docket_Id + '&#39;)">Not Dispatched</span>';
                    return edit;
                } else {
                    var edit = '<span class="badge badge-success cursor-pointer p-2" style = "font-size:0.75rem;" onclick = "viewDispatchedDetails(&#39;' + row.Docket_Id + '&#39;)">Dispatched on '+row.dispatch_date+'</span>';
                    return edit;
                }
            }
        }
    ],
    "sDom": "<t><'row'<p i>>",
    "destroy": true,
    "scrollCollapse": true,
    "oLanguage": {
        "sLengthMenu": "_MENU_ ",
        "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
    },
    "aaSorting": [],
    "iDisplayLength": 10,
    "drawCallback": function( settings ) {
        $('[data-toggle="tooltip"]').tooltip();
    },
};

allMarksheetTable.dataTable(allMarksheetTableSetting);
notDispatchedMarksheetTable.dataTable(notDispatchedMarksheetTableSetting);
dispatchedMarkSheetTable.dataTable(dispatchedMarkSheetTableSetting);

$('#all_marksheet_entry-search').keyup(function() {
    allMarksheetTable.fnFilter($(this).val());
});

$('#not-dispatched-marksheet_entry-search').keyup(function() {
    notDispatchedMarksheetTable.fnFilter($(this).val());
});

$('#dispatched-marksheet_entry-search').keyup(function() {
    dispatchedMarkSheetTable.fnFilter($(this).val());
});

/**
* This function is use for check selected center docket id is already genrated or not if generated and not dispatch
* in that case give option to user that they can assign same docket id 
* And if user not want to same then new docket id is generated 
*/

function checkForCenterDocketId(center_id) {
    $.ajax({
        url: "/app/document-issuence/marksheet/generateDocketId",
        type: 'POST',
        dataType: 'json',
        data: {
            center_id,
            'type' : 'checkCenterDocketID'
        },
        success: function(data) {
            if (data.status == 200) {
                generateDocketId(center_id);
            } else if (data.status == 400) {
                Swal.fire({
                    title: data.title,
                    text: data.text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Assign Same.',
                    cancelButtonText: 'Assign New',
                }).then((result) => {
                    if (result.isConfirmed) {
                    $.ajax({
                        url: "/app/document-issuence/marksheet/generateDocketId",
                        type: 'POST',
                        dataType: 'json',
                        data: {center_id,
                            'docket_id' : data.docket_id
                        },
                        success: function(data) {
                        if (data.status == 200) {
                            notification('success', data.message);
                            $('.table').DataTable().ajax.reload(null, false);
                        } else {
                            notification('danger', data.message);
                            $('.table').DataTable().ajax.reload(null, false);
                        }
                        }
                    });
                    } else {
                        generateDocketId(center_id);
                    }
                })
            }
        }
    });   
}

function generateDocketId(center_id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Want to generate the Docket Id",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Process.'
    }).then((result) => {
        if (result.isConfirmed) {
        $.ajax({
            url: "/app/document-issuence/marksheet/generateDocketId",
            type: 'POST',
            dataType: 'json',
            data: {center_id},
            success: function(data) {
            if (data.status == 200) {
                notification('success', data.message);
                $('.table').DataTable().ajax.reload(null, false);
            } else {
                notification('danger', data.message);
                $('.table').DataTable().ajax.reload(null, false);
            }
            }
        });
        } else {
            $('.table').DataTable().ajax.reload(null, false);
        }
    })
}

function showAllMarkSheetDetails(center_id,docket_id) {
    $.ajax({
        url: '/app/document-issuence/marksheet/showMarksheetCenterBies',
        type: 'POST',
        data : {
            center_id,
            docket_id
        },
        success: function(data) {
            $('#lg-modal-content').html(data);
            $('#lgmodal').modal('show');
        }
    })
}

function uploadFile(docket_id) {
    if( docket_id === 'null') {
        Swal.fire({
            title : "Docket Id Not Assign",
            text : "Please generate the Docket_ID",
            icon: 'error',
        });
    } else {
        $.ajax({
            url: '/app/document-issuence/marksheet/uploadMarkSheet',
            type: 'POST',
            data : {docket_id},
            success: function(data) {
                $('#lg-modal-content').html(data);
                $('#lgmodal').modal('show');
            }
        })
    }
}

function fillDispatchDetails(docket_id) {
    if (docket_id === 'null') {
        Swal.fire({
            title : "Docket Id Not Assign",
            text : "Please generate the Docket_ID",
            icon: 'error',
        });
    } else {
        $.ajax({
            url: '/app/document-issuence/marksheet/insertDispatchDetails',
            type: 'POST',
            data : {docket_id},
            success: function(data) {
                $('#lg-modal-content').html(data);
                $('#lgmodal').modal('show');
            }
        })
    }
}

function viewDispatchedDetails(docket_id) {
    $.ajax({
        url: '/app/document-issuence/marksheet/viewDispatchDetails',
        type: 'POST',
        data : {docket_id},
        success: function(data) {
            $('#lg-modal-content').html(data);
            $('#lgmodal').modal('show');
        }
    });
} 

function showUnAssignDocketIdCenter() {
    $.ajax({
        url: '/app/document-issuence/marksheet/showUnAssignDocketIdCenter',
        type: 'GET',
        success: function(data) {
            if (data == 'all center docket_id assign') {
                Swal.fire({
                    title : "Empty Center List",
                    text : "All center has assign Docket Id",
                    icon: 'warning',
                });
            } else {
                $('#lg-modal-content').html(data);
                $('#lgmodal').modal('show');
            }
        }
    });
}

</script>


<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
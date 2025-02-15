<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  use setasign\Fpdi\Fpdi;

  require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
  require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();
  
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
    
    $pdf = new Fpdi();

    $pdf->SetTitle('Export Documents for '.$id);

    $documents = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id");
    while($document = $documents->fetch_assoc()){
      $files = explode("|", $document['Location']);
      foreach($files as $file){
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        // print_r(pathinfo("../..".$file));die;
        // print_r(mime_content_type("../..".$file));die;
        if (!file_exists("../..".$file)) {
            die('Error: File not found at ' . "../..".$file);
        }
        // if (mime_content_type("../..".$file) !== 'image/jpeg') {
        //     die('Error: File is not a valid PNG.');
        // }
        $pdf->image("../..".$file, 10, 10, 190, 270);
      }
    }

    $pdf->Output('I', $id.'_Documents.pdf');
  }

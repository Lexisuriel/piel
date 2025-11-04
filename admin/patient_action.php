<?php
include_once 'config/Database.php';
include_once 'class/Patient.php';

$database = new Database();
$db = $database->getConnection();

$patient = new Patient($db);

if(!empty($_POST['action']) && $_POST['action'] == 'listPatient') {
    $patient->listPatients();
}

if(!empty($_POST['action']) && $_POST['action'] == 'getPatient') {
    $patient->id = $_POST["id"];
    $patient->getPatient();
}

if(!empty($_POST['action']) && $_POST['action'] == 'addPatient') {    
    $patient->name = $_POST["name"];
    $patient->gender = $_POST["gender"];
    $patient->birthdate = !empty($_POST["birthdate"]) ? $_POST["birthdate"] : NULL;
    $patient->email = $_POST["email"];    
    $patient->mobile = $_POST["mobile"];
    $patient->address = $_POST["address"];
$patient->medical_history = $_POST["medical_history"]; // No "history"
    
    $response = array();
    if($patient->insert()) {
        $response['status'] = 'success';
        $response['message'] = 'Paciente agregado correctamente';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error al agregar paciente';
    }
    echo json_encode($response);
    exit;
}

if(!empty($_POST['action']) && $_POST['action'] == 'updatePatient') {
    $patient->id = $_POST["id"];
    $patient->name = $_POST["name"];
    $patient->gender = $_POST["gender"];
    $patient->email = $_POST["email"];
    $patient->mobile = $_POST["mobile"];
    $patient->address = $_POST["address"];
$patient->birthdate = $_POST["birthdate"]; // No calcular edad
$patient->medical_history = $_POST["medical_history"]; // No "history"
    
    $response = array();
    if($patient->update()) {
        $response['status'] = 'success';
        $response['message'] = 'Paciente actualizado correctamente';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error al actualizar paciente';
    }
    echo json_encode($response);
    exit;
}

if(!empty($_POST['action']) && $_POST['action'] == 'deletePatient') {
    $patient->id = $_POST["id"];
    
    $response = array();
    if($patient->delete()) {
        $response['status'] = 'success';
        $response['message'] = 'Paciente eliminado correctamente';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error al eliminar paciente';
    }
    echo json_encode($response);
    exit;
}

// Si no hay acción válida
echo json_encode(array('status' => 'error', 'message' => 'Acción no válida'));
?>
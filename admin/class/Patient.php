<?php
class Patient {	
    private $userTable = 'usuario';
    private $conn;
    
    public $id;
    public $name;
    public $gender;
    public $birthdate; // Cambiado de age a birthdate
    public $email;
    public $mobile;
    public $address;
    public $medical_history; // Nombre consistente
    
    public function __construct($db){
        $this->conn = $db;
    }
    
    public function listPatients(){
        $sqlWhere = "WHERE rol = 'paciente'";
        
        if($_SESSION["role"] == 'patient') { 
            $sqlWhere .= " AND id = '".$_SESSION["userid"]."'";
        }
        
        $sqlQuery = "SELECT 
                        id, 
                        nombre_completo as name, 
                        genero as gender,
                        TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) as age,
                        email,
                        tell as mobile,
                        direccion as address,
                        medical_history,
                        fecha_nacimiento as birthdate,
                        fecha_registro
                     FROM ".$this->userTable." 
                     $sqlWhere";
    
    // Resto del código de búsqueda y ordenamiento permanece igual
    if(!empty($_POST["search"]["value"])){
        $sqlQuery .= ' AND (nombre_completo LIKE "%'.$_POST["search"]["value"].'%" ';			
        $sqlQuery .= ' OR email LIKE "%'.$_POST["search"]["value"].'%" ';
        $sqlQuery .= ' OR genero LIKE "%'.$_POST["search"]["value"].'%" ';
        $sqlQuery .= ' OR tell LIKE "%'.$_POST["search"]["value"].'%" ';
        $sqlQuery .= ' OR direccion LIKE "%'.$_POST["search"]["value"].'%" ';				
        $sqlQuery .= ' OR medical_history LIKE "%'.$_POST["search"]["value"].'%") ';							
    }
    
    // Resto del método permanece igual hasta la construcción del array
    while ($patient = $result->fetch_assoc()) { 				
        $rows = array();			
        $rows[] = $patient['id'];
        $rows[] = ucfirst($patient['name']);
        $rows[] = $patient['gender'];
        $rows[] = $patient['age'];
        $rows[] = $patient['email'];
        $rows[] = $patient['mobile'];	
        $rows[] = $patient['address'];			
        $rows[] = !empty($patient['medical_history']) ? substr($patient['medical_history'], 0, 50).'...' : 'No history'; // Mostramos un resumen			
        $rows[] = '<button type="button" name="view" id="'.$patient["id"].'" class="btn btn-info btn-xs view"><span class="glyphicon glyphicon-file" title="View">View</span></button>';			
        $rows[] = '<button type="button" name="update" id="'.$patient["id"].'" class="btn btn-warning btn-xs update"><span class="glyphicon glyphicon-edit" title="Edit">Edit</span></button>';
        if($_SESSION["role"] != 'patient') {
            $rows[] = '<button type="button" name="delete" id="'.$patient["id"].'" class="btn btn-danger btn-xs delete" ><span class="glyphicon glyphicon-remove" title="Delete">Delete</span></button>';
        } else {
            $rows[] = '';
        }
        $records[] = $rows;
    }
        $output = array(
            "draw" => intval($_POST["draw"]),			
            "iTotalRecords" => $displayRecords,
            "iTotalDisplayRecords" => $allRecords,
            "data" => $records
        );
        
        echo json_encode($output);
        exit;
    }
    
    public function insert(){
        if($this->name) {
            $stmt = $this->conn->prepare("
            INSERT INTO ".$this->userTable."
            (nombre_completo, email, genero, tell, direccion, fecha_nacimiento, medical_history, rol, password)
            VALUES(?,?,?,?,?,?,?,'paciente',?)");
        
            $tempPassword = password_hash('temp123', PASSWORD_DEFAULT);
            
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->gender = htmlspecialchars(strip_tags($this->gender));
            $this->mobile = htmlspecialchars(strip_tags($this->mobile));
            $this->address = htmlspecialchars(strip_tags($this->address));
            $this->medical_history = htmlspecialchars(strip_tags($this->medical_history));
            
            $birthdate = $this->birthdate ?: NULL;
            
            $stmt->bind_param("ssssssss", 
                $this->name, 
                $this->email, 
                $this->gender, 
                $this->mobile, 
                $this->address, 
                $birthdate,
                $this->medical_history,
                $tempPassword);
            
            if($stmt->execute()){
                return true;
            }
        }
        return false;
    }
    
    public function update(){
        if($this->id) {			
            $stmt = $this->conn->prepare("
            UPDATE ".$this->userTable." 
            SET nombre_completo = ?, 
                email = ?, 
                genero = ?, 
                tell = ?, 
                direccion = ?, 
                fecha_nacimiento = ?, 
                medical_history = ? 
            WHERE id = ?");
     
            $this->id = htmlspecialchars(strip_tags($this->id));
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->gender = htmlspecialchars(strip_tags($this->gender));
            $this->mobile = htmlspecialchars(strip_tags($this->mobile));
            $this->address = htmlspecialchars(strip_tags($this->address));
            $this->medical_history = htmlspecialchars(strip_tags($this->medical_history));
            
            $birthdate = $this->birthdate ?: NULL;
            
            $stmt->bind_param("sssssssi", 
                $this->name, 
                $this->email, 
                $this->gender, 
                $this->mobile, 
                $this->address, 
                $birthdate,
                $this->medical_history,
                $this->id);
            
            if($stmt->execute()){
                return true;
            }
        }
        return false;
    }
    
    public function getPatient(){
        if($this->id) {
            $sqlQuery = "
                SELECT 
                    id,
                    nombre_completo as name,
                    email,
                    genero as gender,
                    tell as mobile,
                    direccion as address,
                    fecha_nacimiento as birthdate,
                    medical_history
                FROM ".$this->userTable." 
                WHERE id = ?";
                
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param("i", $this->id);    
            $stmt->execute();
            $result = $stmt->get_result();
            $record = $result->fetch_assoc();
            
            // Calcular edad
            $age = '';
            if($record['birthdate']) {
                $birthDate = new DateTime($record['birthdate']);
                $today = new DateTime();
                $age = $today->diff($birthDate)->y;
            }
            
            $response = array(
                'id' => $record['id'] ?? '',
                'name' => $record['name'] ?? '',
                'email' => $record['email'] ?? '',
                'gender' => $record['gender'] ?? '',
                'mobile' => $record['mobile'] ?? '',
                'address' => $record['address'] ?? '',
                'birthdate' => $record['birthdate'] ?? '',
                'age' => $age,
                'medical_history' => $record['medical_history'] ?? ''
            );
            
            echo json_encode($response);
            exit;
        }
    }
    
    public function delete(){
        if($this->id) {			
            $stmt = $this->conn->prepare("
                DELETE FROM ".$this->userTable." 
                WHERE id = ? AND rol = 'paciente'");

            $this->id = htmlspecialchars(strip_tags($this->id));

            $stmt->bind_param("i", $this->id);

            if($stmt->execute()){
                return true;
            }
        }
        return false;
    }
    
    public function getTotalPatient(){		
        $stmt = $this->conn->prepare("
        SELECT *
        FROM ".$this->userTable."
        WHERE rol = 'paciente'");				
        $stmt->execute();			
        $result = $stmt->get_result();
        return $result->num_rows;	
    }

    public function patientList(){		
        $stmt = $this->conn->prepare("
        SELECT * FROM ".$this->userTable."
        WHERE rol = 'paciente'");				
        $stmt->execute();			
        $result = $stmt->get_result();		
        return $result;	
    }	
}
?>
$(document).ready(function() {
    var patientTable = $('#patientListing').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "patient_action.php",
            "type": "POST",
            "data": {action: 'listPatient'},
            "dataSrc": function(json) {
                return json.data;
            }
        },
        "columns": [
            {"data": "0"},
            {"data": "1"},
            {"data": "2"},
            {"data": "3"},
            {"data": "4"},
            {"data": "5"},
            {"data": "6"},
            {"data": "7"},
            {
                "data": "8",
                "orderable": false,
                "searchable": false
            },
            {
                "data": "9",
                "orderable": false,
                "searchable": false
            },
            {
                "data": "10",
                "orderable": false,
                "searchable": false
            }
        ],
        "responsive": true,
        "language": {
            "emptyTable": "No hay pacientes registrados",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ pacientes",
            "infoEmpty": "Mostrando 0 a 0 de 0 pacientes",
            "infoFiltered": "(filtrado de _MAX_ pacientes totales)",
            "lengthMenu": "Mostrar _MENU_ pacientes",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "No se encontraron coincidencias",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });

    // Delegación de eventos para los botones dinámicos
    $('#patientListing').on('click', '.view', function() {
        var id = $(this).attr('id');
        viewPatient(id);
    });
    
    $('#patientListing').on('click', '.update', function() {
        var id = $(this).attr('id');
        editPatient(id);
    });
    
    $('#patientListing').on('click', '.delete', function() {
        var id = $(this).attr('id');
        deletePatient(id);
    });

    $('#addPatient').click(function() {
        $('#patientForm')[0].reset();
        $('.modal-title').html('<i class="fa fa-plus"></i> Agregar Paciente');
        $('#action').val('addPatient');
        $('#patientModal').modal('show');
    });
});

function viewPatient(id) {
    $.ajax({
        url: "patient_action.php",
        type: "POST",
        data: {action: 'getPatient', id: id},
        dataType: 'json',
        success: function(data) {
            $('#p_name').text(data.name);
            $('#p_gender').text(data.gender || 'N/A');
            $('#p_age').text(data.age || 'N/A');
            $('#p_email').text(data.email);
            $('#p_mobile').text(data.mobile || 'N/A');
            $('#p_address').text(data.address || 'N/A');
            $('#p_history').text(data.medical_history || 'N/A');
            
            $('#patientDetails').modal('show');
        },
        error: function(xhr, status, error) {
            alert('Error al cargar los datos del paciente: ' + error);
        }
    });
}

function editPatient(id) {
    $.ajax({
        url: "patient_action.php",
        type: "POST",
        data: {action: 'getPatient', id: id},
        dataType: 'json',
        success: function(data) {
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#gender').val(data.gender);
            $('#birthdate').val(data.birthdate);
            $('#age').val(data.age);
            $('#email').val(data.email);
            $('#mobile').val(data.mobile);
            $('#address').val(data.address);
            $('#medical_history').val(data.medical_history);
            
            $('.modal-title').html('<i class="fa fa-pencil"></i> Editar Paciente');
            $('#action').val('updatePatient');
            $('#patientModal').modal('show');
        },
        error: function(xhr, status, error) {
            alert('Error al cargar los datos para editar: ' + error);
        }
    });
}

function deletePatient(id) {
    if(confirm("¿Estás seguro de que deseas eliminar este paciente?")) {
        $.ajax({
            url: "patient_action.php",
            type: "POST",
            data: {action: 'deletePatient', id: id},
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    alert(response.message);
                    $('#patientListing').DataTable().ajax.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al eliminar el paciente: ' + error);
            }
        });
    }
}

$('#patientForm').on('submit', function(e) {
    e.preventDefault();
    
    var formData = $(this).serialize();
    var action = $('#action').val();
    
    $.ajax({
        url: "patient_action.php",
        type: "POST",
        data: formData,
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                $('#patientModal').modal('hide');
                alert(response.message);
                $('#patientListing').DataTable().ajax.reload();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('Error al procesar la solicitud: ' + error);
        }
    });
});

// Calcular edad automáticamente al cambiar la fecha de nacimiento
$('#birthdate').change(function() {
    if(this.value) {
        var birthDate = new Date(this.value);
        var today = new Date();
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        $('#age').val(age);
    }
});
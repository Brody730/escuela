// Guardar este archivo como 'form-handler.js'
document.addEventListener('DOMContentLoaded', function() {
    // Todo el código JavaScript previo va aquí
    document.getElementById('telefono_tipo').addEventListener('change', function() {
        const campoMama = document.getElementById('campoTelefonoMama');
        const campoPapa = document.getElementById('campoTelefonoPapa');
        
        // Ocultar todos los campos primero
        campoMama.style.display = 'none';
        campoPapa.style.display = 'none';
        
        // Mostrar campos según la selección
        switch(this.value) {
            case 'mama':
                campoMama.style.display = 'block';
                document.getElementById('telefono_mama').required = true;
                document.getElementById('nombre_mama').required = true;
                document.getElementById('telefono_papa').required = false;
                document.getElementById('nombre_papa').required = false;
                break;
            case 'papa':
                campoPapa.style.display = 'block';
                document.getElementById('telefono_papa').required = true;
                document.getElementById('nombre_papa').required = true;
                document.getElementById('telefono_mama').required = false;
                document.getElementById('nombre_mama').required = false;
                break;
            case 'ambos':
                campoMama.style.display = 'block';
                campoPapa.style.display = 'block';
                document.getElementById('telefono_mama').required = true;
                document.getElementById('nombre_mama').required = true;
                document.getElementById('telefono_papa').required = true;
                document.getElementById('nombre_papa').required = true;
                break;
        }
    });

    // Manejar el envío del formulario
    document.getElementById('registroForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('procesar.php', {  // Actualizada la ruta al archivo PHP
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Registro completado con éxito');
                this.reset();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error al procesar el registro');
            console.error('Error:', error);
        });
    });
});
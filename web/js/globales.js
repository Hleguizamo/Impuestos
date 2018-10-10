//Functions for all the application
function selectionAlert(){
	swal({
		title: 'Información',
		text: 'Seleccione una fila para Editar',
		showConfirmButton: true,
		type: 'warning'
	});
}

function deleteAlert(){
	swal({
		title: 'Información',
		text: 'Seleccione una fila para Eliminar',
		showConfirmButton: true,
		type: 'warning'
	});
}

function aprobAlert(){
	swal({
		title: 'Información',
		text: 'no hay ningun proveedor para aprobacion de datos',
		showConfirmButton: true,
		type: 'warning'
	});
}


function loadingAlert(){
	swal({
		title: 'Cargando',
		text: '<svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="spinnerMaterialDesign" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg>',
		html: true,
		showConfirmButton: false,
		type: 'info'
	});
}

function savingAlert(){
	swal({
		title: 'Guardando...',
		text: 'Guardando, por favor espere...<br><svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="spinnerMaterialDesign" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg>',
		html: true,
		type: 'info',
		showConfirmButton: false
	});
}

function savedAlert(){
	swal({
		title:'Guardado!!',
		text: 'Registro Guardado con Exito!!',
		type: 'success',
		timer: 2500,
		showConfirmButton: false
	});
}


function errorAlert(){
	swal({
		title:'Error',
		text: 'Ocurrio un error durante la Operación',
		type: 'error',
		showConfirmButton: true
	});
}

function confirmCompountAlert(nombre,id,link){
	swal({
		title : 'Eliminar',
		text: '<p align="center">¿Confirma eliminar el Registro <strong>['+nombre+']</strong>?</p>',
		html: true,
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "Si, Eliminarlo",
		cancelButtonText: "No, Conservarlo",
		closeOnConfirm: false,
		closeOnCancel: false
	},
	function(isConfirm){
		if (isConfirm) {
			$.ajax({
				url: Routing.generate(link,{ id:id }),
				beforeSend:function(){
					swal({
						title:'Eliminando Registro',
						text:'Eliminando, por favor espere...<br><svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="spinnerMaterialDesign" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg>',
						showConfirmButton: false,
						type: 'info',
						html: true
					}); 
				},
				success: function(data){

					setTimeout(function()
					{
						if(data.status == 1){
							ReloadGrid();
							swal({
						        title: 'Registro Eliminado!!',
						        text: 'Registro Eliminado Con Exito',
						        html: true,
						        type : 'success',
						        timer: 1500,
						        showConfirmButton: false
						    });            
							return;
						}else{
							ConfirmMessageSwal('Error al Eliminar el Registro [<b>'+id+'</b>]','error');
						}
					},2500);
				},
				error: function(data)
				{
					ConfirmMessageSwal('Error en el servidor <br> el registro se está usando en otro lugar','error');
				}
			});    
		}else{
			swal({title:"Cancelado", 
				text:"<p align='center'>El Administrador no se Elimino</p>", 
				html: true,
				type: "error",
				timer: 2000,
				showConfirmButton: false
			});
		}      
	});		

	function ConfirmMessageSwal(message,type){
	    swal({
	        title: "Información",
	        text: message,
	        type: type,
	        html: true
	    });
	}

	function AlertaPersonalizable(title,mensaje, tiempo, tipo){
    return 
}

}


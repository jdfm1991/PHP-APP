var subselector = 'All';
var stamps = 'All';
var price = '1';
var existe = false;
var checkimage = false;

$('#optionbody').hide()
$('#substamp').height(250)


$(document).ready(function () {

    session = $.trim($('#session').val());
    if (session=='desactiva') {
        window.open('./');  
    }

    $('#substamp').click(function (e) { 
        e.preventDefault();

        stamps = $('#substamp').val();
        
    });

    $('#price').change(function (e) {
        e.preventDefault();

        price = $('#price').val();
    });

    $("#existe").click(function (e) {
        
        if ($('#existe').prop('checked')) {
            existe = $('#existe').prop('checked');
        } else {
            existe = $('#existe').prop('checked');
        }


    });


    $("#searchform").submit(function (e) { 
        e.preventDefault();

        $('#optionbody').show()
        //console.log(stamps+ price + existe)

        $('#btncatalogue').click(function (e) { 
            e.preventDefault();
            window.open('catalogue_controlador.php?op=catalogue&subselector='+ subselector +'&stamps='+ stamps +'&price='+ price +'&existe='+ existe +'', '_blank');  
            
        });

        $('#btnlist').click(function (e) { 
            e.preventDefault();
            window.open('catalogue_controlador.php?op=list&subselector='+ subselector +'&stamps='+ stamps +'&price='+ price +'&existe='+ existe +'', '_blank');  
            
        });
        //searchcontent(subselector,stamps,price,existe)
       // $("#subsidiary").val(subselector);

    });

    $("#checkimage").click(function (e) {
        
        if ($('#checkimage').prop('checked')) {
            checkimage = $('#checkimage').prop('checked');
        } else {
            checkimage = $('#checkimage').prop('checked');
        }
        getContenttable(checkimage)

    });

    $(document).on("click", ".BtnEditComm", function(){		        
        fila = $(this).closest("tr");	        		            
        idCommodity  = fila.find('td:eq(0)').text();
        descripCommodity = fila.find('td:eq(1)').text(); //capturo el ID

        

        $("#idCommodity").val(idCommodity);
        $("#descripCommodity").val(descripCommodity);
        $('.modal-content').css('background', 'rgb(238,240,38)');
        $('.modal-content').css('background', 'radial-gradient(circle, rgba(238,240,38,1) 0%, rgba(72,145,200,1) 100%)');
        $(".modal-title").text("Editar Informacion de Productos");		
        $('#CommodityModal').modal('show');	
    });

    $('#formCommodity').submit(function (e) { 
        e.preventDefault();

        idCommodity      = $.trim($('#idCommodity').val());
        descripCommodity = $.trim($('#descripCommodity').val());
        image = $("#image")[0].files[0];

        var datos = new FormData();

        datos.append('idCommodity', idCommodity)
        datos.append('descripCommodity', descripCommodity)
        datos.append('image', image)

        $.ajax({
            url: "catalogue_controlador.php?op=save",
            type: "POST",
            dataType:"json",    
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                SweetAlertLoadingShow();
            },
            error: function (e) {
                isError = SweetAlertError(e.responseText, "Error!")
                send_notification_error(e.responseText);
                console.log(e.responseText);
            }, 
            success: function (data) {
                let { icono, mensaje } = data
                ToastSweetMenssage(icono, mensaje);
    
                $('#formCommodity')[0].reset();
                $('#CommodityModal').modal('hide');
                $('#CommoTable').DataTable().ajax.reload();
                wipe();
            }

          });
        
    });


    

    
});


function substamps() {

    $.ajax({
        type: "POST",
        url: "catalogue_controlador.php?op=substamps",
        dataType: "json",
        success: function (data) {
            $('#substamp').append('<option value="All" selected>Todas las Marcas</option>');
            $.each(data, function(idx, opt) {
                $('#substamp').append('<option name="" value="' + opt.CodInst +'">' + opt.Descrip + '</option>');

            });
                       
        }
    });
    
}

function getContenttable(checkimage) {

    if (CommoTable) {

        $('#CommoTable').DataTable().destroy();

        CommoTable = $('#CommoTable').DataTable({  
            //"pageLength": 50,
            "ajax":{            
                "url": "catalogue_controlador.php?op=enlist", 
                "method": 'POST', //usamos el metodo POST
                "data":  {'checkimage':checkimage},
                "dataSrc":""
            },
            "columns":[
                {"data": "CodProd"},
                {"data": "Descrip"},
                {"data": "Refere"},
                {"data": "stamp"},
                {"data": "Existen"},
                {"data": "ImagenC",
                    "render":function(data,type,row) {
                        return '<center><img src="../../public/img/gallery/'+data+'" width="100px" height="100px"></center>'
                    }
                },
                {"defaultContent": "<div class='text-center'><div class='btn-group'><button class='btn btn-primary btn-sm BtnEditComm'>Subir Imagen</div></div>"}
    
            ],
    
        });

    } else {

        CommoTable = $('#CommoTable').DataTable({  
            //"pageLength": 50,
            "ajax":{            
                "url": "catalogue_controlador.php?op=enlist", 
                "method": 'POST', //usamos el metodo POST
                "data":  {'checkimage':checkimage},
                "dataSrc":""
            },
            "columns":[
                {"data": "CodProd"},
                {"data": "Descrip"},
                {"data": "Refere"},
                {"data": "stamp"},
                {"data": "Existen"},
                {"data": "ImagenC",
                    "render":function(data,type,row) {
                        return '<center><img src="../../public/img/gallery/'+data+'" width="100px" height="100px"></center>'
                    }
                },
                {"defaultContent": "<div class='text-center'><div class='btn-group'><button class='btn btn-primary btn-sm BtnEditComm'>Subir Imagen</div></div>"}
    
            ],
    
        });
        
    }

}

function wipe() {
    $("#idCommodity").val("");
    $('#descripCommodity').val("");
}



substamps()
getContenttable(checkimage)
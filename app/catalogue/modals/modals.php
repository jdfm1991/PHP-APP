
<div class="modal fade" id="CommodityModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-5" id="exampleModalLabel">Nueva Infomacion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="formCommodity">
                  <div class="modal-body">
                      <input type="hidden" class="form-control" id="idCommodity">
                      <div class="row">
                        <div class="col">
                            <label for="contenido" class="form-label">Nombre de Productos</label>
                            <input type="text" class="form-control" id="descripCommodity" required>  
                        </div>
                        <div class="col">
                          <label for="image" class="form-label">Imagen</label>
                          <input class="form-control" type="file" onkeyup="loaddds(1);" name="image" id="image"  accept="image/x-png,image/gif,image/jpeg">
                        </div>
                      
                      </div>                        
                  </div>
                    <br />
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-primary btn-light" value="Add">Guardar</button>
                        <button type="button" class="btn btn-outline-danger btn-light" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>




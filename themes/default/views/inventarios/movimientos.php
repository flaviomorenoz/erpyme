<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
    $cSql = "select a.id, a.fechah, a.persona, a.product_id, b.name, a.cantidad, a.store_id, c.state tienda, d.descrip tipo_mov, a.obs, ts.state tienda_destino, a.metodo metodo_id, mi.metodo, 
        if(a.confirmado = '0', 'Pendiente', 'Confirma') confirmado,
        concat('<a href=\"#\" onclick=\"eliminar(',a.id,')\"><span class=\"glyphicon glyphicon-remove iconos\"></span></a>') as op
        from tec_movim a 
        inner join tec_products b on a.product_id = b.id
        inner join tec_stores c on a.store_id = c.id
        inner join tec_tipos_mov d on a.tipo_mov = d.tipo_mov
        left join tec_stores ts on a.store_id_destino = ts.id
        left join tec_metodos_inv mi on a.metodo = mi.id
        order by id desc";
    $query = $this->db->query($cSql); 
    $cad = "";
    foreach($query->result() as $r){
        $cad .= "<tr>";
        $cad .= $this->fm->celda($r->id);
        $cad .= $this->fm->celda($this->fm->estilo_dt($r->fechah));
        $cad .= $this->fm->celda($r->persona);
        $cad .= $this->fm->celda($r->name);
        $cad .= $this->fm->celda($r->cantidad);
        $cad .= $this->fm->celda($r->tienda);
        $cad .= $this->fm->celda($r->tipo_mov);
        $cad .= $this->fm->celda($r->metodo);
        $cad .= $this->fm->celda($r->tienda_destino); // store_id_confirma
        $cad .= $this->fm->celda($r->confirmado);
        $cad .= $this->fm->celda($r->obs);
        if($_SESSION["group_id"]=='1'){
            $cad .= $this->fm->celda($r->op);
        }else{
            $cad .= $this->fm->celda("");
        }
        $cad .= "</tr>";
    }
    $contenido_result = $cad;
?>

<section class="content">

    <div class="row">
        <div class="col-xs-12 col-sm-10">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="SLData" class="table table-striped table-bordered table-condensed table-hover">
                            <thead>
                                <!-- TITULOS -->
                                <tr class="active" style="background-color:rgb(120,120,120)">
                                    <th class="col-xs-2 col-sm-1" style="max-width:30px;"><?= lang("id"); ?></th>
                                    <th class="col-xs-2 col-sm-2">Fecha</th>
                                    <th class="col-xs-2 col-sm-2">Persona</th>
                                    <th class="col-xs-2 col-sm-1">Producto</th>
                                    <th class="col-xs-2 col-sm-1">Cantidad</th>
                                    <th class="col-xs-2 col-sm-1" style="color:rgb(220,0,0)">Tienda<br>Origen</th>
                                    <th class="col-xs-2 col-sm-1">Tipo</th>
                                    <th class="col-xs-2 col-sm-1">M&eacute;todo</th>
                                    <th class="col-xs-2 col-sm-1" style="color:rgb(220,0,0)">Tienda<br>Destino</th>
                                    <th class="col-xs-2 col-sm-1">Confirma</th>
                                    <th class="col-xs-2 col-sm-2">Obs</th>
                                    <th class="col-xs-2 col-sm-1">-</th>
                                </tr>

                            </thead>
                            <tbody>
                                <?php echo $contenido_result; ?>
                           </tbody>
                           <tfoot>
                        </tfoot>
                    </table>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
</section>

<?php if ($Admin) { ?>
<div class="modal fade" id="stModal" tabindex="-1" role="dialog" aria-labelledby="stModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
                <h4 class="modal-title" id="stModalLabel"><?= lang('update_status'); ?> <span id="status-id"></span></h4>
            </div>
            <?= form_open('sales/status'); ?>
            <div class="modal-body">
                <input type="hidden" value="" id="sale_id" name="sale_id" />
                <div class="form-group">
                    <?= lang('status', 'status'); ?>
                    <?php $opts = array('paid' => lang('paid'), 'partial' => lang('partial'), 'due' => lang('due'))  ?>
                    <?= form_dropdown('status', $opts, set_value('status'), 'class="form-control select2 tip" id="status" required="required" style="width:100%;"'); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?php } ?>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/moment.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
    function eliminar(id){
        if(confirm("Desea eliminar el movimiento?")){
            var parametros = {
                id : id,
            }
            $.ajax({
                data    : parametros,
                url     :'<?= base_url('inventarios/eliminar_movimiento') ?>',
                type    :'get',
                success :function(response){
                    let ar = JSON.parse(response)
                    console.log(ar)
                    if(ar["rpta"] == "success"){
                        alert(ar["msg"])
                    }
                    location.reload()
                }
            })
        }
    }
</script>

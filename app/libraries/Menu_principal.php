<?php
class Menu_principal{
    function menu_principal2($Admin, $store_id, $multi_store, $ar_mod){ 
   	
    	/*if ($Admin){ 
            echo "<li>";
            echo "<a href=\"". site_url() ."\" id=\"h0\">";
            echo "	<i class=\"fa fa-dashboard\"></i> <span>" . lang('dashboard') . "</span>";
            echo "</a>";
            echo "</li>";
        }*/
?>
        <!---- CAJA ---->
        <li>
            <a href="#" data-toggle="collapse" data-target="#ul1" id="h1">
                <i class="fa fa-th"></i>
                <span><?= lang('pos'); ?></span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            
            <ul id="ul1" class="treeview-menu">
                <?php
                    if($this->permisos($_SESSION["username"], "CAJA", $ar_mod) != 'DENEGADO'){
                        //die("Rihana");
                        echo $this->menu_opcion(site_url('pos'), "Caja");
                    }
                ?>  
                <li id="salidas_por_dia" class="itom">
                    <a href="<?= site_url('reports/salidas_por_dia'); ?>">
                    <i class="fa fa-circle-o"></i>&nbsp;Cuadre de Caja</a>
                </li>

                <?php if ($Admin){ 
                    if( $this->permisos( $_SESSION["username"], "CAJA", $ar_mod ) != 'DENEGADO' ){
                        echo $this->menu_opcion(site_url('cajas/add'), "Editar Saldo Inicial");
                    }
                } ?>
            </ul>                        
        </li>

        <!-- EGRESOS -->
        <li data-toggle="collapse" data-target="#ul6">
            <a href="#" id="h6">
                <i class="fa fa-plus"></i>
                <span>Compras</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul id="ul6" class="treeview-menu">
                <?php
                    if($this->permisos($_SESSION["username"], "COMPRAS", $ar_mod) != 'DENEGADO'){
                        echo $this->menu_opcion(site_url('purchases/add'), "Agregar Compras");
                    }
                ?>  
                <li id="purchases_index" class="itom"><a href="<?= site_url('purchases'); ?>" onclick="vaceando_vars(<?= $store_id ?>)"><i class="fa fa-circle-o"></i> Listar Compras</a></li>

            </ul>
        </li>

        <!---- PRODUCTOS (CATEGORIA DE PRODUCTOS, PRODUCTOS, INSUMOS, RECETAS) ------>
        <li data-toggle="collapse" data-target="#ul22">
            <a href="#" id="h22">
                <i class="fa fa-barcode"></i>
                <span>Productos</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul id="ul22" class="treeview-menu">
                <!--<li>
                    <a href="#" data-toggle="collapse" data-target="#ul22">-->
                        <!--<i class="fa fa-folder"></i>
                        <span>Categor&iacute;a Productos</span>-->
                        <?php echo $this->menu_opcion_p(site_url('categories/add'), "Categoria Productos"); ?>
                        <!--<i class="fa fa-angle-left pull-right"></i>-->
                    <!--</a>-->
                    <!--<ul id="ul22a" class="treeview-menu">-->
                        <?php echo $this->menu_opcion(site_url('categories/add'), lang('add_category') ); ?>  
                        <?php echo $this->menu_opcion(site_url('categories'), lang('list_categories') ); ?>
                        <?php echo $this->menu_opcion(site_url('categories/import'), lang('import_categories') ); ?>
                    <!--</ul>-->
                    <!--<a href="#" id="h23b">
                        <i class="fa fa-folder"></i>
                        <span>Productos</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>-->
                    <!--<ul id="ul23b" class="treeview-menu">-->
                        <?php echo $this->menu_opcion_p(site_url('products/agregar'), "Productos"); ?>

                        <?php echo $this->menu_opcion(site_url('products/agregar'), 'Agregar' ); ?>  
                        <?php echo $this->menu_opcion(site_url('products'), 'Listar Productos' ); ?>
                        <?php echo $this->menu_opcion(site_url('products/import'), 'Importar Productos' ); ?>
                    <!--</ul>
                </li>-->
            </ul>
        </li>

        <li data-toggle="collapse" data-target="#ul_insumos">
            <a href="#">
                <i class="fa fa-folder"></i>
                <span><?= lang('Insumos'); ?></span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul id="ul_insumos" class="treeview-menu">
                <?php echo $this->menu_opcion(site_url('insumos/agregar_insumos_'), lang('Agregar') ); ?>  
                <?php echo $this->menu_opcion(site_url('insumos/listar_insumos'), lang('Listado') ); ?>
            </ul>
        </li>

        <li data-toggle="collapse" data-target="#ul_recetas">
            <a href="#">
                <i class="fa fa-folder"></i>
                <span><?= lang('recipe'); ?></span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul id="ul_recetas" class="treeview-menu">
                <?php echo $this->menu_opcion(site_url('receta/agregar'), 'Agregar' ); ?>  
                <?php echo $this->menu_opcion(site_url('receta/rep_recetas'), 'Listado' ); ?>
            </ul>
        </li>

        <!-- INVENTARIO -->
        <li data-toggle="collapse" data-target="#ul7">
            <a href="#" id="h7">
                <i class="fa fa-plus"></i>
                <span>Inventario</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul id="ul7" class="treeview-menu">
                <?php
                    if($this->permisos($_SESSION["username"], "INVENTARIO", $ar_mod) != 'DENEGADO'){
                        echo $this->menu_opcion(site_url('inventarios/ajustes'), "Stock Actual");
                    }
                ?>
                <li id="" class="itom">
                    <a href="<?= site_url('inventarios/nuevo_inventario'); ?>">
                        <i class="fa fa-circle-o"></i> Crear Inventario
                    </a>
                </li>
                <li id="" class="itom">
                    <a href="<?= site_url('inventarios/lista_inventarios'); ?>" onclick="vaceando_vars(<?= $store_id ?>)">
                        <i class="fa fa-circle-o"></i> Listar Inventarios
                    </a>
                </li>
                <!--<li id="" class="itom">
                    <a href="<?= site_url('inventarios/add'); ?>">
                        <i class="fa fa-circle-o"></i> Agregar Registros
                    </a>
                </li>-->
                <li id="" class="itom">
                    <a href="<?= site_url('inventarios/movimientos'); ?>">
                        <i class="fa fa-circle-o"></i> Movimientos 
                    </a>
                </li>
                <?php
                    if($this->permisos($_SESSION["username"], "INVENTARIO", $ar_mod) != 'DENEGADO'){
                        echo $this->menu_opcion(site_url('inventarios/add_movimientos'), "Agregar Movimientos");
                    }
                    
                ?>  
                <!--<li id="" class="itom">
                    <a href="<?= site_url('inventarios/add_pases'); ?>">
                        <i class="fa fa-circle-o"></i> Pases
                    </a>
                </li>-->

            </ul>
        </li>
        
        <!-- PLANILLAS -->
        <li data-toggle="collapse" data-target="#ul22">
            <a href="#" id="h22">
                <i class="fa fa-bar-chart-o"></i>
                <span>Planillas</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul id="ul22" class="treeview-menu">
                <?php
                    if($this->permisos($_SESSION["username"], "PLANILLAS", $ar_mod) != 'DENEGADO'){
                        echo $this->menu_opcion(site_url('recursos/login'), 'Login');
                    }
                ?>  
            </ul>
        </li>

            <style type="text/css">
                .iconos{ font-size:18px;}
            </style>

                
            <!-- REPORTES -->
            <li data-toggle="collapse" data-target="#ul12">
                <a href="#" id="h12">
                    <i class="fa fa-bar-chart-o"></i>
                    <span><?= lang('reports'); ?></span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul id="ul12" class="treeview-menu">
                    <?php echo $this->menu_opcion_p(site_url('sales'), "De Ventas"); ?>
                        
                    <?php echo $this->menu_opcion(site_url('sales'), "Reporte de Ventas"); ?>
                    <?php echo $this->menu_opcion(site_url('reports/ventas_por_dia'), 'Ventas por dia' ); ?>
                    <?php echo $this->menu_opcion(site_url('reports/salidas_por_dia'), 'Reporte de Ventas' ); ?>
                    <?php //echo $this->menu_opcion(site_url('reports/acumulado'), 'Vtas Acumulada x d&iacute;a' ); ?>
                    <?php //echo $this->menu_opcion(site_url('sales/'), 'Vtas Acumulada x d&iacute;a' ); ?>

                    <li class="itom">
                        <a href="<?= site_url("sales/vtas_platos_hora") ?>"><i class="fa fa-circle-o"></i> Vtas de Platos xHora</a>
                    </li>

                    <?php echo $this->menu_opcion(site_url('sales/platos_diarios_canales'), 'Vtas Platos Diarios x Canal' ); ?>

                    <?php echo $this->menu_opcion_p(site_url('reports/reporte_a_sunat'), 'Otros' ); ?>

                    <li id="" class="itom">
                        <a href="<?= site_url('reports/reporte_a_sunat'); ?>">
                        <i class="fa fa-circle-o"></i> Reporte a Sunat</a>
                    </li>

                    <li id="analisis_compras" class="itom">
                        <a href="<?= site_url('reports/analisis_compras'); ?>">
                        <i class="fa fa-circle-o"></i>&nbsp;Egresos x Producto</a>
                    </li>

                    <li id="analisis_compras" class="itom">
                        <a href="<?= site_url('reports/especial_productos'); ?>">
                        <i class="fa fa-circle-o"></i>&nbsp;Compras por Insumo</a>
                    </li>


                    

                </ul>
            </li>

            <!-- CLIENTES -->
            <li data-toggle="collapse" data-target="#ul9a">
                <a href="#" id="h9a">
                    <i class="fa fa-users"></i>
                    <span>Clientes</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul id="ul9a" class="treeview-menu">
                    <li id="customers_add" class="itom"><a href="<?= site_url('customers/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_customer'); ?></a></li>
                    <li id="customers_index" class="itom"><a href="<?= site_url('customers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_customers'); ?></a></li>
                </ul>
            </li>

            <!-- PROVEEDOR -->
            <li data-toggle="collapse" data-target="#ul10">
                <a href="#" id="h10">
                    <i class="fa fa-users"></i>
                    <span><?= lang('suppliers'); ?></span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul id="ul10" class="treeview-menu">
                    <li id="suppliers_add" class="itom"><a href="<?= site_url('suppliers/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_supplier'); ?></a></li>
                    <li id="suppliers_index" class="itom"><a href="<?= site_url('suppliers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_suppliers'); ?></a></li>
                </ul>
            </li>

        <?php if ($Admin){ ?>
            <!-- ACCESOS --->
            <li data-toggle="collapse" data-target="#ul_acceso">
                <a href="#" id="h2">
                    <i class="fa fa-barcode"></i>
                    <span>Accesos</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>

                <ul id="ul_acceso" class="treeview-menu" style="padding-left:0px;">

                    <li data-toggle="collapse" data-target="#ul_usuarios">
                        <?php
                            if($this->permisos($_SESSION["username"], "USUARIOS", $ar_mod) != 'DENEGADO'){
                        ?>
                            <a href="#" id="h9">
                                <i class="fa fa-users"></i>
                                <span>Usuarios</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>

                            <ul id="ul_usuarios" style="padding-left:30px;">
                                <li id="auth_add" class="itom"><a href="<?= site_url('users/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_user'); ?></a></li>
                                <li id="auth_users" class="itom"><a href="<?= site_url('users'); ?>"><i class="fa fa-circle-o"></i> Listar Usuarios</a></li>
                            </ul>
                        <?php } ?>
                    </li>
                
                    <li data-toggle="collapse" data-target="#ul_modulos">
                        <?php
                            if($this->permisos($_SESSION["username"], "MODULOS", $ar_mod) != 'DENEGADO'){
                        ?>
                            <a href="#" id="h9">
                                <i class="fa fa-users"></i>
                                <span>Módulos</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>

                            <ul id="ul_modulos" style="padding-left:30px;">
                                <li id="auth_users" class="itom"><a href="<?= site_url('modulos'); ?>"><i class="fa fa-circle-o"></i> Listar Módulos</a></li>
                                <!--<li id="auth_add" class="itom"><a href="<?= site_url('modulos/add'); ?>"><i class="fa fa-circle-o"></i> Agregar Módulos</a></li>-->
                            </ul>
                        <?php } ?>
                    </li>

                    <li data-toggle="collapse" data-target="#ul_modulos">
                        <?php
                            if($this->permisos($_SESSION["username"], "MODULOS", $ar_mod) != 'DENEGADO'){
                        ?>
                            <a href="#" id="h9">
                                <i class="fa fa-users"></i>
                                <span>Permisos Usuarios</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>

                            <ul id="ul_modulos" style="padding-left:30px;">
                                <li id="auth_users" class="itom"><a href="<?= site_url('permisos/index'); ?>"><i class="fa fa-circle-o"></i> Permisos x Perfil</a></li>
                            </ul>
                        <?php } ?>
                    </li>

                </ul>
            </li>

            <!-- SETTINGS -->
            <li data-toggle="collapse" data-target="#ul11">
                <a href="#" id="h11">
                    <i class="fa fa-cogs"></i>
                    <span><?= lang('settings'); ?></span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul id="ul11" class="treeview-menu">
                    <li id="settings_index" class="itom"><a href="<?= site_url('settings'); ?>"><i class="fa fa-circle-o"></i> <?= lang('settings'); ?></a></li>
                    <li class="divider"></li>
                <?php if ($multi_store) { ?>
                    <li id="settings_add_store" class="itom"><a href="<?= site_url('settings/add_store'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_store'); ?></a></li>
                <?php } ?>
                    <li id="settings_stores" class="itom"><a href="<?= site_url('settings/stores'); ?>"><i class="fa fa-circle-o"></i> <?= lang('stores'); ?></a></li>
                    <li class="divider"></li>
                    <li id="settings_add_printer" class="itom"><a href="<?= site_url('settings/add_printer'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_printer'); ?></a></li>
                    <li id="settings_printers" class="itom"><a href="<?= site_url('settings/printers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('printers'); ?></a></li>
                    
                    <li>
                        <a href="<?= site_url('deliverys'); ?>">
                            <i class="fa fa-circle-o"></i>
                            Deliverys
                        </a>
                    </li>

                    <!-- <li class="divider"></li> -->
                <?php if ($this->db->dbdriver != 'sqlite3') { ?>
                    <!-- <li id="settings_backups"><a href="<?= site_url('settings/backups'); ?>"><i class="fa fa-circle-o"></i> <?= lang('backups'); ?></a></li> -->
                <?php } ?>
                    <!-- <li id="settings_updates"><a href="<?= site_url('settings/updates'); ?>"><i class="fa fa-circle-o"></i> <?= lang('updates'); ?></a></li> -->
                </ul>
            </li>
<?php 
        }       
    } // FIN DE LA FUNCION 

    function permisos($usuario, $modulo, $ar_mod){
        
        //echo "Usuario: $usuario|<br>";
        //echo "modulo: $modulo|<br>";
        //echo "Usuario: $usuario<br>";
        //die();
        for($nx=0; $nx<count($ar_mod); $nx++){
            if($ar_mod[$nx]["modulo"] == $modulo){
                //die("Ingreso aqui?");
                //die($ar_mod[$nx]["permiso"]);
                return $ar_mod[$nx]["permiso"];
            }
        }
        return "";
    }

    function menu_opcion($url, $show){
        $cad = "";
        $cad .= "<li class=\"itom\">";
        $cad .=  "      <a href=\"" . $url . "\">";
        $cad .=  "          <i class='fa fa-circle-o'></i> " . $show;
        $cad .=  "      </a>";
        $cad .=  "</li>";
        return $cad;
    }    

    function menu_opcion_p($url, $show){ // Simplemente lo utilizas como titulo
        $cad = "";
        $cad .= "<li class=\"itom\">";
        $cad .=  "      <a href=\"" . $url . "\" style=\"font-weight:bold;\">";
        $cad .=  $show;
        $cad .=  "      </a>";
        $cad .=  "</li>";
        return $cad;
    }    

} // FIN DE LA CLASE
?>
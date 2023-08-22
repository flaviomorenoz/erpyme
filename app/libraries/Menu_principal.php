<?php
class Menu_principal{
    function menu_principal2($Admin, $store_id, $multi_store, $ar_mod){ 
   	
    	if ($Admin){ 
            echo "<li>";
            echo "<a href=\"". site_url() ."\" id=\"h0\">";
            echo "	<i class=\"fa fa-dashboard\"></i> <span>" . lang('dashboard') . "</span>";
            echo "</a>";
            echo "</li>";
        }
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

        <?php       
        if ($Admin){ ?>

            <!-- VENTAS -->
            <li data-toggle="collapse" data-target="#ul5">
                <a href="#" id="h5">
                    <i class="fa fa-shopping-cart"></i>
                    <span>Ventas</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul id="ul5" class="treeview-menu">
                <?php
                    if($this->permisos($_SESSION["username"], "SALES", $ar_mod) != 'DENEGADO'){
                        echo $this->menu_opcion(site_url('sales'), lang('list_sales'));
                    }
                ?>  
                    <!--<li id="sales_index" class="itom"><a href="<?= site_url('sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_sales'); ?></a></li>-->
                    <!--<li id="sales_opened" class="itom"><a href="<?= site_url('sales/opened'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_opened_bills'); ?></a></li>-->

                </ul>
            </li>

            <!-- EGRESOS -->
            <li data-toggle="collapse" data-target="#ul6">
                <a href="#" id="h6">
                    <i class="fa fa-plus"></i>
                    <span>Egresos</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul id="ul6" class="treeview-menu">
                    <?php
                        if($this->permisos($_SESSION["username"], "COMPRAS", $ar_mod) != 'DENEGADO'){
                            echo $this->menu_opcion(site_url('purchases/add'), "Agregar Egreso");
                        }
                    ?>  
                    <li id="purchases_index" class="itom"><a href="<?= site_url('purchases'); ?>" onclick="vaceando_vars(<?= $store_id ?>)"><i class="fa fa-circle-o"></i> Listar Egresos</a></li>

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

            <!---- PRODUCTOS (CATEGORIA DE PRODUCTOS, PRODUCTOS, INSUMOS, RECETAS) ------>
            <li data-toggle="collapse" data-target="#ul22">
                <a href="#" id="h22">
                    <i class="fa fa-barcode"></i>
                    <span>Productos</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul id="ul22" class="treeview-menu">

                    <li data-toggle="collapse" data-target="#ul22a">
                        <a href="#" id="h22a">
                            <i class="fa fa-folder"></i>
                            <span>Categor&iacute;a Productos</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul id="ul22a" class="treeview-menu">
                            <?php echo $this->menu_opcion(site_url('categories/add'), lang('add_category') ); ?>  
                            <?php echo $this->menu_opcion(site_url('categories'), lang('list_categories') ); ?>
                            <?php echo $this->menu_opcion(site_url('categories/import'), lang('import_categories') ); ?>
                        </ul>
                    </li>
                    <li data-toggle="collapse" data-target="#ul23b">
                        <a href="#" id="h23b">
                            <i class="fa fa-folder"></i>
                            <span>Productos</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul id="ul23b" class="treeview-menu">
                            <?php /*echo $this->menu_opcion(site_url('products/add'), 'Agregar Productos' );*/ ?>  
                            <?php echo $this->menu_opcion(site_url('products/agregar'), 'Agregar' ); ?>  
                            <?php echo $this->menu_opcion(site_url('products'), 'Listar Productos' ); ?>
                            <?php echo $this->menu_opcion(site_url('products/import'), 'Importar Productos' ); ?>
                        </ul>
                    </li>
                    <li data-toggle="collapse" data-target="#jl24b">
                        <a href="#" id="j24b">
                            <i class="fa fa-folder"></i>
                            <span><?= lang('Insumos'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul id="jl24b" class="treeview-menu">
                            <?php echo $this->menu_opcion(site_url('insumos/agregar_insumos_'), lang('Agregar') ); ?>  
                            <?php echo $this->menu_opcion(site_url('insumos/listar_insumos'), lang('Listado') ); ?>
                        </ul>
                    </li>
                    <li data-toggle="collapse" data-target="#ul24b">
                        <a href="#" id="h24b">
                            <i class="fa fa-folder"></i>
                            <span><?= lang('recipe'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul id="ul24b" class="treeview-menu">
                            <?php echo $this->menu_opcion(site_url('receta/agregar'), 'Agregar' ); ?>  
                            <?php echo $this->menu_opcion(site_url('receta/rep_recetas'), 'Listado' ); ?>
                        </ul>
                    </li>
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
            
            <style type="text/css">
                .iconos{ font-size:18px;}
            </style>

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
                
            <!-- REPORTES -->
            <li data-toggle="collapse" data-target="#ul12">
                <a href="#" id="h12">
                    <i class="fa fa-bar-chart-o"></i>
                    <span><?= lang('reports'); ?></span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul id="ul12" class="treeview-menu">
                    <!--<li id="reports_daily_sales" class="itom">
                        <a href="<?= site_url('reports/daily_sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('daily_sales'); ?></a>
                    </li>
                    <li id="reports_monthly_sales" class="itom">
                        <a href="<?= site_url('reports/monthly_sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('monthly_sales'); ?></a>
                    </li>
                    
                    <li id="reports_index" class="itom"><a href="<?= site_url('reports'); ?>"><i class="fa fa-circle-o"></i> <?= lang('sales_report'); ?></a></li>
                    
                    <li id="reports_payments" class="itom"><a href="<?= site_url('reports/payments'); ?>"><i class="fa fa-circle-o"></i><?= lang('payments_report'); ?></a></li>
                    
                    <li id="reports_registers" class="itom"><a href="<?= site_url('reports/registers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('registers_report'); ?></a></li>
                    
                    <li id="reports_top_products" class="itom"><a href="<?= site_url('reports/top_products'); ?>"><i class="fa fa-circle-o"></i> <?= lang('top_products'); ?></a></li>
                    -->
                    <li id="ventas_por_dia" class="itom">
                        <a href="<?= site_url('reports/ventas_por_dia'); ?>">
                        <i class="fa fa-circle-o"></i> Ventas por día</a>
                    </li>
                
                    <li id="salidas_por_dia" class="itom">
                        <a href="<?= site_url('reports/salidas_por_dia'); ?>">
                        <i class="fa fa-circle-o"></i> Reporte de Ventas</a>
                    </li>

                    <!--<li id="salidas_por_mes" class="itom">
                        <a href="<?= site_url('reports/salidas_por_mes'); ?>">
                        <i class="fa fa-circle-o"></i>Resumen Mensual de Caja</a>
                    </li>-->

                    <li id="sales_acumulado" class="itom">
                        <a href="<?= site_url("sales/acumulado") ?>"><i class="fa fa-circle-o"></i> Vtas Acumulada x d&iacute;a</a>
                    </li>

                    <li class="itom">
                        <a href="<?= site_url("sales/vtas_platos_hora") ?>"><i class="fa fa-circle-o"></i> Vtas de Platos xHora</a>
                    </li>

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

        <?php }else{ ?>

            <!-- VENTAS -->
            <li data-toggle="collapse" data-target="#ul5">
                <a href="#" id="h5">
                    <i class="fa fa-shopping-cart"></i>
                    <span>Ventas</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul id="ul5" class="treeview-menu">
                    <?php
                    if($this->permisos($_SESSION["username"], "SALES", $ar_mod) != 'DENEGADO'){
                        echo $this->menu_opcion(site_url('sales'), lang('list_sales'));
                    }
                    ?>  
                </ul>
            </li>

            <!-- EGRESOS -->
            <li data-toggle="collapse" data-target="#ul6">
                <a href="#" id="h6">
                    <i class="fa fa-plus"></i>
                    <span>Egresos</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul id="ul6" class="treeview-menu">
                    <?php
                        if($this->permisos($_SESSION["username"], "COMPRAS", $ar_mod) != 'DENEGADO'){
                            echo $this->menu_opcion(site_url('purchases/add'), "Agregar Egreso");
                        }
                    ?>  

                    <li id="purchases_index" class="itom"><a href="<?= site_url('purchases'); ?>" onclick="vaceando_vars(<?= $store_id ?>)"><i class="fa fa-circle-o"></i> Listar Egresos</a></li>

                    <li class="itom">
                        <a href="<?= site_url('recursos/login'); ?>"><i class="fa fa-circle-o"></i> Planilla y Creditos</a>
                    </li>
                    <li id="analisis_compras" class="itom">
                        <a href="<?= site_url('reports/analisis_compras'); ?>">
                        <i class="fa fa-circle-o"></i>&nbsp;Analisis de Egresos</a>
                    </li>
                </ul>
            </li>

            <!---- PRODUCTOS (CATEGORIA DE PRODUCTOS, PRODUCTOS, INSUMOS, RECETAS) ------>
            <li data-toggle="collapse" data-target="#ul2">
                <a href="#" id="h2">
                    <i class="fa fa-barcode"></i>
                    <span>Productos</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                

                <ul id="ul2" class="treeview-menu">

                    <!--- CATEGORIES -------->
                    <li class="itom">
                        <a href="#" id="h3">
                            <i class="fa fa-folder"></i>
                            <span>Categor&iacute;a Productos</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul id="ul3" style="padding: 5px 5px 5px 30px;list-style: none;">
                            <?php
                                if($this->permisos($_SESSION["username"], "PRODUCTOS", $ar_mod) != 'DENEGADO'){
                                    echo $this->menu_opcion(site_url('categories/add'), lang('add_category'));
                                }
                            ?>
                            <li id="categories_index"><a href="<?= site_url('categories'); ?>" onMouseover="this.style.color='white'" onMouseout="this.style.color='rgb(138,164,175)';"><i class="fa fa-circle-o"></i> <?= lang('list_categories'); ?></a></li>
                            <li id="categories_import"><a href="<?= site_url('categories/import'); ?>" onMouseover="this.style.color='white'" onMouseout="this.style.color='rgb(138,164,175)';"><i class="fa fa-circle-o"></i> <?= lang('import_categories'); ?></a></li>
                        </ul>
                    </li>

                    <li class="divider"></li>

                    <!--- PRODUCTS ---------->
                    <li class="itom">

                        <a href="#" id="h_producto">
                            <i class="fa fa-folder"></i>
                            <span>Productos</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul id="ul_producto" style="padding: 5px 5px 5px 30px;list-style: none;">
                            <?php
                                if($this->permisos($_SESSION["username"], "PRODUCTOS", $ar_mod) != 'DENEGADO'){
                                    echo $this->menu_opcion(site_url('products/add'), "Agregar Producto");
                                }
                            ?>
                            <li id="products_index"><a href="<?= site_url('products'); ?>" onMouseover="this.style.color='white'" onMouseout="this.style.color='rgb(138,164,175)';"><i class="fa fa-circle-o"></i> Listar Producto</a></li>
                            <?php
                                if($this->permisos($_SESSION["username"], "PRODUCTOS", $ar_mod) != 'DENEGADO'){
                                    echo $this->menu_opcion(site_url('products/import'), "Importar Productos");
                                }
                            ?>
                            <li id="products_print_barcodes">
                                <a href="<?= site_url('products/print_inicial'); ?>" onMouseover="this.style.color='white'" onMouseout="this.style.color='rgb(138,164,175)';"><i class="fa fa-circle-o"></i> <?= lang('print_barcodes'); ?></a> <!-- print_barcodes -->
                            </li>
                            <!--<li id="products_print_labels">
                                <a href="<?= site_url('products/print_labels'); ?>" data-toggle="ajax"><i class="fa fa-circle-o"></i> <?= lang('print_labels'); ?></a>
                            </li>-->
                        </ul>

                    </li>

                    <li class="divider"></li>

                    <!-- INSUMOS -->
                    <li data-toggle="collapse" data-target="#ul4">
                        <a href="#" id="h4">
                            <i class="fa fa-folder"></i>
                            <span><?= lang('Insumos'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul id="ul4" style="padding: 5px 5px 5px 30px;list-style: none;">
                            <?php
                                if($this->permisos($_SESSION["username"], "PRODUCTOS", $ar_mod) != 'DENEGADO'){
                                    echo $this->menu_opcion(site_url('insumos/agregar_insumos_'), lang('Agregar'));
                                }
                            ?>
                            <li id="sales_index"><a href="<?= site_url('insumos/listar_insumos') ?>" onMouseover="this.style.color='white'" onMouseout="this.style.color='rgb(138,164,175)';">
                                <i class="fa fa-circle-o"></i>
                                <?= lang('Listado'); ?></a>
                            </li>
                        </ul>
                    </li>

                    <li class="divider"></li>

                    <!-- RECETAS -->
                    <li data-toggle="collapse" data-target="#ul8">
                        <a href="#" id="h8">
                            <i class="fa fa-shopping-cart"></i>
                            <span><?= lang('recipe'); ?></span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul id="ul8" style="padding: 5px 5px 5px 30px;list-style: none;">
                           <?php
                                if($this->permisos($_SESSION["username"], "PRODUCTOS", $ar_mod) != 'DENEGADO'){
                                    echo $this->menu_opcion(site_url('receta/agregar'), lang('Agregar'));
                                }
                            ?>                            
                            <li>
                                <a href="<?= site_url('receta/rep_recetas'); ?>" onMouseover="this.style.color='white'" onMouseout="this.style.color='rgb(138,164,175)';">
                                    <i class="fa fa-circle-o"></i>
                                    Listar
                                </a>
                            </li>
                        </ul>   
                    </li>

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
                    <?php
                        if($this->permisos($_SESSION["username"], "INVENTARIO", $ar_mod) != 'DENEGADO'){
                            echo $this->menu_opcion(site_url('inventarios/nuevo_inventario'), "Crear Inventario");
                        }
                    ?>                            
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


        <?php } // FIN DEL ELSE ADMIN ?>

<?php        
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

} // FIN DE LA CLASE
?>
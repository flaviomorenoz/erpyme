<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
    //include_once BASEPATH."app/helpers/menu_principal.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $page_title.' | '.$Settings->site_name; ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="<?= $assets ?>dist/css/styles.css" rel="stylesheet" type="text/css" />
    <?= $Settings->rtl ? '<link href="'.$assets.'dist/css/rtl.css" rel="stylesheet" />' : ''; ?>
    
    <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
    
    <script type="text/javascript" src="<?= $assets ?>toastr-master/toastr.js"></script>
    
    <link href="<?= $assets ?>toastr-master/build/toastr.css" rel="stylesheet"/>
</head>
<body class="skin-<?= $Settings->theme_style; ?> fixed sidebar-mini">
<div class="wrapper rtl rtl-inv">
    <header class="main-header">
        <a href="<?= site_url(); ?>" class="logo">
            <?php if ($store) { ?>
            <span class="logo-mini">POS</span>
            <span class="logo-lg"><?= $store->name == 'SIPOS' ? 'SI<b>POS</b>' : '<img src="'.base_url('uploads/'.$Settings->logo).'" alt="'.$Settings->name.'" />' ?></span>
            <?php } else { ?>
            <span class="logo-mini">POS</span>
            <span class="logo-lg"><?= $Settings->site_name == 'SIPOS' ? 'SI<b>POS</b>' : '<img src="'.base_url('uploads/'.$Settings->logo).'" alt="'.$Settings->site_name.'" />' ?></span>
            <?php } ?>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
           <style type="text/css">
               .oranges{ background-color:red }
           </style>
           <ul class="nav navbar-nav pull-left oranges">
                <!-- <li class="dropdown hidden-xs">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?= $assets; ?>images/<?= $Settings->selected_language; ?>.png" alt="<?= $Settings->selected_language; ?>"></a>
                    <ul class="dropdown-menu">
                        <?php $scanned_lang_dir = array_map(function ($path) {
                            return basename($path);
                        }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
                        foreach ($scanned_lang_dir as $entry) { ?>
                            <li><a href="<?= site_url('pos/language/' . $entry); ?>"><img
                                        src="<?= $assets; ?>images/<?= $entry; ?>.png"
                                        class="language-img"> &nbsp;&nbsp;<?= ucwords($entry); ?></a></li>
                        <?php } ?>
                    </ul>
                </li> -->
                <?php if ($Settings->multi_store && !$this->session->userdata('has_store_id') && $this->session->userdata('store_id')) { ?>
                <li>
                    <a href="<?= site_url('stores/deselect_store'); ?>" data-toggle="tooltip" data-placement="right" 
                        title="<?= lang('deselect_store'); ?>"><i class="fa fa-square"></i>
                    </a>
                </li>
                <?php } ?>
            </ul>
            
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="hidden-xs hidden-sm"><a href="#" class="clock"></a></li>
                    <!-- <li class="hidden-xs"><a href="<?= site_url(); ?>" data-toggle="tooltip" data-placement="bottom" title="<?= lang('dashboard'); ?>"><i class="fa fa-dashboard"></i></a></li> -->
                    <?php if ($Admin) { ?>
                    <!-- <li class="hidden-xs"><a href="<?= site_url('settings'); ?>" data-toggle="tooltip" data-placement="bottom" title="<?= lang('settings'); ?>"><i class="fa fa-cogs"></i></a></li> -->
                    <?php } ?>
                    <?php if ($this->db->dbdriver != 'sqlite3') { ?>
                    <!--<li><a href="<?= site_url('pos/view_bill'); ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="<?= lang('view_bill'); ?>"><i class="fa fa-desktop"></i></a></li>-->
                    <?php } ?>
                    <li><a href="<?= site_url('pos'); ?>" data-toggle="tooltip" data-placement="bottom" title="<?= lang('pos'); ?>"><i class="fa fa-th"></i></a></li>
                    <?php if ($Admin && $qty_alert_num && $this->session->userdata('store_id')) { ?>
                    <!--<li>
                        <a href="<?= site_url('reports/alerts'); ?>" data-toggle="tooltip" data-placement="bottom" title="<?= lang('alerts'); ?>">
                            <i class="fa fa-bullhorn"></i>
                            <span class="label label-warning"><?= $qty_alert_num; ?></span>
                        </a>
                    </li>-->
                    <?php } ?>
                    <?php if ($suspended_sales && $this->session->userdata('store_id')) { ?>
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning"><?=sizeof($suspended_sales);?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header"><?=lang('recent_suspended_sales');?></li>
                            <li>
                                <ul class="menu">
                                    <li>
                                    <?php
                                    foreach ($suspended_sales as $ss) {
                                        echo '<a href="'.site_url('pos/?hold='.$ss->id).'" class="load_suspended">'.$this->tec->hrld($ss->date).' ('.$ss->customer_name.')<br><strong>'.$ss->hold_ref.'</strong></a>';
                                    }
                                    ?>
                                    </li>
                                </ul>
                            </li>
                            <li class="footer"><a href="<?= site_url('sales/opened'); ?>"><?= lang('view_all'); ?></a></li>
                        </ul>
                    </li>
                    <?php } ?>

                    <!-- USUARIO LOGUIN -->
                    <li class="dropdown user user-menu" style="padding-right:5px;border-style: solid; border-color: gray; border-radius: 9px; border-width: 2px;margin-top:3px;">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="border-radius: 7px;">
                            <img src="<?= base_url('uploads/avatars/thumbs/'.($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender').'.png')) ?>" class="user-image" alt="Avatar" />
                            <span class="hidden-xs"><?= $this->session->userdata('first_name').' '.$this->session->userdata('last_name'); ?></span>
                        </a>
                        <ul class="dropdown-menu" style="padding-right:3px;">
                            <li class="user-header">
                                <img src="<?= base_url('uploads/avatars/'.($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender').'.png')) ?>" class="img-circle" alt="Avatar" />
                                <p>
                                    <?= $this->session->userdata('email'); ?>
                                    <small><?= lang('member_since').' '.$this->session->userdata('created_on'); ?></small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?= site_url('users/profile/'.$this->session->userdata('user_id')); ?>" class="btn btn-default btn-flat"><?= lang('profile'); ?></a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?= site_url('logout'); ?>" class="btn btn-default btn-flat<?= $this->session->userdata('register_id') ? ' sign_out' : ''; ?>"><?= lang('sign_out'); ?></a>
                                </div>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </nav>
    </header>

    <link rel="stylesheet" type="text/css" href="<?= site_url('themes/default/views/header.css'); ?>">

    <aside class="main-sidebar">
        <section class="sidebar">
            <ul class="sidebar-menu">
                <!-- <li class="header"><?= lang('mian_navigation'); ?></li> -->

                <?php 
                    // ****** LOS ACCESOS A LOS MODULOS *******
                    $usuario = $_SESSION["username"];
                    $cSql = "select a.username, a.group_id, b.modulo, b.permiso 
                        from tec_users a
                        inner join tec_permisos b on a.group_id = b.group_id
                        where a.username = '{$usuario}'";

                    $query  = $this->db->query($cSql); // ,array($usuario, $modulo)
                    $ar_mod = array();
                    $nx     = 0;
                    foreach($query->result() as $r){
                        $ar_mod[$nx]['modulo'] = $r->modulo;
                        $ar_mod[$nx]['permiso'] = $r->permiso;
                        $nx++;
                    }
                    $this->menu_principal->menu_principal2($Admin, $this->session->userdata('store_id'), $Settings->multi_store, $ar_mod); 
                ?>
            </ul>
        </section>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <h1 style="margin-left:14px;"><?= $page_title; ?></h1>
            <ol class="breadcrumb">
                <li><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i> <?= lang('home'); ?></a></li>
                <?php
                foreach ($bc as $b) {
                    if ($b['link'] === '#') {
                        echo '<li class="active">' . $b['page'] . '</li>';
                    } else {
                        echo '<li><a href="' . $b['link'] . '">' . $b['page'] . '</a></li>';
                    }
                }
                ?>
            </ol>
        </section>

        <div class="col-lg-12 alerts">
            <div id="custom-alerts" style="display:none;">
                <div class="alert alert-dismissable">
                    <div class="custom-msg"></div>
                </div>
            </div>
            <?php if ($error)  { ?>
            <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-ban"></i> <?= lang('error'); ?></h4>
                <?= $error; ?>
            </div>
            <?php } if ($warning) { ?>
            <div class="alert alert-warning alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-warning"></i> <?= lang('warning'); ?></h4>
                <?= $warning; ?>
            </div>
            <?php } if ($message) { ?>
            <div class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4>    <i class="icon fa fa-check"></i> <?= lang('Success'); ?></h4>
                <?= $message; ?>
            </div>
            <?php } ?>
        </div>
        <div class="clearfix"></div>

<script type="text/javascript">
    function pedir_token(){
        let parametros = {
        }
        $.ajax({
            data: parametros,
            url : "<?= base_url() ?>pos/pedir_token",
            type: "get",
            success: function(response){
                if(response){
                    alert("Se actualizó correctamente el Token")
                }else{
                    alert("Inconvenientes con Actualizar el Token")
                }
            }
        })
    }

    function abrir_item_menu(nro){
        /*
        if(!Admin){
            if(nro == 5){  // ventas
                nro = '5b'
            }else if(nro == 4){ // insumos
                nro = '4b'
            }else if(nro == 6){ // compras
                nro = '6b'
            }else if(nro = 7){ // gastos
                //nro = '7b'
            }else if(nro = 10){ // proveedores
                nro = '10b'
            }
        }
        document.getElementById('h'+nro).click()
        */
    }

</script>
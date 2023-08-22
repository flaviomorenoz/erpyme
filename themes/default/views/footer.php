<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); ?>

</div>
</div>
<div class="modal" data-easein="flipYIn" id="posModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal" data-easein="flipYIn" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"></div>
<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>
<script type="text/javascript">
    var base_url = '<?=base_url();?>';
    var site_url = '<?=site_url();?>';
    var dateformat = '<?=$Settings->dateformat;?>', timeformat = '<?= $Settings->timeformat ?>';
    <?php unset($Settings->protocol, $Settings->smtp_host, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->smtp_crypto, $Settings->mailpath, $Settings->timezone, $Settings->setting_id, $Settings->default_email, $Settings->version, $Settings->stripe, $Settings->stripe_secret_key, $Settings->stripe_publishable_key); ?>
    var Settings = <?= json_encode($Settings); ?>;
    $(window).load(function () {
        $('.mm_<?=$m?>').addClass('active');
        $('#<?=$m?>_<?=$v?>').addClass('active');
    });
    var lang = new Array();
    lang['code_error'] = '<?= lang('code_error'); ?>';
    lang['r_u_sure'] = '<?= lang('r_u_sure'); ?>';
    lang['register_open_alert'] = '<?= lang('register_open_alert'); ?>';
    lang['code_error'] = '<?= lang('code_error'); ?>';
    lang['r_u_sure'] = '<?= lang('r_u_sure'); ?>';
    lang['no_match_found'] = '<?= lang('no_match_found'); ?>';

    function vaceando_vars(store_id=0){
        if (typeof(Storage) !== "undefined"){
            localStorage.setItem("compras_filtro_desde", "")
            localStorage.setItem("compras_filtro_hasta", "")
            if(store_id > 0){
                localStorage.setItem("compras_filtro_tienda",store_id)
            }
            localStorage.setItem("compras_filtro_proveedor", "")
            localStorage.setItem("compras_filtro_fec_emi", "")
            localStorage.setItem("compras_filtro_estado", "")
            //alert("En vaceando vars:" + localStorage.getItem("compras_filtro_tienda"))
        }
    }

    function vaceando_vars_gastus(store_id=0){
        if (typeof(Storage) !== "undefined"){
            localStorage.setItem("gastos_filtro_desde", "")
            localStorage.setItem("gastos_filtro_hasta", "")
            if(store_id > 0){
                localStorage.setItem("gastos_filtro_tienda",store_id)
            }
            localStorage.setItem("gastos_filtro_clasifica1", "")
            localStorage.setItem("gastos_filtro_clasifica2", "")
            localStorage.setItem("gastos_filtro_fec_emi", "")
            localStorage.setItem("gastos_filtro_estado", "")
            //alert("En vaceando vars:" + localStorage.getItem("gastos_filtro_tienda"))
        }
    }

</script>

<script src="<?= $assets ?>dist/js/libraries.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/scripts.min.js" type="text/javascript"></script>
<?= (DEMO) ? '<script src="'.$assets.'dist/js/spos_ad.min.js"></script>' : ''; ?>
</body>
</html>

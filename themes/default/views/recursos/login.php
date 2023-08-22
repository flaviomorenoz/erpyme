<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
?>

<section class="content">

    <div class="row">
        <div class="col-xs-12 col-sm-10 col-md-9 col-lg-7">
            <h2 style="text-align:center">Login</h2>
        </div>
    </div>

    <!--<div class="row">
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
            <label>Usuario:</label>
            <input type="text" name="txt_usuario" id="txt_usuario" class="form-control">
        </div>
    </div>-->

    <?php echo form_open_multipart("recursos/login", 'class="validation" id="form1"'); ?>
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <label>Clave:</label>
                <input type="password" name="txt_pass" id="txt_pass" class="form-control">
            </div>
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <label>&nbsp;</label><br>
                <button type="submit" class="btn btn-primary">Entrar</button>
            </div>
        </div>
    </form>

</section>
<script type="text/javascript">
</script>


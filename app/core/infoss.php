<?php
$assets = "../../themes/default/assets/";
$assets = "http://localhost/varios/qsystem/POS/themes/default/assets/";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
</head>
<body>

<h1>Marcas DB:</h1>
<input type="text_area" id="marca" name="marca">

<script type="text/javascript">
	function la_consulta(){
		var parametros = {
			vista : "query"
		}
		$.ajax({
			data 	: parametros,
			url		:"querys.php",
			type	:"get",
			success	: (response)=>{
				//console.log("Multo bene " + response)
				document.write(response)
				setTimeout("la_consulta()",60000)
			}
		})
	}

	la_consulta()
</script>

</body>
</html>
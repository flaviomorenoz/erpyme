<script type="text/javascript">
	function ejecucion(){
		$.ajax({
			data 	:{dia:document.getElementById('dia').value},
			url 	:'<?= base_url("pos/envio_masivo_individual") ?>',
			type 	:"get",
			success :function(res){
				document.getElementById("rpta_ejecucion").innerHTML = res
			}
		})
	}

	function consulta_recurrente(){
		var condor = document.getElementById('dia').value
		if(condor.length == 10){
			$.ajax({
				data 	:{dia:document.getElementById('dia').value},
				url 	:'<?= base_url("pos/consulta_recurrente") ?>',
				type 	:"get",
				success	:function(res){
					document.getElementById("rpta").value = res
					console.log(res)
				}
			})
		}
	}

	setInterval("consulta_recurrente()",7000)
</script>

<div style="margin-left:15px">
	Dia:<input type="text" name="dia" id="dia">

	<button type="button" onclick="ejecucion()">Ejecutar</button><br><br>

	Faltan:<input type="text" name="rpta" id="rpta"><br><br>

	<textarea id="rpta_ejecucion" name="rpta_ejecucion" rows="8" cols="50">
		Respuesta de la Ejecucion
	</textarea>
</div>
<?php
$cSql = "select a.id, 239 product_id, 1 quantity, a.total, a.total from tec_purchases a
	inner join tec_tipo_gastos b on a.clasifica1 = b.id
	inner join tec_subtipo_gastos c on a.clasifica2 = c.id
	where a.tipogasto = 'gastos' and a.clasifica1 = 1 and a.clasifica2 = 26";

echo $cSql . "<br>";

$query = $this->db->query($cSql);

foreach($query->result() as $r){
	$id = $r->id;
	$cSql = "update tec_purchases set tipogasto='caja', supplier_id = 89 where id = {$id}";
	echo $cSql . "<br>";
	//sleep(1000);
	$this->db->query($cSql);	
}
?>

<?php

// Token que sale del Loguin de la Empresa.
$cToken = "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MjE4NjI3MDYsImV4cCI6MTYyMTk0OTEwNiwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiZmxhdmlvbW9yZW5veiJ9.QjMFF4Gqtgo8wxUs0TRMdJnK-dQyowls6xxr7sfIu-XO-HJPEHX2h5_L19_6pCrt6QRNQKJLC6mh9EbvaeR0efVHZJRDyg5DcftCImAZPiZFZkvqB39ELiibEuRBYECFsboVvTdmbneU5CURLskqRT2dq0p8OroGPs_-W9qsRZXxAbO4XY-_J3nrR6M2eLWsJy_BKfwKcaXTJ5fTiCZrNBwIrp9m-LgPAUTSxv7n1ovLSYNEZogx8UfsAfmkBxmAf1lBydMz65FT1oAS5yeaQZOGgQS2T1O7Ko2VfgP3d8YR0MRU0ivOr0yBzizx10BAmqSuxPujLUGxv4lUJUS3gfbKtoEi00LZBMM-qrAz6GVu5nquHTaQJBmG0vs63OH73uoXH7yYh2S8VyRmQBDIq1P_3W3jFPL9mB3aBgufDhwTSolYOOKN5RPMdjD_JBYUQDRz6_oe9m6fVFtQwRwbupPnAoWGeQsEEoZJQiWKghBkdyuwkCy2KsbC0BeC5EQVHaqgytaL2bS5o_tXx_DgoQ2fR5ydSeLwTX2gSjADYojMRIiCaRlL0IOvU0ADtCfJOVjCVvC6ENC1BowBgWy-WtoLb_QjSkdN4gACBK6IgbaF6VG2l8S7VPmVdHE_BfZib7Xt9BogUmxuoybYHTEz-Ch437uE7uSQw3taOxdAWFI";

// Token que sale del GET Empresas (pero no me funciona)
//$cToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MjE4NjM2MzIsImV4cCI6NDc3NTQ2MzYzMiwidXNlcm5hbWUiOiJmbGF2aW9tb3Jlbm96IiwiY29tcGFueSI6IjEwOTk5OTk5OTk5In0.gpqDBY6F95ITH95NVRt1nBorLRo4ps3IroLkq-0DoUk_wQa6vCR5VFAQ7CAwuMLa-TYRJcjVGN-dNB3rmpwnaQpXnxZ-0GdMaSHZ9vi9ljQ-s6dwkMgRbrWIupR0ww29G-aE6m0PqsmH5h2FEF4hx2YsykYTxn9Ou49Rknhf8cdfOdpiYB79DQ6Kqy_7aVuIMPFF-x_W5xbgIMXSandBrpiuZhtdCr5UtCjD8tLpJExv-84-n7a8v3i1Jk_mTTqkFOL2iJqgA4_o-DhKQf95PHahRnnzehwUJZvJ5MZf79HIskatcH8OYR5TJSQJ2PGVcdWfNAQjmgrveJS5cM9O__NabscWK2nW8cR2GjbbIMSMUsq6xvyCohPglzcDjHz8qfAM2uJ--CyrHtugVRGeXAR3uLlxfvFFDuL5aTuSpYOiINQw9Eu3J0_lfMWzhIC5smrt7_GuIfAzRiawBiAc6KhhMuayqziMZGhJHzTsjabo1u5XUhmvbI1tT7FHgjIbq4FcoPqrhbnLcfBg1V9huw1fHL1UTW5cWk9t-QZRO-6c374vvgb_XOkRq6051PdMuUnXvxXPnUNniTgU-F7hWikbfoQocIL4dV48MxGvMkazTV70GRJrzU_r61Vib6pTylkwjIBGD_U0emk4VdyRK5ZV_AICnoOBI1qxxXutVpA";

include("datos_bv.php");  // ********* CASO BOLETA DE VENTA: **********

//include("datos_f.php");  // ********* CASO FACTURA: **********

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, "https://facturacion.apisperu.com/api/v1/invoice/send");

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 

curl_setopt($curl, CURLOPT_POST, true);

curl_setopt($curl, CURLOPT_POSTFIELDS, $campos);

curl_setopt($curl, CURLOPT_HTTPHEADER,
	array(
	    "content-type: application/json",
	    "Authorization: $cToken"
    )
);

$response = curl_exec($curl);

curl_close($curl);

echo $response;
?>
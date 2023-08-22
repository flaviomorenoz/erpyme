class tablas_fm{ 
    constructor(nAlto){
        this.alto_fila  = nAlto
        //this.ar_tit     = ar_tit
    } 

    // Aclarar que ari nace de un query convertido a result_array y luego parse()
    tablar(ari, ar_tit, ar_campos){
        var cad = "<table border='1' class='table'>"

        cad += "<tr>"
        for(let i=0; i<ar_tit.length; i++){
            cad += this.casillar(ar_tit[i],'tistulos')
        }
        cad += "</tr>"

        for(let i = 0; i < ari.length; i++){  // Recorre cada fila
            cad += "<tr>"
            for(let j = 0; j < ar_tit.length; j++){ // Recorre cada celda
                cad += this.casillar(ari[i][ar_campos[j]])
            }
            cad += "</tr>"
        }
        cad += "</table>"
        return cad
    }

    casillar(cado,clase1=''){
        return '<td class=\'' + clase1 + '\'>' + cado + '</td>'
    }
}


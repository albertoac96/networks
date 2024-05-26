exports.install = function (Vue, options) {
    Vue.prototype.brtolf = function (PcTexto) {
        LcResp = PcTexto.replace(/<br>/g, String.fromCharCode(10) )
        return LcResp;
    };
    Vue.prototype.lftobr = function (PcTexto) {
        LcResp = PcTexto.replace(/\n/g, " <br> ");
        return LcResp;
    };
    Vue.prototype.cResumenVerMas = function (cResumen, nMinimo) {
        if(typeof(cResumen) == "string"){
            if(cResumen.length < nMinimo) return cResumen;
           cResumen = cResumen.substr(0, nMinimo) + "...";
       }
        return cResumen;
    };
    Vue.prototype.LongitudMax = function (cTexto, nLongitud) {
        
        if(typeof(cTexto) == "string"){
             if(cTexto.length < nLongitud) return false;
            return true;
        }
       
    };
    Vue.prototype.Acorta = function (cTexto, nLongitud) {
        
        if(typeof(cTexto) == "string"){
             if(cTexto.length < nLongitud) return cTexto;
             cTexto = cTexto.substr(0, nLongitud);
            return cTexto;
            
        } else {
            return "OK"
        }
       
    };
    Vue.prototype.QuitarMedio = function (cTexto, nLongitud) {
        
        if(typeof(cTexto) == "string"){
             if(cTexto.length < nLongitud) return cTexto;
             var nMitad = (nLongitud/2) - 2;
             var LcResp = cTexto.substr(0, nMitad) + "..." + cTexto.substr(-nMitad);
            return LcResp;
        }
       
    };
    Vue.prototype.VerificaPermiso = function (cPermiso) {
        var permisos = this.$store.state.dataUser.permisos;
        var LcResp = permisos.indexOf(cPermiso);
        if(LcResp == -1){
            return false;
        }
        return true;
       
    };
    Vue.prototype.ResaltaTexto = function (string) {
       // console.log(this.$store.state.selected.search);
        var LcResp = "";
        var LaPalabras = this.$store.state.selected.search.split(" ");
        var LcNuevoTexto = "";

        LcNuevoTexto = String(string);
        
        for(Lii=0; Lii < LaPalabras.length; Lii++){
            if(LaPalabras[Lii] != "" && LaPalabras[Lii].length > 3){
                LcNuevoTexto = LcNuevoTexto.replace(/o/gi,"o");
                LcNuevoTexto = LcNuevoTexto.replace(new RegExp(LaPalabras[Lii], 'gi'), "<label style='background-color:yellow'>" + LaPalabras[Lii] + "</label>");           
            }
        }

        LcResp = LcNuevoTexto;
        
        return(LcResp);
        
       
    };
};
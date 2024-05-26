exports.install = function (Vue, options) {
    Vue.prototype.cFechaYMDlarga = function (PcFechaYMD) {// Función global 1
        var LcMes = ",Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre,";
        var LaMes = LcMes.split(",");
        var LaFecha = PcFechaYMD.split("-");
        var LcDia = LaFecha[2];
        var LiMes = parseInt(LaFecha[1]);
        var LiAno = LaFecha[0];
        var LcFecha = "";
        var LcHora = "";
        var LiDia = "";

        LcDia = LcDia + " 00:00";
        LaDia = LcDia.split(" ");
        LiDia = LaDia[0];
        LiDia = LiDia.substring(0, 2);

        LcFecha = LiDia + ' de ' + LaMes[LiMes] + ' de ' + LiAno;

        return LcFecha;
    };
    Vue.prototype.cFechaCompare = function (dInicio, dFin) {// Función global 2
        var LdInicio = dInicio;

        var LdFin = dFin;
        var fecha = "";



        var LdInicioAnyo = LdInicio.substring(0, 4);
        var LdInicioMes = LdInicio.substring(5, 7);
        var LdInicioDia = LdInicio.substring(8, 10);

        var LdFinAnyo = LdFin.substring(0, 4);
        var LdFinMes = LdFin.substring(5, 7);
        var LdFinDia = LdFin.substring(8, 10);

        if(LdInicioDia + LdFinDia == '0101'){
            if(LdInicioMes + LdFinMes == '0101'){
                if (LdInicioAnyo == LdFinAnyo) {
                    return LdInicioAnyo;
                } else {
                    return LdInicioAnyo + " - " + LdFinAnyo;
                }
            }
        }

        

        if (LdInicio == LdFin) {

            var fecha = this.cFechaYMDlarga(LdInicio);
            return fecha;
        } else {
            LdInicio = this.cFechaYMDlarga(LdInicio);
            LdFin = this.cFechaYMDlarga(LdFin);
            var fecha = LdInicio + " - " + LdFin;
            return fecha;
        }




    };

    Vue.prototype.AcortarString = function (string, length, espacios) {
        if (espacios == 1) {
            string = string.replace(/<br>/g, " ")
        }
        if (string.length < length) {
            return string;
        }
        var LcResp = string.substring(0, length);
        LcResp = LcResp + "..."

        return LcResp;
    };
    Vue.prototype.Hoy = function () {
        var hoy = new Date();
        var dd = hoy.getDate();
        var mm = hoy.getMonth() + 1; //hoy es 0!
        var yyyy = hoy.getFullYear();
        var hh = hoy.getHours();
        var min = hoy.getMinutes();
        var ss = hoy.getSeconds();

        if (dd < 10) dd = '0' + dd;
        if (mm < 10) mm = '0' + mm;
        if (hh < 10) hh = '0' + hh;
        if (min < 10) min = '0' + min;
        if (ss < 10) ss = '0' + ss;

        return (yyyy + "-" + mm + '-' + dd + ' ' + hh + ':' + min + ':' + ss);
    };
    Vue.prototype.DateDiff = function (PcInicio, PcFin) {
        var date2 = new Date(this.cFecha(PcInicio));
        var date1 = new Date(this.cFecha(PcFin));

        var timeDiff = Math.abs(date2.getTime() - date1.getTime());
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

        return (diffDays);
    };
    Vue.prototype.cFecha = function (PcFecha) {
        var LaFecha = PcFecha.split("/");
        var LcFecha = LaFecha[1] + "/" + LaFecha[0] + "/" + LaFecha[2];

        return LcFecha;
    };
    Vue.prototype.MiTiempo = function (PcCreado) {
        var Diff = "";

        var Anyo = PcCreado.substring(0, 4);
        var Mes = PcCreado.substring(5, 7);
        var Dia = PcCreado.substring(8, 10);

        //return PcCreado;

        var Hora = PcCreado.substring(12, 14);
        var Min = PcCreado.substring(15, 17);
        var Seg = PcCreado.substring(18, 20);

        if (PcCreado.length != 19) {
            var Hora = PcCreado.substring(12, 14);
            var Min = PcCreado.substring(15, 17);
            var Seg = PcCreado.substring(18, 20);

        } else {
            var Hora = PcCreado.substring(11, 13);
            var Min = PcCreado.substring(14, 16);
            var Seg = PcCreado.substring(17, 19);
        }
        //return Dia;




        var hoy = new Date();
        var dd = hoy.getDate();
        var mm = hoy.getMonth() + 1; //hoy es 0!
        var yyyy = hoy.getFullYear();
        var hh = hoy.getHours();
        var min = hoy.getMinutes();
        var ss = hoy.getSeconds();

        if (dd < 10) dd = '0' + dd;
        if (mm < 10) mm = '0' + mm;
        if (hh < 10) hh = '0' + hh;
        if (min < 10) min = '0' + min;
        if (ss < 10) ss = '0' + ss;



        if (Anyo == yyyy) {
            if (Mes == mm) {
                if (Dia == dd) {
                    if (Hora == hh) {
                        if (Min == min) {
                            return "Hace unos segundos";
                        } else {
                            Diff = min - Min;
                            if (Diff == 1) return "Hace " + Diff + " minuto";
                            return "Hace " + Diff + " minutos";
                        }

                    } else {
                        Diff = hh - Hora;
                        if (Diff == 1) return "Hace " + Diff + " hora";
                        return "Hace " + Diff + " horas";
                    }
                } else {
                    Diff = dd - Dia;
                    if (Diff == 1) return "Hace " + Diff + " día";
                    return "Hace " + Diff + " días";
                }

            } else {
                Diff = mm - Mes;
                if (Diff == 1) return "Hace " + Diff + " mes";
                return "Hace " + Diff + " meses";
            }
        } else {
            Diff = yyyy - Anyo;
            if (Diff == 1) return "Hace " + Diff + " año";
            return "Hace " + Diff + " años";
        }




    }

};

function rounding2decimal(number) {
    let float = parseFloat(number);
    return Math.round(float * 100) / 100;
}

function addZeros(fact, cant_numbers = 6){
    let cad_cero = "";
    for(let i=0; i<(cant_numbers-fact.length); i++)
        cad_cero+=0;
    return cad_cero+fact;
}

function is_int (mixedVar) {
     //   example 1: is_int(23)
     //   returns 1: true
     //   example 2: is_int('23')
     //   returns 2: false
     //   example 3: is_int(23.5)
     //   returns 3: false
     //   example 4: is_int(true)
     //   returns 4: false
    return mixedVar === +mixedVar && isFinite(mixedVar) && !(mixedVar % 1)
}

function is_float (mixedVar) {
     //   example 1: is_float(186.31)
     //   returns 1: true
    return +mixedVar === mixedVar && (!isFinite(mixedVar) || !!(mixedVar % 1))
}
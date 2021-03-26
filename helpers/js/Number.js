
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
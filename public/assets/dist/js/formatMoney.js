var formatMoney = function (value) {
    if(value > 0)
        return (Math.floor(value*100)/100).toLocaleString('pt-br', {minimumFractionDigits: 2});
    else
        return (Math.ceil(value*100)/100).toLocaleString('pt-br', {minimumFractionDigits: 2});
}
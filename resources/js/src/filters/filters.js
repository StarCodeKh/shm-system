const filters = {}

filters.capitalize = function (value) {
    if (!value) return ''
    value = value.toString()
    let arr = value.split(" ")
    let capitalized_array = []
    arr.forEach((word) => {
        let capitalized = word.charAt(0).toUpperCase() + word.slice(1)
        capitalized_array.push(capitalized)
    })
    return capitalized_array.join(" ");
}
filters.title = function (value, replacer="_") {
    if (!value) return ''
    value = value.toString()

    let arr = value.split(replacer)
    let capitalized_array = []
    arr.forEach((word) => {
        let capitalized = word.charAt(0).toUpperCase() + word.slice(1)
        capitalized_array.push(capitalized)
    })
    return capitalized_array.join(" ");
}
filters.truncate = function(value, limit) {
    return value.substring(0, limit)
}
filters.tailing = function(value, tail) {
    return value + tail;
}
filters.time = function(value, is24HrFormat = false) {
    if(value) {
        const date = new Date(Date.parse(value));
        let hours = date.getHours();
        const min = (date.getMinutes() < 10 ? '0' : '') + date.getMinutes()
        if(!is24HrFormat) {
            const time = hours > 12 ? 'AM' : 'PM';
            hours = hours % 12 || 12;
            return hours + ':' + min + ' ' + time
        }
        return hours + ':' + min
    }
}
filters.date = function(value, fullDate = false) {
    value = String(value)
    const date = value.slice(8,10).trim();
    const month = value.slice(4,7).trim();
    const year = value.slice(11,15);

    if(!fullDate) return date + '-' + month + '-' + year;
    else return date + '-' + month + '-' + year;
}
filters.month = function(val, showYear = true) {
    val = String(val)

    const regx = /\w+\s(\w+)\s\d+\s(\d+)./;
    if(!showYear) {
        return regx.exec(val)[1];
    }else {
        return regx.exec(val)[1] + ' ' + regx.exec(val)[2];
    }

}
filters.khMonth = function(value, fullDate = false) {
    const _date = new Date(value);
    const _dateStr = _date.toString();
    const date = _dateStr.slice(8,10).trim();
    const month_alt = _dateStr.slice(4,7).trim();
    const year = _dateStr.slice(11,15);

    var month=new Array();
        month[0]="មករា";
        month[1]="កម្ភះ";
        month[2]="មីនា";
        month[3]="មេសា";
        month[4]="ឧសភា";
        month[5]="មិថុនា";
        month[6]="កក្កដា";
        month[7]="សីហា";
        month[8]="កញ្ញា";
        month[9]="តុលា";
        month[10]="វិច្ឆិកា";
        month[11]="ធ្នូ";

    return month[_date.getMonth()];
}
filters.khDay = function(value, fullDate = false) {
    const _date = new Date(value);
    const _dateStr = _date.toString();
    const date = _dateStr.slice(8,10).trim();
    const month_alt = _dateStr.slice(4,7).trim();
    const year = _dateStr.slice(11,15);

    var month=new Array();
        month[0]="មករា";
        month[1]="កម្ភះ";
        month[2]="មីនា";
        month[3]="មេសា";
        month[4]="ឧសភា";
        month[5]="មិថុនា";
        month[6]="កក្កដា";
        month[7]="សីហា";
        month[8]="កញ្ញា";
        month[9]="តុលា";
        month[10]="វិច្ឆិកា";
        month[11]="ធ្នូ";

    return month[_date.getDay()];
}
filters.khYear = function(value, fullDate = false) {
    let _date = new Date(value);
    let _dateStr = _date.toString();
    let _year = _date.getFullYear();
    let khmerNumber = [
        {'1': '១'},
        {'2': '២'},
        {'3': '៣'},
        {'4': '៤'},
        {'5': '៥'},
        {'6': '៦'},
        {'7': '៧'},
        {'8': '៨'},
        {'9': '៩'},
        {'0': '០'},
    ];
    let arrNumbers = Array.from(_year.toString()).map(Number);//split(_year); //str_split(_year);
    let newKhmerNumber = '';

    for(let i = 0; i < arrNumbers; i++){
        newKhmerNumber += khmerNumber[_year];
    }
    return newKhmerNumber;

    return month[_date.getYear()];
}
filters.csv = function(value) {
    return value.join(', ')
}
filters.filter_tags = function(value) {
    return value.replace(/<\/?[^>]+(>|$)/g, "")
}
filters.k_formatter = function(num) {
    return Number(num).toLocaleString() + '៛';
}

export default filters

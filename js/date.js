//Ajdin Masic 8/26/17

var date = new Date(); // gets date
var month = date.getMonth(); // gets month from date
var monthName = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

var day = date.getDate();

month = monthName[month];

var formatMonthDay = month + " " + day;

document.getElementById('date').innerHTML = formatMonthDay;
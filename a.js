exports.test = "Hello World";

exports.jeanjudas="nique";   
// 🍌
// test
var mysql      = require('mysql');
var connection = mysql.createConnection({
  host     : 'localhost',
  user     : 'root',
  password : ''
});

connection.connect(err=>{
 if(err) {throw err;}
 console.log('MySQL connected');
});
const app = express();
app.get()
/*
connection.query('SELECT 1 + 1 AS solution', function(err, rows, fields) {
  if (err) throw err;
  console.log('The solution is: ', rows[0].solution);
});

connection.end();*/
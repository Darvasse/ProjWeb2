const express = require('express');
const path = require('path');
const mysql = require('mysql');
const router = express.Router();
//const auth = require('./a.js');
//console.log(auth.test);
//create connection
const db = mysql.createConnection({
    host     : 'localhost',
    user     : 'root',
    password : '',
    database: 'nodemysql'
  });

//connect to mysql
db.connect(err=>{
    if(err){
        throw err;
    }
    console.log('MySQL Connected')
})

const app = express();
const port = 3000;

app.get('/',(req,res)=>{
    res.json({ message : "Hello World" });
    //res.sendFile(path.join(__dirname + '/views/index.html'));
    
});

app.get('/store',(req,res)=>{

});

app.get('/store/:search', (req,res)=>{

})

app.get('/games', (req,res)=>{

})

app.get('/games/:id', (req,res)=>{

})

app.get('/createdb',(req,res)=> {
    let sql = 'CREATE DATABASE nodemysql'
    db.query(sql, (err) =>{
        if(err){
            throw err
        }
        res.send('Database Created')
    })
})


//create Table
app.get('/createemployee',(req, res)=>{
    let sql = 'CREATE TABLE employee(id int AUTO_INCREMENT, name VARCHAR(255), designation VARCHAR(255), PRIMARY KEY(id))'
    db.query(sql,err =>{
        if(err){
            throw err;
        }
        res.send('Employee table created')
    })
})
//app.use('/', router);

app.listen(port, () => {
    console.log(`Example app listening at http://localhost:${port}`);
  });
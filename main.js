const express = require('express');
const path = require('path');
const router = express.Router();
//const auth = require('./a.js');
//console.log(auth.test);

const app = express();
const port = 3000;

app.get('/',(req,res)=>{
    res.json({ message : "Hello World" }),
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

app.get('')



//app.use('/', router);

app.listen(port, () => {
    console.log(`Example app listening at http://localhost:${port}`);
  });
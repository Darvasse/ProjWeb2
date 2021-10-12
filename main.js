const express = require('express');
const path = require('path');
const router = express.Router();
const auth = require('./a.js');
console.log(auth.test);

const app = express();
const port = 3000;

app.get('/',(req,res)=>{
    //res.send("Hellow World!");
    res.sendFile(path.join(__dirname + '/views/index.html'));
});

//app.use('/', router);

app.listen(port, () => {
    console.log(`Example app listening at http://localhost:${port}`);
  });
  
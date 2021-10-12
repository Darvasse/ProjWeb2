const express = require('express');
const auth = require('./a.js');
console.log(auth.test);

const app = express();
const port = 3000;

app.get('/',(req,res)=>{
    res.send("Hellw World!");
    
});
app.listen(port, () => {
    console.log(`Example app listening at http://localhost:${port}`);
  });
  
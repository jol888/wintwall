const express=require('express');
const mysql = require('mysql');
const crypto = require('crypto');
const sqlstring = require('sqlstring');
const {promisify} = require('util');
const nodemailer = require('nodemailer');
const smtpTransport = require('nodemailer-smtp-transport');
const assert = require('http-assert');
const fs = require('fs');

const dev=process.env.USERNAME=='jolli';

console.log(dev?"dev":"product");

var app=express();
app.set('view engine', 'ejs');
app.use(express.static(__dirname+'/static'))
app.use((req, res, next) => {
    res.setHeader('Content-type', 'text/html;charset=utf-8' );
    next();
});

/////////constes/////////

const SENDER='jolliu@outlook.com';

const emailcofig={
        host: 'smtp.office365.com', // 服务
        port: 587, // smtp端口
        secure: false,
        auth: {
          user: SENDER, //用户名
          pass: 'Yuxuan20090224' // SMTP授权码
      }
  };

/////////sql/////////

const sql = mysql.createConnection({
   host: 'localhost',
   user: 'root',
   password: (dev?'':'TmIJSzVcnR5aQmIZ')
});

sql.connect(function (err){
  if (err){
      console.error('error connecting: ' + err.stack);
      return;
  }
  sql.query('use wintwall');
  console.log('connected as id ' + sql.threadId);
});

var sha256=(i)=>crypto.createHmac('sha256', i).digest('hex');

const query = promisify(sql.query.bind(sql));

async function getList(){
	return await query('select * from tape');
}

async function verifyAccount(account, password) {
  const [user] = await query(
    sqlstring.format(
      "select * from account where nickname=? and password=?",
      [account, sha256(password)],
      )
    );
  return user ? user.uid : -1;
}

async function namecheck(nickname){
    return (await query(sqlstring.format("select * from account where nickname=? limit 1",[nickname]))).length;
}

/////////Fns/////////

var fnsList=[];

const randomFns=()=> { // 生成6位随机数
    let code = ""
    for(let i= 0;i<6;i++){
        code += parseInt(Math.random()*10)
    }
    return code 
}
const regEmail=/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/ //验证邮箱正则

var verifyCode=(email,fns)=>fnsList.find((v)=>(v.email==email&&v.code==fns));

app.get('/', async (req, res) =>  {
  res.render('index2', {list: await getList()});
});

app.get('/add', async (req, res) =>  {
	let {title,note,nickname,contact,email,fns}=req.query;
	to=0,uid=1;
	console.log({title,note,nickname,contact,email,fns});
	if(title&&note&&nickname&&contact&&email&&fns){
		console.log(fnsList.find((v)=>(v.email==email&&v.code==fns)));
		if(verifyCode(email,fns)||true){
			await query(sqlstring.format('INSERT INTO `tape` (`tid`, `uid`, `title`, `body`, `nickname`,  `track`, `addressee`) VALUES (NULL, ?, ?, ?, ?, ?, ?)',
					[uid,title,note,nickname,contact,to]
				));
			console.log((await query('SELECT max(tid) FROM tape;'))[0]);
			res.render('success',{tid:(await query('SELECT max(tid) FROM tape;'))[0]['max(tid)']});
			return;
		}
	}
  	res.render('add');
});

/*
title: 
note: 
nickname: 
passwd: 
contact: 
email: jolliu@outlook.com
yanzheng: 
submit: 233
*/

app.get('/verify', async (req, res) =>  {
    res.setHeader('Content-type', 'text/plain;charset=utf-8' );
	let {email,fns}=req.query;
	if (email!=undefined&&fns!=undefined){
		if(verifyCode(email,fns)){
  			res.send('1');
  			// const filter=(val)=>(Date.now()-val.time<5*60*1000&&val.email!=email);
  			// fnsList=fnsList.filter(filter);
  			return;
		}
	}
	res.send('0');
});

app.get('/getemailcode',async(req,res)=>{
    // console.log(req);
    res.setHeader('Content-type', 'text/plain;charset=utf-8' );
    let email=req.query.email;
    if (regEmail.test(email)){
        let code=randomFns();

        const transport = nodemailer.createTransport(emailcofig);
        transport.sendMail(
            {
                from: SENDER,
                to: email,
                subject: '验证你的电子邮件',
                html: `
                    <p>你好！</p>
                    <p>您正在验证${req.query.email}的真实性</p>
                    <p>你的验证码是：<strong style="color: #ff4e2a;">${code}</strong></p>
                    <p>***该验证码5分钟内有效***</p>` 
            }, 
            (err, data) => {
                if(err){
                	console.log(err);
                	res.send(err);
                	return;
                }
                transport.close();
                const filter=(val)=>(Date.now()-val.time<5*60*1000&&val.email!=email);
                fnsList=fnsList.filter(filter);
		        fnsList.push({code,email,time:Date.now()});
		        console.log({code,email,time:Date.now()});
		        setTimeout(()=>{fnsList=fnsList.filter(filter)},5*60*1000);
            }
        );
    }
    res.send('done.');
    return;
})

async function getJi(grade,sex)
{
    if(!grade){
        return (await query(
                sqlstring.format(`select * from ji where ${sex?'sex=?':'1'} order by rand() limit 1`,[sex])
            ))
    }else{
        return (await query(
                sqlstring.format(`select * from ji where grade=? ${sex?'and sex=?':''} order by rand() limit 1`,[grade,sex])
            ))
    }
}

app.get('/sign_in', async (req, res) =>  {
    console.log(req);
    let {nickname,password,email,fns,realname,grade,classnum}=req.query;
    if(nickname&&password&&email&&fns&&realname&&grade&&classnum&&verifyCode(email,fns)&&(await namecheck(nickname))||1){
        console.log(await query(sqlstring.format("INSERT INTO `account`(`uid`, `nickname`, `password`) VALUES (NULL,?,?)",[nickname,sha256(password)])));
    }
});

async function getWave(){
    //古早的声音……
    return (await query(`select * from wave where wid=${Math.round((await query('select count() from wave')['count()'])*(1-Math.random()*Math.random()))}`));
}

const hitokoto=JSON.parse(fs.readFileSync('./static/hitokoto/all.json'));
// console.log(hitokoto);

app.get('/wave', async (req,res)=>{
    res.setHeader('Content-type', 'text/plain;charset=utf-8' );
    console.log('wave.');
    if(Math.random()<0.7&&(await query('select count() from wave')['count()']>10))
        res.send(getWave());
    else
        res.send(hitokoto[Math.floor(Math.random()*(hitokoto.length))].hitokoto);
    // res.end();
    return;
})

app.get('/pp',async (req,res)=>{
    res.setHeader('Content-type', 'text/plain;charset=utf-8' );
    res.send(await getJi());
    // console.log(await getJi());
})

app.get('/sendCard',async (req,res)=>{
    res.setHeader('Content-type', 'text/plain;charset=utf-8' );
    res.send(await getJi());
    // console.log(await getJi());
})

app.post('/say', async (req,res)=>{
    console.log('say',req.query);
})

app.listen(1991,()=>{
  console.log('yooo!');
})
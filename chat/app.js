var PORT=9000;
var app=require('express')();
var http=require('http').Server(app);
var io=require('socket.io')(http);

app.get('/chat/:id', function(req, res) {
    res.sendFile(__dirname+'/index.html');
    var roomid = req.params.id;
});

var session = require("express-session")({
    secret: "my-secret",
    resave: true,
    saveUninitialized: true
});
var sharedsession = require("express-socket.io-session");

// Use express-session middleware for express
app.use(session);

// Use shared session middleware for socket.io
// setting autoSave:true
io.use(sharedsession(session, {
    autoSave:true
})); 

http.listen(PORT);

io.on('connection',function (socket){
    var url=socket.request.headers.referer;
    var split_arr=url.split('/');
    var roomid=split_arr[split_arr.length-1];
    var user='';
    var roomUser=[];
    socket.join(roomid);
    socket.on('join',function(username){
        user=username;
        if(!roomUser[roomid]){
            roomUser[roomid]=[];
        }
        roomUser[roomid].push(user);
        socket.join(roomid);
        io.to(roomid).emit('sys',user+'entered');
    });
    socket.on('message',function(user,msg){
        io.to(roomid).emit('newMessage',user,msg);
    });

    // Accept a login event with user's data
    socket.on("login", function(userdata) {
        socket.handshake.session.userdata = userdata;
        socket.handshake.session.save();
    });
    socket.on("logout", function(userdata) {
        if (socket.handshake.session.userdata) {
            delete socket.handshake.session.userdata;
            socket.handshake.session.save();
        }
    });

});



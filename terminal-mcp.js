const { exec } = require("child_process");
const WebSocket = require("ws");

const wss = new WebSocket.Server({ port: 5005 });
wss.on("connection", ws => {
  ws.on("message", msg => {
    exec(msg.toString(), { cwd: "C:\\Users\\USER\\Projects\\my-erp" }, (err, stdout, stderr) => {
      ws.send(JSON.stringify({ stdout, stderr, error: err?.message }));
    });
  });
});
console.log("Terminal MCP running on ws://localhost:5005");

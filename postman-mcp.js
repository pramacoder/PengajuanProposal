const fs = require("fs");
const WebSocket = require("ws");

const wss = new WebSocket.Server({ port: 5006 });
wss.on("connection", ws => {
  ws.on("message", () => {
    const collection = JSON.parse(fs.readFileSync("C:\\Users\\USER\\PostmanCollections\\my-collection.json"));
    ws.send(JSON.stringify({ endpoints: collection.item.map(i => i.request.url.raw) }));
  });
});
console.log("Postman MCP running on ws://localhost:5006");

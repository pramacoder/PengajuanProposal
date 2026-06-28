const { createClient } = require("@supabase/supabase-js");
const WebSocket = require("ws");

const supabase = createClient("YOUR_SUPABASE_URL", "YOUR_SERVICE_ROLE_KEY");
const wss = new WebSocket.Server({ port: 5007 });

wss.on("connection", ws => {
  ws.on("message", async () => {
    const { data: tables } = await supabase.from("information_schema.tables").select("*");
    ws.send(JSON.stringify({ tables }));
  });
});
console.log("Supabase MCP running on ws://localhost:5007");

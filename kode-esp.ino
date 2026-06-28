#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>

//========================
// PIN
//========================
#define PIR_PIN    15
#define RELAY_PIN  18

//========================
// WIFI
//======================== 
const char* ssid = "Taaraini";
const char* password = "Taara2025";

//========================
// SERVER LARAVEL
//========================
String serverBase = "https://monitor-burung.web.id/api";
String serverUrl = serverBase + "/update";

//========================
// VARIABEL
//========================
bool alarmAktif = false;

unsigned long startAlarm = 0;
unsigned long lastPrint = 0;
unsigned long lastCommandPoll = 0;

const unsigned long durasiAlarm = 3000;
const unsigned long intervalPoll = 2000;

int commandId = -1;
int wifiFailCount = 0;
const int maxWifiFail = 3;

//================================================
// SETUP
//================================================
void setup()
{
  Serial.begin(115200);

  pinMode(PIR_PIN, INPUT);
  pinMode(RELAY_PIN, OUTPUT);

  digitalWrite(RELAY_PIN, HIGH);

  Serial.println();
  Serial.println("================================");
  Serial.println(" SISTEM PENGUSIR BURUNG AKTIF ");
  Serial.println("================================");

  hubungkanWiFi();
}

//================================================
// LOOP
//================================================
void loop()
{
  if (WiFi.status() != WL_CONNECTED)
  {
    hubungkanWiFi();
    delay(500);
    return;
  }

  wifiFailCount = 0;
  int pirState = digitalRead(PIR_PIN);

  if (millis() - lastPrint >= 1000)
  {
    lastPrint = millis();

    Serial.print("Status PIR : ");

    if (pirState == HIGH)
      Serial.print("TERDETEKSI");
    else
      Serial.print("AMAN");

    if (alarmAktif)
      Serial.println(" | Alarm AKTIF");
    else
      Serial.println(" | Alarm NONAKTIF");
  }

  if (pirState == HIGH && !alarmAktif)
  {
    alarmAktif = true;
    startAlarm = millis();
    digitalWrite(RELAY_PIN, LOW);

    Serial.println(">>> BURUNG TERDETEKSI <<<");
    Serial.println("Relay ON");

    kirimData("AKTIF", "TERDETEKSI", "ON", "Pergerakan burung terdeteksi oleh sensor PIR");
  }

  if (alarmAktif && millis() - startAlarm >= durasiAlarm)
  {
    digitalWrite(RELAY_PIN, HIGH);
    alarmAktif = false;

    Serial.println("Relay OFF");
    Serial.println("Menunggu deteksi berikutnya");

    kirimData("AKTIF", "AMAN", "OFF", "Menunggu deteksi burung");
  }

  if (millis() - lastCommandPoll >= intervalPoll && !alarmAktif)
  {
    lastCommandPoll = millis();
    pollCommand();
  }
}

//================================================
// HUBUNGKAN WIFI
//================================================
void hubungkanWiFi()
{
  Serial.print("Menghubungkan WiFi");
  WiFi.begin(ssid, password);

  int attempt = 0;
  while (WiFi.status() != WL_CONNECTED && attempt < 20)
  {
    delay(500);
    Serial.print(".");
    attempt++;
  }

  if (WiFi.status() == WL_CONNECTED)
  {
    Serial.println();
    Serial.println("WiFi Connected");
    Serial.print("IP ESP32 : ");
    Serial.println(WiFi.localIP());
    wifiFailCount = 0;
  }
  else
  {
    Serial.println();
    Serial.println("WiFi GAGAL");
    wifiFailCount++;

    if (wifiFailCount >= maxWifiFail)
    {
      Serial.println("Restart ESP32...");
      delay(1000);
      ESP.restart();
    }
  }
}

//================================================
// POLLING PERINTAH BUZZER
//================================================
void pollCommand()
{
  if (WiFi.status() != WL_CONNECTED) return;

  String url = serverBase + "/command";

  WiFiClientSecure client;
  client.setInsecure();
  HTTPClient http;

  http.begin(client, url);
  http.addHeader("Content-Type", "application/json");

  int httpCode = http.GET();

  if (httpCode > 0)
  {
    String response = http.getString();
    Serial.println("COMMAND: " + response);

    int buzzerIdx = response.indexOf("\"buzzer\"");
    int cmdIdIdx = response.indexOf("\"command_id\"");

    if (buzzerIdx >= 0)
    {
      int valStart = response.indexOf(":", buzzerIdx) + 1;
      int valEnd = response.indexOf(",", valStart);
      if (valEnd < 0) valEnd = response.indexOf("}", valStart);

      String buzzerVal = response.substring(valStart, valEnd);
      buzzerVal.trim();
      buzzerVal.replace("\"", "");

      if (buzzerVal == "ON")
      {
        if (cmdIdIdx >= 0)
        {
          int cidStart = response.indexOf(":", cmdIdIdx) + 1;
          int cidEnd = response.indexOf(",", cidStart);
          if (cidEnd < 0) cidEnd = response.indexOf("}", cidStart);
          commandId = response.substring(cidStart, cidEnd).toInt();
        }

        digitalWrite(RELAY_PIN, LOW);
        delay(3000);
        digitalWrite(RELAY_PIN, HIGH);

        kirimData("AKTIF", "AMAN", "ON", "Buzzer dinyalakan dari dashboard");
        kirimAck();
      }
      else if (buzzerVal == "OFF")
      {
        if (cmdIdIdx >= 0)
        {
          int cidStart = response.indexOf(":", cmdIdIdx) + 1;
          int cidEnd = response.indexOf(",", cidStart);
          if (cidEnd < 0) cidEnd = response.indexOf("}", cidStart);
          commandId = response.substring(cidStart, cidEnd).toInt();
        }

        digitalWrite(RELAY_PIN, HIGH);
        kirimData("AKTIF", "AMAN", "OFF", "Buzzer dimatikan dari dashboard");
        kirimAck();
      }
    }
  }
  else
  {
    Serial.println("Polling gagal: " + http.errorToString(httpCode));
  }

  http.end();
}

//================================================
// KIRIM ACKNOWLEDGE
//================================================
void kirimAck()
{
  if (commandId < 0) return;

  String url = serverBase + "/command/ack";

  WiFiClientSecure client;
  client.setInsecure();
  HTTPClient http;

  http.begin(client, url);
  http.addHeader("Content-Type", "application/json");

  String json = "{\"command_id\":" + String(commandId) + "}";
  Serial.println("ACK: " + json);

  int httpCode = http.POST(json);
  if (httpCode > 0) Serial.println("ACK OK");
  else Serial.println("ACK GAGAL");

  http.end();
  commandId = -1;
}

//================================================
// FUNGSI KIRIM DATA KE LARAVEL
//================================================
void kirimData(
  String statusAlat,
  String deteksiBurung,
  String statusBuzzer,
  String keterangan)
{
  if (WiFi.status() != WL_CONNECTED)
  {
    Serial.println("WiFi NOT CONNECTED");
    return;
  }

  WiFiClientSecure client;
  client.setInsecure();
  HTTPClient http;

  http.begin(client, serverUrl);
  http.addHeader("Content-Type", "application/json");

  String json = "{";
  json += "\"status_alat\":\"" + statusAlat + "\",";
  json += "\"deteksi_burung\":\"" + deteksiBurung + "\",";
  json += "\"status_buzzer\":\"" + statusBuzzer + "\",";
  json += "\"keterangan\":\"" + keterangan + "\"";
  json += "}";

  int httpCode = http.POST(json);

  Serial.print("HTTP: ");
  Serial.println(httpCode);

  if (httpCode > 0)
  {
    String response = http.getString();
    Serial.println("RESP: " + response);
  }
  else
  {
    Serial.println("GAGAL: " + http.errorToString(httpCode));
  }

  http.end();
}

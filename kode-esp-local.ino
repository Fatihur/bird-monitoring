//========================
// PIN
//========================
#define PIR_PIN    15
#define RELAY_PIN  18

//========================
// VARIABEL
//========================
bool alarmAktif = false;

unsigned long startAlarm = 0;
unsigned long lastPrint = 0;

const unsigned long durasiAlarm = 3000;

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
  Serial.println("           (LOCAL)              ");
  Serial.println("================================");
}

//================================================
// LOOP
//================================================
void loop()
{
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
  }

  if (alarmAktif && millis() - startAlarm >= durasiAlarm)
  {
    digitalWrite(RELAY_PIN, HIGH);
    alarmAktif = false;

    Serial.println("Relay OFF");
    Serial.println("Menunggu deteksi berikutnya");
  }
}

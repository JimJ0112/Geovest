#include <WiFi.h>
#include <HTTPClient.h>
#include <TinyGPS++.h>

#define heartratePin 35
#define BUZZER_PIN 25
#define BUTTON_PIN 14

const char* ssid = "JG";
const char* password = "#Jimgen52828378";
const char* serverUrl = "http://192.168.1.113/Geovest/server/location_saving.php";

TinyGPSPlus gps;
HardwareSerial neogps(1); // UART1 for GPS

// Heart rate variables
unsigned long startTime = 0;
unsigned long lastPulseTime = 0;
int pulseCount = 0;

void setup() {
  Serial.begin(115200);
  neogps.begin(9600, SERIAL_8N1, 16, 17); // GPS RX=16, TX=17

  Serial.println("Connecting to WiFi...");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println("\nConnected to WiFi!");

  pinMode(BUTTON_PIN, INPUT_PULLUP); // Button with internal pull-up
  pinMode(BUZZER_PIN, OUTPUT);       // Buzzer output

  startTime = millis(); // Start timer
}

void loop() {
  float latitude = 0.00;
  float longitude = 0.00;
  int vestNum = 1;
  String locationName = "ph";

  // Read GPS
  while (neogps.available()) {
    gps.encode(neogps.read());
  }

  if (gps.location.isValid()) {
    latitude = gps.location.lat();
    longitude = gps.location.lng();
  }

  // Heartbeat read
  int reading = analogRead(heartratePin);
  unsigned long currentTime = millis();

  if (reading >= 4095) { 
    if (currentTime - lastPulseTime > 900) {
      pulseCount++;
      lastPulseTime = currentTime;
      Serial.println("<3 Pulse detected");
    }
  }

  // Button check - beep when pressed
  if (digitalRead(BUTTON_PIN) == LOW) { // Active low
    Serial.println("Button pressed! Beep.");
    tone(BUZZER_PIN, 1000); // 1kHz tone
    delay(200);
    noTone(BUZZER_PIN);
  }

  // Check if a minute has passed
  if (currentTime - startTime >= 60000) {
    int currentBPM = 0;

    if (pulseCount > 0) {
      currentBPM = pulseCount;

      if(currentBPM > 200 ){
        currentBPM = 200;
      } else if(currentBPM < 0 ){
        currentBPM = 0;
      }

      Serial.print("BPM: ");
      Serial.println(currentBPM);
    } else {
      Serial.println("No pulse detected in last minute. BPM: 0");
    }

    sendGPSData(vestNum, locationName, latitude, longitude, currentBPM);

    // Reset
    pulseCount = 0;
    startTime = currentTime;
  }

  delay(50); // Quick loop for responsive button
}

void sendGPSData(int vestNum, String locationName, float lat, float lng, int rate) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverUrl);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String postData = "lat=" + String(lat, 6) + "&lng=" + String(lng, 6) +
                      "&vest_num=" + String(vestNum) + "&loc_name=" + locationName +
                      "&hrate=" + String(rate);
    Serial.println("Sending: " + postData);

    int httpResponseCode = http.POST(postData);
    Serial.println("HTTP Code: " + String(httpResponseCode));
    http.end();
  } else {
    Serial.println("WiFi Disconnected!");
  }
}

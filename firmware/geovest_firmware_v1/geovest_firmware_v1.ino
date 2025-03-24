#include <WiFi.h>
#include <HTTPClient.h>
#include <TinyGPS++.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <DFRobot_Heartrate.h>

// WiFi Credentials
const char* ssid = "PLDTHOMEFIBR250c0";
const char* password = "PLDTWIFIk72ge";

// Server URL
const char* serverUrl = "http://192.168.1.14/Geovest/server/location_saving.php";

// GPS Object
TinyGPSPlus gps;
HardwareSerial neogps(1); // UART1 for GPS

#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
#define OLED_RESET -1

Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, OLED_RESET);


const int ledPin = 2; 

#define heartratePin A0

// Create an instance of the Heartrate library
DFRobot_Heartrate heartrate(DIGITAL_MODE);   // ANALOG_MODE or DIGITAL_MODE



void setup() {
    Serial.begin(115200);
    neogps.begin(9600, SERIAL_8N1, 16, 17); // RX=16, TX=17

    pinMode(ledPin, OUTPUT);
    digitalWrite(ledPin, LOW); 

  
  
    if (!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
        Serial.println("SSD1306 allocation failed");
        while (true);
    }

    displayMessage("Starting ESP32...");    
    Serial.println("Connecting to WiFi...");
    displayMessage("Connecting to WiFi...");

    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) {
        delay(1000);
        Serial.print(".");
    }
    Serial.println("\nConnected to WiFi!");
    displayMessage("WiFi Connected!");
}

void loop() {
    while (neogps.available()) {
        gps.encode(neogps.read()); 
    }

   
    float latitude = gps.location.isValid() ? gps.location.lat() : 0.00;
    float longitude = gps.location.isValid() ? gps.location.lng() : 0.00;
    int vestNum = 1;
    String locationName = "ph";


    // Get the heart rate value
  uint8_t rateValue;
  heartrate.getValue(heartratePin);   // A1 foot sampled values
  rateValue = heartrate.getRate();   // Get heart rate value 


    String gpsData = "Lat: " + String(latitude, 6) + "\n Lng: " + String(longitude, 6) + "\n HeartRate: " + rateValue;
    Serial.println(gpsData);
    displayMessage(gpsData);
    Serial.println(rateValue);

    sendGPSData(vestNum, locationName, latitude, longitude, rateValue);

    delay(1000); // Update every 5 seconds
}

void sendGPSData(int vestNum, String locationName, float lat, float lng, uint8_t rate) {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.begin(serverUrl);
        http.addHeader("Content-Type", "application/x-www-form-urlencoded");

        String postData = "lat=" + String(lat, 6) + "&lng=" + String(lng, 6) + "&vest_num=" + String(vestNum) + "&loc_name=" + locationName + "&hrate=" + String(rate);
        Serial.println("Sending: " + postData);
        displayMessage("Sending: " + postData);

        int httpResponseCode = http.POST(postData);
        String responseMsg = "HTTP Code: " + String(httpResponseCode);
        Serial.println(responseMsg);
        //displayMessage(responseMsg);
        http.end();
    } else {
        Serial.println("WiFi Disconnected!");
        displayMessage("WiFi Disconnected!");
    }
}


void displayMessage(String message) {
    display.clearDisplay();
    display.setCursor(0, 10);
    display.setTextSize(1);
    display.setTextColor(WHITE);
    display.println(message);
    display.display();
}

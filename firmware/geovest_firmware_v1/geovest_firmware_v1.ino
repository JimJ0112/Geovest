/*!
 * @file  heartrateAnalogMode.h
 * @brief  This is written for the heart rate sensor the company library. Mainly used for real 
 * @n  time measurement of blood oxygen saturation, based on measured values calculate heart rate values.
 * @copyright  Copyright (c) 2010 DFRobot Co.Ltd (http://www.dfrobot.com)
 * @license  The MIT License (MIT)
 * @author  [linfeng](Musk.lin@dfrobot.com)
 * @maintainer  [qsjhyy](yihuan.huang@dfrobot.com)
 * @version  V1.0
 * @date  2022-04-26
 * @url  https://github.com/DFRobot/DFRobot_Heartrate
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <TinyGPS++.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>

#include "DFRobot_Heartrate.h"
#define heartratePin 35


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




#define heartratePin 35


void setup() {
  Serial.begin(115200);
    neogps.begin(9600, SERIAL_8N1, 16, 17); // RX=16, TX=17
  
    if (!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
        Serial.println("SSD1306 allocation failed");
        while (true);
    }

     
    Serial.println("Connecting to WiFi...");
    

    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) {
        delay(1000);
        Serial.print(".");
    }
    Serial.println("\nConnected to WiFi!");

}

void loop() {

  float latitude =  0.00;
  float longitude = 0.00;
  int vestNum = 1;
  String locationName = "ph";


    while (neogps.available()) {
        gps.encode(neogps.read()); 
    }

    latitude = gps.location.isValid() ? gps.location.lat() : 0.00;
    longitude = gps.location.isValid() ? gps.location.lng() : 0.00;

  int heartValue = analogRead(heartratePin);
  Serial.println(heartValue);

  
  String gpsData = "Lat: " + String(latitude, 6) + "\n Lng: " + String(longitude, 6) + "\n HeartRate: " + String(heartValue);

  sendGPSData(vestNum, locationName, latitude, longitude, heartValue);
  Serial.println(gpsData);
  displayMessage(gpsData);

  delay(20);  

}


void sendGPSData(int vestNum, String locationName, float lat, float lng, int rate) {
    if (WiFi.status() == WL_CONNECTED) {
        HTTPClient http;
        http.begin(serverUrl);
        http.addHeader("Content-Type", "application/x-www-form-urlencoded");

        String postData = "lat=" + String(lat, 6) + "&lng=" + String(lng, 6) + "&vest_num=" + String(vestNum) + "&loc_name=" + locationName + "&hrate=" + String(rate);
        Serial.println("Sending: " + postData);
       
        int httpResponseCode = http.POST(postData);
        String responseMsg = "HTTP Code: " + String(httpResponseCode);
        Serial.println(responseMsg);
        http.end();
    } else {
        Serial.println("WiFi Disconnected!");
      
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
